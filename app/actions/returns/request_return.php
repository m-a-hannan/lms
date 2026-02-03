<?php
// Load core configuration, database connection, and helper functions.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/library_helpers.php';

// Ensure session is active for user context.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Accept only POST requests for return submissions.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'user_dashboard.php');
	exit;
}

// Read user and loan identifiers from session and form.
$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$loanId = isset($_POST['loan_id']) ? (int) $_POST['loan_id'] : 0;

// Validate required identifiers before proceeding.
if ($userId <= 0 || $loanId <= 0) {
	header('Location: ' . BASE_URL . 'user_dashboard.php');
	exit;
}

// Set DB session context for auditing triggers.
library_set_current_user($conn, $userId);

// Wrap validation and insert in a transaction.
$conn->begin_transaction();

try {
	// Lock and load the loan record for this user.
	$stmt = $conn->prepare(
		"SELECT l.loan_id, l.copy_id, l.status, l.return_date, b.title
		 FROM loans l
		 JOIN book_copies c ON l.copy_id = c.copy_id
		 JOIN book_editions e ON c.edition_id = e.edition_id
		 JOIN books b ON e.book_id = b.book_id
		 WHERE l.loan_id = ? AND l.user_id = ?
		 LIMIT 1
		 FOR UPDATE"
	);
	$stmt->bind_param('ii', $loanId, $userId);
	$stmt->execute();
	$result = $stmt->get_result();
	$loan = $result ? $result->fetch_assoc() : null;
	$stmt->close();

	// Reject if the loan is not eligible for return.
	if (!$loan || $loan['status'] !== 'approved' || !empty($loan['return_date'])) {
		$conn->rollback();
		header('Location: ' . BASE_URL . 'user_dashboard.php?return=invalid');
		exit;
	}

	// Prevent duplicate pending return requests.
	$existing = $conn->query(
		"SELECT return_id FROM returns
		 WHERE loan_id = $loanId AND status = 'pending'
		 LIMIT 1"
	);
	if ($existing && $existing->num_rows > 0) {
		$conn->rollback();
		header('Location: ' . BASE_URL . 'user_dashboard.php?return=pending');
		exit;
	}

	// Insert the pending return request.
	$stmt = $conn->prepare(
		"INSERT INTO returns (loan_id, status, created_by)
		 VALUES (?, 'pending', ?)"
	);
	$stmt->bind_param('ii', $loanId, $userId);
	if (!$stmt->execute()) {
		throw new RuntimeException('Failed to create return request.');
	}
	$stmt->close();

	// Commit after successful insert.
	$conn->commit();

	// Notify the requester and staff roles.
	$title = trim((string) ($loan['title'] ?? 'Book'));
	library_notify_user(
		$conn,
		$userId,
		'Return request submitted',
		"Your return request for \"{$title}\" has been submitted.",
		$userId
	);
	library_notify_roles(
		$conn,
		['Admin', 'Librarian'],
		'New return request',
		"A return request was submitted for loan #{$loanId} (\"{$title}\").",
		$userId
	);
} catch (Throwable $e) {
	// Roll back on errors and return a failure status.
	$conn->rollback();
	header('Location: ' . BASE_URL . 'user_dashboard.php?return=error');
	exit;
}

// Redirect with success status after processing.
header('Location: ' . BASE_URL . 'user_dashboard.php?return=success');
exit;
