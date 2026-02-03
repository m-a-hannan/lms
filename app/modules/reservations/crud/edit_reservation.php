<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Validate the incoming id to prevent invalid access.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

// Detect whether the reservations table has a book_id column.
$reservation_id = (int) $_GET['id'];
$hasReservationBookId = false;
$reservationColumnCheck = $conn->query("SHOW COLUMNS FROM reservations LIKE 'book_id'");
if ($reservationColumnCheck && $reservationColumnCheck->num_rows > 0) {
    $hasReservationBookId = true;
}

// Fetch the reservation record using the available schema.
if ($hasReservationBookId) {
    $result = $conn->query(
        "SELECT r.*, u.username, u.email, b.title
         FROM reservations r
         JOIN users u ON r.user_id = u.user_id
         JOIN books b ON r.book_id = b.book_id
         WHERE r.reservation_id = $reservation_id"
    );
} else {
    $result = $conn->query(
        "SELECT r.*, u.username, u.email, b.title
         FROM reservations r
         JOIN users u ON r.user_id = u.user_id
         JOIN book_copies c ON r.copy_id = c.copy_id
         JOIN book_editions e ON c.edition_id = e.edition_id
         JOIN books b ON e.book_id = b.book_id
         WHERE r.reservation_id = $reservation_id"
    );
}
// Validate that exactly one record was returned.
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

// Handle form submission and persist changes.
if (isset($_POST['save'])) {
    $reservation_date = $conn->real_escape_string(trim($_POST['reservation_date']));
    $expiry_date = $conn->real_escape_string(trim($_POST['expiry_date']));
    $status = trim($_POST['status'] ?? '');
    $remarks = $conn->real_escape_string(trim($_POST['remarks'] ?? ''));
    $allowedStatuses = ['pending', 'approved', 'rejected'];
    // Fall back to the current status when invalid.
    if (!in_array($status, $allowedStatuses, true)) {
        $status = $row['status'] ?? 'pending';
    }
    $remarksValue = $remarks !== '' ? "'" . $remarks . "'" : "NULL";

    // Build and execute the update query.
    $sql = "UPDATE reservations
            SET reservation_date = '$reservation_date',
                expiry_date = '$expiry_date',
                status = '" . $conn->real_escape_string($status) . "',
                remarks = $remarksValue
            WHERE reservation_id = $reservation_id";
    $updated = $conn->query($sql);

    if ($updated) {
        // Redirect back to the list after a successful update.
        header("Location: " . BASE_URL . "reservation_list.php");
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
						<h3>Edit Reservation</h3>
						<a href="<?php echo BASE_URL; ?>reservation_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<!-- Edit form card. -->
					<div class="card shadow-sm">
						<div class="card-body">
							<!-- Submission form for updating the reservation. -->
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
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
										<!-- Copy id label (read-only). -->
										<div class="mb-3">
											<label class="form-label">Copy Id</label>
											<input type="text" class="form-control" value="<?= htmlspecialchars($row['copy_id'] ?? '-') ?>" disabled />
										</div>
										<!-- Reservation date input. -->
										<div class="mb-3">
											<label class="form-label">Reservation Date</label>
											<input type="date" class="form-control" name="reservation_date" value="<?= htmlspecialchars($row['reservation_date']) ?>" />
										</div>
										<!-- Expiry date input. -->
										<div class="mb-3">
											<label class="form-label">Expiry Date</label>
											<input type="date" class="form-control" name="expiry_date" value="<?= htmlspecialchars($row['expiry_date']) ?>" />
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
