<?php
// Load core configuration, database connection, RBAC, and helpers.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';
require_once ROOT_PATH . '/app/includes/library_helpers.php';

// Ensure session is active for admin context.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Accept only POST requests for return processing.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'dashboard.php');
	exit;
}

// Require admin or librarian role to approve/reject returns.
$context = rbac_get_context($conn);
$roleName = $context['role_name'] ?? '';
$isLibrarian = strcasecmp($roleName, 'Librarian') === 0;
if (!$context['is_admin'] && !$isLibrarian) {
	http_response_code(403);
	exit('Access denied.');
}

// Validate request inputs.
$adminId = $context['user_id'] ?? 0;
$returnId = isset($_POST['return_id']) ? (int) $_POST['return_id'] : 0;
$action = $_POST['action'] ?? '';

if ($adminId <= 0 || $returnId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
	header('Location: ' . BASE_URL . 'dashboard.php');
	exit;
}

// Set DB session context for auditing triggers.
library_set_current_user($conn, $adminId);

// Wrap all updates in a transaction.
$conn->begin_transaction();

try {
	// Lock and load the return record with loan details.
$stmt = $conn->prepare(
	"SELECT r.return_id, r.loan_id, r.status, l.copy_id, l.user_id, b.title, b.book_id
	 FROM returns r
	 JOIN loans l ON r.loan_id = l.loan_id
	 JOIN book_copies c ON l.copy_id = c.copy_id
	 JOIN book_editions e ON c.edition_id = e.edition_id
	 JOIN books b ON e.book_id = b.book_id
	 WHERE r.return_id = ?
	 LIMIT 1
	 FOR UPDATE"
);
	$stmt->bind_param('i', $returnId);
	$stmt->execute();
	$result = $stmt->get_result();
	$returnRow = $result ? $result->fetch_assoc() : null;
	$stmt->close();

	// Reject when the return is missing or not pending.
	if (!$returnRow || $returnRow['status'] !== 'pending') {
		$conn->rollback();
		header('Location: ' . BASE_URL . 'dashboard.php?return=invalid');
		exit;
	}

	$loanId = (int) $returnRow['loan_id'];
	$copyId = (int) $returnRow['copy_id'];
	$userId = (int) $returnRow['user_id'];
	$bookId = (int) $returnRow['book_id'];
	$title = trim((string) ($returnRow['title'] ?? 'Book'));

	// Approve flow: update return, loan, and copy status.
	if ($action === 'approve') {
		$stmt = $conn->prepare(
			"UPDATE returns
			 SET status = 'approved', return_date = CURDATE(), modified_by = ?
			 WHERE return_id = ?"
		);
		$stmt->bind_param('ii', $adminId, $returnId);
		if (!$stmt->execute()) {
			throw new RuntimeException('Failed to approve return.');
		}
		$stmt->close();

		// Mark the loan as returned.
		$stmt = $conn->prepare(
			"UPDATE loans
			 SET status = 'returned', return_date = CURDATE(), modified_by = ?
			 WHERE loan_id = ?"
		);
		$stmt->bind_param('ii', $adminId, $loanId);
		if (!$stmt->execute()) {
			throw new RuntimeException('Failed to update loan.');
		}
		$stmt->close();

		// Release the copy back to available status.
		$stmt = $conn->prepare("UPDATE book_copies SET status = 'available', modified_by = ? WHERE copy_id = ?");
		$stmt->bind_param('ii', $adminId, $copyId);
		if (!$stmt->execute()) {
			throw new RuntimeException('Failed to update copy status.');
		}
		$stmt->close();

		// Notify the user about approval.
		library_notify_user(
			$conn,
			$userId,
			'Return approved',
			"Your return for \"{$title}\" has been approved.",
			$adminId
		);

		// Auto-approve the next reservation when possible.
		if ($bookId > 0) {
			$queueStmt = $conn->prepare(
				"SELECT reservation_id, user_id
				 FROM reservations
				 WHERE book_id = ? AND status = 'pending'
				 ORDER BY created_date ASC, reservation_id ASC
				 LIMIT 1
				 FOR UPDATE"
			);
			$queueStmt->bind_param('i', $bookId);
			$queueStmt->execute();
			$queueResult = $queueStmt->get_result();
			$queueRow = $queueResult ? $queueResult->fetch_assoc() : null;
			$queueStmt->close();

			// Assign the copy to the next reservation in queue.
			if ($queueRow) {
				$reservationId = (int) $queueRow['reservation_id'];
				$reservationUserId = (int) $queueRow['user_id'];
				$days = library_get_policy_days($conn, 'reservation_expiry_days', 3);
				$expiryDate = (new DateTimeImmutable('today'))->modify('+' . $days . ' days')->format('Y-m-d');

				// Approve reservation and set expiry date.
				$updateRes = $conn->prepare(
					"UPDATE reservations
					 SET status = 'approved',
					     copy_id = ?,
					     expiry_date = COALESCE(expiry_date, ?),
					     modified_by = ?
					 WHERE reservation_id = ?"
				);
				$updateRes->bind_param('isii', $copyId, $expiryDate, $adminId, $reservationId);
				if (!$updateRes->execute()) {
					throw new RuntimeException('Failed to assign reservation.');
				}
				$updateRes->close();

				// Mark the copy as reserved.
				$updateCopy = $conn->prepare(
					"UPDATE book_copies
					 SET status = 'reserved', modified_by = ?
					 WHERE copy_id = ?"
				);
				$updateCopy->bind_param('ii', $adminId, $copyId);
				$updateCopy->execute();
				$updateCopy->close();

				// Notify the reservation holder.
				library_notify_user(
					$conn,
					$reservationUserId,
					'Reservation approved',
					"Your reservation for \"{$title}\" is now available.",
					$adminId
				);
			}
		}
	} else {
		// Reject flow: update return status and notify user.
		$stmt = $conn->prepare("UPDATE returns SET status = 'rejected', modified_by = ? WHERE return_id = ?");
		$stmt->bind_param('ii', $adminId, $returnId);
		if (!$stmt->execute()) {
			throw new RuntimeException('Failed to reject return.');
		}
		$stmt->close();

		library_notify_user(
			$conn,
			$userId,
			'Return rejected',
			"Your return for \"{$title}\" has been rejected.",
			$adminId
		);
	}

	// Commit all changes after successful processing.
	$conn->commit();
} catch (Throwable $e) {
	// Roll back and report errors.
	$conn->rollback();
	header('Location: ' . BASE_URL . 'dashboard.php?return=error');
	exit;
}

// Redirect back with success status.
header('Location: ' . BASE_URL . 'dashboard.php?return=success');
exit;
