<?php
require_once "include/connection.php";

$result = $conn->query("SELECT * FROM books ORDER BY book_id DESC");
if ($result === false) {
	die("Query failed: " . $conn->error);
}
?>
<?php include('include/header_resources.php') ?>

<?php include('include/header.php') ?>
<?php include('sidebar.php') ?>
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
											<th>Author</th>
											<th>ISBN</th>
											<th>Publisher</th>
											<th>Year</th>
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
											<td><?= htmlspecialchars($row["author"]) ?></td>
											<td><?= htmlspecialchars($row["isbn"]) ?></td>
											<td><?= htmlspecialchars($row["publisher"]) ?></td>
											<td><?= htmlspecialchars($row["publication_year"]) ?></td>
											<td class="text-center">
												<a href="edit_book.php?id=<?= $row['book_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>

												<a href="delete_book.php?id=<?= $row['book_id'] ?>" class="text-danger" title="Delete"
													onclick="return confirm('Are you sure you want to delete this book?');">
													<i class="bi bi-trash fs-5"></i>
												</a>
											</td>
										</tr>
										<?php endwhile; ?>
										<?php else: ?>
										<tr>
											<td colspan="7" class="text-center text-muted">
												No books found.
											</td>
										</tr>
										<?php endif; ?>

									</tbody>
								</table>
							</div>

						</div>
					</div>
				</div>
			</div>
			<!-- row end -->
		</div>
	</div>
</main>
<!--end::App Main-->
<?php include('include/footer.php') ?>
<?php include('include/footer_resources.php') ?>
