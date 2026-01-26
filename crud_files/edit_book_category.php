<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$book_cat_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM book_categories WHERE book_cat_id = $book_cat_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

if (isset($_POST['save'])) {
    $book_id = (int) $_POST['book_id'];
    $category_id = (int) $_POST['category_id'];

    $sql = "UPDATE book_categories SET book_id = $book_id, category_id = $category_id WHERE book_cat_id = $book_cat_id";
    $updated = $conn->query($sql);

    if ($updated) {
        header("Location: " . BASE_URL . "book_category_list.php");
        exit;
    } else {
        die("Update failed: " . $conn->error);
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
						<h3>Edit Book Category</h3>
						<a href="<?php echo BASE_URL; ?>book_category_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">Book Id</label>
							<input type="number" class="form-control" name="book_id" value="<?= htmlspecialchars($row['book_id']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Category Id</label>
							<input type="number" class="form-control" name="category_id" value="<?= htmlspecialchars($row['category_id']) ?>" />
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
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>
