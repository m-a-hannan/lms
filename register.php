<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

if (!empty($_SESSION['user_id'])) {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = trim($_POST['username'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$password = $_POST['password'] ?? '';

	if ($username === '' || $email === '' || $password === '') {
		$errors[] = 'Username, email, and password are required.';
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = 'Please enter a valid email address.';
	} elseif (strlen($password) < 8) {
		$errors[] = 'Password must be at least 8 characters.';
	} elseif (!preg_match('/[a-z]/i', $password)) {
		$errors[] = 'Password must contain at least one letter.';
	} elseif (!preg_match('/[0-9]/', $password)) {
		$errors[] = 'Password must contain at least one number.';
	} else {
		$stmt = $conn->prepare('SELECT user_id, account_status FROM users WHERE email = ? OR username = ? LIMIT 1');
		if ($stmt === false) {
			$errors[] = 'Registration failed. Please try again.';
		} else {
			$stmt->bind_param('ss', $email, $username);
			$stmt->execute();
			$result = $stmt->get_result();
			$existing = $result ? $result->fetch_assoc() : null;
			$stmt->close();

			if ($existing) {
				if (($existing['account_status'] ?? '') === 'blocked') {
					$errors[] = 'This account has been blocked. Contact the library.';
				} else {
					$errors[] = 'Email or username already exists.';
				}
			} else {
				$passwordHash = password_hash($password, PASSWORD_DEFAULT);
				$insert = $conn->prepare('INSERT INTO users (username, email, password_hash, account_status) VALUES (?, ?, ?, ?)');
				if ($insert === false) {
					$errors[] = 'Registration failed. Please try again.';
				} else {
					$status = 'pending';
					$insert->bind_param('ssss', $username, $email, $passwordHash, $status);
					if ($insert->execute()) {
						$newUserId = (int) $insert->insert_id;
						$insert->close();
						if ($newUserId > 0) {
							$emptyPicture = $conn->real_escape_string('');
							$conn->query("INSERT INTO user_profiles (user_id, profile_picture) VALUES ($newUserId, '$emptyPicture')");
						}
						header('Location: ' . BASE_URL . 'login.php?registered=1');
						exit;
					}
					$insert->close();
					$errors[] = 'Registration failed. Please try again.';
				}
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Register – Booklore</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap 5.3 -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Bootstrap Icons -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/main.css">

	
</head>

<body class="auth-page">
	<section>
		<div class="form-box">
			<div class="form-value">
				<form method="post">
					<h2>Register</h2>

					<?php if (!empty($errors)): ?>
						<div class="alert alert-danger">
							<?php echo htmlspecialchars(implode(' ', $errors)); ?>
						</div>
					<?php endif; ?>

					<div class="inputbox">
						<i class="bi bi-person input-icon"></i>
						<input type="text" name="username" placeholder="Username" aria-label="Username" value="<?php echo htmlspecialchars($username); ?>" required>
						<label class="sr-only">Username</label>
					</div>

					<div class="inputbox">
						<i class="bi bi-envelope input-icon"></i>
						<input type="email" name="email" placeholder="Email" aria-label="Email" value="<?php echo htmlspecialchars($email); ?>" required>
						<label class="sr-only">Email</label>
					</div>

					<div class="inputbox">
						<i class="bi bi-lock input-icon"></i>
						<input type="password" name="password" placeholder="Password" aria-label="Password" required>
						<label class="sr-only">Password</label>
					</div>

					<button type="submit">Create Account</button>

					<div class="register">
						<p>Already have an account? <a href="login.php">Login</a></p>
					</div>
				</form>
			</div>
		</div>
	</section>
	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
