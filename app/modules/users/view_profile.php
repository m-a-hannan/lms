<?php
// Load core configuration, database connection, RBAC, and helpers.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';
require_once ROOT_PATH . '/app/includes/library_helpers.php';

// Ensure session is active for user context.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Initialize profile state and enforce authentication.
$profile = null;
$profileMissing = false;
$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
if ($userId <= 0) {
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

// Determine role-based UI visibility.
$context = rbac_get_context($conn);
$roleName = $context['role_name'] ?? '';
$isLibrarian = strcasecmp($roleName, 'Librarian') === 0;
$showAuditColumns = $context['is_admin'] || $isLibrarian;
$userLookup = $showAuditColumns ? library_user_map($conn) : [];
$canChangePassword = rbac_can_access($conn, 'change_password.php', 'read');
// Load the most recent profile for the current user.
$result = $conn->query("SELECT * FROM user_profiles WHERE user_id = $userId ORDER BY profile_id DESC LIMIT 1");
if ($result && $result->num_rows === 1) {
	$profile = $result->fetch_assoc();
}
// Track missing profile for conditional UI.
if (!$profile) {
	$profileMissing = true;
}

// Normalize profile data to an array.
if (!is_array($profile)) {
	$profile = [];
}

// Format values for safe display with fallbacks.
function display_value($value)
{
	if ($value === null) {
		return '-';
	}
	if (is_string($value)) {
		$value = trim($value);
	}
	if ($value === '') {
		return '-';
	}
	return htmlspecialchars((string) $value);
}

// Resolve profile image and display name.
$profileImage = $profile['profile_picture'] ?? '';
$profileImage = $profileImage !== '' ? htmlspecialchars($profileImage) : 'assets/img/avatar.png';
$fullName = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
$fullName = $fullName !== '' ? htmlspecialchars($fullName) : '-';
$bodyClass = 'page-view-profile';
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
					<div class="col-md-12">
						<div class="d-flex justify-content-between align-items-center mb-4">
							<h3 class="mb-0">Profile Details</h3>
							<div class="d-flex gap-2">
								<a href="<?php echo BASE_URL; ?>edit_profile.php" class="btn btn-primary btn-sm">Edit</a>
							</div>
						</div>

						<?php // Show an onboarding message if the profile is missing. ?>
						<?php if ($profileMissing): ?>
						<div class="alert alert-warning">
							Profile not found. Please create your profile first.
							<a href="<?php echo BASE_URL; ?>edit_profile.php" class="btn btn-primary btn-sm ms-2">Create Profile</a>
						</div>
						<?php else: ?>
						<div class="page-content" id="page-content">
							<div class="padding">
								<div class="row">
									<div class="col-12">
										<div class="card user-card-full">
											<div class="row m-l-0 m-r-0">
												<div class="col-sm-4 bg-c-lite-green user-profile">
													<div class="card-block text-center text-white">
														<div class="m-b-25">
															<img src="<?php echo $profileImage; ?>" class="img-radius" alt="User Profile">
														</div>
														<h6 class="f-w-600"><?php echo $fullName; ?></h6>
														<p><?php echo display_value($profile['designation'] ?? null); ?></p>
													</div>
												</div>
												<div class="col-sm-8">
													<div class="card-block">
														<h6 class="m-b-20 p-b-5 b-b-default f-w-600">Information</h6>
														<div class="row">
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">Phone</p>
																<h6 class="text-muted f-w-400"><?php echo display_value($profile['phone'] ?? null); ?>
																</h6>
															</div>
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">DOB</p>
																<h6 class="text-muted f-w-400"><?php echo display_value($profile['dob'] ?? null); ?>
																</h6>
															</div>
														</div>
														<div class="row m-t-40">
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">Institution</p>
																<h6 class="text-muted f-w-400">
																	<?php echo display_value($profile['institution_name'] ?? null); ?></h6>
															</div>
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">Address</p>
																<h6 class="text-muted f-w-400"><?php echo display_value($profile['address'] ?? null); ?>
																</h6>
															</div>
														</div>


														<h6 class="m-b-20 m-t-40 p-b-5 b-b-default f-w-600">Security</h6>
														<div class="row">
															<div class="col-sm-12">
																<a href="<?php echo BASE_URL; ?>change_password.php" class="btn btn-outline-primary">
																	Change Password
																</a>
															</div>
														</div>


														<?php // Show audit metadata only for admins/librarians. ?>
														<?php if ($showAuditColumns): ?>
														<h6 class="m-b-20 m-t-40 p-b-5 b-b-default f-w-600">Metadata</h6>
														<div class="row">
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">Created</p>
																<h6 class="text-muted f-w-400">
																	<?php echo display_value($profile['created_date'] ?? null); ?></h6>
															</div>
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">Modified</p>
																<h6 class="text-muted f-w-400">
																	<?php echo display_value($profile['modified_date'] ?? null); ?></h6>
															</div>
														</div>
														<?php endif; ?>

													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
<!--end::App Main-->
<?php // Shared footer markup for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php // Shared JS resources for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
