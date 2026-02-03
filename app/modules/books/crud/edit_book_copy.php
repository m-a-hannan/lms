<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the incoming id to prevent invalid access.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Fetch the current copy record for editing.
$copy_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM book_copies WHERE copy_id = $copy_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

// Handle form submission and persist changes.
if (isset($_POST['save'])) {
    $edition_id = (int) $_POST['edition_id'];
    $barcode = $conn->real_escape_string(trim($_POST['barcode']));
    $status = $conn->real_escape_string(trim($_POST['status']));
    $location = $conn->real_escape_string(trim($_POST['location']));

    // Update the copy record with the submitted values.
    $sql = "UPDATE book_copies SET edition_id = $edition_id, barcode = '$barcode', status = '$status', location = '$location' WHERE copy_id = $copy_id";
    $updated = $conn->query($sql);

    if ($updated) {
        // Redirect back to the list after a successful update.
        header("Location: " . BASE_URL . "book_copy_list.php");
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
						<h3>Edit Book Copy</h3>
						<a href="<?php echo BASE_URL; ?>book_copy_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<!-- Edit form card. -->
					<div class="card shadow-sm">
						<div class="card-body">
							<!-- Submission form for updating the copy. -->
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
										<!-- Edition reference input. -->
										<div class="mb-3">
											<label class="form-label">Edition Id</label>
											<input type="number" class="form-control" name="edition_id" value="<?= htmlspecialchars($row['edition_id']) ?>" />
										</div>
										<!-- Barcode input. -->
										<div class="mb-3">
											<label class="form-label">Barcode</label>
											<input type="text" class="form-control" name="barcode" value="<?= htmlspecialchars($row['barcode']) ?>" />
										</div>
										<!-- Status input. -->
										<div class="mb-3">
											<label class="form-label">Status</label>
											<input type="text" class="form-control" name="status" value="<?= htmlspecialchars($row['status']) ?>" />
										</div>
										<!-- Location input. -->
										<div class="mb-3">
											<label class="form-label">Location</label>
											<input type="text" class="form-control" name="location" value="<?= htmlspecialchars($row['location']) ?>" />
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
