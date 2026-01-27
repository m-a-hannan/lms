<?php
require_once __DIR__ . '/../include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/library_helpers.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'user_dashboard.php');
	exit;
}

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$loanId = isset($_POST['loan_id']) ? (int) $_POST['loan_id'] : 0;

if ($userId <= 0 || $loanId <= 0) {
	header('Location: ' . BASE_URL . 'user_dashboard.php');
	exit;
}

library_set_current_user($conn, $userId);

$conn->begin_transaction();

try {
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

	if (!$loan || $loan['status'] !== 'approved' || !empty($loan['return_date'])) {
		$conn->rollback();
		header('Location: ' . BASE_URL . 'user_dashboard.php?return=invalid');
		exit;
	}

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

	$stmt = $conn->prepare(
		"INSERT INTO returns (loan_id, status, created_by)
		 VALUES (?, 'pending', ?)"
	);
	$stmt->bind_param('ii', $loanId, $userId);
	if (!$stmt->execute()) {
		throw new RuntimeException('Failed to create return request.');
	}
	$stmt->close();

	$conn->commit();

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
	$conn->rollback();
	header('Location: ' . BASE_URL . 'user_dashboard.php?return=error');
	exit;
}

header('Location: ' . BASE_URL . 'user_dashboard.php?return=success');
exit;
