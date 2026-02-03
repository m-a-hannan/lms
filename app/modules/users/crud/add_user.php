<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Handle user creation submission.
if (isset($_POST['save'])) {
    // Sanitize incoming form values.
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password_hash = $conn->real_escape_string(trim($_POST['password_hash']));

    // Insert the new user record.
    $sql = "INSERT INTO users (email, password_hash) VALUES ('$email', '$password_hash')";
    $result = $conn->query($sql);

    // Redirect back to list on success.
    if ($result) {
        header("Location: " . BASE_URL . "user_list.php");
        exit;
    } else {
        die("Database error: " . $conn->error);
    }
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
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<div class="mb-4 d-flex justify-content-between">
						<h3>Add User</h3>
						<a href="<?php echo BASE_URL; ?>user_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_user.php" method="post">
									<div class="card-body">
						<div class="mb-3">
							<label class="form-label">Email</label>
							<input type="email" class="form-control" name="email" />
						</div>
						<div class="mb-3">
							<label class="form-label">Password Hash</label>
							<input type="text" class="form-control" name="password_hash" />
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
<?php // Shared footer markup for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php // Shared JS resources for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
