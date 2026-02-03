<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the book-category id input.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Confirm the record exists before deleting.
$book_cat_id = (int) $_GET['id'];
$result = $conn->query("SELECT book_cat_id FROM book_categories WHERE book_cat_id = $book_cat_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

// Delete the book-category record and redirect on success.
$deleted = $conn->query("DELETE FROM book_categories WHERE book_cat_id = $book_cat_id");
if ($deleted) {
    header("Location: " . BASE_URL . "book_category_list.php");
    exit;
}

// Fall back to an error message on failure.
die('Delete failed.');
