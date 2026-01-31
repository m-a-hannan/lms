<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';
require_once ROOT_PATH . '/app/includes/library_helpers.php';

$context = rbac_get_context($conn);
$roleName = $context['role_name'] ?? '';
$isLibrarian = strcasecmp($roleName, 'Librarian') === 0;
$showAuditColumns = $context['is_admin'] || $isLibrarian;
$isUserView = !$showAuditColumns;
$userLookup = $showAuditColumns ? library_user_map($conn) : [];

$alerts = [];
$removeStatus = $_GET['remove'] ?? '';
if ($removeStatus !== '') {
	if ($removeStatus === 'success') {
		$alerts[] = ['success', 'Notification removed from your account.'];
	} elseif ($removeStatus === 'notfound') {
		$alerts[] = ['warning', 'Notification not found or already removed.'];
	} elseif ($removeStatus === 'error') {
		$alerts[] = ['danger', 'Unable to remove notification.'];
	}
}

if ($isUserView) {
	$userId = (int) ($context['user_id'] ?? 0);
	$stmt = $conn->prepare(
		"SELECT notification_id, user_id, title, message, created_at, read_status
		 FROM notifications
		 WHERE user_id = ? AND deleted_date IS NULL
		 ORDER BY created_at DESC, notification_id DESC"
	);
	if (!$stmt) {
		die("Query failed: " . $conn->error);
	}
	$stmt->bind_param('i', $userId);
	$stmt->execute();
	$result = $stmt->get_result();
} else {
	$result = $conn->query("SELECT * FROM notifications ORDER BY notification_id DESC");
	if ($result === false) {
		die("Query failed: " . $conn->error);
	}
}
?>
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
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h3 class="mb-0">Notification List</h3>
						<?php if ($showAuditColumns): ?>
						<a href="<?php echo BASE_URL; ?>crud_files/add_notification.php" class="btn btn-primary btn-sm">Add Notification</a>
						<?php endif; ?>
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
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-light">
										<tr>
									<th>Notification Id</th>
									<th>User Id</th>
									<th>Title</th>
									<th>Message</th>
									<th>Created At</th>
									<th>Read Status</th>
									<?php if ($showAuditColumns): ?>
									<th>Created By</th>
									<th>Created Date</th>
									<th>Modified By</th>
									<th>Modified Date</th>
									<th>Deleted By</th>
									<th>Deleted Date</th>
									<?php endif; ?>
											<th class="text-center">Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php if ($result->num_rows > 0): ?>
										<?php while ($row = $result->fetch_assoc()): ?>
										<tr>
									<td><?= htmlspecialchars($row['notification_id']) ?></td>
									<td><?= htmlspecialchars($row['user_id']) ?></td>
									<td><?= htmlspecialchars($row['title']) ?></td>
									<td><?= htmlspecialchars($row['message']) ?></td>
									<td><?= htmlspecialchars($row['created_at']) ?></td>
									<td><?= htmlspecialchars($row['read_status']) ?></td>
									<?php if ($showAuditColumns): ?>
									<td><?= htmlspecialchars(library_user_label($row['created_by'] ?? null, $userLookup)) ?></td>
									<td><?= htmlspecialchars($row['created_date']) ?></td>
									<td><?= htmlspecialchars(library_user_label($row['modified_by'] ?? null, $userLookup)) ?></td>
									<td><?= htmlspecialchars($row['modified_date']) ?></td>
									<td><?= htmlspecialchars(library_user_label($row['deleted_by'] ?? null, $userLookup)) ?></td>
									<td><?= htmlspecialchars($row['deleted_date']) ?></td>
									<?php endif; ?>
											<td class="text-center">
												<?php if ($showAuditColumns): ?>
												<a href="<?php echo BASE_URL; ?>crud_files/edit_notification.php?id=<?= $row['notification_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>
												<a href="<?php echo BASE_URL; ?>crud_files/delete_notification.php?id=<?= $row['notification_id'] ?>" class="text-danger" title="Delete"
													onclick="return confirm('Are you sure you want to delete this item?');">
													<i class="bi bi-trash fs-5"></i>
												</a>
												<?php else: ?>
												<form method="post" action="<?php echo BASE_URL; ?>actions/remove_notification.php" class="d-inline">
													<input type="hidden" name="notification_id" value="<?= (int) $row['notification_id'] ?>">
													<button class="btn btn-sm btn-outline-danger">
														Remove
													</button>
												</form>
												<?php endif; ?>
											</td>
										</tr>
										<?php endwhile; ?>
										<?php else: ?>
										<tr>
											<td colspan="<?php echo $showAuditColumns ? 13 : 7; ?>" class="text-center text-muted">No records found.</td>
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
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>