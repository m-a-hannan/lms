<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$book_cat_id = (int) $_GET['id'];
$result = $conn->query("SELECT book_cat_id FROM book_categories WHERE book_cat_id = $book_cat_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

$deleted = $conn->query("DELETE FROM book_categories WHERE book_cat_id = $book_cat_id");
if ($deleted) {
    header("Location: " . BASE_URL . "book_category_list.php");
    exit;
}

die('Delete failed.');
