<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the user id input.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Fetch the user record for editing.
$user_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

// Handle update submission.
if (isset($_POST['save'])) {
    // Sanitize incoming form values.
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password_hash = $conn->real_escape_string(trim($_POST['password_hash']));

    // Update the user record.
    $sql = "UPDATE users SET email = '$email', password_hash = '$password_hash' WHERE user_id = $user_id";
    $updated = $conn->query($sql);

    // Redirect back to list on success.
    if ($updated) {
        header("Location: " . BASE_URL . "user_list.php");
        exit;
    } else {
        die("Update failed: " . $conn->error);
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
						<h3>Edit User</h3>
						<a href="<?php echo BASE_URL; ?>user_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">Email</label>
							<input type="email" class="form-control" name="email" value="<?= htmlspecialchars($row['email']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Password Hash</label>
							<input type="text" class="form-control" name="password_hash" value="<?= htmlspecialchars($row['password_hash']) ?>" />
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
<?php // Shared footer markup for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php // Shared JS resources for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
