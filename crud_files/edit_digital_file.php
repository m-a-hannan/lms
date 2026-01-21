<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$file_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM digital_files WHERE file_id = $file_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

if (isset($_POST['save'])) {
    $resource_id = (int) $_POST['resource_id'];
    $file_path = $conn->real_escape_string(trim($_POST['file_path']));
    $file_size = (int) $_POST['file_size'];
    $download_count = (int) $_POST['download_count'];

    $sql = "UPDATE digital_files SET resource_id = $resource_id, file_path = '$file_path', file_size = $file_size, download_count = $download_count WHERE file_id = $file_id";
    $updated = $conn->query($sql);

    if ($updated) {
        header("Location: " . BASE_URL . "digital_file_list.php");
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
						<h3>Edit Digital File</h3>
						<a href="<?php echo BASE_URL; ?>digital_file_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">Resource Id</label>
							<input type="number" class="form-control" name="resource_id" value="<?= htmlspecialchars($row['resource_id']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">File Path</label>
							<input type="text" class="form-control" name="file_path" value="<?= htmlspecialchars($row['file_path']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">File Size</label>
							<input type="number" class="form-control" name="file_size" value="<?= htmlspecialchars($row['file_size']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Download Count</label>
							<input type="number" class="form-control" name="download_count" value="<?= htmlspecialchars($row['download_count']) ?>" />
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
