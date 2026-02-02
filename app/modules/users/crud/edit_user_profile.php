<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$profile_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM user_profiles WHERE profile_id = $profile_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

if (isset($_POST['save'])) {
    $user_id = (int) $_POST['user_id'];
    $first_name = $conn->real_escape_string(trim($_POST['first_name']));
    $last_name = $conn->real_escape_string(trim($_POST['last_name']));
    $dob = $conn->real_escape_string(trim($_POST['dob']));
    $address = $conn->real_escape_string(trim($_POST['address']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $photo = $conn->real_escape_string(trim($_POST['photo']));
    $institution_name = $conn->real_escape_string(trim($_POST['institution_name']));
    $designation = $conn->real_escape_string(trim($_POST['designation']));

    $sql = "UPDATE user_profiles SET user_id = $user_id, first_name = '$first_name', last_name = '$last_name', dob = '$dob', address = '$address', phone = '$phone', photo = '$photo', institution_name = '$institution_name', designation = '$designation' WHERE profile_id = $profile_id";
    $updated = $conn->query($sql);

    if ($updated) {
        header("Location: " . BASE_URL . "user_profile_list.php");
        exit;
    } else {
        die("Update failed: " . $conn->error);
    }
}
?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php include(ROOT_PATH . '/app/views/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<div class="mb-4 d-flex justify-content-between">
						<h3>Edit User Profile</h3>
						<a href="<?php echo BASE_URL; ?>user_profile_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">User Id</label>
							<input type="number" class="form-control" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">First Name</label>
							<input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($row['first_name']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Last Name</label>
							<input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($row['last_name']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Dob</label>
							<input type="date" class="form-control" name="dob" value="<?= htmlspecialchars($row['dob']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Address</label>
							<textarea class="form-control" name="address"><?= htmlspecialchars($row['address']) ?></textarea>
						</div>
						<div class="mb-3">
							<label class="form-label">Phone</label>
							<input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Photo</label>
							<input type="text" class="form-control" name="photo" value="<?= htmlspecialchars($row['photo']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Institution Name</label>
							<input type="text" class="form-control" name="institution_name" value="<?= htmlspecialchars($row['institution_name']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Designation</label>
							<input type="text" class="form-control" name="designation" value="<?= htmlspecialchars($row['designation']) ?>" />
						</div>
										<button type="submit" name="save" class="btn btn-primary">Update</button>
									</div>
								</div>
							</form>
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