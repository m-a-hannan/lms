<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$policy_id = (int) $_GET['id'];
$result = $conn->query("SELECT policy_id FROM library_policies WHERE policy_id = $policy_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}

$deleted = $conn->query("DELETE FROM library_policies WHERE policy_id = $policy_id");
if ($deleted) {
    header("Location: " . BASE_URL . "library_policy_list.php");
    exit;
}

die('Delete failed.');