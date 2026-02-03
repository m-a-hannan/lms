<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Accept only POST requests for password resets.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

// Read token and password fields from the form.
$token = trim($_POST['token'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirmation = $_POST['password_confirmation'] ?? '';

// Helper to redirect back to reset form with an error code.
$redirectWithError = function (string $code) use ($token) {
	$location = BASE_URL . 'reset_password.php';
	$query = [];
	// Preserve the token in the redirect when present.
	if ($token !== '') {
		$query['token'] = $token;
	}
	$query['error'] = $code;
	$location .= '?' . http_build_query($query);
	header('Location: ' . $location);
	exit;
};

// Require a valid token to proceed.
if ($token === '') {
	$redirectWithError('invalid');
}

// Look up the user associated with the token hash.
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

// Validate token existence and expiry timestamp.
if (!$user || empty($user['reset_token_expires_at'])) {
	$redirectWithError('invalid');
}

// Reject expired tokens.
if (strtotime($user['reset_token_expires_at']) <= time()) {
	$redirectWithError('invalid');
}

// Require both password fields.
if ($password === '' || $passwordConfirmation === '') {
	$redirectWithError('missing');
}

// Enforce minimum password length.
if (strlen($password) < 8) {
	$redirectWithError('policy');
}

// Enforce letter inclusion.
if (!preg_match('/[a-z]/i', $password)) {
	$redirectWithError('policy');
}

// Enforce number inclusion.
if (!preg_match('/[0-9]/', $password)) {
	$redirectWithError('policy');
}

// Require confirmation match.
if ($password !== $passwordConfirmation) {
	$redirectWithError('mismatch');
}

// Hash the new password before storing.
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$userId = (int) $user['user_id'];

// Update the password and clear the reset token.
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

// Execute the update or report an error.
if (!$update->execute()) {
	$update->close();
	$redirectWithError('server');
}
$update->close();

// Redirect to login with success flag.
header('Location: ' . BASE_URL . 'login.php?reset=success');
exit;
