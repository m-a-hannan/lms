<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

$summaryResult = $conn->query(
	"SELECT b.book_id, b.title,
		COUNT(c.copy_id) AS total_copies,
		SUM(CASE WHEN c.status IS NULL OR c.status = '' OR c.status = 'available' THEN 1 ELSE 0 END) AS available_copies
	 FROM books b
	 LEFT JOIN book_editions e ON e.book_id = b.book_id
	 LEFT JOIN book_copies c ON c.edition_id = e.edition_id
	 GROUP BY b.book_id
	 ORDER BY b.book_id DESC"
);

$result = $conn->query(
	"SELECT c.copy_id, c.edition_id, c.barcode, c.status, c.location, b.title
	 FROM book_copies c
	 LEFT JOIN book_editions e ON c.edition_id = e.edition_id
	 LEFT JOIN books b ON e.book_id = b.book_id
	 ORDER BY c.copy_id DESC"
);
if ($result === false) {
	die("Query failed: " . $conn->error);
}
?>
<?php include(ROOT_PATH . '/include/header_resources.php') ?>
<?php include(ROOT_PATH . '/include/header.php') ?>
<?php include(ROOT_PATH . '/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<!--begin::App Content-->
	<div class="app-content">
		<!--begin::Container-->
		<div class="container-fluid">
			<!--begin::Row-->
			<div class="row">
				<div class="container py-5">
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h3 class="mb-0">Book Copy List</h3>
						<a href="<?php echo BASE_URL; ?>crud_files/add_book_copy.php" class="btn btn-primary btn-sm">Add Book Copy</a>
					</div>
					<?php if ($summaryResult && $summaryResult->num_rows > 0): ?>
					<div class="card shadow-sm mb-4">
						<div class="card-body">
							<h6 class="mb-3">Copies Summary</h6>
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-light">
										<tr>
											<th>Book ID</th>
											<th>Title</th>
											<th>Total Copies</th>
											<th>Available Copies</th>
										</tr>
									</thead>
									<tbody>
										<?php while ($summary = $summaryResult->fetch_assoc()): ?>
										<tr>
											<td><?= htmlspecialchars($summary['book_id']) ?></td>
											<td><?= htmlspecialchars($summary['title'] ?: '-') ?></td>
											<td><?= htmlspecialchars($summary['total_copies']) ?></td>
											<td><?= htmlspecialchars($summary['available_copies']) ?></td>
										</tr>
										<?php endwhile; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<?php endif; ?>
					<div class="card shadow-sm">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-light">
										<tr>
											<th>#</th>
											<th>Book</th>
									<th>Edition Id</th>
									<th>Barcode</th>
									<th>Status</th>
									<th>Location</th>
											<th class="text-center">Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php if ($result->num_rows > 0): ?>
										<?php while ($row = $result->fetch_assoc()): ?>
										<tr>
											<td><?= $row["copy_id"] ?></td>
											<td><?= htmlspecialchars($row['title'] ?? '-') ?></td>
									<td><?= htmlspecialchars($row['edition_id']) ?></td>
									<td><?= htmlspecialchars($row['barcode']) ?></td>
									<td><?= htmlspecialchars($row['status']) ?></td>
									<td><?= htmlspecialchars($row['location']) ?></td>
											<td class="text-center">
												<a href="<?php echo BASE_URL; ?>crud_files/edit_book_copy.php?id=<?= $row['copy_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>
												<a href="<?php echo BASE_URL; ?>crud_files/delete_book_copy.php?id=<?= $row['copy_id'] ?>" class="text-danger" title="Delete"
													onclick="return confirm('Are you sure you want to delete this item?');">
													<i class="bi bi-trash fs-5"></i>
												</a>
											</td>
										</tr>
										<?php endwhile; ?>
										<?php else: ?>
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
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>
