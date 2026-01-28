<?php
require_once __DIR__ . '/../include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/library_helpers.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

$wantsJson = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
if (!$wantsJson && isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
	$wantsJson = true;
}

$respondJson = function (array $payload, int $code = 200) use ($wantsJson) {
	if (!$wantsJson) {
		return false;
	}
	http_response_code($code);
	header('Content-Type: application/json');
	echo json_encode($payload);
	return true;
};

$email = trim($_POST['email'] ?? '');
if ($email === '') {
	if ($respondJson(['status' => 'error', 'message' => 'Email is required.'], 400)) {
		exit;
	}
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

$tableExists = $conn->query("SHOW TABLES LIKE 'password_reset_requests'");
$canLogRequest = $tableExists && $tableExists->num_rows > 0;

$stmt = $conn->prepare("SELECT user_id, username FROM users WHERE email = ? LIMIT 1");
if (!$stmt) {
	if ($respondJson(['status' => 'error', 'message' => 'Unable to start request.'], 500)) {
		exit;
	}
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$user) {
	if ($respondJson(['status' => 'sent', 'message' => 'Request submitted.'], 200)) {
		exit;
	}
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

$userId = (int) ($user['user_id'] ?? 0);
$username = $user['username'] ?? 'User';

library_notify_roles(
	$conn,
	['Admin', 'Librarian'],
	'Password change request',
	"{$username} requested a password reset. Please set a temporary password from the Users list.",
	$userId > 0 ? $userId : null
);

library_notify_user(
	$conn,
	$userId,
	'Password reset requested',
	'Your request has been sent. An admin will set a temporary password for you.',
	$userId > 0 ? $userId : null
);

if ($canLogRequest) {
	$checkStmt = $conn->prepare(
		"SELECT request_id FROM password_reset_requests
		 WHERE user_id = ? AND status = 'pending'
		 ORDER BY created_date DESC LIMIT 1"
	);
	if ($checkStmt) {
		$checkStmt->bind_param('i', $userId);
		$checkStmt->execute();
		$existsResult = $checkStmt->get_result();
		$existing = $existsResult ? $existsResult->fetch_assoc() : null;
		$checkStmt->close();

		if (!$existing) {
			$insertStmt = $conn->prepare(
				"INSERT INTO password_reset_requests (user_id, email, status, created_date)
				 VALUES (?, ?, 'pending', NOW())"
			);
			if ($insertStmt) {
				$insertStmt->bind_param('is', $userId, $email);
				$insertStmt->execute();
				$insertStmt->close();
			}
		}
	}
}

if ($respondJson(['status' => 'sent', 'message' => 'Request submitted.'], 200)) {
	exit;
}
header('Location: ' . BASE_URL . 'login.php');
exit;
