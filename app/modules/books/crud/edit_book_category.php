<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the incoming id to prevent invalid access.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Fetch the current book-category record for editing.
$book_cat_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM book_categories WHERE book_cat_id = $book_cat_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

// Handle form submission and persist changes.
if (isset($_POST['save'])) {
    $book_id = (int) $_POST['book_id'];
    $category_id = (int) $_POST['category_id'];

    // Update the junction record with the new values.
    $sql = "UPDATE book_categories SET book_id = $book_id, category_id = $category_id WHERE book_cat_id = $book_cat_id";
    $updated = $conn->query($sql);

    if ($updated) {
        // Redirect back to the list after a successful update.
        header("Location: " . BASE_URL . "book_category_list.php");
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
						<h3>Edit Book Category</h3>
						<a href="<?php echo BASE_URL; ?>book_category_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<!-- Edit form card. -->
					<div class="card shadow-sm">
						<div class="card-body">
							<!-- Submission form for updating the mapping. -->
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
										<!-- Book selection input. -->
										<div class="mb-3">
											<label class="form-label">Book Id</label>
											<input type="number" class="form-control" name="book_id" value="<?= htmlspecialchars($row['book_id']) ?>" />
										</div>
										<!-- Category selection input. -->
										<div class="mb-3">
											<label class="form-label">Category Id</label>
											<input type="number" class="form-control" name="category_id" value="<?= htmlspecialchars($row['category_id']) ?>" />
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
