<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$notification_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM notifications WHERE notification_id = $notification_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

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

    $sql = "UPDATE notifications SET user_id = $user_id, title = '$title', message = '$message', created_at = '$created_at', read_status = $read_status, created_by = $created_by, created_date = '$created_date', modified_by = $modified_by, modified_date = '$modified_date', deleted_by = $deleted_by, deleted_date = '$deleted_date' WHERE notification_id = $notification_id";
    $updated = $conn->query($sql);

    if ($updated) {
        header("Location: " . BASE_URL . "notification_list.php");
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
						<h3>Edit Notification</h3>
						<a href="<?php echo BASE_URL; ?>notification_list.php" class="btn btn-secondary btn-sm">Back</a>
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
								<label class="form-label">Title</label>
								<input type="text" class="form-control" name="title" value="<?= htmlspecialchars($row['title']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Message</label>
								<textarea class="form-control" name="message"><?= htmlspecialchars($row['message']) ?></textarea>
							</div>
							<div class="mb-3">
								<label class="form-label">Created At</label>
								<input type="datetime-local" class="form-control" name="created_at" value="<?= htmlspecialchars(str_replace(' ', 'T', $row['created_at'])) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Read Status</label>
								<input type="number" class="form-control" name="read_status" value="<?= htmlspecialchars($row['read_status']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Created By</label>
								<input type="number" class="form-control" name="created_by" value="<?= htmlspecialchars($row['created_by']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Created Date</label>
								<input type="datetime-local" class="form-control" name="created_date" value="<?= htmlspecialchars(str_replace(' ', 'T', $row['created_date'])) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Modified By</label>
								<input type="number" class="form-control" name="modified_by" value="<?= htmlspecialchars($row['modified_by']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Modified Date</label>
								<input type="datetime-local" class="form-control" name="modified_date" value="<?= htmlspecialchars(str_replace(' ', 'T', $row['modified_date'])) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Deleted By</label>
								<input type="number" class="form-control" name="deleted_by" value="<?= htmlspecialchars($row['deleted_by']) ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Deleted Date</label>
								<input type="datetime-local" class="form-control" name="deleted_date" value="<?= htmlspecialchars(str_replace(' ', 'T', $row['deleted_date'])) ?>" />
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