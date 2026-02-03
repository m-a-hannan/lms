<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Ensure session is active for auth flow.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Redirect already logged-in users to home.
if (!empty($_SESSION['user_id'])) {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

// Initialize form state and message flags.
$errors = [];
$identifier = '';
$registered = isset($_GET['registered']) && $_GET['registered'] === '1';
$loggedOut = isset($_GET['logged_out']) && $_GET['logged_out'] === '1';
$resetRequest = $_GET['reset_request'] ?? '';
$resetSuccess = isset($_GET['reset']) && $_GET['reset'] === 'success';

// Handle login form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$identifier = trim($_POST['identifier'] ?? '');
	$password = $_POST['password'] ?? '';

	// Validate required credentials.
	if ($identifier === '' || $password === '') {
		$errors[] = 'Email/username and password are required.';
	} else {
		// Look up the user by email or username.
		$stmt = $conn->prepare('SELECT user_id, username, email, password_hash, account_status FROM users WHERE email = ? OR username = ? LIMIT 1');
		if ($stmt === false) {
			$errors[] = 'Login failed. Please try again.';
		} else {
			$stmt->bind_param('ss', $identifier, $identifier);
			$stmt->execute();
			$result = $stmt->get_result();
			$user = $result ? $result->fetch_assoc() : null;
			$stmt->close();

			// Validate password and account status.
			if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
				$errors[] = 'Invalid credentials.';
			} elseif (isset($user['account_status']) && $user['account_status'] !== 'approved') {
				$status = $user['account_status'];
				if ($status === 'blocked') {
					$errors[] = 'Your account has been blocked. Contact the library.';
				} elseif ($status === 'rejected') {
					$errors[] = 'Your account has been rejected. Contact the library.';
				} elseif ($status === 'suspended') {
					$errors[] = 'Your account has been suspended. Contact the library.';
				} else {
					$errors[] = 'Your account is pending approval.';
				}
			} else {
				// Establish a fresh session for the authenticated user.
				session_regenerate_id(true);
				$userId = (int) $user['user_id'];
				$_SESSION['user_id'] = $userId;
				$_SESSION['user_email'] = $user['email'] ?? '';
				$_SESSION['user_username'] = $user['username'] ?? '';

				// Ensure a profile record exists for the user.
				if ($userId > 0) {
					$profileCheck = $conn->query("SELECT profile_id FROM user_profiles WHERE user_id = $userId LIMIT 1");
					if (!$profileCheck || $profileCheck->num_rows === 0) {
						$emptyPicture = $conn->real_escape_string('');
						$conn->query("INSERT INTO user_profiles (user_id, profile_picture) VALUES ($userId, '$emptyPicture')");
					}
				}

				// Respect safe local redirect targets.
				$next = $_GET['next'] ?? '';
				$redirect = BASE_URL . 'home.php';
				if ($next !== '' && strpos($next, '://') === false && str_starts_with($next, '/')) {
					$redirect = $next;
				}

				header('Location: ' . $redirect);
				exit;
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Login – Booklore</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap 5.3 -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Bootstrap Icons -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/main.css">
</head>

<body class="auth-page" data-base-url="<?php echo BASE_URL; ?>">

	<?php // Show logout confirmation toast. ?>
	<?php if ($loggedOut): ?>
	<div class="toast-container position-fixed top-0 end-0 p-3">
		<div id="logoutToast" class="toast text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
			<div class="d-flex">
				<div class="toast-body">Logged out successfully.</div>
				<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<section>

		<div class="form-box">

			<div class="form-value">

				<form method="post">

					<h2>Login</h2>

					<?php // Show registration success message. ?>
					<?php if ($registered): ?>
						<div class="alert alert-success">Registration complete. Please log in.</div>
					<?php endif; ?>

					<?php // Show reset success message. ?>
					<?php if ($resetSuccess): ?>
						<div class="alert alert-success">Password updated. Please log in with your new password.</div>
					<?php endif; ?>

					<?php // Show reset request status messages. ?>
					<?php if ($resetRequest === 'sent'): ?>
						<div class="alert alert-success">If the email exists and is approved, a reset link has been sent.</div>
					<?php elseif ($resetRequest === 'error'): ?>
						<div class="alert alert-danger">Unable to send reset email. Please try again.</div>
					<?php endif; ?>

					<?php // Show validation errors when present. ?>
					<?php if (!empty($errors)): ?>
						<div class="alert alert-danger">
							<?php echo htmlspecialchars(implode(' ', $errors)); ?>
						</div>
					<?php endif; ?>
					<div class="inputbox">

					<i class="bi bi-person input-icon"></i>

						<input type="text" name="identifier" placeholder="Email or Username" aria-label="Email or Username" value="<?php echo htmlspecialchars($identifier); ?>" required>

						<label class="sr-only">Email or Username</label>

					</div>

					<div class="inputbox">

						<i class="bi bi-lock input-icon password-toggle" data-target="loginPassword" role="button" aria-label="Show password" tabindex="0"></i>

						<input type="password" id="loginPassword" name="password" placeholder="Password" aria-label="Password" required>

						<label class="sr-only">Password</label>

					</div>

					<div class="forget">

						<label><input type="checkbox">Remember Me</label>

						<a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password</a>

					</div>

					<button type="submit">Log In</button>

					<div class="register">

						<p>Don't have an account? <a href="register.php" target="_blank">Sign Up</a></p>

					</div>

				</form>

			</div>

		</div>

	</section>

	<!-- Forgot password modal -->
	<div class="modal fade glass-modal" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Request Password Reset</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form method="post" action="<?php echo BASE_URL; ?>actions/request_password_reset.php" id="forgotPasswordForm">
					<div class="modal-body">
						<p class="small mb-3">
							Enter the email linked to your account. We’ll email you a reset link if the account is approved.
							The link expires in 30 minutes.
						</p>
						<div class="mb-3">
							<label class="form-label">Email Address</label>
							<input type="email" name="email" class="form-control" required>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-outline-secondary sr">Send Request</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

	<!-- Page-specific behavior -->
	<script src="<?php echo BASE_URL; ?>assets/js/pages/login.js"></script>
	<!-- Password visibility toggle behavior -->
	<script src="<?php echo BASE_URL; ?>assets/js/password_toggle.js"></script>

</body>

</html>
