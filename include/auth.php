<?php
if (!defined('AUTH_ENABLED') || AUTH_ENABLED !== true) {
	return;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

if (empty($_SESSION['user_id'])) {
	$next = $_SERVER['REQUEST_URI'] ?? '';
	$redirect = BASE_URL . 'login.php';
	if ($next !== '') {
		$redirect .= '?next=' . urlencode($next);
	}
	header('Location: ' . $redirect);
	exit;
}
