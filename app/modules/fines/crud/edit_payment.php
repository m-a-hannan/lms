<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the incoming id to prevent invalid access.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Fetch the current payment record for editing.
$payment_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM payments WHERE payment_id = $payment_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

// Handle form submission and persist changes.
if (isset($_POST['save'])) {
    $fine_id = (int) $_POST['fine_id'];
    $payment_date = $conn->real_escape_string(trim($_POST['payment_date']));
    $amount = (float) $_POST['amount'];
    $payment_method = $conn->real_escape_string(trim($_POST['payment_method']));
    $created_by = (int) $_POST['created_by'];
    $created_date = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['created_date'])));
    $modified_by = (int) $_POST['modified_by'];
    $modified_date = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['modified_date'])));
    $deleted_by = (int) $_POST['deleted_by'];
    $deleted_date = $conn->real_escape_string(str_replace('T', ' ', trim($_POST['deleted_date'])));

    // Update the payment record with the submitted values.
    $sql = "UPDATE payments SET fine_id = $fine_id, payment_date = '$payment_date', amount = $amount, payment_method = '$payment_method', created_by = $created_by, created_date = '$created_date', modified_by = $modified_by, modified_date = '$modified_date', deleted_by = $deleted_by, deleted_date = '$deleted_date' WHERE payment_id = $payment_id";
    $updated = $conn->query($sql);

    if ($updated) {
        // Redirect back to the list after a successful update.
        header("Location: " . BASE_URL . "payment_list.php");
        exit;
    } else {
        die("Update failed: " . $conn->error);
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
						<h3>Edit Payment</h3>
						<a href="<?php echo BASE_URL; ?>payment_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<!-- Edit form card. -->
					<div class="card shadow-sm">
						<div class="card-body">
							<!-- Submission form for updating the payment. -->
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
										<!-- Fine reference input. -->
										<div class="mb-3">
											<label class="form-label">Fine Id</label>
											<input type="number" class="form-control" name="fine_id" value="<?= htmlspecialchars($row['fine_id']) ?>" />
										</div>
										<!-- Payment date input. -->
										<div class="mb-3">
											<label class="form-label">Payment Date</label>
											<input type="date" class="form-control" name="payment_date" value="<?= htmlspecialchars($row['payment_date']) ?>" />
										</div>
										<!-- Amount input. -->
										<div class="mb-3">
											<label class="form-label">Amount</label>
											<input type="number" class="form-control" name="amount" step="0.01" value="<?= htmlspecialchars($row['amount']) ?>" />
										</div>
										<!-- Payment method input. -->
										<div class="mb-3">
											<label class="form-label">Payment Method</label>
											<input type="text" class="form-control" name="payment_method" value="<?= htmlspecialchars($row['payment_method']) ?>" />
										</div>
										<!-- Created by input. -->
										<div class="mb-3">
											<label class="form-label">Created By</label>
											<input type="number" class="form-control" name="created_by" value="<?= htmlspecialchars($row['created_by']) ?>" />
										</div>
										<!-- Created date input. -->
										<div class="mb-3">
											<label class="form-label">Created Date</label>
											<input type="datetime-local" class="form-control" name="created_date" value="<?= htmlspecialchars(str_replace(' ', 'T', $row['created_date'])) ?>" />
										</div>
										<!-- Modified by input. -->
										<div class="mb-3">
											<label class="form-label">Modified By</label>
											<input type="number" class="form-control" name="modified_by" value="<?= htmlspecialchars($row['modified_by']) ?>" />
										</div>
										<!-- Modified date input. -->
										<div class="mb-3">
											<label class="form-label">Modified Date</label>
											<input type="datetime-local" class="form-control" name="modified_date" value="<?= htmlspecialchars(str_replace(' ', 'T', $row['modified_date'])) ?>" />
										</div>
										<!-- Deleted by input. -->
										<div class="mb-3">
											<label class="form-label">Deleted By</label>
											<input type="number" class="form-control" name="deleted_by" value="<?= htmlspecialchars($row['deleted_by']) ?>" />
										</div>
										<!-- Deleted date input. -->
										<div class="mb-3">
											<label class="form-label">Deleted Date</label>
											<input type="datetime-local" class="form-control" name="deleted_date" value="<?= htmlspecialchars(str_replace(' ', 'T', $row['deleted_date'])) ?>" />
										</div>
										<!-- Submit button. -->
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
<?php // Shared footer layout and scripts. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
