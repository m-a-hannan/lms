<?php
// Load app configuration, database connection, and permissions helpers.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';

// Track validation errors and success messages.
$errors = [];
$success = isset($_GET['saved']) && $_GET['saved'] === '1';

// Resolve the selected role id from query or form data.
$roleId = 0;
if (isset($_GET['role_id'])) {
	$roleId = (int) $_GET['role_id'];
}
if (isset($_POST['role_id'])) {
	$roleId = (int) $_POST['role_id'];
}

// Load available roles for the selector.
$roles = [];
$roleResult = $conn->query("SELECT role_id, role_name FROM roles ORDER BY role_name ASC");
if ($roleResult) {
	while ($row = $roleResult->fetch_assoc()) {
		$roles[] = $row;
	}
} else {
	$errors[] = 'Failed to load roles: ' . $conn->error;
}

// Default to the first role when none is selected.
if ($roleId <= 0 && !empty($roles)) {
	$roleId = (int) $roles[0]['role_id'];
}

// Load active pages and filter out auth-related routes.
$skipPages = ['login.php', 'logout.php', 'register.php'];
$pages = [];
$pageResult = $conn->query("SELECT page_id, page_name, page_path FROM page_list WHERE is_active = 1 ORDER BY page_name ASC");
if ($pageResult) {
	while ($row = $pageResult->fetch_assoc()) {
		$path = rbac_normalize_path($row['page_path'] ?? '');
		if ($path === '' || in_array($path, $skipPages, true)) {
			continue;
		}
		$row['page_path'] = $path;
		$pages[] = $row;
	}
} else {
	$errors[] = 'Failed to load pages: ' . $conn->error;
}

// Load existing permissions for the selected role.
$stored = [];
if ($roleId > 0) {
	$permResult = $conn->query("SELECT page_id, can_read, can_write, deny FROM permissions WHERE role_id = $roleId");
	if ($permResult) {
		while ($row = $permResult->fetch_assoc()) {
			$pageId = (int) $row['page_id'];
			$stored[$pageId] = [
				'read' => (int) ($row['can_read'] ?? 0),
				'write' => (int) ($row['can_write'] ?? 0),
				'deny' => (int) ($row['deny'] ?? 0),
			];
		}
	} else {
		$errors[] = 'Failed to load permissions: ' . $conn->error;
	}
}

// Handle permission save submissions.
if (isset($_POST['save'])) {
	if ($roleId <= 0) {
		$errors[] = 'Please select a role.';
	} else {
		// Clear existing permissions for the role.
		$deleted = $conn->query("DELETE FROM permissions WHERE role_id = $roleId");
		if ($deleted === false) {
			$errors[] = 'Failed to clear existing permissions: ' . $conn->error;
		} else {
			// Insert the new permission selections.
			foreach ($pages as $page) {
				$pageId = (int) $page['page_id'];
				$read = isset($_POST['perm'][$pageId]['read']) ? 1 : 0;
				$write = isset($_POST['perm'][$pageId]['write']) ? 1 : 0;
				$deny = isset($_POST['perm'][$pageId]['deny']) ? 1 : 0;

				// Deny overrides read/write selections.
				if ($deny) {
					$read = 0;
					$write = 0;
				}

				// Persist permissions only when at least one flag is set.
				if ($read || $write || $deny) {
					$sql = "INSERT INTO permissions (role_id, page_id, can_read, can_write, deny)
						VALUES ($roleId, $pageId, $read, $write, $deny)";
					if (!$conn->query($sql)) {
						$errors[] = 'Database error: ' . $conn->error;
						break;
					}
				}
			}
		}
	}

	// Redirect after a successful save.
	if (empty($errors)) {
		header("Location: " . BASE_URL . "permission_management.php?role_id=$roleId&saved=1");
		exit;
	}
}

