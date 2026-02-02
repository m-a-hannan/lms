<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (isset($_POST['save'])) {
    $resource_id = (int) $_POST['resource_id'];
    $file_path = $conn->real_escape_string(trim($_POST['file_path']));
    $file_size = (int) $_POST['file_size'];
    $download_count = (int) $_POST['download_count'];

    $sql = "INSERT INTO digital_files (resource_id, file_path, file_size, download_count) VALUES ($resource_id, '$file_path', $file_size, $download_count)";
    $result = $conn->query($sql);

    if ($result) {
        header("Location: " . BASE_URL . "digital_file_list.php");
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
						<h3>Add Digital File</h3>
						<a href="<?php echo BASE_URL; ?>digital_file_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_digital_file.php" method="post">
									<div class="card-body">
						<div class="mb-3">
							<label class="form-label">Resource Id</label>
							<input type="number" class="form-control" name="resource_id" />
						</div>
						<div class="mb-3">
							<label class="form-label">File Path</label>
							<input type="text" class="form-control" name="file_path" />
						</div>
						<div class="mb-3">
							<label class="form-label">File Size</label>
							<input type="number" class="form-control" name="file_size" />
						</div>
						<div class="mb-3">
							<label class="form-label">Download Count</label>
							<input type="number" class="form-control" name="download_count" />
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