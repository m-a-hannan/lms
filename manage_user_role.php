<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

$errors = [];
$editId = 0;
$selectedUserId = 0;
$selectedRoleId = 0;

$userRoleColumns = [];
$columnResult = $conn->query("SHOW COLUMNS FROM user_roles");
if ($columnResult) {
	while ($row = $columnResult->fetch_assoc()) {
		$userRoleColumns[] = $row['Field'];
	}
} else {
	$errors[] = 'Failed to load user role schema: ' . $conn->error;
}

$hasUserId = in_array('user_id', $userRoleColumns, true);
$hasRoleId = in_array('role_id', $userRoleColumns, true);
$hasUsername = in_array('username', $userRoleColumns, true);
$hasRoleName = in_array('role_name', $userRoleColumns, true);

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
	$deleteId = (int) $_GET['delete'];
	$conn->query("DELETE FROM user_roles WHERE user_role_id = $deleteId");
	header("Location: " . BASE_URL . "manage_user_role.php");
	exit;
}

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
	$editId = (int) $_GET['edit'];
	$editResult = $conn->query("SELECT user_role_id, user_id, role_id FROM user_roles WHERE user_role_id = $editId");
	if ($editResult && $editResult->num_rows === 1) {
		$editRow = $editResult->fetch_assoc();
		$selectedUserId = (int) $editRow['user_id'];
		$selectedRoleId = (int) $editRow['role_id'];
	} else {
		$editId = 0;
		$errors[] = 'Selected assignment not found.';
	}
}

if (isset($_POST['save'])) {
	$userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
	$roleId = isset($_POST['role_id']) ? (int) $_POST['role_id'] : 0;
	$userRoleId = isset($_POST['user_role_id']) ? (int) $_POST['user_role_id'] : 0;

	if ($userId <= 0 || $roleId <= 0) {
		$errors[] = 'Please select both a user and a role.';
	} else {
		$usernameValue = '';
		$roleNameValue = '';

		if ($hasUsername) {
			$userLookup = $conn->query("SELECT username, email FROM users WHERE user_id = $userId LIMIT 1");
			if ($userLookup && $userLookup->num_rows === 1) {
				$userRow = $userLookup->fetch_assoc();
				$usernameValue = trim((string) ($userRow['username'] ?? ''));
				if ($usernameValue === '' && !empty($userRow['email'])) {
					$usernameValue = $userRow['email'];
				}
				if ($usernameValue === '') {
					$usernameValue = 'User #' . $userId;
				}
			} else {
				$errors[] = 'Selected user not found.';
			}
		}

		if ($hasRoleName) {
			$roleLookup = $conn->query("SELECT role_name FROM roles WHERE role_id = $roleId LIMIT 1");
			if ($roleLookup && $roleLookup->num_rows === 1) {
				$roleRow = $roleLookup->fetch_assoc();
				$roleNameValue = trim((string) ($roleRow['role_name'] ?? ''));
				if ($roleNameValue === '') {
					$roleNameValue = 'Role #' . $roleId;
				}
			} else {
				$errors[] = 'Selected role not found.';
			}
		}

		if (empty($errors)) {
			$fields = [];
			$values = [];
			$setParts = [];

			if ($hasUserId) {
				$fields[] = 'user_id';
				$values[] = $userId;
				$setParts[] = "user_id = $userId";
			}
			if ($hasRoleId) {
				$fields[] = 'role_id';
				$values[] = $roleId;
				$setParts[] = "role_id = $roleId";
			}
			if ($hasUsername) {
				$usernameSql = $conn->real_escape_string($usernameValue);
				$fields[] = 'username';
				$values[] = "'" . $usernameSql . "'";
				$setParts[] = "username = '" . $usernameSql . "'";
			}
			if ($hasRoleName) {
				$roleNameSql = $conn->real_escape_string($roleNameValue);
				$fields[] = 'role_name';
				$values[] = "'" . $roleNameSql . "'";
				$setParts[] = "role_name = '" . $roleNameSql . "'";
			}

			if ($userRoleId > 0) {
				if (empty($setParts)) {
					$errors[] = 'No writable columns found for update.';
				} else {
					$sql = "UPDATE user_roles SET " . implode(', ', $setParts) . " WHERE user_role_id = $userRoleId";
				}
			} else {
				if (empty($fields)) {
					$errors[] = 'No writable columns found for insert.';
				} else {
					$sql = "INSERT INTO user_roles (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
				}
			}

			if (empty($errors)) {
				$saved = $conn->query($sql);
				if ($saved) {
					header("Location: " . BASE_URL . "manage_user_role.php");
					exit;
				}
				$errors[] = 'Database error: ' . $conn->error;
			}
		}
	}

	$editId = $userRoleId;
	$selectedUserId = $userId;
	$selectedRoleId = $roleId;
}

$users = [];
$userResult = $conn->query("SELECT user_id, username, email FROM users ORDER BY user_id DESC");
if ($userResult) {
	while ($row = $userResult->fetch_assoc()) {
		$users[] = $row;
	}
} else {
	$errors[] = 'Failed to load users: ' . $conn->error;
}

$roles = [];
$roleResult = $conn->query("SELECT role_id, role_name FROM roles ORDER BY role_name ASC");
if ($roleResult) {
	while ($row = $roleResult->fetch_assoc()) {
		$roles[] = $row;
	}
} else {
	$errors[] = 'Failed to load roles: ' . $conn->error;
}

