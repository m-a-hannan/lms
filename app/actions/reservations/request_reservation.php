<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/library_helpers.php';

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
	$stmt = $conn->prepare(
		"SELECT b.title,
				(SELECT COUNT(*)
				 FROM book_copies c
				 JOIN book_editions e ON c.edition_id = e.edition_id
				 WHERE e.book_id = b.book_id
				   AND (c.status IS NULL OR c.status = '' OR c.status = 'available')) AS available_count
		 FROM books b
		 WHERE b.book_id = ?
		 LIMIT 1
		 FOR UPDATE"
	);
	$stmt->bind_param('i', $bookId);
	$stmt->execute();
	$result = $stmt->get_result();
	$book = $result ? $result->fetch_assoc() : null;
	$stmt->close();

	if (!$book) {
		$conn->rollback();
		header('Location: ' . BASE_URL . 'home.php?reserve=invalid');
		exit;
	}

	$availableCount = (int) ($book['available_count'] ?? 0);
	if ($availableCount > 0) {
		$conn->rollback();
		header('Location: ' . BASE_URL . 'home.php?reserve=available');
		exit;
	}

	$stmt = $conn->prepare(
		"INSERT INTO reservations (user_id, book_id, reservation_date, status, created_by)
		 VALUES (?, ?, CURDATE(), 'pending', ?)"
	);
	$stmt->bind_param('iii', $userId, $bookId, $userId);
	if (!$stmt->execute()) {
		throw new RuntimeException('Failed to create reservation.');
	}
	$stmt->close();

	$conn->commit();

	$title = trim((string) ($book['title'] ?? 'Book'));
	library_notify_user(
		$conn,
		$userId,
		'Reservation request submitted',
		"Your reservation request for \"{$title}\" has been submitted.",
		$userId
	);
	library_notify_roles(
		$conn,
		['Admin', 'Librarian'],
		'New reservation request',
		"A new reservation request was submitted for \"{$title}\".",
		$userId
	);
} catch (Throwable $e) {
	$conn->rollback();
	header('Location: ' . BASE_URL . 'home.php?reserve=error');
	exit;
}

header('Location: ' . BASE_URL . 'home.php?reserve=success');
exit;