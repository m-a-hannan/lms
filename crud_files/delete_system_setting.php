<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$setting_id = (int) $_GET['id'];
$result = $conn->query("SELECT setting_id FROM system_settings WHERE setting_id = $setting_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

$deleted = $conn->query("DELETE FROM system_settings WHERE setting_id = $setting_id");
if ($deleted) {
    header("Location: " . BASE_URL . "system_setting_list.php");
    exit;
}

die('Delete failed.');
