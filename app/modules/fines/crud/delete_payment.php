<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the incoming id to prevent invalid access.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Confirm the payment exists before attempting deletion.
$payment_id = (int) $_GET['id'];
$result = $conn->query("SELECT payment_id FROM payments WHERE payment_id = $payment_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

// Delete the payment record.
$deleted = $conn->query("DELETE FROM payments WHERE payment_id = $payment_id");
if ($deleted) {
    // Return to the list after a successful delete.
    header("Location: " . BASE_URL . "payment_list.php");
    exit;
}

// Fallback error if the delete fails.
die('Delete failed.');
