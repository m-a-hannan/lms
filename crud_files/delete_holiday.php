<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$holiday_id = (int) $_GET['id'];
$result = $conn->query("SELECT holiday_id FROM holidays WHERE holiday_id = $holiday_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

$deleted = $conn->query("DELETE FROM holidays WHERE holiday_id = $holiday_id");
if ($deleted) {
    header("Location: " . BASE_URL . "holiday_list.php");
    exit;
}

die('Delete failed.');
