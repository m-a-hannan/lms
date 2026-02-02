<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (isset($_POST['save'])) {
    $date = $conn->real_escape_string(trim($_POST['date']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    $created_by = (int) $_POST['created_by'];
    $created_date = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['created_date'])));
    $modified_by = (int) $_POST['modified_by'];
    $modified_date = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['modified_date'])));
    $deleted_by = (int) $_POST['deleted_by'];
    $deleted_date = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['deleted_date'])));

    $sql = "INSERT INTO holidays (date, description, created_by, created_date, modified_by, modified_date, deleted_by, deleted_date) VALUES ('$date', '$description', $created_by, '$created_date', $modified_by, '$modified_date', $deleted_by, '$deleted_date')";
    $result = $conn->query($sql);

    if ($result) {
        header("Location: " . BASE_URL . "holiday_list.php");
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
						<h3>Add Holiday</h3>
						<a href="<?php echo BASE_URL; ?>holiday_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_holiday.php" method="post">
									<div class="card-body">
							<div class="mb-3">
								<label class="form-label">Date</label>
								<input type="date" class="form-control" name="date" />
							</div>
							<div class="mb-3">
								<label class="form-label">Description</label>
								<input type="text" class="form-control" name="description" />
							</div>
							<div class="mb-3">
								<label class="form-label">Created By</label>
								<input type="number" class="form-control" name="created_by" />
							</div>
							<div class="mb-3">
								<label class="form-label">Created Date</label>
								<input type="datetime-local" class="form-control" name="created_date" />
							</div>
							<div class="mb-3">
								<label class="form-label">Modified By</label>
								<input type="number" class="form-control" name="modified_by" />
							</div>
							<div class="mb-3">
								<label class="form-label">Modified Date</label>
								<input type="datetime-local" class="form-control" name="modified_date" />
							</div>
							<div class="mb-3">
								<label class="form-label">Deleted By</label>
								<input type="number" class="form-control" name="deleted_by" />
							</div>
							<div class="mb-3">
								<label class="form-label">Deleted Date</label>
								<input type="datetime-local" class="form-control" name="deleted_date" />
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