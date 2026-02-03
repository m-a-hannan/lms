<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the loan id input.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Confirm the record exists before deleting.
$loan_id = (int) $_GET['id'];
$result = $conn->query("SELECT loan_id FROM loans WHERE loan_id = $loan_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

// Delete the loan record and redirect on success.
$deleted = $conn->query("DELETE FROM loans WHERE loan_id = $loan_id");
if ($deleted) {
    header("Location: " . BASE_URL . "loan_list.php");
    exit;
}

// Fall back to an error message on failure.
die('Delete failed.');