// Track posted permissions for sticky checkbox states.
$posted = isset($_POST['perm']) ? $_POST['perm'] : [];
function is_checked($pageId, $key, $stored, $posted)
{
	if (!empty($posted)) {
		return isset($posted[$pageId][$key]);
	}
	return !empty($stored[$pageId][$key]);
}
?>
<?php // Shared header resources and layout chrome. ?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
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
					<!-- Page header. -->
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h3 class="mb-0">Permission Management</h3>
					</div>

					<!-- Success feedback message. -->
					<?php if ($success): ?>
					<div class="alert alert-success">Permissions saved successfully.</div>
					<?php endif; ?>

					<!-- Error feedback messages. -->
					<?php if (!empty($errors)): ?>
					<div class="alert alert-warning">
						<?php foreach ($errors as $message): ?>
						<div><?php echo htmlspecialchars($message); ?></div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<!-- Role selector card. -->
					<div class="card shadow-sm mb-4">
						<div class="card-body">
							<!-- Role filter form. -->
							<form method="get" class="row g-3 align-items-end">
								<div class="col-md-6">
									<label class="form-label">Role</label>
									<select class="form-select w-100" name="role_id" onchange="this.form.submit()">
										<option value="">Select Role</option>
										<?php foreach ($roles as $role): ?>
										<?php $selected = ((int) $role['role_id'] === (int) $roleId) ? 'selected' : ''; ?>
										<option value="<?php echo (int) $role['role_id']; ?>" <?php echo $selected; ?>>
											<?php echo htmlspecialchars($role['role_name'] ?? ''); ?>
										</option>
										<?php endforeach; ?>
									</select>
								</div>
							</form>
						</div>
					</div>

					<!-- Permissions matrix card. -->
					<div class="card shadow-sm">
						<div class="card-body">
							<!-- Empty state when no pages exist. -->
							<?php if (empty($pages)): ?>
							<div class="text-muted">No pages found in page_list.</div>
							<?php else: ?>
							<!-- Permissions save form. -->
							<form method="post">
								<input type="hidden" name="role_id" value="<?php echo (int) $roleId; ?>">
								<div class="table-responsive">
									<table class="table table-bordered table-hover align-middle">
										<!-- Table headers. -->
										<thead class="table-light">
											<tr>
												<th>Page</th>
												<th>Path</th>
												<th class="text-center">Read</th>
												<th class="text-center">Write</th>
												<th class="text-center">Deny</th>
											</tr>
										</thead>
										<tbody>
											<!-- Render permissions for each page. -->
											<?php foreach ($pages as $page): ?>
											<?php
														$pageId = (int) $page['page_id'];
														$readChecked = is_checked($pageId, 'read', $stored, $posted);
														$writeChecked = is_checked($pageId, 'write', $stored, $posted);
														$denyChecked = is_checked($pageId, 'deny', $stored, $posted);
														if ($denyChecked) {
															$readChecked = false;
															$writeChecked = false;
														}
													?>
											<tr>
												<td><?php echo htmlspecialchars($page['page_name'] ?? ''); ?></td>
												<td><?php echo htmlspecialchars($page['page_path'] ?? ''); ?></td>
												<td class="text-center">
													<input type="checkbox" class="form-check-input perm-read"
														name="perm[<?php echo $pageId; ?>][read]" <?php echo $readChecked ? 'checked' : ''; ?>>
												</td>
												<td class="text-center">
													<input type="checkbox" class="form-check-input perm-write"
														name="perm[<?php echo $pageId; ?>][write]" <?php echo $writeChecked ? 'checked' : ''; ?>>
												</td>
												<td class="text-center">
													<input type="checkbox" class="form-check-input perm-deny"
														name="perm[<?php echo $pageId; ?>][deny]" <?php echo $denyChecked ? 'checked' : ''; ?>>
												</td>
											</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<!-- Form actions. -->
								<div class="d-flex justify-content-end">
									<button type="submit" name="save" class="btn btn-primary">Save Permissions</button>
								</div>
							</form>
							<?php endif; ?>
						</div>
					</div>

				</div>
			</div>
			<!-- end::Row -->
		</div>
	</div>
</main>
<!--end::App Main-->
<?php // Shared footer layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php // Page-specific JS. ?>
<script src="<?php echo BASE_URL; ?>assets/js/pages/permission_management.js"></script>
<?php // Shared footer scripts. ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
