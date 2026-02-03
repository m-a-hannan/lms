<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the user role id input.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Confirm the record exists before deleting.
$user_role_id = (int) $_GET['id'];
$result = $conn->query("SELECT user_role_id FROM user_roles WHERE user_role_id = $user_role_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

// Delete the user role record and redirect on success.
$deleted = $conn->query("DELETE FROM user_roles WHERE user_role_id = $user_role_id");
if ($deleted) {
    header("Location: " . BASE_URL . "user_role_list.php");
    exit;
}

// Fall back to an error message on failure.
die('Delete failed.');
