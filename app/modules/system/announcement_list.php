<?php
// Load app configuration, database connection, and RBAC helpers.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';
require_once ROOT_PATH . '/app/includes/library_helpers.php';

// Determine whether to show audit columns for privileged roles.
$context = rbac_get_context($conn);
$roleName = $context['role_name'] ?? '';
$isLibrarian = strcasecmp($roleName, 'Librarian') === 0;
$showAuditColumns = $context['is_admin'] || $isLibrarian;
$userLookup = $showAuditColumns ? library_user_map($conn) : [];

// Fetch announcement records for display.
$result = $conn->query("SELECT * FROM announcements ORDER BY announcement_id DESC");
if ($result === false) {
	die("Query failed: " . $conn->error);
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
					<!-- Page header with title and create action. -->
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h3 class="mb-0">Announcement List</h3>
						<a href="<?php echo BASE_URL; ?>crud_files/add_announcement.php" class="btn btn-primary btn-sm">Add Announcement</a>
					</div>
					<!-- Results table card. -->
					<div class="card shadow-sm">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<!-- Table headers. -->
									<thead class="table-light">
										<tr>
											<th>Announcement Id</th>
											<th>Title</th>
											<th>Message</th>
											<th>Created At</th>
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
										<!-- Render rows when records exist. -->
										<?php if ($result->num_rows > 0): ?>
										<?php while ($row = $result->fetch_assoc()): ?>
										<tr>
											<td><?= htmlspecialchars($row['announcement_id']) ?></td>
											<td><?= htmlspecialchars($row['title']) ?></td>
											<td><?= htmlspecialchars($row['message']) ?></td>
											<td><?= htmlspecialchars($row['created_at']) ?></td>
											<?php if ($showAuditColumns): ?>
											<td><?= htmlspecialchars(library_user_label($row['created_by'] ?? null, $userLookup)) ?></td>
											<td><?= htmlspecialchars($row['created_date']) ?></td>
											<td><?= htmlspecialchars(library_user_label($row['modified_by'] ?? null, $userLookup)) ?></td>
											<td><?= htmlspecialchars($row['modified_date']) ?></td>
											<td><?= htmlspecialchars(library_user_label($row['deleted_by'] ?? null, $userLookup)) ?></td>
											<td><?= htmlspecialchars($row['deleted_date']) ?></td>
											<?php endif; ?>
											<td class="text-center">
												<!-- Row actions for edit and delete. -->
												<a href="<?php echo BASE_URL; ?>crud_files/edit_announcement.php?id=<?= $row['announcement_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>
												<a href="<?php echo BASE_URL; ?>crud_files/delete_announcement.php?id=<?= $row['announcement_id'] ?>" class="text-danger" title="Delete"
													onclick="return confirm('Are you sure you want to delete this item?');">
													<i class="bi bi-trash fs-5"></i>
												</a>
											</td>
										</tr>
										<?php endwhile; ?>
										<?php else: ?>
										<!-- Empty state message. -->
										<tr>
											<td colspan="<?php echo $showAuditColumns ? 11 : 5; ?>" class="text-center text-muted">No records found.</td>
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
<?php // Shared footer layout and scripts. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
