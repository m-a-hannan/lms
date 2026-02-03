<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Fetch loan records with related user and book info.
$result = $conn->query(
	"SELECT l.loan_id, l.issue_date, l.due_date, l.return_date, l.status,
		u.username, u.email, b.title
	 FROM loans l
	 JOIN users u ON l.user_id = u.user_id
	 LEFT JOIN book_copies c ON l.copy_id = c.copy_id
	 LEFT JOIN book_editions e ON c.edition_id = e.edition_id
	 LEFT JOIN books b ON e.book_id = b.book_id
	 ORDER BY l.loan_id DESC"
);
if ($result === false) {
	die("Query failed: " . $conn->error);
}
?>
<?php // Shared CSS/JS resources for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php // Top navigation bar for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php // Sidebar navigation for admin sections. ?>
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
						<h3 class="mb-0">Loan List</h3>
						<a href="<?php echo BASE_URL; ?>crud_files/add_loan.php" class="btn btn-primary btn-sm">Add Loan</a>
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
											<th>Issue Date</th>
											<th>Due Date</th>
											<th>Return Date</th>
											<th>Status</th>
											<th class="text-center">Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php // Show records when the result set has rows. ?>
										<?php if ($result->num_rows > 0): ?>
										<?php // Render each loan row. ?>
										<?php while ($row = $result->fetch_assoc()): ?>
										<tr>
											<td><?= $row["loan_id"] ?></td>
											<td><?= htmlspecialchars($row['username'] ?: $row['email']) ?></td>
											<td><?= htmlspecialchars($row['title'] ?? '-') ?></td>
											<td><?= htmlspecialchars($row['issue_date']) ?></td>
											<td><?= htmlspecialchars($row['due_date']) ?></td>
											<td><?= htmlspecialchars($row['return_date']) ?></td>
											<td><?= htmlspecialchars($row['status'] ?? '-') ?></td>
											<td class="text-center">
												<!-- Row actions for edit and delete. -->
												<a href="<?php echo BASE_URL; ?>crud_files/edit_loan.php?id=<?= $row['loan_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>
												<a href="<?php echo BASE_URL; ?>crud_files/delete_loan.php?id=<?= $row['loan_id'] ?>" class="text-danger" title="Delete"
													onclick="return confirm('Are you sure you want to delete this item?');">
													<i class="bi bi-trash fs-5"></i>
												</a>
											</td>
										</tr>
										<?php endwhile; ?>
										<?php else: ?>
										<!-- Empty state message. -->
										<tr>
											<td colspan="8" class="text-center text-muted">No records found.</td>
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
<?php // Shared footer markup for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php // Shared JS resources for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
