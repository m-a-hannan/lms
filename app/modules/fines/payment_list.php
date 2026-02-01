<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';
require_once ROOT_PATH . '/app/includes/library_helpers.php';

$context = rbac_get_context($conn);
$roleName = $context['role_name'] ?? '';
$isLibrarian = strcasecmp($roleName, 'Librarian') === 0;
$showAuditColumns = $context['is_admin'] || $isLibrarian;
$userLookup = $showAuditColumns ? library_user_map($conn) : [];

$result = $conn->query("SELECT * FROM payments WHERE deleted_date IS NULL ORDER BY payment_id DESC");
if ($result === false) {
	die("Query failed: " . $conn->error);
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
						<h3 class="mb-0">Payment List</h3>
						<a href="<?php echo BASE_URL; ?>crud_files/add_payment.php" class="btn btn-primary btn-sm">Add Payment</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-light">
										<tr>
									<th>Payment Id</th>
									<th>Fine Id</th>
									<th>Payment Date</th>
									<th>Amount</th>
									<th>Payment Method</th>
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
									<td><?= htmlspecialchars($row['payment_id']) ?></td>
									<td><?= htmlspecialchars($row['fine_id']) ?></td>
									<td><?= htmlspecialchars($row['payment_date']) ?></td>
									<td><?= htmlspecialchars($row['amount']) ?></td>
									<td><?= htmlspecialchars($row['payment_method']) ?></td>
									<?php if ($showAuditColumns): ?>
									<td><?= htmlspecialchars(library_user_label($row['created_by'] ?? null, $userLookup)) ?></td>
									<td><?= htmlspecialchars($row['created_date']) ?></td>
									<td><?= htmlspecialchars(library_user_label($row['modified_by'] ?? null, $userLookup)) ?></td>
									<td><?= htmlspecialchars($row['modified_date']) ?></td>
									<td><?= htmlspecialchars(library_user_label($row['deleted_by'] ?? null, $userLookup)) ?></td>
									<td><?= htmlspecialchars($row['deleted_date']) ?></td>
									<?php endif; ?>
											<td class="text-center">
												<a href="<?php echo BASE_URL; ?>crud_files/edit_payment.php?id=<?= $row['payment_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>
												<a href="<?php echo BASE_URL; ?>crud_files/delete_payment.php?id=<?= $row['payment_id'] ?>" class="text-danger" title="Delete"
 data-confirm-delete>
													<i class="bi bi-trash fs-5"></i>
												</a>
											</td>
										</tr>
										<?php endwhile; ?>
										<?php else: ?>
										<tr>
											<td colspan="<?php echo $showAuditColumns ? 12 : 6; ?>" class="text-center text-muted">No records found.</td>
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