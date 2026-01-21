<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (isset($_POST['save'])) {
    $user_id = (int) $_POST['user_id'];
    $action = $conn->real_escape_string(trim($_POST['action']));
    $target_table = $conn->real_escape_string(trim($_POST['target_table']));
    $target_id = (int) $_POST['target_id'];
    $time_stamp = $conn->real_escape_string(trim($_POST['time_stamp']));
    $created_by = (int) $_POST['created_by'];
    $created_date = $conn->real_escape_string(trim($_POST['created_date']));
    $modified_by = (int) $_POST['modified_by'];
    $modified_date = $conn->real_escape_string(trim($_POST['modified_date']));
    $deleted_by = (int) $_POST['deleted_by'];
    $deleted_date = $conn->real_escape_string(trim($_POST['deleted_date']));

    $sql = "INSERT INTO audit_logs (user_id, action, target_table, target_id, time_stamp, created_by, created_date, modified_by, modified_date, deleted_by, deleted_date) VALUES ($user_id, '$action', '$target_table', $target_id, '$time_stamp', $created_by, '$created_date', $modified_by, '$modified_date', $deleted_by, '$deleted_date')";
    $result = $conn->query($sql);

    if ($result) {
        header("Location: " . BASE_URL . "audit_log_list.php");
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
						<h3>Add Audit Log</h3>
						<a href="<?php echo BASE_URL; ?>audit_log_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_audit_log.php" method="post">
									<div class="card-body">
							<div class="mb-3">
								<label class="form-label">User Id</label>
								<input type="number" class="form-control" name="user_id" />
							</div>
							<div class="mb-3">
								<label class="form-label">Action</label>
								<input type="text" class="form-control" name="action" />
							</div>
							<div class="mb-3">
								<label class="form-label">Target Table</label>
								<input type="text" class="form-control" name="target_table" />
							</div>
							<div class="mb-3">
								<label class="form-label">Target Id</label>
								<input type="number" class="form-control" name="target_id" />
							</div>
							<div class="mb-3">
								<label class="form-label">Time Stamp</label>
								<input type="date" class="form-control" name="time_stamp" />
							</div>
							<div class="mb-3">
								<label class="form-label">Created By</label>
								<input type="number" class="form-control" name="created_by" />
							</div>
							<div class="mb-3">
								<label class="form-label">Created Date</label>
								<input type="date" class="form-control" name="created_date" />
							</div>
							<div class="mb-3">
								<label class="form-label">Modified By</label>
								<input type="number" class="form-control" name="modified_by" />
							</div>
							<div class="mb-3">
								<label class="form-label">Modified Date</label>
								<input type="date" class="form-control" name="modified_date" />
							</div>
							<div class="mb-3">
								<label class="form-label">Deleted By</label>
								<input type="number" class="form-control" name="deleted_by" />
							</div>
							<div class="mb-3">
								<label class="form-label">Deleted Date</label>
								<input type="date" class="form-control" name="deleted_date" />
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
