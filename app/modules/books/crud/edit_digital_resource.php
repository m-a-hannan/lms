<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the incoming id to prevent invalid access.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Fetch the current digital resource record for editing.
$resource_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM digital_resources WHERE resource_id = $resource_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

// Handle form submission and persist changes.
if (isset($_POST['save'])) {
    $title = $conn->real_escape_string(trim($_POST['title']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    $type = $conn->real_escape_string(trim($_POST['type']));

    // Update the digital resource record with the submitted values.
    $sql = "UPDATE digital_resources SET title = '$title', description = '$description', type = '$type' WHERE resource_id = $resource_id";
    $updated = $conn->query($sql);

    if ($updated) {
        // Redirect back to the list after a successful update.
        header("Location: " . BASE_URL . "digital_resource_list.php");
        exit;
    } else {
        die("Update failed: " . $conn->error);
    }
}
?>
<?php // Shared header resources and layout chrome. ?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php include(ROOT_PATH . '/app/views/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<!-- Page header with title and navigation. -->
					<div class="mb-4 d-flex justify-content-between">
						<h3>Edit Digital Resource</h3>
						<a href="<?php echo BASE_URL; ?>digital_resource_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<!-- Edit form card. -->
					<div class="card shadow-sm">
						<div class="card-body">
							<!-- Submission form for updating the resource. -->
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
										<!-- Title input. -->
										<div class="mb-3">
											<label class="form-label">Title</label>
											<input type="text" class="form-control" name="title" value="<?= htmlspecialchars($row['title']) ?>" />
										</div>
										<!-- Description input. -->
										<div class="mb-3">
											<label class="form-label">Description</label>
											<textarea class="form-control" name="description"><?= htmlspecialchars($row['description']) ?></textarea>
										</div>
										<!-- Type input. -->
										<div class="mb-3">
											<label class="form-label">Type</label>
											<input type="text" class="form-control" name="type" value="<?= htmlspecialchars($row['type']) ?>" />
										</div>
										<!-- Submit button. -->
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
<?php // Shared footer layout and scripts. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
