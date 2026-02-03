<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the incoming id to prevent invalid access.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Fetch the return record with related display data.
$return_id = (int) $_GET['id'];
$result = $conn->query(
    "SELECT r.*, l.loan_id, l.user_id, b.title, u.username, u.email
     FROM returns r
     JOIN loans l ON r.loan_id = l.loan_id
     JOIN users u ON l.user_id = u.user_id
     JOIN book_copies c ON l.copy_id = c.copy_id
     JOIN book_editions e ON c.edition_id = e.edition_id
     JOIN books b ON e.book_id = b.book_id
     WHERE r.return_id = $return_id"
);
// Validate that exactly one record was returned.
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

// Handle form submission and persist changes.
if (isset($_POST['save'])) {
    $return_date = $conn->real_escape_string(trim($_POST['return_date']));
    $status = trim($_POST['status'] ?? '');
    $remarks = $conn->real_escape_string(trim($_POST['remarks'] ?? ''));
    $allowedStatuses = ['pending', 'approved', 'rejected'];
    // Fall back to the current status when invalid.
    if (!in_array($status, $allowedStatuses, true)) {
        $status = $row['status'] ?? 'pending';
    }
    $remarksValue = $remarks !== '' ? "'" . $remarks . "'" : "NULL";

    // Build and execute the update query.
    $sql = "UPDATE returns
            SET return_date = '$return_date',
                status = '" . $conn->real_escape_string($status) . "',
                remarks = $remarksValue
            WHERE return_id = $return_id";
    $updated = $conn->query($sql);

    if ($updated) {
        // Redirect back to the list after a successful update.
        header("Location: " . BASE_URL . "return_list.php");
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
						<h3>Edit Return</h3>
						<a href="<?php echo BASE_URL; ?>return_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<!-- Edit form card. -->
					<div class="card shadow-sm">
						<div class="card-body">
							<!-- Submission form for updating the return. -->
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
										<!-- Loan id label (read-only). -->
										<div class="mb-3">
											<label class="form-label">Loan Id</label>
											<input type="text" class="form-control" value="<?= htmlspecialchars($row['loan_id']) ?>" disabled />
										</div>
										<!-- User label (read-only). -->
										<div class="mb-3">
											<label class="form-label">User</label>
											<input type="text" class="form-control" value="<?= htmlspecialchars($row['username'] ?: $row['email']) ?>" disabled />
										</div>
										<!-- Book title label (read-only). -->
										<div class="mb-3">
											<label class="form-label">Book Title</label>
											<input type="text" class="form-control" value="<?= htmlspecialchars($row['title'] ?? '-') ?>" disabled />
										</div>
										<!-- Return date input. -->
										<div class="mb-3">
											<label class="form-label">Return Date</label>
											<input type="date" class="form-control" name="return_date" value="<?= htmlspecialchars($row['return_date']) ?>" />
										</div>
										<!-- Status selector. -->
										<div class="mb-3">
											<label class="form-label">Status</label>
											<select class="form-select" name="status">
												<?php
												$statusOptions = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'];
												$currentStatus = $row['status'] ?? 'pending';
												foreach ($statusOptions as $value => $label):
												?>
												<option value="<?= $value ?>" <?= $currentStatus === $value ? 'selected' : '' ?>><?= $label ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<!-- Remarks input. -->
										<div class="mb-3">
											<label class="form-label">Remarks</label>
											<textarea class="form-control" name="remarks" rows="3" placeholder="Reason for rejection or notes"><?= htmlspecialchars($row['remarks'] ?? '') ?></textarea>
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
