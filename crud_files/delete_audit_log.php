<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$log_id = (int) $_GET['id'];
$result = $conn->query("SELECT log_id FROM audit_logs WHERE log_id = $log_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

$deleted = $conn->query("DELETE FROM audit_logs WHERE log_id = $log_id");
if ($deleted) {
    header("Location: " . BASE_URL . "audit_log_list.php");
    exit;
}

die('Delete failed.');
