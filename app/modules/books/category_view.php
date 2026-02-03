<?php
// Load core configuration, database connection, and RBAC helpers.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . "/app/includes/connection.php";
require_once ROOT_PATH . "/app/includes/permissions.php";

// Resolve the appropriate dashboard link based on role.
$dashboardUrl = BASE_URL . rbac_dashboard_path($conn);

// Read category and type filters from the query string.
$categoryId = (int) ($_GET['category_id'] ?? 0);
$type = strtolower(trim($_GET['type'] ?? ''));

// Initialize data containers for the view.
$categoryName = '';
$books = [];
$availableByBook = [];
$ebookResources = [];

// Load category details and book list when a category is selected.
if ($categoryId > 0) {
	$nameStmt = $conn->prepare("SELECT category_name FROM categories WHERE category_id = ?");
	if ($nameStmt) {
		$nameStmt->bind_param('i', $categoryId);
		$nameStmt->execute();
		$nameResult = $nameStmt->get_result();
		if ($nameResult && ($row = $nameResult->fetch_assoc())) {
			$categoryName = $row['category_name'] ?? '';
		}
		$nameStmt->close();
	}

	// Fetch books for the selected category.
	$stmt = $conn->prepare(
		"SELECT books.*, categories.category_name
		 FROM books
		 LEFT JOIN categories ON categories.category_id = books.category_id
		 WHERE books.category_id = ? AND books.deleted_date IS NULL
		 ORDER BY books.title ASC"
	);
	if ($stmt) {
		$stmt->bind_param('i', $categoryId);
		$stmt->execute();
		$result = $stmt->get_result();
		while ($result && ($row = $result->fetch_assoc())) {
			$books[] = $row;
		}
		$stmt->close();
	}

	// Count available copies per book for the summary badge.
	if ($books) {
		$bookIds = array_map(
			static fn($row) => (int) ($row['book_id'] ?? 0),
			$books
		);
		$bookIds = array_values(array_filter($bookIds));
		if ($bookIds) {
			$placeholders = implode(',', array_fill(0, count($bookIds), '?'));
			$types = str_repeat('i', count($bookIds));
			$sql = "SELECT e.book_id, COUNT(*) AS available_count
				FROM book_copies c
				JOIN book_editions e ON c.edition_id = e.edition_id
				WHERE e.book_id IN ($placeholders)
				  AND (c.status IS NULL OR c.status = '' OR c.status = 'available')
				GROUP BY e.book_id";
			$availStmt = $conn->prepare($sql);
			if ($availStmt) {
				$availStmt->bind_param($types, ...$bookIds);
				$availStmt->execute();
				$availResult = $availStmt->get_result();
				while ($availResult && ($row = $availResult->fetch_assoc())) {
					$availableByBook[(int) $row['book_id']] = (int) $row['available_count'];
				}
				$availStmt->close();
			}
		}
	}
} elseif ($type === 'ebook') {
	// Load ebook resources when switching to ebook view.
	$digitalCheck = $conn->query("SHOW TABLES LIKE 'digital_resources'");
	if ($digitalCheck && $digitalCheck->num_rows > 0) {
		$ebookStmt = $conn->prepare(
			"SELECT resource_id, title, description, type
			 FROM digital_resources
			 WHERE deleted_date IS NULL
			 ORDER BY created_date DESC"
		);
		if ($ebookStmt) {
			$ebookStmt->execute();
			$ebookResult = $ebookStmt->get_result();
			while ($ebookResult && ($row = $ebookResult->fetch_assoc())) {
				$ebookResources[] = $row;
			}
			$ebookStmt->close();
		}
	}
}

// Render safe values with default placeholders.
function display_value($value)
{
	if ($value === null) {
		return "-";
	}
	if (is_string($value)) {
		$value = trim($value);
	}
	if ($value === "") {
		return "-";
	}
	return htmlspecialchars((string) $value);
}

