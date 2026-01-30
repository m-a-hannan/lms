<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

$token = trim($_GET['token'] ?? '');
$tokenError = '';
$formError = '';

if ($token === '') {
	$tokenError = 'This reset link is invalid.';
} else {
	$tokenHash = hash('sha256', $token);
	$stmt = $conn->prepare(
		"SELECT user_id, reset_token_expires_at
		 FROM users
		 WHERE reset_token_hash = ? AND account_status = 'approved'
		 LIMIT 1"
	);
	if ($stmt) {
		$stmt->bind_param('s', $tokenHash);
		$stmt->execute();
		$result = $stmt->get_result();
		$user = $result ? $result->fetch_assoc() : null;
		$stmt->close();
		if (!$user || empty($user['reset_token_expires_at'])) {
			$tokenError = 'This reset link is invalid or has already been used.';
		} elseif (strtotime($user['reset_token_expires_at']) <= time()) {
			$tokenError = 'This reset link has expired.';
		}
	} else {
		$tokenError = 'Unable to validate the reset link.';
	}
}

if ($tokenError === '') {
	$errorCode = $_GET['error'] ?? '';
	if ($errorCode === 'missing') {
		$formError = 'Password and confirmation are required.';
	} elseif ($errorCode === 'policy') {
		$formError = 'Password must be at least 8 characters and include a letter and a number.';
	} elseif ($errorCode === 'mismatch') {
		$formError = 'Passwords do not match.';
	} elseif ($errorCode === 'invalid') {
		$tokenError = 'This reset link is invalid or has expired.';
	} elseif ($errorCode === 'server') {
		$formError = 'Unable to reset password. Please try again.';
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Reset Password – Booklore</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap 5.3 -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

	<style>
		* {
			margin: 0;
			padding: 0;
			font-family: "Trebuchet MS", "Lucida Sans Unicode", "Lucida Grande",
				"Lucida Sans", Arial, sans-serif;
		}

		html, body {
			min-height: 100%;
		}

		body {
			background: url("https://images.unsplash.com/photo-1529148482759-b35b25c5f217") no-repeat center center fixed;
			background-size: cover;
		}

		body::before {
			content: "";
			position: fixed;
			inset: 0;
			background: rgba(0, 0, 0, 0.35);
		}

		section {
			position: relative;
			display: flex;
			justify-content: center;
			align-items: center;
			min-height: 100vh;
			width: 100%;
		}

		.form-box {
			position: relative;
			width: 440px;
			padding: 36px 28px;
			margin: 25px 0;
			background: rgba(255, 255, 255, 0.18);
			border: 1px solid rgba(255, 255, 255, 0.2);
			backdrop-filter: blur(16px) brightness(80%);
			border-radius: 20px;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		h2 {
			font-size: 2em;
			color: #111827;
			text-align: center;
			margin-bottom: 20px;
		}

		.form-control {
			background: rgba(255, 255, 255, 0.7);
			border: none;
		}

		.btn-primary {
			width: 100%;
			border-radius: 40px;
			background-color: #111827;
			border: none;
			font-weight: 600;
		}

		.helper-link {
			display: block;
			text-align: center;
			margin-top: 16px;
		}

		@media screen and (max-width: 480px) {
			.form-box {
				width: calc(100% - 32px);
				border-radius: 16px;
			}
		}
	</style>
</head>

<body>
	<section>
		<div class="form-box">
			<div class="form-value w-100">
				<h2>Reset Password</h2>

				<?php if ($tokenError !== ''): ?>
					<div class="alert alert-danger"><?php echo htmlspecialchars($tokenError); ?></div>
					<a class="helper-link" href="<?php echo BASE_URL; ?>login.php">Back to login</a>
				<?php else: ?>
					<?php if ($formError !== ''): ?>
						<div class="alert alert-danger"><?php echo htmlspecialchars($formError); ?></div>
					<?php endif; ?>
					<form method="post" action="<?php echo BASE_URL; ?>actions/process_reset_password.php">
						<input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
						<div class="mb-3">
							<label class="form-label">New Password</label>
							<input type="password" name="password" class="form-control" required>
						</div>
						<div class="mb-3">
							<label class="form-label">Confirm Password</label>
							<input type="password" name="password_confirmation" class="form-control" required>
						</div>
						<button type="submit" class="btn btn-primary">Update Password</button>
					</form>
					<a class="helper-link" href="<?php echo BASE_URL; ?>login.php">Back to login</a>
				<?php endif; ?>
			</div>
		</div>
	</section>
</body>

</html>

