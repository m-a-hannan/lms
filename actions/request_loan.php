<?php
require_once __DIR__ . '/../include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/library_helpers.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$bookId = isset($_POST['book_id']) ? (int) $_POST['book_id'] : 0;

if ($userId <= 0 || $bookId <= 0) {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

library_set_current_user($conn, $userId);

$conn->begin_transaction();

try {
	$copy = null;
	$resStmt = $conn->prepare(
		"SELECT r.copy_id, b.title
		 FROM reservations r
		 JOIN books b ON r.book_id = b.book_id
		 JOIN book_copies c ON r.copy_id = c.copy_id
		 WHERE r.book_id = ?
		   AND r.user_id = ?
		   AND r.status = 'approved'
		   AND (r.expiry_date IS NULL OR r.expiry_date >= CURDATE())
		   AND c.status = 'reserved'
		 ORDER BY r.created_date ASC
		 LIMIT 1
		 FOR UPDATE"
	);
	$resStmt->bind_param('ii', $bookId, $userId);
	$resStmt->execute();
	$resResult = $resStmt->get_result();
	$copy = $resResult ? $resResult->fetch_assoc() : null;
	$resStmt->close();

	if (!$copy) {
		$stmt = $conn->prepare(
			"SELECT c.copy_id, c.status, b.title
			 FROM book_copies c
			 JOIN book_editions e ON c.edition_id = e.edition_id
			 JOIN books b ON e.book_id = b.book_id
			 WHERE b.book_id = ?
			   AND (c.status IS NULL OR c.status = '' OR c.status = 'available')
			 ORDER BY c.copy_id ASC
			 LIMIT 1
			 FOR UPDATE"
		);
		$stmt->bind_param('i', $bookId);
		$stmt->execute();
		$result = $stmt->get_result();
		$copy = $result ? $result->fetch_assoc() : null;
		$stmt->close();
	}

	if (!$copy) {
		$conn->rollback();
		header('Location: ' . BASE_URL . 'home.php?loan=unavailable');
		exit;
	}

	$copyId = (int) $copy['copy_id'];
	$stmt = $conn->prepare(
		"INSERT INTO loans (copy_id, user_id, status, created_by)
		 VALUES (?, ?, 'pending', ?)"
	);
	$stmt->bind_param('iii', $copyId, $userId, $userId);
	if (!$stmt->execute()) {
		throw new RuntimeException('Failed to create loan request.');
	}
	$stmt->close();

	$conn->commit();

	$title = trim((string) ($copy['title'] ?? 'Book'));
	library_notify_user(
		$conn,
		$userId,
		'Loan request submitted',
		"Your loan request for \"{$title}\" has been submitted.",
		$userId
	);
	library_notify_roles(
		$conn,
		['Admin', 'Librarian'],
		'New loan request',
		"A new loan request was submitted for \"{$title}\".",
		$userId
	);
} catch (Throwable $e) {
	$conn->rollback();
	header('Location: ' . BASE_URL . 'home.php?loan=error');
	exit;
}

header('Location: ' . BASE_URL . 'home.php?loan=success');
exit;
