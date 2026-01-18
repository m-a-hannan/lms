<?php
require_once dirname(__DIR__) . "/include/config.php";
require_once ROOT_PATH . "/include/connection.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid request.");
}

$category_id = (int) $_GET["id"];

/* Fetch image path */
$stmt = $conn->prepare("SELECT category_name FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Category not found.");
}

$category = $result->fetch_assoc();

/* Delete DB record */
$del = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
$del->bind_param("i", $category_id);

if ($del->execute()) {
    header("Location: " . BASE_URL . "category_list.php");
    exit;
}

die("Delete failed.");
