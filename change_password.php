<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/permissions.php';

$context = rbac_get_context($conn);
$userId = (int) ($context['user_id'] ?? 0);
if ($userId <= 0) {
	$next = urlencode($_SERVER['REQUEST_URI'] ?? '/');
	header('Location: ' . BASE_URL . 'login.php?next=' . $next);
	exit;
}

$alerts = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$currentPassword = $_POST['current_password'] ?? '';
	$newPassword = $_POST['new_password'] ?? '';
	$confirmPassword = $_POST['confirm_password'] ?? '';

	if ($newPassword === '' || $confirmPassword === '') {
		$alerts[] = ['danger', 'New password and confirmation are required.'];
	} elseif (strlen($newPassword) < 8) {
		$alerts[] = ['danger', 'Password must be at least 8 characters.'];
	} elseif (!preg_match('/[a-z]/i', $newPassword)) {
		$alerts[] = ['danger', 'Password must contain at least one letter.'];
	} elseif (!preg_match('/[0-9]/', $newPassword)) {
		$alerts[] = ['danger', 'Password must contain at least one number.'];
	} elseif ($newPassword !== $confirmPassword) {
		$alerts[] = ['danger', 'New password and confirmation do not match.'];
	}

	if (!$alerts) {
		$stmt = $conn->prepare('SELECT password_hash FROM users WHERE user_id = ? LIMIT 1');
		if (!$stmt) {
			$alerts[] = ['danger', 'Unable to verify current password.'];
		} else {
			$stmt->bind_param('i', $userId);
			$stmt->execute();
			$result = $stmt->get_result();
			$user = $result ? $result->fetch_assoc() : null;
			$stmt->close();

			$existingHash = $user['password_hash'] ?? '';
			if ($existingHash !== '' && !password_verify($currentPassword, $existingHash)) {
				$alerts[] = ['danger', 'Current password is incorrect.'];
			}
		}
	}

	if (!$alerts) {
		$newHash = password_hash($newPassword, PASSWORD_DEFAULT);
		$update = $conn->prepare('UPDATE users SET password_hash = ? WHERE user_id = ?');
		if (!$update) {
			$alerts[] = ['danger', 'Unable to update password.'];
		} else {
			$update->bind_param('si', $newHash, $userId);
			$update->execute();
			$update->close();
			$alerts[] = ['success', 'Password updated successfully.'];
		}
	}
}
?>

<?php include('include/header_resources.php') ?>
<?php include('include/header.php') ?>
<?php include('sidebar.php') ?>
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h1 class="mb-0">Change Password</h1>
					</div>

					<?php if ($alerts): ?>
					<div class="mb-3">
						<?php foreach ($alerts as $alert): ?>
						<div class="alert alert-<?php echo htmlspecialchars($alert[0]); ?> mb-2">
							<?php echo htmlspecialchars($alert[1]); ?>
						</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<div class="card shadow-sm">
						<div class="card-body">
							<form method="post" class="row g-3" autocomplete="off">
								<div class="col-md-6">
									<label class="form-label">Current Password</label>
									<input type="password" name="current_password" class="form-control" autocomplete="current-password" required>
								</div>
								<div class="col-md-6">
									<label class="form-label">New Password</label>
									<input type="password" name="new_password" class="form-control" autocomplete="new-password" required>
								</div>
								<div class="col-md-6">
									<label class="form-label">Confirm New Password</label>
									<input type="password" name="confirm_password" class="form-control" autocomplete="new-password" required>
								</div>
								<div class="col-12">
									<button type="submit" class="btn btn-primary">Update Password</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
<?php include('include/footer.php') ?>
<?php include('include/footer_resources.php') ?>
