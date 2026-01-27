<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/permissions.php';

$context = rbac_get_context($conn);
$isLibrarian = strcasecmp($context['role_name'] ?? '', 'Librarian') === 0;
$canManageUsers = $context['is_admin'] || $isLibrarian;

$alerts = [];
$status = $_GET['status'] ?? '';
if ($status === 'approved') {
	$alerts[] = ['success', 'User approved successfully.'];
} elseif ($status === 'rejected') {
	$alerts[] = ['warning', 'User rejected.'];
} elseif ($status === 'blocked') {
	$alerts[] = ['warning', 'User blocked.'];
} elseif ($status === 'suspended') {
	$alerts[] = ['warning', 'User suspended.'];
} elseif ($status === 'deleted') {
	$alerts[] = ['danger', 'User deleted.'];
} elseif ($status === 'error') {
	$alerts[] = ['danger', 'Unable to update user status.'];
}

$result = $conn->query(
	"SELECT user_id, username, email, account_status, created_date
	 FROM users
	 WHERE deleted_date IS NULL
	 ORDER BY user_id DESC"
);
if ($result === false) {
	die("Query failed: " . $conn->error);
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
						<h3 class="mb-0">Users</h3>
						<a href="<?php echo BASE_URL; ?>edit_profile.php" class="btn btn-primary btn-sm">Add Profile</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<?php if ($alerts): ?>
							<div class="mb-3">
								<?php foreach ($alerts as $alert): ?>
								<div class="alert alert-<?php echo htmlspecialchars($alert[0]); ?> mb-2">
									<?php echo htmlspecialchars($alert[1]); ?>
								</div>
								<?php endforeach; ?>
							</div>
							<?php endif; ?>
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-light">
										<tr>
											<th>#</th>
											<th>Username</th>
											<th>Email</th>
											<th>Created</th>
											<th>Status</th>
											<th class="text-center">Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php if ($result->num_rows > 0): ?>
										<?php while ($row = $result->fetch_assoc()): ?>
										<?php
											$statusValue = strtolower($row['account_status'] ?? 'pending');
											$statusLabel = ucfirst($statusValue !== '' ? $statusValue : 'pending');
											$statusClass = 'secondary';
											if ($statusValue === 'approved') {
												$statusClass = 'success';
											} elseif ($statusValue === 'blocked') {
												$statusClass = 'danger';
											} elseif ($statusValue === 'rejected') {
												$statusClass = 'danger';
											} elseif ($statusValue === 'pending') {
												$statusClass = 'warning';
											} elseif ($statusValue === 'suspended') {
												$statusClass = 'dark';
											}
										?>
										<tr>
											<td><?= (int) $row['user_id'] ?></td>
											<td><?= htmlspecialchars($row['username'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['email'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['created_date'] ?? '') ?></td>
											<td><span class="badge bg-<?php echo $statusClass; ?>"><?php echo htmlspecialchars($statusLabel); ?></span></td>
											
											<td class="text-center">
												<?php if ($canManageUsers): ?>
													<form method="post" action="<?php echo BASE_URL; ?>actions/admin_process_user.php" class="d-inline">
														<input type="hidden" name="user_id" value="<?= (int) $row['user_id'] ?>">
														<input type="hidden" name="action" value="approve">
														<button type="submit" class="btn btn-sm btn-outline-success" <?php echo $statusValue === 'approved' ? 'disabled' : ''; ?>>Approve</button>
													</form>
													<form method="post" action="<?php echo BASE_URL; ?>actions/admin_process_user.php" class="d-inline">
														<input type="hidden" name="user_id" value="<?= (int) $row['user_id'] ?>">
														<input type="hidden" name="action" value="block">
														<button type="submit" class="btn btn-sm btn-outline-danger" <?php echo $statusValue === 'blocked' ? 'disabled' : ''; ?>>Block</button>
													</form>
													<form method="post" action="<?php echo BASE_URL; ?>actions/admin_process_user.php" class="d-inline">
														<input type="hidden" name="user_id" value="<?= (int) $row['user_id'] ?>">
														<input type="hidden" name="action" value="suspend">
														<button type="submit" class="btn btn-sm btn-outline-warning" <?php echo $statusValue === 'suspended' ? 'disabled' : ''; ?>>Suspend</button>
													</form>
													<form method="post" action="<?php echo BASE_URL; ?>actions/admin_process_user.php" class="d-inline" onsubmit="return confirm('Delete this user?');">
														<input type="hidden" name="user_id" value="<?= (int) $row['user_id'] ?>">
														<input type="hidden" name="action" value="delete">
														<button type="submit" class="btn btn-sm btn-outline-dark">Delete</button>
													</form>
												<?php else: ?>
													<span class="text-muted">No actions</span>
												<?php endif; ?>
											</td>
										</tr>
										<?php endwhile; ?>
										<?php else: ?>
										<tr>
											<td colspan="17" class="text-center text-muted">No records found.</td>
										</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
<!--end::App Main-->
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>
