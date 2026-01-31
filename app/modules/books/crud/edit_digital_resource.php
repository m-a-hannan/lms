<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$resource_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM digital_resources WHERE resource_id = $resource_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

if (isset($_POST['save'])) {
    $title = $conn->real_escape_string(trim($_POST['title']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    $type = $conn->real_escape_string(trim($_POST['type']));

    $sql = "UPDATE digital_resources SET title = '$title', description = '$description', type = '$type' WHERE resource_id = $resource_id";
    $updated = $conn->query($sql);

    if ($updated) {
        header("Location: " . BASE_URL . "digital_resource_list.php");
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
						<h3>Edit Digital Resource</h3>
						<a href="<?php echo BASE_URL; ?>digital_resource_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">Title</label>
							<input type="text" class="form-control" name="title" value="<?= htmlspecialchars($row['title']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Description</label>
							<textarea class="form-control" name="description"><?= htmlspecialchars($row['description']) ?></textarea>
						</div>
						<div class="mb-3">
							<label class="form-label">Type</label>
							<input type="text" class="form-control" name="type" value="<?= htmlspecialchars($row['type']) ?>" />
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