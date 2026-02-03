<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . "/app/includes/connection.php";

// Handle category creation submission.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Basic input handling
    $category_name = $conn->real_escape_string(trim($_POST["category_name"]));

    // Insert the new category record.
    $sql = "INSERT INTO categories (category_name) VALUES ('$category_name')";
    if ($conn->query($sql)) {
        header("Location: " . BASE_URL . "category_list.php?success=1");
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
	<!--begin::App Content-->
	<div class="app-content">
		<!--begin::Container-->
		<div class="container-fluid">
			<!--begin::Row-->
			<div class="row">
				<div class="container py-5">
				<!-- Add contents Below-->
					<div class="mb-4 d-flex justify-content-between">
						<h3>Add Category</h3>
						<a href="<?php echo BASE_URL; ?>category_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<!-- FORM ELELEMNTS -->
					<div class="row mt">
						<div class="col-md-6">
							<!--begin::Form-->
							<div class="card card-primary card-outline mb-4">
								<!-- must add form action -->
								<form action="<?php echo BASE_URL; ?>crud_files/add_category.php" method="post">
									<!--begin::Body-->
									<div class="card-body">
										<div class="mb-3">
											<label class="form-label">Category Name</label>
											<input type="text" class="form-control" name="category_name" aria-describedby="#" />
										</div>
									<!--end::Body-->
									<!--begin::Footer-->
									<div class="card-footer">
										<button type="submit" class="btn btn-primary">Submit</button>
									</div>
									<!--end::Footer-->
								</form>
								<!--end::Form-->
							</div>
							<!--end::Form-->
						</div>
						<!-- col-md-6-->
						<div class="col-md-6">
							<!-- Add content here if needed -->
						</div>
					</div>
				</div>
				<!-- row end -->
			</div>
		</div>
	</div>
</main>
<!--end::App Main-->
<?php // Shared footer markup for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php // Shared JS resources for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>

<?php // Show success alert after adding a category. ?>
<?php if (isset($_GET["success"])): ?>
<div class="alert alert-success">Category added successfully.</div>
<?php endif; ?>
