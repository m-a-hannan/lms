<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$payment_id = (int) $_GET['id'];
$result = $conn->query("SELECT payment_id FROM payments WHERE payment_id = $payment_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

$deleted = $conn->query("DELETE FROM payments WHERE payment_id = $payment_id");
if ($deleted) {
    header("Location: " . BASE_URL . "payment_list.php");
    exit;
}

die('Delete failed.');