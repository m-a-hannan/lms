<?php
if (!defined('AUTH_ENABLED') || AUTH_ENABLED !== true) {
	return;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

if (empty($_SESSION['user_id'])) {
	return;
}

$skipPages = ['index.php', 'login.php', 'logout.php', 'register.php'];
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

require_once ROOT_PATH . '/app/includes/permissions.php';

$normalized = rbac_normalize_path($scriptName);
if (in_array($normalized, $skipPages, true)) {
	return;
}

require_once ROOT_PATH . '/app/includes/connection.php';

if (!rbac_can_access($conn, $normalized, 'read')) {
	http_response_code(403);
	echo 'Access denied.';
	exit;
}

?>
