<?php
// Load core configuration for base URLs.
require_once dirname(__DIR__, 2) . '/includes/config.php';

// Ensure session is active before clearing it.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Clear all session data.
$_SESSION = [];
// Expire session cookie if cookies are in use.
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

// Destroy the session and redirect to login.
session_destroy();
header('Location: ' . BASE_URL . 'login.php?logged_out=1');
exit;
