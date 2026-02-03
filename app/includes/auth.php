<?php
// Exit early when authentication is disabled.
if (!defined('AUTH_ENABLED') || AUTH_ENABLED !== true) {
	return;
}

// Ensure session is active before checking user state.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Redirect unauthenticated users to login with return URL.
if (empty($_SESSION['user_id'])) {
	$next = $_SERVER['REQUEST_URI'] ?? '';
	$redirect = BASE_URL . 'login.php';
	// Preserve the originally requested URL when present.
	if ($next !== '') {
		$redirect .= '?next=' . urlencode($next);
	}
	header('Location: ' . $redirect);
	exit;
}
