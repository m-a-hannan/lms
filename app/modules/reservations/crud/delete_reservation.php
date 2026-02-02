<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$reservation_id = (int) $_GET['id'];
$result = $conn->query("SELECT reservation_id FROM reservations WHERE reservation_id = $reservation_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

$deleted = $conn->query("DELETE FROM reservations WHERE reservation_id = $reservation_id");
if ($deleted) {
    header("Location: " . BASE_URL . "reservation_list.php");
    exit;
}

die('Delete failed.');