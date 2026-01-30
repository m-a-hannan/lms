<?php
require_once __DIR__ . '/../include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/mailer.php';

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
	header('Location: ' . BASE_URL . 'login.php?reset_request=error');
	exit;
}

$stmt = $conn->prepare(
	"SELECT user_id, email, account_status
	 FROM users
	 WHERE email = ?
	 LIMIT 1"
);
if (!$stmt) {
	if ($respondJson(['status' => 'error', 'message' => 'Unable to start request.'], 500)) {
		exit;
	}
	header('Location: ' . BASE_URL . 'login.php?reset_request=error');
	exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$user || ($user['account_status'] ?? '') !== 'approved') {
	if ($respondJson(['status' => 'sent', 'message' => 'Request submitted.'], 200)) {
		exit;
	}
	header('Location: ' . BASE_URL . 'login.php?reset_request=sent');
	exit;
}

$token = bin2hex(random_bytes(16));
$tokenHash = hash('sha256', $token);
$expiry = date('Y-m-d H:i:s', time() + 60 * 30);
$userId = (int) $user['user_id'];

$update = $conn->prepare(
	"UPDATE users
	 SET reset_token_hash = ?,
		 reset_token_expires_at = ?
	 WHERE user_id = ? AND account_status = 'approved'"
);
if (!$update) {
	if ($respondJson(['status' => 'error', 'message' => 'Unable to process request.'], 500)) {
		exit;
	}
	header('Location: ' . BASE_URL . 'login.php?reset_request=error');
	exit;
}
$update->bind_param('ssi', $tokenHash, $expiry, $userId);
$update->execute();
$updatedRows = $update->affected_rows;
$update->close();

if ($updatedRows <= 0) {
	if ($respondJson(['status' => 'error', 'message' => 'Unable to process request.'], 500)) {
		exit;
	}
	header('Location: ' . BASE_URL . 'login.php?reset_request=error');
	exit;
}

$appUrl = mailer_env('APP_URL');
if ($appUrl === null || $appUrl === '') {
	$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
		$forwarded = explode(',', (string) $_SERVER['HTTP_X_FORWARDED_PROTO']);
		$scheme = trim($forwarded[0]);
	}
	$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
	$basePath = rtrim(BASE_URL, '/');
	$appUrl = $scheme . '://' . $host . $basePath;
}
$appUrl = rtrim($appUrl, '/');
$resetUrl = $appUrl . '/reset_password.php?token=' . urlencode($token);

try {
	$mail = mailer_create();
	$fromAddress = mailer_env('MAIL_FROM_ADDRESS', 'no-reply@karigori.site') ?? 'no-reply@karigori.site';
	$fromName = mailer_env('MAIL_FROM_NAME', 'Karigori Library') ?? 'Karigori Library';

	$mail->setFrom($fromAddress, $fromName);
	$mail->addAddress($email);
	$mail->Subject = 'Password Reset';
	$mail->Body = 'Click <a href="' . htmlspecialchars($resetUrl) . '">here</a> to reset your password. This link will expire in 30 minutes.';
	$mail->AltBody = 'Use this link to reset your password: ' . $resetUrl . ' (expires in 30 minutes).';
	$mail->send();
} catch (Exception $e) {
	$clear = $conn->prepare(
		"UPDATE users
		 SET reset_token_hash = NULL,
			 reset_token_expires_at = NULL
		 WHERE user_id = ?"
	);
	if ($clear) {
		$clear->bind_param('i', $userId);
		$clear->execute();
		$clear->close();
	}
	if ($respondJson(['status' => 'error', 'message' => 'Unable to send reset email.'], 500)) {
		exit;
	}
	header('Location: ' . BASE_URL . 'login.php?reset_request=error');
	exit;
}

if ($respondJson(['status' => 'sent', 'message' => 'Request submitted.'], 200)) {
	exit;
}
header('Location: ' . BASE_URL . 'login.php?reset_request=sent');
exit;
