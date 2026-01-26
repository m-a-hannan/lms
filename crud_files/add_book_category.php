<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (isset($_POST['save'])) {
    $book_id = (int) $_POST['book_id'];
    $category_id = (int) $_POST['category_id'];

    $sql = "INSERT INTO book_categories (book_id, category_id) VALUES ($book_id, $category_id)";
    $result = $conn->query($sql);

    if ($result) {
        header("Location: " . BASE_URL . "book_category_list.php");
        exit;
    } else {
        die("Database error: " . $conn->error);
    }
}
?>
<?php include(ROOT_PATH . '/include/header_resources.php') ?>
<?php include(ROOT_PATH . '/include/header.php') ?>
<?php include(ROOT_PATH . '/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<div class="mb-4 d-flex justify-content-between">
						<h3>Add Book Category</h3>
						<a href="<?php echo BASE_URL; ?>book_category_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_book_category.php" method="post">
									<div class="card-body">
						<div class="mb-3">
							<label class="form-label">Book Id</label>
							<input type="number" class="form-control" name="book_id" />
						</div>
						<div class="mb-3">
							<label class="form-label">Category Id</label>
							<input type="number" class="form-control" name="category_id" />
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
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>
