<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/library_helpers.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$profile_id = (int) $_GET['id'];
$result = $conn->query("SELECT profile_id FROM user_profiles WHERE profile_id = $profile_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

$mode = library_delete_mode();
$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
library_set_current_user($conn, $userId);

$deleted = $mode === 'soft'
	? library_soft_delete($conn, 'user_profiles', 'profile_id', $profile_id, $userId)
	: library_hard_delete($conn, 'user_profiles', 'profile_id', $profile_id);
if ($deleted) {
    header("Location: " . BASE_URL . "user_profile_list.php");
    exit;
}

die('Delete failed.');
