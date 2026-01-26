<?php
require_once dirname(__DIR__) . "/include/config.php";
require_once ROOT_PATH . "/include/connection.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid request.");
}

$category_id = (int) $_GET["id"];

/* Fetch image path */
$result = $conn->query("SELECT category_name FROM categories WHERE category_id = $category_id");

if ($result->num_rows !== 1) {
    die("Category not found.");
}

$category = $result->fetch_assoc();

/* Delete DB record */
if ($conn->query("DELETE FROM categories WHERE category_id = $category_id")) {
    header("Location: " . BASE_URL . "category_list.php");
    exit;
}

die("Delete failed.");
