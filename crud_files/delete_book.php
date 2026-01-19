<?php
require_once dirname(__DIR__) . "/include/config.php";
require_once ROOT_PATH . "/include/connection.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid request.");
}

$book_id = (int) $_GET["id"];

/* Fetch image path */
$result = $conn->query("SELECT book_cover_path FROM books WHERE book_id = $book_id");

if ($result->num_rows !== 1) {
    die("Book not found.");
}

$book = $result->fetch_assoc();

/* Delete DB record */
$deleted = $conn->query("DELETE FROM books WHERE book_id = $book_id");
if ($deleted) {

    if (!empty($book["book_cover_path"])) {
        $filePath = ROOT_PATH . '/' . ltrim($book["book_cover_path"], '/');
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    header("Location: " . BASE_URL . "book_list.php");
    exit;
}

die("Delete failed.");
