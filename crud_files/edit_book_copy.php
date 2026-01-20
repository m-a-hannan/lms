<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$copy_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM book_copies WHERE copy_id = $copy_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

if (isset($_POST['save'])) {
    $edition_id = (int) $_POST['edition_id'];
    $barcode = $conn->real_escape_string(trim($_POST['barcode']));
    $status = $conn->real_escape_string(trim($_POST['status']));
    $location = $conn->real_escape_string(trim($_POST['location']));

    $sql = "UPDATE book_copies SET edition_id = $edition_id, barcode = '$barcode', status = '$status', location = '$location' WHERE copy_id = $copy_id";
    $updated = $conn->query($sql);

    if ($updated) {
        header("Location: " . BASE_URL . "book_copy_list.php");
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
						<h3>Edit Book Copy</h3>
						<a href="<?php echo BASE_URL; ?>book_copy_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">Edition Id</label>
							<input type="number" class="form-control" name="edition_id" value="<?= htmlspecialchars($row['edition_id']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Barcode</label>
							<input type="text" class="form-control" name="barcode" value="<?= htmlspecialchars($row['barcode']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Status</label>
							<input type="text" class="form-control" name="status" value="<?= htmlspecialchars($row['status']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Location</label>
							<input type="text" class="form-control" name="location" value="<?= htmlspecialchars($row['location']) ?>" />
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
