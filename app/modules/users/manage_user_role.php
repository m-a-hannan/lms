<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Initialize form state and error collection.
$errors = [];
$editId = 0;
$selectedUserId = 0;
$selectedRoleId = 0;

// Inspect user_roles table columns to handle schema variants.
$userRoleColumns = [];
$columnResult = $conn->query("SHOW COLUMNS FROM user_roles");
if ($columnResult) {
	// Collect column names for conditional field handling.
	while ($row = $columnResult->fetch_assoc()) {
		$userRoleColumns[] = $row['Field'];
	}
} else {
	$errors[] = 'Failed to load user role schema: ' . $conn->error;
}

// Record which optional columns exist.
$hasUserId = in_array('user_id', $userRoleColumns, true);
$hasRoleId = in_array('role_id', $userRoleColumns, true);
$hasUsername = in_array('username', $userRoleColumns, true);
$hasRoleName = in_array('role_name', $userRoleColumns, true);

// Handle delete action via query string.
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
	$deleteId = (int) $_GET['delete'];
	$conn->query("DELETE FROM user_roles WHERE user_role_id = $deleteId");
	header("Location: " . BASE_URL . "manage_user_role.php");
	exit;
}

// Handle edit action via query string.
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

// Handle form submission for create/update.
if (isset($_POST['save'])) {
	$userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
	$roleId = isset($_POST['role_id']) ? (int) $_POST['role_id'] : 0;
	$userRoleId = isset($_POST['user_role_id']) ? (int) $_POST['user_role_id'] : 0;

	// Validate required selection values.
	if ($userId <= 0 || $roleId <= 0) {
		$errors[] = 'Please select both a user and a role.';
	} else {
		// Detect existing role assignment for this user.
		if ($userRoleId <= 0 && $hasUserId) {
			$existingRole = $conn->query("SELECT user_role_id FROM user_roles WHERE user_id = $userId LIMIT 1");
			if ($existingRole && $existingRole->num_rows === 1) {
				$existingRow = $existingRole->fetch_assoc();
				$userRoleId = (int) ($existingRow['user_role_id'] ?? 0);
			}
		}

		// Initialize optional username/role name values.
		$usernameValue = '';
		$roleNameValue = '';

		// Look up username when the column is available.
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

		// Look up role name when the column is available.
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

		// Build insert or update statements based on schema.
		if (empty($errors)) {
			$fields = [];
			$values = [];
			$setParts = [];

			// Add user_id if present in schema.
			if ($hasUserId) {
				$fields[] = 'user_id';
				$values[] = $userId;
				$setParts[] = "user_id = $userId";
			}
			// Add role_id if present in schema.
			if ($hasRoleId) {
				$fields[] = 'role_id';
				$values[] = $roleId;
				$setParts[] = "role_id = $roleId";
			}
			// Add username if present in schema.
			if ($hasUsername) {
				$usernameSql = $conn->real_escape_string($usernameValue);
				$fields[] = 'username';
				$values[] = "'" . $usernameSql . "'";
				$setParts[] = "username = '" . $usernameSql . "'";
			}
			// Add role_name if present in schema.
			if ($hasRoleName) {
				$roleNameSql = $conn->real_escape_string($roleNameValue);
				$fields[] = 'role_name';
				$values[] = "'" . $roleNameSql . "'";
				$setParts[] = "role_name = '" . $roleNameSql . "'";
			}

			// Build an update or insert query.
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

			// Execute the built SQL if no errors remain.
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

// Load user options for the selector.
$users = [];
$userResult = $conn->query("SELECT user_id, username, email FROM users ORDER BY user_id DESC");
if ($userResult) {
	// Collect user rows for dropdown.
	while ($row = $userResult->fetch_assoc()) {
		$users[] = $row;
	}
} else {
	$errors[] = 'Failed to load users: ' . $conn->error;
}

// Load role options for the selector.
$roles = [];
$roleResult = $conn->query("SELECT role_id, role_name FROM roles ORDER BY role_name ASC");
if ($roleResult) {
	// Collect role rows for dropdown.
	while ($row = $roleResult->fetch_assoc()) {
		$roles[] = $row;
	}
} else {
	$errors[] = 'Failed to load roles: ' . $conn->error;
}

// Build assignment query fields based on available columns.
$assignmentFields = ['ur.user_role_id', 'ur.user_id', 'ur.role_id', 'u.username', 'u.email', 'r.role_name'];
if ($hasUsername) {
	$assignmentFields[] = 'ur.username AS assigned_username';
}
if ($hasRoleName) {
	$assignmentFields[] = 'ur.role_name AS assigned_role_name';
}

// Load role assignments with related user and role info.
$assignments = $conn->query(
	"SELECT " . implode(', ', $assignmentFields) . "
	 FROM user_roles ur
	 LEFT JOIN users u ON ur.user_id = u.user_id
	 LEFT JOIN roles r ON ur.role_id = r.role_id
	 ORDER BY ur.user_role_id DESC"
);
?>
<?php // Shared CSS/JS resources for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php // Top navigation bar for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php // Sidebar navigation for admin sections. ?>
<?php include(ROOT_PATH . '/app/views/sidebar.php') ?>
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
						<?php // Show clear button when editing an assignment. ?>
						<?php if ($editId > 0): ?>
						<a href="<?php echo BASE_URL; ?>manage_user_role.php" class="btn btn-secondary btn-sm">Clear</a>
						<?php endif; ?>
					</div>

					<?php // Render validation errors when present. ?>
					<?php if (!empty($errors)): ?>
					<div class="alert alert-warning">
						<?php // Output each error message. ?>
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
											<?php // Render user options with readable labels. ?>
											<?php foreach ($users as $user): ?>
												<?php
													// Build the option label from username/email.
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
											<?php // Render role options with selected state. ?>
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
							<?php // Switch button label based on edit state. ?>
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
										<?php // Show records when assignments exist. ?>
										<?php if ($assignments && $assignments->num_rows > 0): ?>
										<?php // Render each assignment row. ?>
										<?php while ($row = $assignments->fetch_assoc()): ?>
											<?php
												// Build user label with fallbacks.
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
												// Build role label with fallbacks.
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
<?php // Shared footer markup for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php // Shared JS resources for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
