<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

$result = $conn->query("SELECT * FROM roles WHERE deleted_date IS NULL ORDER BY role_id DESC");
if ($result === false) {
	die("Query failed: " . $conn->error);
}
?>
<?php
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
					<div class="col-md-12">
						<!-- Content title -->
						<div class="d-flex justify-content-between align-items-center mb-4">
							<h3 class="mb-0">Available Roles</h3>
							<a href="<?php echo BASE_URL; ?>crud_files/add_role.php" class="btn btn-primary btn-sm">Add Role</a>
						</div>
						<!-- Add Contents below -->

						<div class="card shadow-sm">
							<div class="card-body">

								<div class="table-responsive">
									<table class="table table-bordered table-hover align-middle">
										<thead class="table-light">
											<tr>
												<th>#</th>
												<th>Role Name</th>
												<th class="text-center">Actions</th>
											</tr>
										</thead>
										<tbody>

											<?php if ($result->num_rows > 0): ?>
											<?php while ($row = $result->fetch_assoc()): ?>
											<tr>
												<td><?= $row["role_id"] ?></td>
												<td><?= htmlspecialchars($row["role_name"]) ?></td>

												<!-- Action buttons -->
												<td class="text-center">
													<a href="<?php echo BASE_URL; ?>crud_files/edit_role.php?id=<?= $row['role_id'] ?>"
														class="text-primary me-2" title="Edit">
														<i class="bi bi-pencil-square fs-5"></i>
													</a>

													<a href="<?php echo BASE_URL; ?>crud_files/delete_role.php?id=<?= $row['role_id'] ?>"
														class="text-danger" title="Delete"
 data-confirm-delete>
														<i class="bi bi-trash fs-5"></i>
													</a>
												</td>
											</tr>
											<?php endwhile; ?>
											<?php else: ?>
											<tr>
												<td colspan="7" class="text-center text-muted">
													No Category found.
												</td>
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
			<!-- end::Row -->
		</div>
	</div>
</main>
<!--end::App Main-->
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>