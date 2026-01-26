<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

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
?>
<?php include(ROOT_PATH . '/include/header_resources.php') ?>
<?php include(ROOT_PATH . '/include/header.php') ?>

<style>
	body {
    background-color: #f9f9fa
}

.user-card-full {
    overflow: hidden;
    width: 100%;
}

.card {
    border-radius: 5px;
    -webkit-box-shadow: 0 1px 20px 0 rgba(69,90,100,0.08);
    box-shadow: 0 1px 20px 0 rgba(69,90,100,0.08);
    border: none;
    margin-bottom: 30px;
}

.m-r-0 {
    margin-right: 0px;
}

.m-l-0 {
    margin-left: 0px;
}

.user-card-full .user-profile {
    border-radius: 5px 0 0 5px;
}

.bg-c-lite-green {
        background: -webkit-gradient(linear, left top, right top, from(#f29263), to(#ee5a6f));
    background: linear-gradient(to right, #ee5a6f, #f29263);
}

.user-profile {
    padding: 20px 0;
}

.card-block {
    padding: 1.25rem;
}

.m-b-25 {
    margin-bottom: 25px;
}

.img-radius {
    border-radius: 50%;
    width: 150px;
    height: 150px;
    object-fit: cover;
}

h6 {
    font-size: 14px;
}

.card .card-block p {
    line-height: 25px;
}

@media only screen and (min-width: 1400px){
p {
    font-size: 14px;
}
}

.card-block {
    padding: 1.25rem;
}

.b-b-default {
    border-bottom: 1px solid #e0e0e0;
}

.m-b-20 {
    margin-bottom: 20px;
}

.p-b-5 {
    padding-bottom: 5px !important;
}

.card .card-block p {
    line-height: 25px;
}

.m-b-10 {
    margin-bottom: 10px;
}

.text-muted {
    color: #919aa3 !important;
}

.b-b-default {
    border-bottom: 1px solid #e0e0e0;
}

.f-w-600 {
    font-weight: 600;
}

.m-b-20 {
    margin-bottom: 20px;
}

.m-t-40 {
    margin-top: 20px;
}

.p-b-5 {
    padding-bottom: 5px !important;
}

.m-b-10 {
    margin-bottom: 10px;
}

.m-t-40 {
    margin-top: 20px;
}

.user-card-full .social-link li {
    display: inline-block;
}

.user-card-full .social-link li a {
    font-size: 20px;
    margin: 0 10px 0 0;
    -webkit-transition: all 0.3s ease-in-out;
    transition: all 0.3s ease-in-out;
}
</style>

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
								<a href="<?php echo BASE_URL; ?>edit_profile.php" class="btn btn-primary btn-sm">Edit</a>
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
																<h6 class="text-muted f-w-400"><?php echo display_value($profile['phone'] ?? null); ?></h6>
															</div>
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">DOB</p>
																<h6 class="text-muted f-w-400"><?php echo display_value($profile['dob'] ?? null); ?></h6>
															</div>
														</div>
														<div class="row m-t-40">
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">Institution</p>
																<h6 class="text-muted f-w-400"><?php echo display_value($profile['institution_name'] ?? null); ?></h6>
															</div>
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">Address</p>
																<h6 class="text-muted f-w-400"><?php echo display_value($profile['address'] ?? null); ?></h6>
															</div>
														</div>

														<h6 class="m-b-20 m-t-40 p-b-5 b-b-default f-w-600">Metadata</h6>
														<div class="row">
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">Created</p>
																<h6 class="text-muted f-w-400"><?php echo display_value($profile['created_date'] ?? null); ?></h6>
															</div>
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">Modified</p>
																<h6 class="text-muted f-w-400"><?php echo display_value($profile['modified_date'] ?? null); ?></h6>
															</div>
														</div>
														<div class="row m-t-40">
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">Created By</p>
																<h6 class="text-muted f-w-400"><?php echo display_value($profile['created_by'] ?? null); ?></h6>
															</div>
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">Modified By</p>
																<h6 class="text-muted f-w-400"><?php echo display_value($profile['modified_by'] ?? null); ?></h6>
															</div>
														</div>
														<div class="row m-t-40">
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">Deleted By</p>
																<h6 class="text-muted f-w-400"><?php echo display_value($profile['deleted_by'] ?? null); ?></h6>
															</div>
															<div class="col-sm-6">
																<p class="m-b-10 f-w-600">Deleted Date</p>
																<h6 class="text-muted f-w-400"><?php echo display_value($profile['deleted_date'] ?? null); ?></h6>
															</div>
														</div>
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
