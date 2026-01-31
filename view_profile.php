<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/permissions.php';
require_once ROOT_PATH . '/include/library_helpers.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$profile = null;
$profileMissing = false;
$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
if ($userId <= 0) {
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

$context = rbac_get_context($conn);
$roleName = $context['role_name'] ?? '';
$isLibrarian = strcasecmp($roleName, 'Librarian') === 0;
$showAuditColumns = $context['is_admin'] || $isLibrarian;
$userLookup = $showAuditColumns ? library_user_map($conn) : [];
$canChangePassword = rbac_can_access($conn, 'change_password.php', 'read');
$result = $conn->query("SELECT * FROM user_profiles WHERE user_id = $userId ORDER BY profile_id DESC LIMIT 1");
if ($result && $result->num_rows === 1) {
	$profile = $result->fetch_assoc();
}
if (!$profile) {
	$profileMissing = true;
}

if (!is_array($profile)) {
	$profile = [];
}

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

$profileImage = $profile['profile_picture'] ?? '';
$profileImage = $profileImage !== '' ? htmlspecialchars($profileImage) : 'assets/img/avatar.png';
$fullName = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
$fullName = $fullName !== '' ? htmlspecialchars($fullName) : '-';
$bodyClass = 'page-view-profile';
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
					<div class="col-md-12">
						<div class="d-flex justify-content-between align-items-center mb-4">
							<h3 class="mb-0">Profile Details</h3>
							<div class="d-flex gap-2">
								<a href="<?php echo BASE_URL; ?>edit_profile.php" class="btn btn-primary btn-sm">Edit</a>
							</div>
						</div>

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
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>