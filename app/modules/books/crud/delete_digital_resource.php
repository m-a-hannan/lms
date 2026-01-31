<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$resource_id = (int) $_GET['id'];
$result = $conn->query("SELECT resource_id FROM digital_resources WHERE resource_id = $resource_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

$deleted = $conn->query("DELETE FROM digital_resources WHERE resource_id = $resource_id");
if ($deleted) {
    header("Location: " . BASE_URL . "digital_resource_list.php");
    exit;
}

die('Delete failed.');