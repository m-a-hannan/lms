<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . "/app/includes/connection.php";
require_once ROOT_PATH . '/app/includes/library_helpers.php';

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

$mode = library_delete_mode();
$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
library_set_current_user($conn, $userId);

/* Delete DB record */
$deleted = $mode === 'soft'
	? library_soft_delete($conn, 'roles', 'role_id', $role_id, $userId)
	: library_hard_delete($conn, 'roles', 'role_id', $role_id);
if ($deleted) {
    header("Location: " . BASE_URL . "role_list.php");
    exit;
}

die("Delete failed.");
