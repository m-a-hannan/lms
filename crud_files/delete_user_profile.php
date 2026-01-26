<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$profile_id = (int) $_GET['id'];
$result = $conn->query("SELECT profile_id FROM user_profiles WHERE profile_id = $profile_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

$deleted = $conn->query("DELETE FROM user_profiles WHERE profile_id = $profile_id");
if ($deleted) {
    header("Location: " . BASE_URL . "user_profile_list.php");
    exit;
}

die('Delete failed.');
