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
  width: 420px;
  padding: 36px 28px;
	margin: 25px 0;
  background: rgba(255, 255, 255, 0.18);
  border: 1px solid rgba(255, 255, 255, 0.2);
	backdrop-filter: blur(15px) brightness(80%);
  border-radius: 20px;
  overflow: hidden;
  backdrop-filter: blur(16px);
  display: flex;
  justify-content: center;
  align-items: center;
}

h2 {
  font-size: 2em;
  color: #111827;
  text-align: center;
}

.inputbox {
  position: relative;
  margin: 24px 0;
  width: 310px;
  border-bottom: 2px solid rgba(17, 24, 39, 0.4);
}

.inputbox label {
  position: absolute;
  top: 50%;
  left: 5px;
  transform: translateY(-50%);
  color: #111827;
  font-size: 1em;
  pointer-events: none;
  transition: 0.5s;
}


.inputbox input {
  width: 100%;
  height: 50px;
  background: transparent;
  border: none;
  outline: none;
  font-size: 1em;
  padding: 0 35px 0 5px;
  color: #111827;
}

.inputbox .input-icon {
  position: absolute;
  right: 8px;
  color: #1a1c20;
  font-size: 1.2em;
  top: 50%;
  transform: translateY(-50%);
}

button {
  width: 100%;
  height: 42px;
  border-radius: 40px;
  background-color: #111827;
  border: none;
  outline: none;
  cursor: pointer;
  font-size: 1em;
  font-weight: 600;
  color: #fff;
}

.register {
  font-size: 0.9em;
  color: #374151;
  text-align: center;
  margin: 18px 0 0;
}

.register a {
  text-decoration: none;
  color: #111827;
  font-weight: 600;
}

.register a:hover {
  text-decoration: underline;
}

@media screen and (max-width: 480px) {
  .form-box {
    width: calc(100% - 32px);
    border-radius: 16px;
  }
}

	
	

.forget {
  margin: -6px 0 18px;
  font-size: 0.9em;
  color: #111827;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.forget a {
  color: #111827;
  text-decoration: none;
  font-weight: 600;
}

.forget a:hover {
  text-decoration: underline;
}


.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

.inputbox input:focus,
.inputbox input:focus-visible {
  outline: none;
  box-shadow: none;
}

.inputbox input::placeholder {
  color: #111827;
  opacity: 0.8;
}
</style>
</head>

<body>
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
