<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Ensure session is active for profile edits.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Load the current user's profile if logged in.
$profile = null;
$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

// Redirect unauthenticated users to login.
if ($userId <= 0) {
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

// Fetch the current profile record for the user.
$result = $conn->query("SELECT * FROM user_profiles WHERE user_id = $userId LIMIT 1");
if ($result && $result->num_rows === 1) {
	$profile = $result->fetch_assoc();
	$profileId = (int) $profile['profile_id'];
}

// Collect validation errors for display.
$errors = [];

// Handle profile update form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Sanitize input fields for SQL usage.
	$firstName = $conn->real_escape_string(trim($_POST['first_name'] ?? ''));
	$lastName = $conn->real_escape_string(trim($_POST['last_name'] ?? ''));
	$dob = $conn->real_escape_string(trim($_POST['dob'] ?? ''));
	$address = $conn->real_escape_string(trim($_POST['address'] ?? ''));
	$phone = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
	$institution = $conn->real_escape_string(trim($_POST['institution_name'] ?? ''));
	$designation = $conn->real_escape_string(trim($_POST['designation'] ?? ''));

	// Prepare upload paths and current image reference.
	$uploadDir = ROOT_PATH . '/public/uploads/profile_picture/';
	$imagePath = $profile['profile_picture'] ?? '';

	// Handle profile image upload if provided.
	if (!empty($_FILES['profile_picture']['name'])) {
		$fileName = time() . '_' . basename($_FILES['profile_picture']['name']);
		$targetFile = $uploadDir . $fileName;
		$ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
		$allowed = ['jpg', 'jpeg', 'png', 'webp'];

		// Validate image extension.
		if (!in_array($ext, $allowed, true)) {
			$errors[] = 'Invalid image format.';
		}

		// Enforce image size limit.
		if ($_FILES['profile_picture']['size'] > 2 * 1024 * 1024) {
			$errors[] = 'Image must be under 2MB.';
		}

		// Ensure the upload directory exists.
		if (empty($errors)) {
			if (!is_dir($uploadDir)) {
				if (!mkdir($uploadDir, 0755, true)) {
					$errors[] = 'Upload directory not available.';
				}
			}
		}

		// Move the uploaded file into place.
		if (empty($errors) && !move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
			$errors[] = 'Image upload failed.';
		}

		// Replace the old profile image if the upload succeeded.
		if (empty($errors)) {
			if (!empty($imagePath)) {
				$oldPath = ROOT_PATH . '/' . ltrim($imagePath, '/');
				if (file_exists($oldPath)) {
					unlink($oldPath);
				}
			}
			$imagePath = 'uploads/profile_picture/' . $fileName;
		}
	}

	// Normalize nullable fields for SQL.
	$dobValue = $dob !== '' ? "'$dob'" : 'NULL';
	$addressValue = $address !== '' ? "'$address'" : 'NULL';
	$phoneValue = $phone !== '' ? "'$phone'" : 'NULL';
	$institutionValue = $institution !== '' ? "'$institution'" : 'NULL';
	$designationValue = $designation !== '' ? "'$designation'" : 'NULL';
	$imageValue = $imagePath !== '' ? "'" . $conn->real_escape_string($imagePath) . "'" : 'NULL';

	// Update or insert profile record when no errors exist.
	if (empty($errors)) {
		if ($profile) {
			$sql = "UPDATE user_profiles
				SET first_name = '$firstName',
					last_name = '$lastName',
					dob = $dobValue,
					address = $addressValue,
					phone = $phoneValue,
					institution_name = $institutionValue,
					designation = $designationValue,
					profile_picture = $imageValue
				WHERE profile_id = $profileId";
		} else {
			$sql = "INSERT INTO user_profiles
				(user_id, first_name, last_name, dob, address, phone, institution_name, designation, profile_picture)
				VALUES ($userId, '$firstName', '$lastName', $dobValue, $addressValue, $phoneValue, $institutionValue, $designationValue, $imageValue)";
		}

		// Execute the profile write and redirect on success.
		if ($conn->query($sql)) {
			$redirect = BASE_URL . 'view_profile.php?success=1';
			if ($profileId) {
				$redirect .= '&id=' . $profileId;
			}
			header('Location: ' . $redirect);
			exit;
		}
		$errors[] = 'Database error: ' . $conn->error;
	}
}

// Resolve preview image and body class.
$profileImage = $profile['profile_picture'] ?? '';
$profileImage = $profileImage !== '' ? htmlspecialchars($profileImage) : 'assets/img/avatar.png';
$bodyClass = 'page-edit-profile';
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
					<div class="mb-4 d-flex justify-content-between">
						<h3>Profile Customization</h3>
						<a href="<?php echo BASE_URL; ?>view_profile.php" class="btn btn-secondary btn-sm">Back</a>
					</div>

					<?php // Show validation errors when present. ?>
					<?php if (!empty($errors)): ?>
						<div class="alert alert-danger">
							<?php echo htmlspecialchars(implode(' ', $errors)); ?>
						</div>
					<?php endif; ?>

					<?php // Show success message after update. ?>
					<?php if (isset($_GET['success'])): ?>
						<div class="alert alert-success">Profile updated successfully.</div>
					<?php endif; ?>

					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form method="post" enctype="multipart/form-data">
									<div class="card-body">
										<div class="mb-3">
											<label class="form-label">First Name</label>
											<input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>">
										</div>
										<div class="mb-3">
											<label class="form-label">Last Name</label>
											<input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>">
										</div>
										<div class="mb-3">
											<label class="form-label">Date of Birth</label>
											<input type="date" class="form-control" name="dob" value="<?php echo htmlspecialchars($profile['dob'] ?? ''); ?>">
										</div>
										<div class="mb-3">
											<label class="form-label">Phone</label>
											<input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
										</div>
										<div class="mb-3">
											<label class="form-label">Institution</label>
											<input type="text" class="form-control" name="institution_name" value="<?php echo htmlspecialchars($profile['institution_name'] ?? ''); ?>">
										</div>
										<div class="mb-3">
											<label class="form-label">Designation</label>
											<input type="text" class="form-control" name="designation" value="<?php echo htmlspecialchars($profile['designation'] ?? ''); ?>">
										</div>
										<div class="mb-3">
											<label class="form-label">Address</label>
											<textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
										</div>
										<div class="mb-3">
											<label class="form-label">Profile Picture</label>
											<input type="file" id="profileInput" accept="image/*" class="form-control" name="profile_picture">
										</div>
									</div>
									<div class="card-footer">
										<button type="submit" class="btn btn-primary">Save Profile</button>
									</div>
								</form>
							</div>
						</div>
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<div class="preview-box">
									<h6 class="mb-3">Preview</h6>
									<div class="preview-area">
										<img id="profilePreview" src="<?php echo $profileImage; ?>" class="img-fluid" alt="Profile preview">
									</div>
								</div>
							</div>
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
<!-- Page-specific behavior for profile preview -->
<script src="<?php echo BASE_URL; ?>assets/js/pages/edit_profile.js"></script>
<?php // Shared JS resources for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
