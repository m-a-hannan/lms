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

// Accept only POST requests for loan processing.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'dashboard.php');
	exit;
}

// Require admin or librarian role to approve/reject loans.
$context = rbac_get_context($conn);
$roleName = $context['role_name'] ?? '';
$isLibrarian = strcasecmp($roleName, 'Librarian') === 0;
if (!$context['is_admin'] && !$isLibrarian) {
	http_response_code(403);
	exit('Access denied.');
}

// Validate request inputs.
$adminId = $context['user_id'] ?? 0;
$loanId = isset($_POST['loan_id']) ? (int) $_POST['loan_id'] : 0;
$action = $_POST['action'] ?? '';

if ($adminId <= 0 || $loanId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
	header('Location: ' . BASE_URL . 'dashboard.php');
	exit;
}

// Set DB session context for auditing triggers.
library_set_current_user($conn, $adminId);

// Wrap all updates in a transaction.
$conn->begin_transaction();

try {
	// Lock and load the loan record with book details.
	$stmt = $conn->prepare(
		"SELECT l.loan_id, l.copy_id, l.user_id, l.status, b.title, b.book_id
		 FROM loans l
		 JOIN book_copies c ON l.copy_id = c.copy_id
		 JOIN book_editions e ON c.edition_id = e.edition_id
		 JOIN books b ON e.book_id = b.book_id
		 WHERE l.loan_id = ?
		 LIMIT 1
		 FOR UPDATE"
	);
	$stmt->bind_param('i', $loanId);
	$stmt->execute();
	$result = $stmt->get_result();
	$loan = $result ? $result->fetch_assoc() : null;
	$stmt->close();

	// Reject when the loan is missing or not pending.
	if (!$loan || $loan['status'] !== 'pending') {
		$conn->rollback();
		header('Location: ' . BASE_URL . 'dashboard.php?loan=invalid');
		exit;
	}

	$copyId = (int) $loan['copy_id'];
	$userId = (int) $loan['user_id'];
	$title = trim((string) ($loan['title'] ?? 'Book'));
	$bookId = (int) ($loan['book_id'] ?? 0);

	// Approve flow: allocate a copy and update statuses.
	if ($action === 'approve') {
		$useReservedCopy = false;
		// Prefer the reserved copy when it's valid for this user.
		if ($copyId > 0) {
			$checkStmt = $conn->prepare(
				"SELECT c.copy_id
				 FROM book_copies c
				 LEFT JOIN reservations r ON r.copy_id = c.copy_id
				 WHERE c.copy_id = ?
				   AND (
						(c.status = 'reserved' AND r.user_id = ? AND r.status = 'approved'
						 AND (r.expiry_date IS NULL OR r.expiry_date >= CURDATE()))
						OR c.status = 'available'
				   )
				 LIMIT 1
				 FOR UPDATE"
			);
			$checkStmt->bind_param('ii', $copyId, $userId);
			$checkStmt->execute();
			$checkResult = $checkStmt->get_result();
			$copyOk = $checkResult ? $checkResult->fetch_assoc() : null;
			$checkStmt->close();
			$useReservedCopy = (bool) $copyOk;
		}

		// Fall back to any available copy for the book.
		if (!$useReservedCopy) {
			$copyStmt = $conn->prepare(
				"SELECT c.copy_id
				 FROM book_copies c
				 JOIN book_editions e ON c.edition_id = e.edition_id
				 WHERE e.book_id = ?
				   AND (c.status IS NULL OR c.status = '' OR c.status = 'available')
				 ORDER BY c.copy_id ASC
				 LIMIT 1
				 FOR UPDATE"
			);
			$copyStmt->bind_param('i', $bookId);
			$copyStmt->execute();
			$copyResult = $copyStmt->get_result();
			$copyRow = $copyResult ? $copyResult->fetch_assoc() : null;
			$copyStmt->close();

			if (!$copyRow) {
				$conn->rollback();
				header('Location: ' . BASE_URL . 'dashboard.php?loan=unavailable');
				exit;
			}

			$copyId = (int) $copyRow['copy_id'];
		}
		// Calculate due date from policy days.
		$days = library_get_policy_days($conn, 'loan_period_days', 14);
		$dueDate = (new DateTimeImmutable('today'))->modify('+' . $days . ' days')->format('Y-m-d');

		// Update the loan record with approval details.
		$stmt = $conn->prepare(
			"UPDATE loans
			 SET status = 'approved',
			     copy_id = ?,
			     issue_date = CURDATE(),
			     due_date = ?,
			     modified_by = ?
			 WHERE loan_id = ?"
		);
		$stmt->bind_param('isii', $copyId, $dueDate, $adminId, $loanId);
		if (!$stmt->execute()) {
			throw new RuntimeException('Failed to approve loan.');
		}
		$stmt->close();

		// Mark the allocated copy as loaned.
		$stmt = $conn->prepare("UPDATE book_copies SET status = 'loaned', modified_by = ? WHERE copy_id = ?");
		$stmt->bind_param('ii', $adminId, $copyId);
		if (!$stmt->execute()) {
			throw new RuntimeException('Failed to update copy status.');
		}
		$stmt->close();

		// Fulfill any matching reservation when applicable.
		if ($useReservedCopy) {
			$resStmt = $conn->prepare(
				"UPDATE reservations
				 SET status = 'fulfilled',
				     modified_by = ?
				 WHERE user_id = ?
				   AND copy_id = ?
				   AND status = 'approved'"
			);
			$resStmt->bind_param('iii', $adminId, $userId, $copyId);
			$resStmt->execute();
			$resStmt->close();
		}

		// Notify the user about approval.
		library_notify_user(
			$conn,
			$userId,
			'Loan request approved',
			"Your loan request for \"{$title}\" has been approved.",
			$adminId
		);
	} else {
		// Reject flow: update loan status and notify user.
		$stmt = $conn->prepare("UPDATE loans SET status = 'rejected', modified_by = ? WHERE loan_id = ?");
		$stmt->bind_param('ii', $adminId, $loanId);
		if (!$stmt->execute()) {
			throw new RuntimeException('Failed to reject loan.');
		}
		$stmt->close();

		library_notify_user(
			$conn,
			$userId,
			'Loan request rejected',
			"Your loan request for \"{$title}\" has been rejected.",
			$adminId
		);
	}

	// Commit all changes after successful processing.
	$conn->commit();
} catch (Throwable $e) {
	// Roll back and report errors.
	$conn->rollback();
	header('Location: ' . BASE_URL . 'dashboard.php?loan=error');
	exit;
}

// Redirect back with success status.
header('Location: ' . BASE_URL . 'dashboard.php?loan=success');
exit;
