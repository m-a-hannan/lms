<?php
require_once __DIR__ . '/../include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/permissions.php';
require_once ROOT_PATH . '/include/library_helpers.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$context = rbac_get_context($conn);
$isLibrarian = strcasecmp($context['role_name'] ?? '', 'Librarian') === 0;
if (!($context['is_admin'] || $isLibrarian)) {
	header('Location: ' . BASE_URL . 'user_list.php?status=error');
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'user_list.php');
	exit;
}

$redirect = trim($_POST['redirect'] ?? '');
$redirectTarget = 'user_list.php';
if ($redirect !== '') {
	$redirect = ltrim($redirect, '/');
	if (preg_match('/^(dashboard|user_list)\.php(\?.*)?$/', $redirect)) {
		$redirectTarget = $redirect;
	}
}

$withStatus = function (string $status) use ($redirectTarget) {
	$separator = strpos($redirectTarget, '?') !== false ? '&' : '?';
	return BASE_URL . $redirectTarget . $separator . 'status=' . $status;
};

$userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
$tempPassword = trim($_POST['temp_password'] ?? '');
if ($userId <= 0 || $tempPassword === '') {
	header('Location: ' . $withStatus('error'));
	exit;
}

$passwordHash = password_hash($tempPassword, PASSWORD_DEFAULT);
$actorId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

$stmt = $conn->prepare("UPDATE users SET password_hash = ?, modified_by = ? WHERE user_id = ?");
if (!$stmt) {
	header('Location: ' . $withStatus('error'));
	exit;
}

$stmt->bind_param('sii', $passwordHash, $actorId, $userId);
if ($stmt->execute()) {
	$stmt->close();
	library_notify_user(
		$conn,
		$userId,
		'Temporary password set',
		'An admin has set a temporary password for you. Please log in and change it immediately.',
		$actorId > 0 ? $actorId : null
	);
	$exists = $conn->query("SHOW TABLES LIKE 'password_reset_requests'");
	if ($exists && $exists->num_rows > 0) {
		$closeStmt = $conn->prepare(
			"UPDATE password_reset_requests
			 SET status = 'completed', handled_by = ?, handled_date = NOW()
			 WHERE user_id = ? AND status = 'pending'"
		);
		if ($closeStmt) {
			$closeStmt->bind_param('ii', $actorId, $userId);
			$closeStmt->execute();
			$closeStmt->close();
		}
	}
	header('Location: ' . $withStatus('temp_password'));
	exit;
}

$stmt->close();
header('Location: ' . $withStatus('error'));
exit;
