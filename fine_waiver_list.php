<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

$result = $conn->query("SELECT * FROM fine_waivers ORDER BY waiver_id DESC");
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
						<h3 class="mb-0">Fine Waiver List</h3>
						<a href="<?php echo BASE_URL; ?>crud_files/add_fine_waiver.php" class="btn btn-primary btn-sm">Add Fine Waiver</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-light">
										<tr>
									<th>Waiver Id</th>
									<th>Fine Id</th>
									<th>Approved By</th>
									<th>Waiver Date</th>
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
									<td><?= htmlspecialchars($row['waiver_id']) ?></td>
									<td><?= htmlspecialchars($row['fine_id']) ?></td>
									<td><?= htmlspecialchars($row['approved_by']) ?></td>
									<td><?= htmlspecialchars($row['waiver_date']) ?></td>
									<td><?= htmlspecialchars($row['created_by']) ?></td>
									<td><?= htmlspecialchars($row['created_date']) ?></td>
									<td><?= htmlspecialchars($row['modified_by']) ?></td>
									<td><?= htmlspecialchars($row['modified_date']) ?></td>
									<td><?= htmlspecialchars($row['deleted_by']) ?></td>
									<td><?= htmlspecialchars($row['deleted_date']) ?></td>
											<td class="text-center">
												<a href="<?php echo BASE_URL; ?>crud_files/edit_fine_waiver.php?id=<?= $row['waiver_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>
												<a href="<?php echo BASE_URL; ?>crud_files/delete_fine_waiver.php?id=<?= $row['waiver_id'] ?>" class="text-danger" title="Delete"
													onclick="return confirm('Are you sure you want to delete this item?');">
													<i class="bi bi-trash fs-5"></i>
												</a>
											</td>
										</tr>
										<?php endwhile; ?>
										<?php else: ?>
										<tr>
											<td colspan="11" class="text-center text-muted">No records found.</td>
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
