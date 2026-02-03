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

// Accept only POST requests for reservation processing.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'dashboard.php');
	exit;
}

// Require admin or librarian role to approve/reject reservations.
$context = rbac_get_context($conn);
$roleName = $context['role_name'] ?? '';
$isLibrarian = strcasecmp($roleName, 'Librarian') === 0;
if (!$context['is_admin'] && !$isLibrarian) {
	http_response_code(403);
	exit('Access denied.');
}

// Validate request inputs.
$adminId = $context['user_id'] ?? 0;
$reservationId = isset($_POST['reservation_id']) ? (int) $_POST['reservation_id'] : 0;
$action = $_POST['action'] ?? '';

if ($adminId <= 0 || $reservationId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
	header('Location: ' . BASE_URL . 'dashboard.php');
	exit;
}

// Set DB session context for auditing triggers.
library_set_current_user($conn, $adminId);

// Wrap all updates in a transaction.
$conn->begin_transaction();

try {
// Lock and load the reservation record.
$stmt = $conn->prepare(
	"SELECT r.reservation_id, r.copy_id, r.book_id, r.user_id, r.status, b.title
	 FROM reservations r
	 JOIN books b ON r.book_id = b.book_id
	 WHERE r.reservation_id = ?
	 LIMIT 1
	 FOR UPDATE"
);
	$stmt->bind_param('i', $reservationId);
	$stmt->execute();
	$result = $stmt->get_result();
	$reservation = $result ? $result->fetch_assoc() : null;
	$stmt->close();

	// Reject when the reservation is missing or not pending.
	if (!$reservation || $reservation['status'] !== 'pending') {
		$conn->rollback();
		header('Location: ' . BASE_URL . 'dashboard.php?reservation=invalid');
		exit;
	}

$copyId = (int) ($reservation['copy_id'] ?? 0);
$bookId = (int) ($reservation['book_id'] ?? 0);
$userId = (int) $reservation['user_id'];
$title = trim((string) ($reservation['title'] ?? 'Book'));

// Approve flow: assign a copy and update statuses.
if ($action === 'approve') {
	// Require a valid book id for approval.
	if ($bookId <= 0) {
		$conn->rollback();
		header('Location: ' . BASE_URL . 'dashboard.php?reservation=invalid');
		exit;
	}

	// Find an available copy when none is assigned yet.
	if ($copyId <= 0) {
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

		// Reject when no available copy exists.
		if (!$copyRow) {
			$conn->rollback();
			header('Location: ' . BASE_URL . 'dashboard.php?reservation=unavailable');
			exit;
		}

		$copyId = (int) $copyRow['copy_id'];
	}

	// Calculate reservation expiry date from policy.
	$days = library_get_policy_days($conn, 'reservation_expiry_days', 3);
	$expiryDate = (new DateTimeImmutable('today'))->modify('+' . $days . ' days')->format('Y-m-d');

	// Approve the reservation and assign a copy.
	$stmt = $conn->prepare(
		"UPDATE reservations
		 SET status = 'approved',
		     copy_id = ?,
		     expiry_date = COALESCE(expiry_date, ?),
		     modified_by = ?
		 WHERE reservation_id = ?"
	);
	$stmt->bind_param('isii', $copyId, $expiryDate, $adminId, $reservationId);
	if (!$stmt->execute()) {
		throw new RuntimeException('Failed to approve reservation.');
	}
	$stmt->close();

	// Mark the copy as reserved.
	$stmt = $conn->prepare("UPDATE book_copies SET status = 'reserved', modified_by = ? WHERE copy_id = ?");
		$stmt->bind_param('ii', $adminId, $copyId);
		if (!$stmt->execute()) {
			throw new RuntimeException('Failed to update copy status.');
		}
		$stmt->close();

		// Notify the user about approval.
		library_notify_user(
			$conn,
			$userId,
			'Reservation approved',
			"Your reservation for \"{$title}\" has been approved.",
			$adminId
		);
	} else {
		// Reject flow: update reservation status and notify user.
		$stmt = $conn->prepare("UPDATE reservations SET status = 'rejected', modified_by = ? WHERE reservation_id = ?");
		$stmt->bind_param('ii', $adminId, $reservationId);
		if (!$stmt->execute()) {
			throw new RuntimeException('Failed to reject reservation.');
		}
		$stmt->close();

		$stmt = $conn->prepare(
			"UPDATE book_copies
			 SET status = 'available', modified_by = ?
			 WHERE copy_id = ? AND status = 'hold_reservation'"
		);
		$stmt->bind_param('ii', $adminId, $copyId);
		$stmt->execute();
		$stmt->close();

		// Notify the user about rejection.
		library_notify_user(
			$conn,
			$userId,
			'Reservation rejected',
			"Your reservation for \"{$title}\" has been rejected.",
			$adminId
		);
	}

	// Commit all changes after successful processing.
	$conn->commit();
} catch (Throwable $e) {
	// Roll back and report errors.
	$conn->rollback();
	header('Location: ' . BASE_URL . 'dashboard.php?reservation=error');
	exit;
}

// Redirect back with success status.
header('Location: ' . BASE_URL . 'dashboard.php?reservation=success');
exit;
