<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . "/app/includes/connection.php";

// Validate the category id input.
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid request.");
}

$category_id = (int) $_GET["id"];

// Confirm the category exists before deleting.
$result = $conn->query("SELECT category_name FROM categories WHERE category_id = $category_id");

if ($result->num_rows !== 1) {
    die("Category not found.");
}

$category = $result->fetch_assoc();

// Delete the category record and redirect on success.
if ($conn->query("DELETE FROM categories WHERE category_id = $category_id")) {
    header("Location: " . BASE_URL . "category_list.php");
    exit;
}

// Fall back to an error message on failure.
die("Delete failed.");
