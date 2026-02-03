<?php
// Load core configuration, database connection, and helper functions.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/library_helpers.php';

// Ensure session is active for user context.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Accept only POST requests for reservations.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

// Read user and book identifiers from session and form.
$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$bookId = isset($_POST['book_id']) ? (int) $_POST['book_id'] : 0;

// Validate required identifiers before proceeding.
if ($userId <= 0 || $bookId <= 0) {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

// Set DB session context for auditing triggers.
library_set_current_user($conn, $userId);

// Wrap validation and insert in a transaction.
$conn->begin_transaction();

try {
	// Lock the book row and compute available copies.
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

	// Reject when the book is missing.
	if (!$book) {
		$conn->rollback();
		header('Location: ' . BASE_URL . 'home.php?reserve=invalid');
		exit;
	}

	// Avoid reservations when copies are still available.
	$availableCount = (int) ($book['available_count'] ?? 0);
	if ($availableCount > 0) {
		$conn->rollback();
		header('Location: ' . BASE_URL . 'home.php?reserve=available');
		exit;
	}

	// Insert the pending reservation request.
	$stmt = $conn->prepare(
		"INSERT INTO reservations (user_id, book_id, reservation_date, status, created_by)
		 VALUES (?, ?, CURDATE(), 'pending', ?)"
	);
	$stmt->bind_param('iii', $userId, $bookId, $userId);
	if (!$stmt->execute()) {
		throw new RuntimeException('Failed to create reservation.');
	}
	$stmt->close();

	// Commit after successful insert.
	$conn->commit();

	// Notify the requester and staff roles.
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
	// Roll back on errors and return a failure status.
	$conn->rollback();
	header('Location: ' . BASE_URL . 'home.php?reserve=error');
	exit;
}

// Redirect with success status after processing.
header('Location: ' . BASE_URL . 'home.php?reserve=success');
exit;
