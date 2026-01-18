<?php
require_once dirname(__DIR__) . "/include/config.php";
require_once ROOT_PATH . "/include/connection.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid request.");
}

$book_id = (int) $_GET["id"];

/* Fetch image path */
$stmt = $conn->prepare("SELECT book_cover_path FROM books WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Book not found.");
}

$book = $result->fetch_assoc();

/* Delete DB record */
$del = $conn->prepare("DELETE FROM books WHERE book_id = ?");
$del->bind_param("i", $book_id);

if ($del->execute()) {

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
