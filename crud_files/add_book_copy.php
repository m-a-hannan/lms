<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (isset($_POST['save'])) {
    $edition_id = (int) $_POST['edition_id'];
    $barcode = $conn->real_escape_string(trim($_POST['barcode']));
    $status = $conn->real_escape_string(trim($_POST['status']));
    $location = $conn->real_escape_string(trim($_POST['location']));

    $sql = "INSERT INTO book_copies (edition_id, barcode, status, location) VALUES ($edition_id, '$barcode', '$status', '$location')";
    $result = $conn->query($sql);

    if ($result) {
        header("Location: " . BASE_URL . "book_copy_list.php");
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
						<h3>Add Book Copy</h3>
						<a href="<?php echo BASE_URL; ?>book_copy_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_book_copy.php" method="post">
									<div class="card-body">
						<div class="mb-3">
							<label class="form-label">Edition Id</label>
							<input type="number" class="form-control" name="edition_id" />
						</div>
						<div class="mb-3">
							<label class="form-label">Barcode</label>
							<input type="text" class="form-control" name="barcode" />
						</div>
						<div class="mb-3">
							<label class="form-label">Status</label>
							<input type="text" class="form-control" name="status" />
						</div>
						<div class="mb-3">
							<label class="form-label">Location</label>
							<input type="text" class="form-control" name="location" />
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
