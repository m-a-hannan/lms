<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$edition_id = (int) $_GET['id'];
$result = $conn->query("SELECT edition_id FROM book_editions WHERE edition_id = $edition_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

$deleted = $conn->query("DELETE FROM book_editions WHERE edition_id = $edition_id");
if ($deleted) {
    header("Location: " . BASE_URL . "book_edition_list.php");
    exit;
}

die('Delete failed.');
