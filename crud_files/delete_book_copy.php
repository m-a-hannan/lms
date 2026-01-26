<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$copy_id = (int) $_GET['id'];
$result = $conn->query("SELECT copy_id FROM book_copies WHERE copy_id = $copy_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

$deleted = $conn->query("DELETE FROM book_copies WHERE copy_id = $copy_id");
if ($deleted) {
    header("Location: " . BASE_URL . "book_copy_list.php");
    exit;
}

die('Delete failed.');
