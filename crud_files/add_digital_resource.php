<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (isset($_POST['save'])) {
    $title = $conn->real_escape_string(trim($_POST['title']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    $type = $conn->real_escape_string(trim($_POST['type']));

    $sql = "INSERT INTO digital_resources (title, description, type) VALUES ('$title', '$description', '$type')";
    $result = $conn->query($sql);

    if ($result) {
        header("Location: " . BASE_URL . "digital_resource_list.php");
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
						<h3>Add Digital Resource</h3>
						<a href="<?php echo BASE_URL; ?>digital_resource_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_digital_resource.php" method="post">
									<div class="card-body">
						<div class="mb-3">
							<label class="form-label">Title</label>
							<input type="text" class="form-control" name="title" />
						</div>
						<div class="mb-3">
							<label class="form-label">Description</label>
							<textarea class="form-control" name="description"></textarea>
						</div>
						<div class="mb-3">
							<label class="form-label">Type</label>
							<input type="text" class="form-control" name="type" />
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
