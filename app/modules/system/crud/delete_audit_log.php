<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the incoming id to prevent invalid access.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Confirm the audit log exists before attempting deletion.
$log_id = (int) $_GET['id'];
$result = $conn->query("SELECT log_id FROM audit_logs WHERE log_id = $log_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

// Delete the audit log record.
$deleted = $conn->query("DELETE FROM audit_logs WHERE log_id = $log_id");
if ($deleted) {
    // Return to the list after a successful delete.
    header("Location: " . BASE_URL . "audit_log_list.php");
    exit;
}

// Fallback error if the delete fails.
die('Delete failed.');
