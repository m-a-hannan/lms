<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (isset($_POST['save'])) {
    $copy_id = (int) $_POST['copy_id'];
    $user_id = (int) $_POST['user_id'];
    $issue_date = $conn->real_escape_string(trim($_POST['issue_date']));
    $due_date = $conn->real_escape_string(trim($_POST['due_date']));
    $return_date = $conn->real_escape_string(trim($_POST['return_date']));

    $sql = "INSERT INTO loans (copy_id, user_id, issue_date, due_date, return_date) VALUES ($copy_id, $user_id, '$issue_date', '$due_date', '$return_date')";
    $result = $conn->query($sql);

    if ($result) {
        header("Location: " . BASE_URL . "loan_list.php");
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
						<h3>Add Loan</h3>
						<a href="<?php echo BASE_URL; ?>loan_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_loan.php" method="post">
									<div class="card-body">
						<div class="mb-3">
							<label class="form-label">Copy Id</label>
							<input type="number" class="form-control" name="copy_id" />
						</div>
						<div class="mb-3">
							<label class="form-label">User Id</label>
							<input type="number" class="form-control" name="user_id" />
						</div>
						<div class="mb-3">
							<label class="form-label">Issue Date</label>
							<input type="date" class="form-control" name="issue_date" />
						</div>
						<div class="mb-3">
							<label class="form-label">Due Date</label>
							<input type="date" class="form-control" name="due_date" />
						</div>
						<div class="mb-3">
							<label class="form-label">Return Date</label>
							<input type="date" class="form-control" name="return_date" />
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