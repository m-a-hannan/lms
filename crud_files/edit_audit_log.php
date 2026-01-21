<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$log_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM audit_logs WHERE log_id = $log_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

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

    $sql = "UPDATE audit_logs SET user_id = $user_id, action = '$action', target_table = '$target_table', target_id = $target_id, time_stamp = '$time_stamp', created_by = $created_by, created_date = '$created_date', modified_by = $modified_by, modified_date = '$modified_date', deleted_by = $deleted_by, deleted_date = '$deleted_date' WHERE log_id = $log_id";
    $updated = $conn->query($sql);

    if ($updated) {
        header("Location: " . BASE_URL . "audit_log_list.php");
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
						<h3>Edit Audit Log</h3>
						<a href="<?php echo BASE_URL; ?>audit_log_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">User Id</label>
								<input type="number" class="form-control" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Action</label>
								<input type="text" class="form-control" name="action" value="<?= htmlspecialchars($row['action']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Target Table</label>
								<input type="text" class="form-control" name="target_table" value="<?= htmlspecialchars($row['target_table']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Target Id</label>
								<input type="number" class="form-control" name="target_id" value="<?= htmlspecialchars($row['target_id']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Time Stamp</label>
								<input type="date" class="form-control" name="time_stamp" value="<?= htmlspecialchars($row['time_stamp']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Created By</label>
								<input type="number" class="form-control" name="created_by" value="<?= htmlspecialchars($row['created_by']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Created Date</label>
								<input type="date" class="form-control" name="created_date" value="<?= htmlspecialchars($row['created_date']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Modified By</label>
								<input type="number" class="form-control" name="modified_by" value="<?= htmlspecialchars($row['modified_by']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Modified Date</label>
								<input type="date" class="form-control" name="modified_date" value="<?= htmlspecialchars($row['modified_date']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Deleted By</label>
								<input type="number" class="form-control" name="deleted_by" value="<?= htmlspecialchars($row['deleted_by']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Deleted Date</label>
								<input type="date" class="form-control" name="deleted_date" value="<?= htmlspecialchars($row['deleted_date']) ?>" />
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
