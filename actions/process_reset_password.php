<?php
require_once __DIR__ . '/../include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

$token = trim($_POST['token'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirmation = $_POST['password_confirmation'] ?? '';

$redirectWithError = function (string $code) use ($token) {
	$location = BASE_URL . 'reset_password.php';
	$query = [];
	if ($token !== '') {
		$query['token'] = $token;
	}
	$query['error'] = $code;
	$location .= '?' . http_build_query($query);
	header('Location: ' . $location);
	exit;
};

if ($token === '') {
	$redirectWithError('invalid');
}

$tokenHash = hash('sha256', $token);
$stmt = $conn->prepare(
	"SELECT user_id, reset_token_expires_at
	 FROM users
	 WHERE reset_token_hash = ? AND account_status = 'approved'
	 LIMIT 1"
);
if (!$stmt) {
	$redirectWithError('server');
}
$stmt->bind_param('s', $tokenHash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$user || empty($user['reset_token_expires_at'])) {
	$redirectWithError('invalid');
}

if (strtotime($user['reset_token_expires_at']) <= time()) {
	$redirectWithError('invalid');
}

if ($password === '' || $passwordConfirmation === '') {
	$redirectWithError('missing');
}

if (strlen($password) < 8) {
	$redirectWithError('policy');
}

if (!preg_match('/[a-z]/i', $password)) {
	$redirectWithError('policy');
}

if (!preg_match('/[0-9]/', $password)) {
	$redirectWithError('policy');
}

if ($password !== $passwordConfirmation) {
	$redirectWithError('mismatch');
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$userId = (int) $user['user_id'];

$update = $conn->prepare(
	"UPDATE users
	 SET password_hash = ?,
		 reset_token_hash = NULL,
		 reset_token_expires_at = NULL
	 WHERE user_id = ? AND account_status = 'approved'"
);
if (!$update) {
	$redirectWithError('server');
}
$update->bind_param('si', $passwordHash, $userId);

if (!$update->execute()) {
	$update->close();
	$redirectWithError('server');
}
$update->close();

header('Location: ' . BASE_URL . 'login.php?reset=success');
exit;
