<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (isset($_POST['save'])) {
    $user_id = (int) $_POST['user_id'];
    $title = $conn->real_escape_string(trim($_POST['title']));
    $message = $conn->real_escape_string(trim($_POST['message']));
    $created_at = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['created_at'])));
    $read_status = (int) $_POST['read_status'];
    $created_by = (int) $_POST['created_by'];
    $created_date = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['created_date'])));
    $modified_by = (int) $_POST['modified_by'];
    $modified_date = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['modified_date'])));
    $deleted_by = (int) $_POST['deleted_by'];
    $deleted_date = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['deleted_date'])));

    $sql = "INSERT INTO notifications (user_id, title, message, created_at, read_status, created_by, created_date, modified_by, modified_date, deleted_by, deleted_date) VALUES ($user_id, '$title', '$message', '$created_at', $read_status, $created_by, '$created_date', $modified_by, '$modified_date', $deleted_by, '$deleted_date')";
    $result = $conn->query($sql);

    if ($result) {
        header("Location: " . BASE_URL . "notification_list.php");
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
						<h3>Add Notification</h3>
						<a href="<?php echo BASE_URL; ?>notification_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_notification.php" method="post">
									<div class="card-body">
							<div class="mb-3">
								<label class="form-label">User Id</label>
								<input type="number" class="form-control" name="user_id" />
							</div>
							<div class="mb-3">
								<label class="form-label">Title</label>
								<input type="text" class="form-control" name="title" />
							</div>
							<div class="mb-3">
								<label class="form-label">Message</label>
								<textarea class="form-control" name="message"></textarea>
							</div>
							<div class="mb-3">
								<label class="form-label">Created At</label>
								<input type="datetime-local" class="form-control" name="created_at" />
							</div>
							<div class="mb-3">
								<label class="form-label">Read Status</label>
								<input type="number" class="form-control" name="read_status" />
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