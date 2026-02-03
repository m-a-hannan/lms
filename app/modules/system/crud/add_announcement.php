<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Handle form submission for creating an announcement.
if (isset($_POST['save'])) {
    // Read and sanitize incoming form values.
    $title = $conn->real_escape_string(trim($_POST['title']));
    $message = $conn->real_escape_string(trim($_POST['message']));
    $created_at = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['created_at'])));
    $created_by = (int) $_POST['created_by'];
    $created_date = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['created_date'])));
    $modified_by = (int) $_POST['modified_by'];
    $modified_date = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['modified_date'])));
    $deleted_by = (int) $_POST['deleted_by'];
    $deleted_date = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['deleted_date'])));

    // Insert the announcement record into the database.
    $sql = "INSERT INTO announcements (title, message, created_at, created_by, created_date, modified_by, modified_date, deleted_by, deleted_date) VALUES ('$title', '$message', '$created_at', $created_by, '$created_date', $modified_by, '$modified_date', $deleted_by, '$deleted_date')";
    $result = $conn->query($sql);

    if ($result) {
        // Redirect back to the list after saving.
        header("Location: " . BASE_URL . "announcement_list.php");
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
						<h3>Add Announcement</h3>
						<a href="<?php echo BASE_URL; ?>announcement_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<!-- Announcement creation form card. -->
							<div class="card card-primary card-outline mb-4">
								<!-- Submission form for a new announcement. -->
								<form action="<?php echo BASE_URL; ?>crud_files/add_announcement.php" method="post">
									<div class="card-body">
										<!-- Title input. -->
										<div class="mb-3">
											<label class="form-label">Title</label>
											<input type="text" class="form-control" name="title" />
										</div>
										<!-- Message input. -->
										<div class="mb-3">
											<label class="form-label">Message</label>
											<textarea class="form-control" name="message"></textarea>
										</div>
										<!-- Created at input. -->
										<div class="mb-3">
											<label class="form-label">Created At</label>
											<input type="datetime-local" class="form-control" name="created_at" />
										</div>
										<!-- Created by input. -->
										<div class="mb-3">
											<label class="form-label">Created By</label>
											<input type="number" class="form-control" name="created_by" />
										</div>
										<!-- Created date input. -->
										<div class="mb-3">
											<label class="form-label">Created Date</label>
											<input type="datetime-local" class="form-control" name="created_date" />
										</div>
										<!-- Modified by input. -->
										<div class="mb-3">
											<label class="form-label">Modified By</label>
											<input type="number" class="form-control" name="modified_by" />
										</div>
										<!-- Modified date input. -->
										<div class="mb-3">
											<label class="form-label">Modified Date</label>
											<input type="datetime-local" class="form-control" name="modified_date" />
										</div>
										<!-- Deleted by input. -->
										<div class="mb-3">
											<label class="form-label">Deleted By</label>
											<input type="number" class="form-control" name="deleted_by" />
										</div>
										<!-- Deleted date input. -->
										<div class="mb-3">
											<label class="form-label">Deleted Date</label>
											<input type="datetime-local" class="form-control" name="deleted_date" />
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
