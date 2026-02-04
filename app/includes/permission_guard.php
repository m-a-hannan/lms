<?php
// Exit early when authentication is disabled.
if (!defined('AUTH_ENABLED') || AUTH_ENABLED !== true) {
	return;
}

// Ensure session is active before checking user state.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Skip guard when no user is logged in.
if (empty($_SESSION['user_id'])) {
	return;
}

// Define public pages that bypass RBAC checks.
$skipPages = ['login.php', 'logout.php', 'register.php'];
// Capture the current script path for normalization.
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

// Load RBAC helpers for path normalization and checks.
require_once ROOT_PATH . '/app/includes/permissions.php';

// Normalize the script path for permission lookup.
$normalized = rbac_normalize_path($scriptName);
if (in_array($normalized, $skipPages, true)) {
	return;
}

// Load DB connection for permission resolution.
require_once ROOT_PATH . '/app/includes/connection.php';

// Deny access when the user lacks read permission.
if (!rbac_can_access($conn, $normalized, 'read')) {
	http_response_code(403);
	echo 'Access denied.';
	exit;
}

?>