// Format file sizes into MB labels.
function format_file_size($bytes)
{
	$bytes = (int) $bytes;
	if ($bytes <= 0) {
		return "-";
	}
	$mb = $bytes / 1048576;
	return number_format($mb, 2) . " MB";
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
	<meta charset="UTF-8" />
	<title>LMS - View All</title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<!-- Bootstrap -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Bootstrap Icons -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link rel="stylesheet" href="assets/css/home.css">
</head>

<body class="app-body">

	<!-- Top Navbar -->
	<nav class="navbar navbar-dark fixed-top px-3">
		<button class="btn btn-icon" id="sidebarToggle">
			<i class="bi bi-list"></i>
		</button>

		<span class="navbar-brand ms-2">LMS</span>

		<div class="mx-auto search-wrap">
			<div class="search-container">
				<form id="searchBox" class="search-box" action="<?php echo BASE_URL; ?>search_results.php" method="get" data-suggest-url="<?php echo BASE_URL; ?>actions/search_suggest.php" autocomplete="off">
					<i class="bi bi-binoculars-fill"></i>
					<input type="text" name="q" id="searchInput" placeholder="Type book or author name">
					<i class="bi bi-mic-fill"></i>
				</form>
				<div id="searchSuggest" class="search-suggest"></div>
			</div>
		</div>

		<div class="d-flex align-items-center gap-2">
			<a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-outline-light btn-sm">Logout</a>
			<button class="btn btn-icon" id="themeToggle">
				<i class="bi bi-moon"></i>
			</button>
		</div>
	</nav>

	<!-- Layout -->
	<div class="layout">

		<!-- Sidebar -->
		<aside id="sidebar" class="sidebar">
			<div class="sidebar-section">
				<small>HOME</small>
				<a class="active" href="<?php echo $dashboardUrl; ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
				<a><i class="bi bi-book"></i> All Books</a>
			</div>

			<div class="sidebar-section">
				<small>LIBRARIES</small>
				<a><i class="bi bi-journal-bookmark"></i> Novels</a>
				<a><i class="bi bi-cpu"></i> Technology</a>
				<a><i class="bi bi-brush"></i> Comics</a>
				<a href="<?php echo $dashboardUrl; ?>"><i class="bi bi-brush"></i> Dashboard</a>
			</div>
		</aside>

		<!-- Main Content -->
		<main class="content">
			<section>
				<div class="d-flex justify-content-between align-items-center mb-2">
					<h5 class="mb-0">
						<?php
						// Choose title based on current filter.
						if ($categoryId > 0) {
							echo htmlspecialchars($categoryName !== '' ? $categoryName : 'Category');
						} elseif ($type === 'ebook') {
							echo 'Ebooks';
						} else {
							echo 'View All';
						}
						?>
					</h5>
					<a href="<?php echo BASE_URL; ?>home.php" class="btn btn-sm btn-outline-light">Back to Home</a>
				</div>

				<?php // Show appropriate content for category or ebook views. ?>
				<?php if ($categoryId <= 0 && $type !== 'ebook'): ?>
					<p class="text-muted">Pick a category from the Home page to see all books.</p>
				<?php elseif ($categoryId > 0): ?>
					<?php if (!$books): ?>
						<p class="text-muted">No books found in this category.</p>
					<?php else: ?>
						<div class="book-grid">
							<?php // Render each book card for the category. ?>
							<?php foreach ($books as $row): ?>
							<?php
								// Prepare cover and identifiers for the card.
								$cover = !empty($row['book_cover_path']) ? htmlspecialchars($row['book_cover_path']) : 'assets/img/book-cover.jpg';
								$bookId = (int) ($row['book_id'] ?? 0);
							?>
							<div class="book-item">
								<div class="book-card">
									<img src="<?php echo $cover; ?>" alt="Book cover">
									<div class="book-overlay">
										<i class="bi bi-eye" data-bs-toggle="modal" data-bs-target="#bookModal<?php echo $bookId; ?>" title="View details"></i>
										<i class="bi bi-collection" title="Add to shelf"></i>
									</div>
								</div>
								<div class="book-meta">
									<div class="book-title"><?php echo display_value($row['title'] ?? null); ?></div>
									<div class="book-author"><?php echo display_value($row['author'] ?? null); ?></div>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				<?php else: ?>
					<?php if (!$ebookResources): ?>
						<p class="text-muted">No ebooks available right now.</p>
					<?php else: ?>
						<div class="book-grid">
							<?php // Render each ebook resource card. ?>
							<?php foreach ($ebookResources as $row): ?>
							<div class="book-item">
								<div class="book-card">
									<img src="assets/img/book-cover.jpg" alt="Ebook cover">
								</div>
								<div class="book-meta">
									<div class="book-title"><?php echo display_value($row['title'] ?? null); ?></div>
									<div class="book-author"><?php echo display_value($row['type'] ?? null); ?></div>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</section>
		</main>
	</div>

	<?php // Render book detail modals for category items. ?>
	<?php if ($books): ?>
	<?php foreach ($books as $row): ?>
	<?php
	// Prepare modal-specific fields for the book.
	$bookId = (int) ($row['book_id'] ?? 0);
	$availableCopies = $availableByBook[$bookId] ?? 0;
	$cover = !empty($row['book_cover_path']) ? htmlspecialchars($row['book_cover_path']) : 'assets/img/book-cover.jpg';
	$bookType = strtolower($row['book_type'] ?? 'physical');
	$isEbook = $bookType === 'ebook';
	$fileType = $isEbook ? strtoupper((string) ($row['ebook_format'] ?? '')) : '';
	$fileSize = $isEbook ? format_file_size((int) ($row['ebook_file_size'] ?? 0)) : '-';
	$filePathLabel = $isEbook ? display_value(basename((string) ($row['ebook_file_path'] ?? ''))) : '-';
	$canDownload = $isEbook && !empty($row['ebook_file_path']);
	$canRead = $canDownload;
?>
	<div class="modal fade book-details-modal" id="bookModal<?php echo $bookId; ?>" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-body p-0">
					<div class="book-details-card">
						<div class="book-details-header">
							<div class="d-flex align-items-center gap-3">
								<span class="text-success fw-semibold">
									<i class="bi bi-journal-text"></i> Book Details
								</span>
							</div>
							<button type="button" class="btn-close close-book-detail" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>

						<div class="book-details-body row g-4">
							<div class="col-md-3 text-center">
								<img src="<?php echo $cover; ?>" class="book-cover" alt="Book cover">
							</div>

							<div class="col-md-9">
								<div class="d-flex align-items-center gap-2">
									<h2 class="mb-0"><?php echo display_value($row['title'] ?? null); ?></h2>
									<i class="bi bi-unlock text-success fs-5"></i>
								</div>

								<a href="#" class="author-name"><?php echo display_value($row['author'] ?? null); ?></a>

								<div class="rating-row mt-2">
									<i class="bi bi-hand-thumbs-up-fill text-warning"></i>
									<div class="stars">
										<i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i>
										<i class="bi bi-star"></i><i class="bi bi-star"></i>
										<i class="bi bi-star"></i><i class="bi bi-star"></i>
										<i class="bi bi-star"></i><i class="bi bi-star"></i>
									</div>
									<i class="bi bi-arrow-clockwise text-warning"></i>
								</div>

								<div class="row mt-4 small">
									<div class="col-md-6">
										<div><strong>Category:</strong> <span class="text-success"><?php echo display_value($row['category_name'] ?? null); ?></span></div>
										<div><strong>Available Copies:</strong> <?php echo (int) $availableCopies; ?></div>
										<div><strong>Published:</strong> <?php echo display_value($row['publication_year'] ?? null); ?></div>
										<div><strong>File Type:</strong> <span class="badge bg-primary"><?php echo $fileType !== '' ? $fileType : '-'; ?></span></div>
										<div><strong>Read Status:</strong> <span class="badge bg-secondary">UNSET</span> <i class="bi bi-pencil"></i></div>
										<div><strong>File Size:</strong> <span class="text-success"><?php echo $fileSize; ?></span></div>
										<div><strong>File Path:</strong> <?php echo $filePathLabel; ?></div>
									</div>

									<div class="col-md-6">
										<div><strong>Publisher:</strong> <?php echo display_value($row['publisher'] ?? null); ?></div>
										<div><strong>Language:</strong> <span class="text-success">-</span></div>
										<div><strong>ISBN:</strong> <?php echo display_value($row['isbn'] ?? null); ?></div>
										<div><strong>Excerpt:</strong> <?php echo display_value($row['book_excerpt'] ?? null); ?></div>
									</div>
								</div>

								<div class="book-actions mt-4">
									<?php // Action buttons for loan/reservation and ebook access. ?>
									<form action="<?php echo BASE_URL; ?>actions/request_loan.php" method="post" class="d-inline">
										<input type="hidden" name="book_id" value="<?php echo $bookId; ?>">
										<button class="btn btn-outline-info" type="submit" <?php echo $bookId <= 0 ? 'disabled' : ''; ?>>
											<i class="bi bi-box-arrow-in-right"></i> Request Loan
										</button>
									</form>
									<form action="<?php echo BASE_URL; ?>actions/request_reservation.php" method="post" class="d-inline">
										<input type="hidden" name="book_id" value="<?php echo $bookId; ?>">
										<button class="btn btn-outline-secondary" type="submit" <?php echo ($bookId <= 0 || $availableCopies > 0) ? 'disabled' : ''; ?>>
											<i class="bi bi-bookmark-plus"></i> Reserve
										</button>
									</form>
									<button class="btn btn-outline-success" <?php echo $canRead ? '' : 'disabled'; ?>>
										<i class="bi bi-book"></i> Read
									</button>
									<button class="btn btn-outline-primary">
										<i class="bi bi-folder"></i> Shelf
									</button>
									<?php // Show download link only for available ebooks. ?>
									<?php if ($canDownload): ?>
									<a class="btn btn-outline-success" href="<?php echo BASE_URL; ?>actions/download_ebook.php?book_id=<?php echo $bookId; ?>">
										<i class="bi bi-download"></i> Download
									</a>
									<?php else: ?>
									<button class="btn btn-outline-success" disabled>
										<i class="bi bi-download"></i> Download
									</button>
									<?php endif; ?>
									<div class="btn-group">
										<button class="btn btn-outline-warning">
											<i class="bi bi-lightning"></i> Fetch
										</button>
										<button class="btn btn-outline-warning dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
										<ul class="dropdown-menu dropdown-menu-dark">
											<li><a class="dropdown-item" href="#">Fetch Cover</a></li>
											<li><a class="dropdown-item" href="#">Fetch Metadata</a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	<?php endif; ?>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
	<!-- App JS -->
	<script src="assets/js/home.js"></script>
</body>

</html>
