<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/permissions.php';

$errors = [];
$success = isset($_GET['saved']) && $_GET['saved'] === '1';

$roleId = 0;
if (isset($_GET['role_id'])) {
	$roleId = (int) $_GET['role_id'];
}
if (isset($_POST['role_id'])) {
	$roleId = (int) $_POST['role_id'];
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

if ($roleId <= 0 && !empty($roles)) {
	$roleId = (int) $roles[0]['role_id'];
}

$skipPages = ['login.php', 'logout.php', 'register.php', 'index.php'];
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

if (isset($_POST['save'])) {
	if ($roleId <= 0) {
		$errors[] = 'Please select a role.';
	} else {
		$deleted = $conn->query("DELETE FROM permissions WHERE role_id = $roleId");
		if ($deleted === false) {
			$errors[] = 'Failed to clear existing permissions: ' . $conn->error;
		} else {
			foreach ($pages as $page) {
				$pageId = (int) $page['page_id'];
				$read = isset($_POST['perm'][$pageId]['read']) ? 1 : 0;
				$write = isset($_POST['perm'][$pageId]['write']) ? 1 : 0;
				$deny = isset($_POST['perm'][$pageId]['deny']) ? 1 : 0;

				if ($deny) {
					$read = 0;
					$write = 0;
				}

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

	if (empty($errors)) {
		header("Location: " . BASE_URL . "permission_management.php?role_id=$roleId&saved=1");
		exit;
	}
}

$posted = isset($_POST['perm']) ? $_POST['perm'] : [];
function is_checked($pageId, $key, $stored, $posted)
{
	if (!empty($posted)) {
		return isset($posted[$pageId][$key]);
	}
	return !empty($stored[$pageId][$key]);
}
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
						<h3 class="mb-0">Permission Management</h3>
					</div>

					<?php if ($success): ?>
					<div class="alert alert-success">Permissions saved successfully.</div>
					<?php endif; ?>

					<?php if (!empty($errors)): ?>
					<div class="alert alert-warning">
						<?php foreach ($errors as $message): ?>
						<div><?php echo htmlspecialchars($message); ?></div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<div class="card shadow-sm mb-4">
						<div class="card-body">
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

					<div class="card shadow-sm">
						<div class="card-body">
							<?php if (empty($pages)): ?>
							<div class="text-muted">No pages found in page_list.</div>
							<?php else: ?>
							<form method="post">
								<input type="hidden" name="role_id" value="<?php echo (int) $roleId; ?>">
								<div class="table-responsive">
									<table class="table table-bordered table-hover align-middle">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.perm-deny').forEach(function(checkbox) {
		checkbox.addEventListener('change', function() {
			var row = checkbox.closest('tr');
			if (!row) {
				return;
			}
			if (checkbox.checked) {
				var readBox = row.querySelector('.perm-read');
				var writeBox = row.querySelector('.perm-write');
				if (readBox) {
					readBox.checked = false;
				}
				if (writeBox) {
					writeBox.checked = false;
				}
			}
		});
	});
});
</script>
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>
