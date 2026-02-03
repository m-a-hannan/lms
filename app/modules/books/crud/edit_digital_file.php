<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the incoming id to prevent invalid access.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Fetch the current digital file record for editing.
$file_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM digital_files WHERE file_id = $file_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

// Handle form submission and persist changes.
if (isset($_POST['save'])) {
    $resource_id = (int) $_POST['resource_id'];
    $file_path = $conn->real_escape_string(trim($_POST['file_path']));
    $file_size = (int) $_POST['file_size'];
    $download_count = (int) $_POST['download_count'];

    // Update the digital file record with the submitted values.
    $sql = "UPDATE digital_files SET resource_id = $resource_id, file_path = '$file_path', file_size = $file_size, download_count = $download_count WHERE file_id = $file_id";
    $updated = $conn->query($sql);

    if ($updated) {
        // Redirect back to the list after a successful update.
        header("Location: " . BASE_URL . "digital_file_list.php");
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
						<h3>Edit Digital File</h3>
						<a href="<?php echo BASE_URL; ?>digital_file_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<!-- Edit form card. -->
					<div class="card shadow-sm">
						<div class="card-body">
							<!-- Submission form for updating the file metadata. -->
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
										<!-- Resource reference input. -->
										<div class="mb-3">
											<label class="form-label">Resource Id</label>
											<input type="number" class="form-control" name="resource_id" value="<?= htmlspecialchars($row['resource_id']) ?>" />
										</div>
										<!-- File path input. -->
										<div class="mb-3">
											<label class="form-label">File Path</label>
											<input type="text" class="form-control" name="file_path" value="<?= htmlspecialchars($row['file_path']) ?>" />
										</div>
										<!-- File size input. -->
										<div class="mb-3">
											<label class="form-label">File Size</label>
											<input type="number" class="form-control" name="file_size" value="<?= htmlspecialchars($row['file_size']) ?>" />
										</div>
										<!-- Download count input. -->
										<div class="mb-3">
											<label class="form-label">Download Count</label>
											<input type="number" class="form-control" name="download_count" value="<?= htmlspecialchars($row['download_count']) ?>" />
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
