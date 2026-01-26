<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$loan_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM loans WHERE loan_id = $loan_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

if (isset($_POST['save'])) {
    $copy_id = (int) $_POST['copy_id'];
    $user_id = (int) $_POST['user_id'];
    $issue_date = $conn->real_escape_string(trim($_POST['issue_date']));
    $due_date = $conn->real_escape_string(trim($_POST['due_date']));
    $return_date = $conn->real_escape_string(trim($_POST['return_date']));

    $sql = "UPDATE loans SET copy_id = $copy_id, user_id = $user_id, issue_date = '$issue_date', due_date = '$due_date', return_date = '$return_date' WHERE loan_id = $loan_id";
    $updated = $conn->query($sql);

    if ($updated) {
        header("Location: " . BASE_URL . "loan_list.php");
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
						<h3>Edit Loan</h3>
						<a href="<?php echo BASE_URL; ?>loan_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">Copy Id</label>
							<input type="number" class="form-control" name="copy_id" value="<?= htmlspecialchars($row['copy_id']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">User Id</label>
							<input type="number" class="form-control" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Issue Date</label>
							<input type="date" class="form-control" name="issue_date" value="<?= htmlspecialchars($row['issue_date']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Due Date</label>
							<input type="date" class="form-control" name="due_date" value="<?= htmlspecialchars($row['due_date']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Return Date</label>
							<input type="date" class="form-control" name="return_date" value="<?= htmlspecialchars($row['return_date']) ?>" />
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
