<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the incoming id to prevent invalid access.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Fetch the current edition record for editing.
$edition_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM book_editions WHERE edition_id = $edition_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

// Handle form submission and persist changes.
if (isset($_POST['save'])) {
    $book_id = (int) $_POST['book_id'];
    $edition_number = (int) $_POST['edition_number'];
    $publication_year = (int) $_POST['publication_year'];
    $pages = (int) $_POST['pages'];

    // Update the edition record with the submitted values.
    $sql = "UPDATE book_editions SET book_id = $book_id, edition_number = $edition_number, publication_year = $publication_year, pages = $pages WHERE edition_id = $edition_id";
    $updated = $conn->query($sql);

    if ($updated) {
        // Redirect back to the list after a successful update.
        header("Location: " . BASE_URL . "book_edition_list.php");
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
						<h3>Edit Book Edition</h3>
						<a href="<?php echo BASE_URL; ?>book_edition_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<!-- Edit form card. -->
					<div class="card shadow-sm">
						<div class="card-body">
							<!-- Submission form for updating the edition. -->
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
										<!-- Book reference input. -->
										<div class="mb-3">
											<label class="form-label">Book Id</label>
											<input type="number" class="form-control" name="book_id" value="<?= htmlspecialchars($row['book_id']) ?>" />
										</div>
										<!-- Edition number input. -->
										<div class="mb-3">
											<label class="form-label">Edition Number</label>
											<input type="number" class="form-control" name="edition_number" value="<?= htmlspecialchars($row['edition_number']) ?>" />
										</div>
										<!-- Publication year input. -->
										<div class="mb-3">
											<label class="form-label">Publication Year</label>
											<input type="number" class="form-control" name="publication_year" value="<?= htmlspecialchars($row['publication_year']) ?>" />
										</div>
										<!-- Page count input. -->
										<div class="mb-3">
											<label class="form-label">Pages</label>
											<input type="number" class="form-control" name="pages" value="<?= htmlspecialchars($row['pages']) ?>" />
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
