<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the incoming id to prevent invalid access.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Confirm the policy change exists before attempting deletion.
$change_id = (int) $_GET['id'];
$result = $conn->query("SELECT change_id FROM policy_changes WHERE change_id = $change_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

// Delete the policy change record.
$deleted = $conn->query("DELETE FROM policy_changes WHERE change_id = $change_id");
if ($deleted) {
    // Return to the list after a successful delete.
    header("Location: " . BASE_URL . "policy_change_list.php");
    exit;
}

// Fallback error if the delete fails.
die('Delete failed.');
