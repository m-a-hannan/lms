<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Fetch user profile details with related user and role info.
$result = $conn->query(
	"SELECT up.profile_id, up.user_id, up.first_name, up.last_name, up.dob, up.address, up.phone,
		up.institution_name, up.designation, up.profile_picture,
		u.username, u.email,
		ur.role_name
	 FROM user_profiles up
	 LEFT JOIN users u ON u.user_id = up.user_id
	 LEFT JOIN user_roles ur ON ur.user_id = up.user_id
	 ORDER BY up.profile_id DESC"
);
if ($result === false) {
	die("Query failed: " . $conn->error);
}
?>
<?php // Shared CSS/JS resources for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php // Top navigation bar for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php // Sidebar navigation for admin sections. ?>
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
						<h3 class="mb-0">User Profile List</h3>
						<a href="<?php echo BASE_URL; ?>crud_files/add_user_profile.php" class="btn btn-primary btn-sm">Add User Profile</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-light">
										<tr>
											<th>#</th>
											<th>User Id</th>
											<th>Username</th>
											<th>Email</th>
											<th>Role</th>
											<th>First Name</th>
											<th>Last Name</th>
											<th>Dob</th>
											<th>Address</th>
											<th>Phone</th>
											<th>Photo</th>
											<th>Institution Name</th>
											<th>Designation</th>
											<th class="text-center">Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php // Show records when the result set has rows. ?>
										<?php if ($result->num_rows > 0): ?>
										<?php // Render each user profile row. ?>
										<?php while ($row = $result->fetch_assoc()): ?>
										<tr>
											<td><?= $row["profile_id"] ?></td>
											<td><?= htmlspecialchars($row['user_id'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['username'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['email'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['role_name'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['first_name'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['last_name'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['dob'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['address'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
											<td>
												<?php
													// Resolve profile image path with a default fallback.
													$profilePic = $row['profile_picture'] ?? '';
													$profilePic = $profilePic !== '' ? BASE_URL . ltrim($profilePic, '/') : BASE_URL . 'assets/img/user2-160x160.jpg';
												?>
												<img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile" style="width:40px;height:40px;object-fit:cover;border-radius:50%;">
											</td>
											<td><?= htmlspecialchars($row['institution_name'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['designation'] ?? '') ?></td>
											<td class="text-center">
												<a href="<?php echo BASE_URL; ?>crud_files/edit_user_profile.php?id=<?= $row['profile_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>
												<a href="<?php echo BASE_URL; ?>crud_files/delete_user_profile.php?id=<?= $row['profile_id'] ?>" class="text-danger" title="Delete"
													onclick="return confirm('Are you sure you want to delete this item?');">
													<i class="bi bi-trash fs-5"></i>
												</a>
											</td>
										</tr>
										<?php endwhile; ?>
										<?php else: ?>
										<tr>
											<td colspan="14" class="text-center text-muted">No records found.</td>
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