$assignmentFields = ['ur.user_role_id', 'ur.user_id', 'ur.role_id', 'u.username', 'u.email', 'r.role_name'];
if ($hasUsername) {
	$assignmentFields[] = 'ur.username AS assigned_username';
}
if ($hasRoleName) {
	$assignmentFields[] = 'ur.role_name AS assigned_role_name';
}

$assignments = $conn->query(
	"SELECT " . implode(', ', $assignmentFields) . "
	 FROM user_roles ur
	 LEFT JOIN users u ON ur.user_id = u.user_id
	 LEFT JOIN roles r ON ur.role_id = r.role_id
	 ORDER BY ur.user_role_id DESC"
);
?>
<?php include(ROOT_PATH . '/include/header_resources.php') ?>
<?php include(ROOT_PATH . '/include/header.php') ?>
<?php include(ROOT_PATH . '/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<!--begin::App Content-->
	<div class="app-content">
		<!--begin::Container-->
		<div class="container-fluid">
			<!--begin::Row-->
			<div class="row">
				<div class="container py-5">
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h3 class="mb-0">Manage User Roles</h3>
						<?php if ($editId > 0): ?>
						<a href="<?php echo BASE_URL; ?>manage_user_role.php" class="btn btn-secondary btn-sm">Clear</a>
						<?php endif; ?>
					</div>

					<?php if (!empty($errors)): ?>
					<div class="alert alert-warning">
						<?php foreach ($errors as $message): ?>
							<div><?php echo htmlspecialchars($message); ?></div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<div class="card shadow-sm mb-4">
						<div class="card-body">
							<form method="post">
								<input type="hidden" name="user_role_id" value="<?php echo (int) $editId; ?>">
								<div class="row g-3">
									<div class="col-md-6">
										<label class="form-label">User</label>
										<select class="form-select w-100" name="user_id" required>
											<option value="">Select User</option>
											<?php foreach ($users as $user): ?>
												<?php
													$userLabel = trim((string) ($user['username'] ?? ''));
													if ($userLabel === '' && !empty($user['email'])) {
														$userLabel = $user['email'];
													}
													if ($userLabel === '') {
														$userLabel = 'User #' . (int) $user['user_id'];
													}
													$selected = ((int) $user['user_id'] === (int) $selectedUserId) ? 'selected' : '';
												?>
												<option value="<?php echo (int) $user['user_id']; ?>" <?php echo $selected; ?>>
													<?php echo htmlspecialchars($userLabel); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-md-6">
										<label class="form-label">Role</label>
										<select class="form-select w-100" name="role_id" required>
											<option value="">Select Role</option>
											<?php foreach ($roles as $role): ?>
												<?php $selected = ((int) $role['role_id'] === (int) $selectedRoleId) ? 'selected' : ''; ?>
												<option value="<?php echo (int) $role['role_id']; ?>" <?php echo $selected; ?>>
													<?php echo htmlspecialchars($role['role_name'] ?? ''); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
								<div class="d-flex justify-content-end mt-3">
									<button type="submit" name="save" class="btn btn-primary">
										<?php echo $editId > 0 ? 'Update Role' : 'Assign Role'; ?>
									</button>
								</div>
							</form>
						</div>
					</div>

					<div class="card shadow-sm">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-light">
										<tr>
											<th>#</th>
											<th>User</th>
											<th>Role</th>
											<th class="text-center">Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php if ($assignments && $assignments->num_rows > 0): ?>
										<?php while ($row = $assignments->fetch_assoc()): ?>
											<?php
												$name = trim((string) ($row['username'] ?? ''));
												if ($name === '' && !empty($row['email'])) {
													$name = $row['email'];
												}
												if ($name === '' && !empty($row['assigned_username'])) {
													$name = $row['assigned_username'];
												}
												if ($name === '') {
													$name = 'User #' . (int) $row['user_id'];
												}
												$roleLabel = trim((string) ($row['role_name'] ?? ''));
												if ($roleLabel === '' && !empty($row['assigned_role_name'])) {
													$roleLabel = $row['assigned_role_name'];
												}
											?>
											<tr>
												<td><?php echo (int) $row['user_role_id']; ?></td>
												<td><?php echo htmlspecialchars($name); ?></td>
												<td><?php echo htmlspecialchars($roleLabel); ?></td>
												<td class="text-center">
													<a href="<?php echo BASE_URL; ?>manage_user_role.php?edit=<?php echo (int) $row['user_role_id']; ?>" class="text-primary me-2" title="Edit">
														<i class="bi bi-pencil-square fs-5"></i>
													</a>
													<a href="<?php echo BASE_URL; ?>manage_user_role.php?delete=<?php echo (int) $row['user_role_id']; ?>" class="text-danger" title="Delete"
														onclick="return confirm('Are you sure you want to delete this assignment?');">
														<i class="bi bi-trash fs-5"></i>
													</a>
												</td>
											</tr>
										<?php endwhile; ?>
										<?php else: ?>
										<tr>
											<td colspan="4" class="text-center text-muted">No assigned roles found.</td>
										</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- end::Row -->
		</div>
	</div>
</main>
<!--end::App Main-->
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>
