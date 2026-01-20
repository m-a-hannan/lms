<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

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

    $sql = "INSERT INTO user_profiles (user_id, first_name, last_name, dob, address, phone, photo, institution_name, designation) VALUES ($user_id, '$first_name', '$last_name', '$dob', '$address', '$phone', '$photo', '$institution_name', '$designation')";
    $result = $conn->query($sql);

    if ($result) {
        header("Location: " . BASE_URL . "user_profile_list.php");
        exit;
    } else {
        die("Database error: " . $conn->error);
    }
}
?>
<?php include(ROOT_PATH . '/include/header_resources.php') ?>
<?php include(ROOT_PATH . '/include/header.php') ?>
<?php include(ROOT_PATH . '/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<div class="mb-4 d-flex justify-content-between">
						<h3>Add User Profile</h3>
						<a href="<?php echo BASE_URL; ?>user_profile_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_user_profile.php" method="post">
									<div class="card-body">
						<div class="mb-3">
							<label class="form-label">User Id</label>
							<input type="number" class="form-control" name="user_id" />
						</div>
						<div class="mb-3">
							<label class="form-label">First Name</label>
							<input type="text" class="form-control" name="first_name" />
						</div>
						<div class="mb-3">
							<label class="form-label">Last Name</label>
							<input type="text" class="form-control" name="last_name" />
						</div>
						<div class="mb-3">
							<label class="form-label">Dob</label>
							<input type="date" class="form-control" name="dob" />
						</div>
						<div class="mb-3">
							<label class="form-label">Address</label>
							<textarea class="form-control" name="address"></textarea>
						</div>
						<div class="mb-3">
							<label class="form-label">Phone</label>
							<input type="text" class="form-control" name="phone" />
						</div>
						<div class="mb-3">
							<label class="form-label">Photo</label>
							<input type="text" class="form-control" name="photo" />
						</div>
						<div class="mb-3">
							<label class="form-label">Institution Name</label>
							<input type="text" class="form-control" name="institution_name" />
						</div>
						<div class="mb-3">
							<label class="form-label">Designation</label>
							<input type="text" class="form-control" name="designation" />
						</div>
									</div>
									<div class="card-footer">
										<button type="submit" name="save" class="btn btn-primary">Submit</button>
									</div>
								</form>
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
