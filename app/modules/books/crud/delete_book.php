<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . "/app/includes/connection.php";
require_once ROOT_PATH . "/app/includes/library_helpers.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	die('Invalid request.');
}

$book_id = (int) $_GET['id'];
$mode = library_delete_mode();

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
if ($userId > 0) {
	library_set_current_user($conn, $userId);
}

if ($mode === 'soft') {
	if ($userId > 0) {
		$stmt = $conn->prepare("UPDATE books SET deleted_date = NOW(), deleted_by = ? WHERE book_id = ?");
		if (!$stmt) {
			die('Delete failed.');
		}
		$stmt->bind_param('ii', $userId, $book_id);
	} else {
		$stmt = $conn->prepare("UPDATE books SET deleted_date = NOW(), deleted_by = NULL WHERE book_id = ?");
		if (!$stmt) {
			die('Delete failed.');
		}
		$stmt->bind_param('i', $book_id);
	}

	$stmt->execute();
	$stmt->close();

	header('Location: ' . BASE_URL . 'book_list.php');
	exit;
}

/* Hard delete with dependents */
$bookResult = $conn->query("SELECT book_cover_path, ebook_file_path FROM books WHERE book_id = $book_id");
if (!$bookResult || $bookResult->num_rows !== 1) {
	die('Book not found.');
}

$book = $bookResult->fetch_assoc();
$coverPath = trim((string) ($book['book_cover_path'] ?? ''));
$ebookPath = trim((string) ($book['ebook_file_path'] ?? ''));

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
	$conn->begin_transaction();

	$conn->query(
		"DELETE FROM returns
		 WHERE loan_id IN (
		 	SELECT loan_id
		 	FROM loans
		 	WHERE copy_id IN (
		 		SELECT copy_id
		 		FROM book_copies
		 		WHERE edition_id IN (
		 			SELECT edition_id
		 			FROM book_editions
		 			WHERE book_id = $book_id
		 		)
		 	)
		 )"
	);

	$conn->query(
		"DELETE FROM fines
		 WHERE loan_id IN (
		 	SELECT loan_id
		 	FROM loans
		 	WHERE copy_id IN (
		 		SELECT copy_id
		 		FROM book_copies
		 		WHERE edition_id IN (
		 			SELECT edition_id
		 			FROM book_editions
		 			WHERE book_id = $book_id
		 		)
		 	)
		 )"
	);

	$conn->query(
		"DELETE FROM reservations
		 WHERE book_id = $book_id
		    OR copy_id IN (
		    	SELECT copy_id
		    	FROM book_copies
		    	WHERE edition_id IN (
		    		SELECT edition_id
		    		FROM book_editions
		    		WHERE book_id = $book_id
		    	)
		    )"
	);

	$conn->query(
		"DELETE FROM loans
		 WHERE copy_id IN (
		 	SELECT copy_id
		 	FROM book_copies
		 	WHERE edition_id IN (
		 		SELECT edition_id
		 		FROM book_editions
		 		WHERE book_id = $book_id
		 	)
		 )"
	);

	$conn->query(
		"DELETE FROM book_copies
		 WHERE edition_id IN (
		 	SELECT edition_id
		 	FROM book_editions
		 	WHERE book_id = $book_id
		 )"
	);

	$conn->query("DELETE FROM book_editions WHERE book_id = $book_id");
	$conn->query("DELETE FROM book_categories WHERE book_id = $book_id");
	$conn->query("DELETE FROM books WHERE book_id = $book_id");

	$conn->commit();
} catch (mysqli_sql_exception $e) {
	$conn->rollback();
	die('Delete failed: ' . $e->getMessage());
}

foreach ([$coverPath, $ebookPath] as $path) {
	if ($path === '') {
		continue;
	}
	$filePath = ROOT_PATH . '/' . ltrim($path, '/');
	if (is_file($filePath)) {
		unlink($filePath);
	}
}

header('Location: ' . BASE_URL . 'book_list.php');
exit;
