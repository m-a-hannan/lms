<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/permissions.php';

$categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;
$categories = [];

$catResult = $conn->query(
	"SELECT category_id, category_name
	 FROM categories
	 ORDER BY category_name ASC"
);
if ($catResult) {
	while ($row = $catResult->fetch_assoc()) {
		$categories[] = $row;
	}
}

$baseSql =
	"SELECT b.book_id, b.title, c.category_name,
		COUNT(cp.copy_id) AS total_copies,
		SUM(CASE WHEN cp.status IS NULL OR cp.status = '' OR cp.status = 'available' THEN 1 ELSE 0 END) AS available_copies,
		SUM(CASE WHEN cp.status = 'loaned' THEN 1 ELSE 0 END) AS loaned_copies,
		(
			SELECT COUNT(*)
			FROM returns r
			JOIN loans l ON r.loan_id = l.loan_id
			JOIN book_copies cp2 ON l.copy_id = cp2.copy_id
			JOIN book_editions e2 ON cp2.edition_id = e2.edition_id
			WHERE r.status = 'pending' AND e2.book_id = b.book_id
		) AS pending_returns
	 FROM books b
	 LEFT JOIN categories c ON c.category_id = b.category_id
	 LEFT JOIN book_editions e ON e.book_id = b.book_id
	 LEFT JOIN book_copies cp ON cp.edition_id = e.edition_id
	 WHERE b.deleted_date IS NULL";

$params = [];
$types = '';
if ($categoryId > 0) {
	$baseSql .= " AND b.category_id = ?";
	$params[] = $categoryId;
	$types .= 'i';
}

$baseSql .= " GROUP BY b.book_id ORDER BY b.title ASC";

$stockRows = [];
$stmt = $conn->prepare($baseSql);
if ($stmt) {
	if ($params) {
		$stmt->bind_param($types, ...$params);
	}
	$stmt->execute();
	$result = $stmt->get_result();
	while ($result && ($row = $result->fetch_assoc())) {
		$stockRows[] = $row;
	}
	$stmt->close();
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
					<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
						<h3 class="mb-0">Library Stock Summary</h3>
						<form method="get" class="d-flex align-items-center gap-2">
							<label for="categoryFilter" class="form-label mb-0">Category</label>
							<select id="categoryFilter" name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
								<option value="0">All Categories</option>
								<?php foreach ($categories as $category): ?>
									<option value="<?php echo (int) $category['category_id']; ?>" <?php echo $categoryId === (int) $category['category_id'] ? 'selected' : ''; ?>>
										<?php echo htmlspecialchars($category['category_name']); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</form>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-light">
										<tr>
											<th>Book ID</th>
											<th>Title</th>
											<th>Category</th>
											<th>Loaned Copies</th>
											<th>Pending Returns</th>
											<th>Available Copies</th>
											<th>Total Copies</th>
										</tr>
									</thead>
									<tbody>
										<?php if ($stockRows): ?>
											<?php foreach ($stockRows as $row): ?>
											<tr>
												<td><?php echo htmlspecialchars($row['book_id']); ?></td>
												<td><?php echo htmlspecialchars($row['title'] ?? '-'); ?></td>
												<td><?php echo htmlspecialchars($row['category_name'] ?? '-'); ?></td>
												<td><?php echo (int) ($row['loaned_copies'] ?? 0); ?></td>
												<td><?php echo (int) ($row['pending_returns'] ?? 0); ?></td>
												<td><?php echo (int) ($row['available_copies'] ?? 0); ?></td>
												<td><?php echo (int) ($row['total_copies'] ?? 0); ?></td>
											</tr>
											<?php endforeach; ?>
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
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>
