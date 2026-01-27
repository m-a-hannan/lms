<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$user_role_id = (int) $_GET['id'];
$result = $conn->query("SELECT user_role_id FROM user_roles WHERE user_role_id = $user_role_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

$deleted = $conn->query("DELETE FROM user_roles WHERE user_role_id = $user_role_id");
if ($deleted) {
    header("Location: " . BASE_URL . "user_role_list.php");
    exit;
}

die('Delete failed.');
