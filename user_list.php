<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

$result = $conn->query(
	"SELECT profile_id, user_id, first_name, last_name, dob, address, phone, institution_name, designation, profile_picture, created_by, created_date, modified_by, modified_date, deleted_by, deleted_date
	 FROM user_profiles
	 ORDER BY profile_id DESC"
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
						<h3 class="mb-0">User Profiles</h3>
						<a href="<?php echo BASE_URL; ?>edit_profile.php" class="btn btn-primary btn-sm">Add Profile</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-light">
										<tr>
											<th>#</th>
											<th>First Name</th>
											<th>Last Name</th>
											<th>DOB</th>
											<th>Address</th>
											<th>Phone</th>
											<th>Institution</th>
											<th>Designation</th>
											<th class="text-center">Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php if ($result->num_rows > 0): ?>
										<?php while ($row = $result->fetch_assoc()): ?>
										<tr>
											<td><?= (int) $row['profile_id'] ?></td>
											<td><?= htmlspecialchars($row['first_name'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['last_name'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['dob'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['address'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['institution_name'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['designation'] ?? '') ?></td>
											
											<td class="text-center">
												<a href="<?php echo BASE_URL; ?>edit_profile.php?id=<?= (int) $row['profile_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>
												<a href="<?php echo BASE_URL; ?>crud_files/delete_user_profile.php?id=<?= (int) $row['profile_id'] ?>" class="text-danger" title="Delete"
													onclick="return confirm('Are you sure you want to delete this profile?');">
													<i class="bi bi-trash fs-5"></i>
												</a>
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
