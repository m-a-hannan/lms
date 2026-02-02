<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$edition_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM book_editions WHERE edition_id = $edition_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

if (isset($_POST['save'])) {
    $book_id = (int) $_POST['book_id'];
    $edition_number = (int) $_POST['edition_number'];
    $publication_year = (int) $_POST['publication_year'];
    $pages = (int) $_POST['pages'];

    $sql = "UPDATE book_editions SET book_id = $book_id, edition_number = $edition_number, publication_year = $publication_year, pages = $pages WHERE edition_id = $edition_id";
    $updated = $conn->query($sql);

    if ($updated) {
        header("Location: " . BASE_URL . "book_edition_list.php");
        exit;
    } else {
        die("Update failed: " . $conn->error);
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
						<h3>Edit Book Edition</h3>
						<a href="<?php echo BASE_URL; ?>book_edition_list.php" class="btn btn-secondary btn-sm">Back</a>
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
							<label class="form-label">Edition Number</label>
							<input type="number" class="form-control" name="edition_number" value="<?= htmlspecialchars($row['edition_number']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Publication Year</label>
							<input type="number" class="form-control" name="publication_year" value="<?= htmlspecialchars($row['publication_year']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Pages</label>
							<input type="number" class="form-control" name="pages" value="<?= htmlspecialchars($row['pages']) ?>" />
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
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>