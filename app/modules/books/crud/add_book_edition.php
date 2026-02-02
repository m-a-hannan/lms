<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (isset($_POST['save'])) {
    $book_id = (int) $_POST['book_id'];
    $edition_number = (int) $_POST['edition_number'];
    $publication_year = (int) $_POST['publication_year'];
    $pages = (int) $_POST['pages'];

    $sql = "INSERT INTO book_editions (book_id, edition_number, publication_year, pages) VALUES ($book_id, $edition_number, $publication_year, $pages)";
    $result = $conn->query($sql);

    if ($result) {
        header("Location: " . BASE_URL . "book_edition_list.php");
        exit;
    } else {
        die("Database error: " . $conn->error);
    }
}
?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php include(ROOT_PATH . '/app/views/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<div class="mb-4 d-flex justify-content-between">
						<h3>Add Book Edition</h3>
						<a href="<?php echo BASE_URL; ?>book_edition_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_book_edition.php" method="post">
									<div class="card-body">
						<div class="mb-3">
							<label class="form-label">Book Id</label>
							<input type="number" class="form-control" name="book_id" />
						</div>
						<div class="mb-3">
							<label class="form-label">Edition Number</label>
							<input type="number" class="form-control" name="edition_number" />
						</div>
						<div class="mb-3">
							<label class="form-label">Publication Year</label>
							<input type="number" class="form-control" name="publication_year" />
						</div>
						<div class="mb-3">
							<label class="form-label">Pages</label>
							<input type="number" class="form-control" name="pages" />
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
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>