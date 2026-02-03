<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Handle form submission for creating an audit log entry.
if (isset($_POST['save'])) {
    // Read and sanitize incoming form values.
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

    // Insert the audit log record into the database.
    $sql = "INSERT INTO audit_logs (user_id, action, target_table, target_id, time_stamp, created_by, created_date, modified_by, modified_date, deleted_by, deleted_date) VALUES ($user_id, '$action', '$target_table', $target_id, '$time_stamp', $created_by, '$created_date', $modified_by, '$modified_date', $deleted_by, '$deleted_date')";
    $result = $conn->query($sql);

    if ($result) {
        // Redirect back to the list after saving.
        header("Location: " . BASE_URL . "audit_log_list.php");
        exit;
    } else {
        die("Database error: " . $conn->error);
    }
}
?>
<?php // Shared header resources and layout chrome. ?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php include(ROOT_PATH . '/app/views/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<!-- Page header with title and navigation. -->
					<div class="mb-4 d-flex justify-content-between">
						<h3>Add Audit Log</h3>
						<a href="<?php echo BASE_URL; ?>audit_log_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<!-- Audit log creation form card. -->
							<div class="card card-primary card-outline mb-4">
								<!-- Submission form for a new audit log. -->
								<form action="<?php echo BASE_URL; ?>crud_files/add_audit_log.php" method="post">
									<div class="card-body">
										<!-- User reference input. -->
										<div class="mb-3">
											<label class="form-label">User Id</label>
											<input type="number" class="form-control" name="user_id" />
										</div>
										<!-- Action input. -->
										<div class="mb-3">
											<label class="form-label">Action</label>
											<input type="text" class="form-control" name="action" />
										</div>
										<!-- Target table input. -->
										<div class="mb-3">
											<label class="form-label">Target Table</label>
											<input type="text" class="form-control" name="target_table" />
										</div>
										<!-- Target id input. -->
										<div class="mb-3">
											<label class="form-label">Target Id</label>
											<input type="number" class="form-control" name="target_id" />
										</div>
										<!-- Time stamp input. -->
										<div class="mb-3">
											<label class="form-label">Time Stamp</label>
											<input type="date" class="form-control" name="time_stamp" />
										</div>
										<!-- Created by input. -->
										<div class="mb-3">
											<label class="form-label">Created By</label>
											<input type="number" class="form-control" name="created_by" />
										</div>
										<!-- Created date input. -->
										<div class="mb-3">
											<label class="form-label">Created Date</label>
											<input type="date" class="form-control" name="created_date" />
										</div>
										<!-- Modified by input. -->
										<div class="mb-3">
											<label class="form-label">Modified By</label>
											<input type="number" class="form-control" name="modified_by" />
										</div>
										<!-- Modified date input. -->
										<div class="mb-3">
											<label class="form-label">Modified Date</label>
											<input type="date" class="form-control" name="modified_date" />
										</div>
										<!-- Deleted by input. -->
										<div class="mb-3">
											<label class="form-label">Deleted By</label>
											<input type="number" class="form-control" name="deleted_by" />
										</div>
										<!-- Deleted date input. -->
										<div class="mb-3">
											<label class="form-label">Deleted Date</label>
											<input type="date" class="form-control" name="deleted_date" />
										</div>
									</div>
									<!-- Form actions. -->
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
<?php // Shared footer layout and scripts. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
