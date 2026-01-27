<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

$result = $conn->query("SELECT * FROM notifications ORDER BY notification_id DESC");
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
						<h3 class="mb-0">Notification List</h3>
						<a href="<?php echo BASE_URL; ?>crud_files/add_notification.php" class="btn btn-primary btn-sm">Add Notification</a>
					</div>
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
									<th>Created By</th>
									<th>Created Date</th>
									<th>Modified By</th>
									<th>Modified Date</th>
									<th>Deleted By</th>
									<th>Deleted Date</th>
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
									<td><?= htmlspecialchars($row['created_by']) ?></td>
									<td><?= htmlspecialchars($row['created_date']) ?></td>
									<td><?= htmlspecialchars($row['modified_by']) ?></td>
									<td><?= htmlspecialchars($row['modified_date']) ?></td>
									<td><?= htmlspecialchars($row['deleted_by']) ?></td>
									<td><?= htmlspecialchars($row['deleted_date']) ?></td>
											<td class="text-center">
												<a href="<?php echo BASE_URL; ?>crud_files/edit_notification.php?id=<?= $row['notification_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>
												<a href="<?php echo BASE_URL; ?>crud_files/delete_notification.php?id=<?= $row['notification_id'] ?>" class="text-danger" title="Delete"
													onclick="return confirm('Are you sure you want to delete this item?');">
													<i class="bi bi-trash fs-5"></i>
												</a>
											</td>
										</tr>
										<?php endwhile; ?>
										<?php else: ?>
										<tr>
											<td colspan="13" class="text-center text-muted">No records found.</td>
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
