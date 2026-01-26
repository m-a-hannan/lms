<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$profile = null;
$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

if ($userId <= 0) {
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

$result = $conn->query("SELECT * FROM user_profiles WHERE user_id = $userId LIMIT 1");
if ($result && $result->num_rows === 1) {
	$profile = $result->fetch_assoc();
	$profileId = (int) $profile['profile_id'];
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$firstName = $conn->real_escape_string(trim($_POST['first_name'] ?? ''));
	$lastName = $conn->real_escape_string(trim($_POST['last_name'] ?? ''));
	$dob = $conn->real_escape_string(trim($_POST['dob'] ?? ''));
	$address = $conn->real_escape_string(trim($_POST['address'] ?? ''));
	$phone = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
	$institution = $conn->real_escape_string(trim($_POST['institution_name'] ?? ''));
	$designation = $conn->real_escape_string(trim($_POST['designation'] ?? ''));

	$uploadDir = ROOT_PATH . '/uploads/profile_picture/';
	$imagePath = $profile['profile_picture'] ?? '';

	if (!empty($_FILES['profile_picture']['name'])) {
		$fileName = time() . '_' . basename($_FILES['profile_picture']['name']);
		$targetFile = $uploadDir . $fileName;
		$ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
		$allowed = ['jpg', 'jpeg', 'png', 'webp'];

		if (!in_array($ext, $allowed, true)) {
			$errors[] = 'Invalid image format.';
		}

		if ($_FILES['profile_picture']['size'] > 2 * 1024 * 1024) {
			$errors[] = 'Image must be under 2MB.';
		}

		if (empty($errors)) {
			if (!is_dir($uploadDir)) {
				if (!mkdir($uploadDir, 0755, true)) {
					$errors[] = 'Upload directory not available.';
				}
			}
		}

		if (empty($errors) && !move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
			$errors[] = 'Image upload failed.';
		}

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

	$dobValue = $dob !== '' ? "'$dob'" : 'NULL';
	$addressValue = $address !== '' ? "'$address'" : 'NULL';
	$phoneValue = $phone !== '' ? "'$phone'" : 'NULL';
	$institutionValue = $institution !== '' ? "'$institution'" : 'NULL';
	$designationValue = $designation !== '' ? "'$designation'" : 'NULL';
	$imageValue = $imagePath !== '' ? "'" . $conn->real_escape_string($imagePath) . "'" : 'NULL';

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

$profileImage = $profile['profile_picture'] ?? '';
$profileImage = $profileImage !== '' ? htmlspecialchars($profileImage) : 'assets/img/avatar.png';
?>

<?php include(ROOT_PATH . '/include/header_resources.php') ?>
<style>
input[type="date"]::-webkit-calendar-picker-indicator {
  filter: invert(0);
  opacity: 0.9;
}
</style>
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
					<div class="mb-4 d-flex justify-content-between">
						<h3>Profile Customization</h3>
						<a href="<?php echo BASE_URL; ?>view_profile.php" class="btn btn-secondary btn-sm">Back</a>
					</div>

					<?php if (!empty($errors)): ?>
						<div class="alert alert-danger">
							<?php echo htmlspecialchars(implode(' ', $errors)); ?>
						</div>
					<?php endif; ?>

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

<?php include(ROOT_PATH . '/include/footer.php') ?>
<script>
const profileInput = document.getElementById('profileInput');
const profilePreview = document.getElementById('profilePreview');

if (profileInput && profilePreview) {
	profileInput.addEventListener('change', () => {
		const file = profileInput.files && profileInput.files[0];
		if (!file) {
			return;
		}
		const reader = new FileReader();
		reader.onload = (event) => {
			profilePreview.src = event.target.result;
		};
		reader.readAsDataURL(file);
	});
}
</script>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>
