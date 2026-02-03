<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the incoming id to prevent invalid access.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Confirm the return record exists before attempting deletion.
$return_id = (int) $_GET['id'];
$result = $conn->query("SELECT return_id FROM returns WHERE return_id = $return_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

// Delete the return record.
$deleted = $conn->query("DELETE FROM returns WHERE return_id = $return_id");
if ($deleted) {
    // Return to the list after a successful delete.
    header("Location: " . BASE_URL . "return_list.php");
    exit;
}

// Fallback error if the delete fails.
die('Delete failed.');
