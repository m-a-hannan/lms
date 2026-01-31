<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

$result = $conn->query("SELECT * FROM book_editions WHERE deleted_date IS NULL ORDER BY edition_id DESC");
if ($result === false) {
	die("Query failed: " . $conn->error);
}
?>
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
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h3 class="mb-0">Book Edition List</h3>
						<a href="<?php echo BASE_URL; ?>crud_files/add_book_edition.php" class="btn btn-primary btn-sm">Add Book Edition</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-light">
										<tr>
											<th>#</th>
									<th>Book Id</th>
									<th>Edition Number</th>
									<th>Publication Year</th>
									<th>Pages</th>
											<th class="text-center">Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php if ($result->num_rows > 0): ?>
										<?php while ($row = $result->fetch_assoc()): ?>
										<tr>
											<td><?= $row["edition_id"] ?></td>
									<td><?= htmlspecialchars($row['book_id']) ?></td>
									<td><?= htmlspecialchars($row['edition_number']) ?></td>
									<td><?= htmlspecialchars($row['publication_year']) ?></td>
									<td><?= htmlspecialchars($row['pages']) ?></td>
											<td class="text-center">
												<a href="<?php echo BASE_URL; ?>crud_files/edit_book_edition.php?id=<?= $row['edition_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>
												<a href="<?php echo BASE_URL; ?>crud_files/delete_book_edition.php?id=<?= $row['edition_id'] ?>" class="text-danger" title="Delete"
 data-confirm-delete>
													<i class="bi bi-trash fs-5"></i>
												</a>
											</td>
										</tr>
										<?php endwhile; ?>
										<?php else: ?>
										<tr>
											<td colspan="6" class="text-center text-muted">No records found.</td>
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
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>