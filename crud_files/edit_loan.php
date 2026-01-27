<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/library_helpers.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$loan_id = (int) $_GET['id'];
$requiredColumns = ['status', 'remarks'];
$missingColumns = [];
foreach ($requiredColumns as $column) {
	$colResult = $conn->query("SHOW COLUMNS FROM loans LIKE '$column'");
	if (!$colResult || $colResult->num_rows === 0) {
		$missingColumns[] = $column;
	}
}
if ($missingColumns) {
	die('Missing column(s) in loans table: ' . implode(', ', $missingColumns) . '. Run DB/library_workflow_updates.sql.');
}
$result = $conn->query(
	"SELECT l.*,
		COALESCE(NULLIF(u.username, ''), NULLIF(u.email, ''), CONCAT('User #', l.user_id)) AS user_label,
		COALESCE(b.title, CASE WHEN l.copy_id IS NOT NULL THEN CONCAT('Copy #', l.copy_id) ELSE '-' END) AS book_title
	 FROM loans l
	 LEFT JOIN users u ON l.user_id = u.user_id
	 LEFT JOIN book_copies c ON l.copy_id = c.copy_id
	 LEFT JOIN book_editions e ON c.edition_id = e.edition_id
	 LEFT JOIN books b ON e.book_id = b.book_id
	 WHERE l.loan_id = $loan_id"
);
if ($result === false) {
    die('Query failed: ' . $conn->error);
}
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

if (isset($_POST['save'])) {
    $issue_input = trim($_POST['issue_date'] ?? '');
    $due_input = trim($_POST['due_date'] ?? '');
    $return_input = trim($_POST['return_date'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $remarks = $conn->real_escape_string(trim($_POST['remarks'] ?? ''));
    $allowedStatuses = ['pending', 'approved', 'rejected', 'returned'];
    if (!in_array($status, $allowedStatuses, true)) {
        $status = $row['status'] ?? 'pending';
    }
    $remarksValue = $remarks !== '' ? "'" . $remarks . "'" : "''";

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $currentUserId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
    library_set_current_user($conn, $currentUserId);

    $issue_date = $issue_input;
    $due_date = $due_input;
    $return_date = $return_input;

    if ($status === 'approved') {
        if ($issue_date === '') {
            $issue_date = date('Y-m-d');
        }
        if ($due_date === '' && $issue_date !== '') {
            $days = library_get_policy_days($conn, 'loan_period_days', 14);
            $due_date = date('Y-m-d', strtotime($issue_date . ' +' . $days . ' days'));
        }
    }

    if ($status === 'returned' && $return_date === '') {
        $return_date = date('Y-m-d');
    }

    $issueValue = $issue_date !== '' ? "'" . $conn->real_escape_string($issue_date) . "'" : "NULL";
    $dueValue = $due_date !== '' ? "'" . $conn->real_escape_string($due_date) . "'" : "NULL";
    $returnValue = $return_date !== '' ? "'" . $conn->real_escape_string($return_date) . "'" : "NULL";

    $sql = "UPDATE loans
            SET issue_date = $issueValue,
                due_date = $dueValue,
                return_date = $returnValue,
                status = '" . $conn->real_escape_string($status) . "',
                remarks = $remarksValue
            WHERE loan_id = $loan_id";
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
							<label class="form-label">User</label>
							<input type="text" class="form-control" value="<?= htmlspecialchars($row['user_label'] ?? '-') ?>" disabled />
						</div>
						<div class="mb-3">
							<label class="form-label">Book Title</label>
							<input type="text" class="form-control" value="<?= htmlspecialchars($row['book_title'] ?? '-') ?>" disabled />
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
						<div class="mb-3">
							<label class="form-label">Status</label>
							<select class="form-select" name="status">
								<?php
								$statusOptions = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'returned' => 'Returned'];
								$currentStatus = $row['status'] ?? 'pending';
								foreach ($statusOptions as $value => $label):
								?>
								<option value="<?= $value ?>" <?= $currentStatus === $value ? 'selected' : '' ?>><?= $label ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="mb-3">
							<label class="form-label">Remarks</label>
							<textarea class="form-control" name="remarks" rows="3" placeholder="Reason for rejection or notes"><?= htmlspecialchars($row['remarks'] ?? '') ?></textarea>
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
