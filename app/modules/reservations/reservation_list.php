<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Detect whether the reservations table has a book_id column.
$hasReservationBookId = false;
$reservationColumnCheck = $conn->query("SHOW COLUMNS FROM reservations LIKE 'book_id'");
if ($reservationColumnCheck && $reservationColumnCheck->num_rows > 0) {
	$hasReservationBookId = true;
}

// Fetch reservation records using the available schema.
if ($hasReservationBookId) {
	$result = $conn->query(
		"SELECT r.reservation_id, r.reservation_date, r.expiry_date, r.status,
			u.username, u.email, b.title
		 FROM reservations r
		 JOIN users u ON r.user_id = u.user_id
		 JOIN books b ON r.book_id = b.book_id
		 ORDER BY r.reservation_id DESC"
	);
} else {
	$result = $conn->query(
		"SELECT r.reservation_id, r.reservation_date, r.expiry_date, r.status,
			u.username, u.email, b.title
		 FROM reservations r
		 JOIN users u ON r.user_id = u.user_id
		 JOIN book_copies c ON r.copy_id = c.copy_id
		 JOIN book_editions e ON c.edition_id = e.edition_id
		 JOIN books b ON e.book_id = b.book_id
		 ORDER BY r.reservation_id DESC"
	);
}
// Validate the query executed successfully.
if ($result === false) {
	die("Query failed: " . $conn->error);
}
?>
<?php // Shared header resources and layout chrome. ?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php include(ROOT_PATH . '/app/views/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<!--begin::App Content-->
	<div class="app-content">
		<!--begin::Container-->
		<div class="container-fluid">
			<!--begin::Row-->
			<div class="row">
				<div class="container py-5">
					<!-- Page header with title and create action. -->
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h3 class="mb-0">Reservation List</h3>
						<a href="<?php echo BASE_URL; ?>crud_files/add_reservation.php" class="btn btn-primary btn-sm">Add Reservation</a>
					</div>
					<!-- Results table card. -->
					<div class="card shadow-sm">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<!-- Table headers. -->
									<thead class="table-light">
										<tr>
											<th>#</th>
											<th>User</th>
											<th>Book Title</th>
											<th>Reservation Date</th>
											<th>Expiry Date</th>
											<th>Status</th>
											<th class="text-center">Actions</th>
										</tr>
									</thead>
									<tbody>
										<!-- Render rows when records exist. -->
										<?php if ($result->num_rows > 0): ?>
										<?php while ($row = $result->fetch_assoc()): ?>
										<tr>
											<td><?= $row["reservation_id"] ?></td>
											<td><?= htmlspecialchars($row['username'] ?: $row['email']) ?></td>
											<td><?= htmlspecialchars($row['title'] ?? '-') ?></td>
											<td><?= htmlspecialchars($row['reservation_date']) ?></td>
											<td><?= htmlspecialchars($row['expiry_date']) ?></td>
											<td><?= htmlspecialchars($row['status'] ?? '-') ?></td>
											<td class="text-center">
												<!-- Row actions for edit and delete. -->
												<a href="<?php echo BASE_URL; ?>crud_files/edit_reservation.php?id=<?= $row['reservation_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>
												<a href="<?php echo BASE_URL; ?>crud_files/delete_reservation.php?id=<?= $row['reservation_id'] ?>" class="text-danger" title="Delete"
													onclick="return confirm('Are you sure you want to delete this item?');">
													<i class="bi bi-trash fs-5"></i>
												</a>
											</td>
										</tr>
										<?php endwhile; ?>
										<?php else: ?>
										<!-- Empty state message. -->
										<tr>
											<td colspan="7" class="text-center text-muted">No records found.</td>
										</tr>
										<?php endif; ?>
									</tbody>
								</table>
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
