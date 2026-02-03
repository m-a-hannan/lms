<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . "/app/includes/connection.php";

/* ---------------------------
   Validate & fetch role
---------------------------- */
// Validate the role id input.
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid role ID.");
}

$role_id = (int) $_GET["id"];

// Fetch the role record for editing.
$result = $conn->query("SELECT * FROM roles WHERE role_id = $role_id");

if ($result->num_rows !== 1) {
    die("role not found.");
}

$role = $result->fetch_assoc();

/* ---------------------------
   Handle update submission
---------------------------- */
// Handle update submission.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Read the updated role name from the form.
    $role_name = $conn->real_escape_string(trim($_POST["role_name"]));
    if ($role_name === '') {
        die("role name is required.");
    }

    // Update the role record.
    $sql = "UPDATE roles SET role_name = '$role_name' WHERE role_id = $role_id";
    if ($conn->query($sql)) {
        // Redirect back to list on success.
        header("Location: " . BASE_URL . "role_list.php");
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
	<!--begin::App Content-->
	<div class="app-content">
		<!--begin::Container-->
		<div class="container-fluid">
			<!--begin::Row-->
			<div class="row">

				<div class="container py-5">
					<!-- Add contents Below-->
					<div class="mb-4 d-flex justify-content-between">
						<h3>Edit role</h3>
						<a href="<?php echo BASE_URL; ?>role_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>

					<div class="card shadow-sm">
						<div class="card-body">

							<form method="post" enctype="multipart/form-data">
								<div class="row g-4">

									<div class="col-md-6">
										<div class="mb-3">
											<label class="form-label">Role Name</label>
											<input type="text" name="role_name" class="form-control"
												value="<?= htmlspecialchars($role["role_name"]) ?>" required>
										</div>

										<button type="submit" class="btn btn-primary">Update role</button>
									</div>

									<div class="col-md-6">
										<!-- Add content if needed -->
									</div>
								</div>
							</form>
						</div>
					</div>
					<!-- row end -->
				</div>
			</div>
</main>
<!--end::App Main-->
<?php // Shared footer markup for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php // Shared JS resources for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
