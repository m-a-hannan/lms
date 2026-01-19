<?php
require_once dirname(__DIR__) . "/include/config.php";
require_once ROOT_PATH . "/include/connection.php";

/* ---------------------------
   Validate & fetch category
---------------------------- */
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid category ID.");
}

$category_id = (int) $_GET["id"];

$stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Category not found.");
}

$category = $result->fetch_assoc();

/* ---------------------------
   Handle update submission
---------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Read the updated category name from the form.
    $category_name = trim($_POST["category_name"]);
    if ($category_name === '') {
        die("Category name is required.");
    }

    // Update category
    $update = $conn->prepare(
        "UPDATE categories 
         SET category_name = ?
         WHERE category_id = ?"
    );

    $update->bind_param(
        "si",
        $category_name,
        $category_id
    );

    if ($update->execute()) {
        header("Location: " . BASE_URL . "category_list.php");
        exit;
    } else {
        die("Update failed: " . $update->error);
    }
}
?>

<?php include(ROOT_PATH . '/include/header_resources.php') ?>

<?php include(ROOT_PATH . '/include/header.php') ?>
<?php include(ROOT_PATH . '/sidebar.php') ?>
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
						<h3>Edit Category</h3>
						<a href="<?php echo BASE_URL; ?>category_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>

					<div class="card shadow-sm">
						<div class="card-body">

							<form method="post" enctype="multipart/form-data">
								<div class="row g-4">

									<div class="col-md-6">
										<div class="mb-3">
											<label class="form-label">Category Name</label>
											<input type="text" name="category_name" class="form-control"
												value="<?= htmlspecialchars($category["category_name"]) ?>" required>
										</div>


										<button type="submit" class="btn btn-primary">Update category</button>
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
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>