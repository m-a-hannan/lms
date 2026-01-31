<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

$result = $conn->query(
	"SELECT books.*,
	        categories.category_name,
	        (SELECT COUNT(*) FROM book_editions WHERE book_id = books.book_id) AS edition_count,
	        (SELECT COUNT(*)
	         FROM book_copies bc
	         JOIN book_editions be ON bc.edition_id = be.edition_id
	         WHERE be.book_id = books.book_id) AS copy_count,
	        (SELECT COUNT(*)
	         FROM loans l
	         JOIN book_copies bc ON l.copy_id = bc.copy_id
	         JOIN book_editions be ON bc.edition_id = be.edition_id
	         WHERE be.book_id = books.book_id) AS loan_count,
	        (SELECT COUNT(*)
	         FROM returns r
	         JOIN loans l ON r.loan_id = l.loan_id
	         JOIN book_copies bc ON l.copy_id = bc.copy_id
	         JOIN book_editions be ON bc.edition_id = be.edition_id
	         WHERE be.book_id = books.book_id) AS return_count,
	        (SELECT COUNT(*)
	         FROM fines f
	         JOIN loans l ON f.loan_id = l.loan_id
	         JOIN book_copies bc ON l.copy_id = bc.copy_id
	         JOIN book_editions be ON bc.edition_id = be.edition_id
	         WHERE be.book_id = books.book_id) AS fine_count,
	        (SELECT COUNT(*)
	         FROM reservations r
	         WHERE r.book_id = books.book_id
	            OR r.copy_id IN (
	                SELECT bc.copy_id
	                FROM book_copies bc
	                JOIN book_editions be ON bc.edition_id = be.edition_id
	                WHERE be.book_id = books.book_id
	            )) AS reservation_count,
	        (SELECT COUNT(*) FROM book_categories bc WHERE bc.book_id = books.book_id) AS category_count
	 FROM books
	 LEFT JOIN categories ON categories.category_id = books.category_id
	 WHERE books.deleted_date IS NULL
	 ORDER BY books.book_id DESC"
);
if ($result === false) {
	die("Query failed: " . $conn->error);
}

$deleteModals = [];
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
						<h3 class="mb-0">Available Books</h3>
						<a href="<?php echo BASE_URL; ?>crud_files/add_book.php" class="btn btn-primary btn-sm">Add Book</a>
					</div>

					<div class="card shadow-sm">
						<div class="card-body">

							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-light">
										<tr>
											<th>#</th>
										<th>Cover</th>
										<th>Title</th>
										<th>Excerpt</th>
										<th>Author</th>
										<th>ISBN</th>
										<th>Publisher</th>
										<th>Year</th>
										<th>Category</th>
										<th class="text-center">Actions</th>
									</tr>
									</thead>
									<tbody>

										<?php if ($result->num_rows > 0): ?>
										<?php while ($row = $result->fetch_assoc()): ?>
										<tr>
											<td><?= $row["book_id"] ?></td>
											<td>
												<?php if (!empty($row["book_cover_path"])): ?>
												<img src="<?= htmlspecialchars($row["book_cover_path"]) ?>" class="cover-thumb" alt="Cover">
												<?php else: ?>
												<span class="text-muted">N/A</span>
												<?php endif; ?>
											</td>
											<td><?= htmlspecialchars($row["title"]) ?></td>
											<td><?= htmlspecialchars($row["book_excerpt"]) ?></td>
											<td><?= htmlspecialchars($row["author"]) ?></td>
											<td><?= htmlspecialchars($row["isbn"]) ?></td>
											<td><?= htmlspecialchars($row["publisher"]) ?></td>
											<td><?= htmlspecialchars($row["publication_year"]) ?></td>
											<td><?= htmlspecialchars($row["category_name"] ?? '') ?></td>
											<td class="text-center">
												<a href="<?php echo BASE_URL; ?>crud_files/edit_book.php?id=<?= $row['book_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>

												<a href="#" class="text-danger" title="Delete"
													data-bs-toggle="modal" data-bs-target="#deleteBookModal<?= (int) $row['book_id'] ?>">
													<i class="bi bi-trash fs-5"></i>
												</a>
											</td>
										</tr>
										<?php
											ob_start();
										?>
										<div class="modal fade" id="deleteBookModal<?= (int) $row['book_id'] ?>" tabindex="-1" aria-hidden="true">
											<div class="modal-dialog modal-dialog-centered">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title">Delete book #<?= (int) $row['book_id'] ?></h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
													</div>
													<div class="modal-body">
														<p class="mb-2">Related records found:</p>
														<ul class="list-unstyled mb-3">
															<li>Book editions: <strong><?= (int) $row['edition_count'] ?></strong></li>
															<li>Book copies: <strong><?= (int) $row['copy_count'] ?></strong></li>
															<li>Loans: <strong><?= (int) $row['loan_count'] ?></strong></li>
															<li>Returns: <strong><?= (int) $row['return_count'] ?></strong></li>
															<li>Fines: <strong><?= (int) $row['fine_count'] ?></strong></li>
															<li>Reservations: <strong><?= (int) $row['reservation_count'] ?></strong></li>
															<li>Book categories: <strong><?= (int) $row['category_count'] ?></strong></li>
														</ul>
														<div class="alert alert-warning mb-0" role="alert" data-no-toast>
															Soft delete hides the book but keeps related history. Hard delete permanently removes related records.
														</div>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
														<a class="btn btn-warning"
															href="<?php echo BASE_URL; ?>crud_files/delete_book.php?id=<?= (int) $row['book_id'] ?>&mode=soft">
															Soft delete
														</a>
														<a class="btn btn-danger"
															href="<?php echo BASE_URL; ?>crud_files/delete_book.php?id=<?= (int) $row['book_id'] ?>&mode=hard">
															Hard delete
														</a>
													</div>
												</div>
											</div>
										</div>
										<?php
											$deleteModals[] = ob_get_clean();
										?>
										<?php endwhile; ?>
										<?php else: ?>
										<tr>
											<td colspan="9" class="text-center text-muted">
												No books found.
											</td>
										</tr>
										<?php endif; ?>

									</tbody>
								</table>
							</div>
							<?php if ($deleteModals): ?>
								<?php echo implode("\n", $deleteModals); ?>
							<?php endif; ?>

						</div>
					</div>
					
				</div>
			</div>
			<!-- row end -->
		</div>
	</div>
</main>
<!--end::App Main-->
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
