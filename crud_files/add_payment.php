<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

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

    $sql = "INSERT INTO payments (fine_id, payment_date, amount, payment_method, created_by, created_date, modified_by, modified_date, deleted_by, deleted_date) VALUES ($fine_id, '$payment_date', $amount, '$payment_method', $created_by, '$created_date', $modified_by, '$modified_date', $deleted_by, '$deleted_date')";
    $result = $conn->query($sql);

    if ($result) {
        header("Location: " . BASE_URL . "payment_list.php");
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
						<h3>Add Payment</h3>
						<a href="<?php echo BASE_URL; ?>payment_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_payment.php" method="post">
									<div class="card-body">
							<div class="mb-3">
								<label class="form-label">Fine Id</label>
								<input type="number" class="form-control" name="fine_id" />
							</div>
							<div class="mb-3">
								<label class="form-label">Payment Date</label>
								<input type="date" class="form-control" name="payment_date" />
							</div>
							<div class="mb-3">
								<label class="form-label">Amount</label>
								<input type="number" class="form-control" name="amount" step="0.01" />
							</div>
							<div class="mb-3">
								<label class="form-label">Payment Method</label>
								<input type="text" class="form-control" name="payment_method" />
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
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>
