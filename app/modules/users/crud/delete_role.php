<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . "/app/includes/connection.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid request.");
}

$role_id = (int) $_GET["id"];

/* Fetch image path */
$result = $conn->query("SELECT role_name FROM roles WHERE role_id = $role_id");

if ($result->num_rows !== 1) {
    die("role not found.");
}

$role = $result->fetch_assoc();

/* Delete DB record */
if ($conn->query("DELETE FROM roles WHERE role_id = $role_id")) {
    header("Location: " . BASE_URL . "role_list.php");
    exit;
}

die("Delete failed.");