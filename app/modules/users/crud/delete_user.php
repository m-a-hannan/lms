<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the user id input.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Confirm the record exists before deleting.
$user_id = (int) $_GET['id'];
$result = $conn->query("SELECT user_id FROM users WHERE user_id = $user_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

// Delete the user record and redirect on success.
$deleted = $conn->query("DELETE FROM users WHERE user_id = $user_id");
if ($deleted) {
    header("Location: " . BASE_URL . "user_list.php");
    exit;
}

// Fall back to an error message on failure.
die('Delete failed.');
