<?php
// Load app configuration, database connection, and permissions helpers.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . "/app/includes/connection.php";
require_once ROOT_PATH . "/app/includes/permissions.php";

// Build the dashboard link based on RBAC settings.
$dashboardUrl = BASE_URL . rbac_dashboard_path($conn);

// Initialize collections for query results and UI state.
$recentBooks = [];
$allCategories = [];
$categoriesWithBooks = [];
$displayCategories = [];
$categoryBooks = [];
$selectedCategoryIds = [];
$categoryLimitOptions = [5, 10, 15, 20, 0];
$categoryLimit = 10;
$ebookResources = [];
$uniqueBooks = [];
$bookIds = [];
$availableByBook = [];
$reservedByBook = [];
// Resolve the current user id for personalized data.
$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

$recentPool = [];
// Fetch a pool of recent books with available copies.
$recentResult = $conn->query(
	"SELECT DISTINCT books.*, categories.category_name
	 FROM books
	 LEFT JOIN categories ON categories.category_id = books.category_id
	 JOIN book_editions ON book_editions.book_id = books.book_id
	 JOIN book_copies ON book_copies.edition_id = book_editions.edition_id
	 WHERE books.deleted_date IS NULL
	   AND (book_copies.status IS NULL OR book_copies.status = '' OR book_copies.status = 'available')
	 ORDER BY books.created_date DESC
	 LIMIT 30"
);
// Populate the recent books list when the query succeeds.
if ($recentResult) {
	// Collect recent book rows for randomization.
	while ($row = $recentResult->fetch_assoc()) {
		$recentPool[] = $row;
	}
	// Shuffle and take a smaller subset for display.
	shuffle($recentPool);
	$recentBooks = array_slice($recentPool, 0, 7);
}

// Load categories with book counts for filtering and shelves.
$categoryResult = $conn->query(
	"SELECT c.category_id, c.category_name,
			COUNT(b.book_id) AS book_count,
			MAX(b.created_date) AS latest_book_date
	 FROM categories c
	 LEFT JOIN books b ON b.category_id = c.category_id AND b.deleted_date IS NULL
	 GROUP BY c.category_id, c.category_name
	 ORDER BY c.category_name ASC"
);
// Build category metadata maps for filtering.
if ($categoryResult) {
	// Store each valid category id and metadata.
	while ($row = $categoryResult->fetch_assoc()) {
		$categoryId = (int) ($row['category_id'] ?? 0);
		// Ignore invalid category ids.
		if ($categoryId > 0) {
			$bookCount = (int) ($row['book_count'] ?? 0);
			$categoryData = [
				'id' => $categoryId,
				'name' => $row['category_name'] ?? '',
				'book_count' => $bookCount,
				'latest_book_date' => $row['latest_book_date'] ?? null,
			];
			$allCategories[$categoryId] = $categoryData;
			// Track categories that currently have books.
			if ($bookCount > 0) {
				$categoriesWithBooks[$categoryId] = $categoryData;
			}
		}
	}
}

// Parse selected category filters from the query string.
$rawSelectedCategories = $_GET['categories'] ?? [];
if (!is_array($rawSelectedCategories)) {
	$rawSelectedCategories = [$rawSelectedCategories];
}
// Keep only valid categories from the filter list.
foreach ($rawSelectedCategories as $rawCategoryId) {
	$categoryId = (int) $rawCategoryId;
	if ($categoryId > 0 && isset($allCategories[$categoryId])) {
		$selectedCategoryIds[$categoryId] = $categoryId;
	}
}

// Validate the category limit selection.
if (isset($_GET['category_limit'])) {
	$requestedLimit = (int) $_GET['category_limit'];
	if (in_array($requestedLimit, $categoryLimitOptions, true)) {
		$categoryLimit = $requestedLimit;
	}
}

// Determine which categories should be displayed.
if ($selectedCategoryIds) {
	// Use the user-selected categories when provided.
	foreach ($selectedCategoryIds as $categoryId) {
		$displayCategories[] = $allCategories[$categoryId];
	}
} else {
	// Default to categories that have books.
	$displayCategories = array_values($categoriesWithBooks);
}

// Sort categories by book count, recency, then name.
usort($displayCategories, function ($a, $b) {
	$countDiff = ($b['book_count'] ?? 0) <=> ($a['book_count'] ?? 0);
	if ($countDiff !== 0) {
		return $countDiff;
	}
	$dateA = (string) ($a['latest_book_date'] ?? '');
	$dateB = (string) ($b['latest_book_date'] ?? '');
	if ($dateA !== $dateB) {
		return strcmp($dateB, $dateA);
	}
	return strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
});

// Apply the category limit when configured.
if ($categoryLimit > 0 && count($displayCategories) > $categoryLimit) {
	$displayCategories = array_slice($displayCategories, 0, $categoryLimit);
}

// Fetch recent books for each displayed category shelf.
foreach ($displayCategories as $category) {
	$categoryId = (int) ($category['id'] ?? 0);
	// Initialize an empty list for categories without books.
	if ($categoryId <= 0) {
		continue;
	}
	$categoryBooks[$categoryId] = [];
	// Skip book queries for categories with no books.
	if (($category['book_count'] ?? 0) <= 0) {
		continue;
	}
	// Prepare a category-specific query.
	$stmt = $conn->prepare(
		"SELECT books.*, categories.category_name
		 FROM books
		 LEFT JOIN categories ON categories.category_id = books.category_id
		 WHERE books.category_id = ? AND books.deleted_date IS NULL
		 ORDER BY books.created_date DESC
		 LIMIT 10"
	);
	// Skip this category if the statement fails to prepare.
	if (!$stmt) {
		continue;
	}
	// Bind the category id and execute the query.
	$stmt->bind_param('i', $categoryId);
	$stmt->execute();
	$result = $stmt->get_result();
	$books = [];
	// Collect books returned for this category.
	while ($result && ($row = $result->fetch_assoc())) {
		$books[] = $row;
	}
	$stmt->close();
	// Store book lists (may be empty).
	$categoryBooks[$categoryId] = $books;
}

// Check if digital resources exist before querying ebooks.
$digitalCheck = $conn->query("SHOW TABLES LIKE 'digital_resources'");
// Load recent ebook resources when the table exists.
if ($digitalCheck && $digitalCheck->num_rows > 0) {
	// Prepare the ebook resources query.
	$ebookStmt = $conn->prepare(
		"SELECT resource_id, title, description, type
		 FROM digital_resources
		 WHERE deleted_date IS NULL
		 ORDER BY created_date DESC
		 LIMIT 10"
	);
	// Execute and collect ebook results.
	if ($ebookStmt) {
		$ebookStmt->execute();
		$ebookResult = $ebookStmt->get_result();
		// Build the ebook list for display.
		while ($ebookResult && ($row = $ebookResult->fetch_assoc())) {
			$ebookResources[] = $row;
		}
		$ebookStmt->close();
	}
}

// Build a unique set of books from recent results.
foreach ($recentBooks as $row) {
	$bookId = (int) ($row['book_id'] ?? 0);
	// Store only valid ids for later lookups.
	if ($bookId > 0) {
		$uniqueBooks[$bookId] = $row;
		$bookIds[$bookId] = $bookId;
	}
}

// Merge category books into the unique book set.
foreach ($categoryBooks as $books) {
	// Normalize each book entry into the unique map.
	foreach ($books as $row) {
		$bookId = (int) ($row['book_id'] ?? 0);
		// Store only valid ids for later lookups.
		if ($bookId > 0) {
			$uniqueBooks[$bookId] = $row;
			$bookIds[$bookId] = $bookId;
		}
	}
}

// Fetch available copy counts for each displayed book.
if ($bookIds) {
	$ids = array_values($bookIds);
	$placeholders = implode(',', array_fill(0, count($ids), '?'));
	$types = str_repeat('i', count($ids));
	$sql = "SELECT e.book_id, COUNT(*) AS available_count
		FROM book_copies c
		JOIN book_editions e ON c.edition_id = e.edition_id
		WHERE e.book_id IN ($placeholders)
		  AND (c.status IS NULL OR c.status = '' OR c.status = 'available')
		GROUP BY e.book_id";
	$availStmt = $conn->prepare($sql);
	// Execute the availability query if prepared.
	if ($availStmt) {
		$availStmt->bind_param($types, ...$ids);
		$availStmt->execute();
		$availResult = $availStmt->get_result();
		// Store available counts by book id.
		while ($availResult && ($row = $availResult->fetch_assoc())) {
			$availableByBook[(int) $row['book_id']] = (int) $row['available_count'];
		}
		$availStmt->close();
	}
}

// Fetch current user's reserved counts for each displayed book.
if ($bookIds && $userId > 0) {
	$ids = array_values($bookIds);
	$placeholders = implode(',', array_fill(0, count($ids), '?'));
	$types = 'i' . str_repeat('i', count($ids));
	$sql = "SELECT r.book_id, COUNT(*) AS reserved_count
		FROM reservations r
		JOIN book_copies c ON r.copy_id = c.copy_id
		WHERE r.user_id = ?
		  AND r.status = 'approved'
		  AND (r.expiry_date IS NULL OR r.expiry_date >= CURDATE())
		  AND c.status = 'reserved'
		  AND r.book_id IN ($placeholders)
		GROUP BY r.book_id";
	$resStmt = $conn->prepare($sql);
	// Execute the reservation query if prepared.
	if ($resStmt) {
		$params = array_merge([$userId], $ids);
		$resStmt->bind_param($types, ...$params);
		$resStmt->execute();
		$resResult = $resStmt->get_result();
		// Store reserved counts by book id.
		while ($resResult && ($row = $resResult->fetch_assoc())) {
			$reservedByBook[(int) $row['book_id']] = (int) $row['reserved_count'];
		}
		$resStmt->close();
	}
}

// Format display values with safe escaping and placeholders.
function display_value($value)
{
	// Return placeholder for null values.
	if ($value === null) {
		return "-";
	}
	// Normalize strings before checking emptiness.
	if (is_string($value)) {
		$value = trim($value);
	}
	// Return placeholder for empty strings.
	if ($value === "") {
		return "-";
	}
	// Escape output for safe HTML display.
	return htmlspecialchars((string) $value);
}

// Convert a byte size into a readable MB string.
function format_file_size($bytes)
{
	// Normalize the incoming size to an integer.
	$bytes = (int) $bytes;
	// Return placeholder for non-positive sizes.
	if ($bytes <= 0) {
		return "-";
	}
	// Convert to MB with two decimal places.
	$mb = $bytes / 1048576;
	return number_format($mb, 2) . " MB";
}

$alerts = [];
$loanStatus = $_GET['loan'] ?? '';
// Build loan status alerts from the query string.
if ($loanStatus !== '') {
	// Loan request succeeded.
	if ($loanStatus === 'success') {
		$alerts[] = ['success', 'Loan request submitted successfully.'];
	// Loan request failed due to availability.
	} elseif ($loanStatus === 'unavailable') {
		$alerts[] = ['warning', 'No copies are currently available for loan.'];
	// Loan request failed due to invalid input.
	} elseif ($loanStatus === 'invalid') {
		$alerts[] = ['warning', 'Invalid loan request.'];
	// Loan request failed due to a server error.
	} elseif ($loanStatus === 'error') {
		$alerts[] = ['danger', 'Loan request failed. Please try again.'];
	}
}

$reserveStatus = $_GET['reserve'] ?? '';
// Build reservation status alerts from the query string.
if ($reserveStatus !== '') {
	// Reservation request succeeded.
	if ($reserveStatus === 'success') {
		$alerts[] = ['success', 'Reservation request submitted successfully.'];
	// Reservation not needed when copies are available.
	} elseif ($reserveStatus === 'available') {
		$alerts[] = ['warning', 'Copies are available. Please request a loan instead of reserving.'];
	// Reservation failed due to no availability.
	} elseif ($reserveStatus === 'unavailable') {
		$alerts[] = ['warning', 'No copies are available to reserve at the moment.'];
	// Reservation failed due to invalid input.
	} elseif ($reserveStatus === 'invalid') {
		$alerts[] = ['warning', 'Invalid reservation request.'];
	// Reservation failed due to a server error.
	} elseif ($reserveStatus === 'error') {
		$alerts[] = ['danger', 'Reservation request failed. Please try again.'];
	}
}
?>
<!-- Home page layout and assets. -->
<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
	<meta charset="UTF-8" />
	<title>LMS - Group A</title>
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
		<!-- Sidebar toggle button. -->
		<button class="btn btn-icon" id="sidebarToggle">
			<i class="bi bi-list"></i>
		</button>

		<!-- Brand label. -->
		<span class="navbar-brand ms-2">LMS</span>

		<!-- Search bar with live suggestions. -->
		<div class="mx-auto search-wrap">
			<div class="search-container">
				<!-- Search form targets the results page. -->
				<form id="searchBox" class="search-box" action="<?php echo BASE_URL; ?>search_results.php" method="get" data-suggest-url="<?php echo BASE_URL; ?>actions/search_suggest.php" autocomplete="off">
					<i class="bi bi-binoculars-fill"></i>
					<input type="text" name="q" id="searchInput" placeholder="Type book or author name">
					<i class="bi bi-mic-fill"></i>
				</form>
				<!-- Suggestion dropdown container. -->
				<div id="searchSuggest" class="search-suggest"></div>
			</div>
		</div>

		<!-- User actions: logout and theme toggle. -->
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
			<!-- Home navigation links. -->
			<div class="sidebar-section">
				<small>HOME</small>
				<a class="active" href="<?php echo $dashboardUrl; ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
				<a><i class="bi bi-book"></i> All Books</a>
			</div>

			<!-- Category shortcuts. -->
			<div class="sidebar-section">
				<div class="d-flex align-items-center justify-content-between">
					<small>CATEGORIES</small>
					<!-- Category filter trigger. -->
					<button class="btn btn-icon btn-sm sidebar-filter-btn" type="button" data-bs-toggle="modal" data-bs-target="#categoryFilterModal" aria-label="Filter categories">
						<i class="bi bi-funnel"></i>
					</button>
				</div>
				<?php if ($displayCategories): ?>
					<!-- Render category links based on the current filter. -->
					<?php foreach ($displayCategories as $category): ?>
						<?php
							// Prepare sidebar link data for the category.
							$categoryId = (int) ($category['id'] ?? 0);
							$categoryName = $category['name'] ?? 'Category';
							$bookCount = (int) ($category['book_count'] ?? 0);
						?>
						<a href="<?php echo BASE_URL; ?>category_view.php?category_id=<?php echo $categoryId; ?>">
							<i class="bi bi-tag"></i> <?php echo htmlspecialchars($categoryName); ?> (<?php echo $bookCount; ?>)
						</a>
					<?php endforeach; ?>
				<?php else: ?>
					<span class="text-muted small">No categories available.</span>
				<?php endif; ?>
			</div>
		</aside>

		<!-- Main Content -->
		<main class="content">
			<!-- Alert messages for loan/reservation actions. -->
			<?php if ($alerts): ?>
			<div class="mb-3">
				<!-- Render each alert message. -->
				<?php foreach ($alerts as $alert): ?>
				<div class="alert alert-<?php echo htmlspecialchars($alert[0]); ?> mb-2">
					<?php echo htmlspecialchars($alert[1]); ?>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<!-- Recently added books shelf. -->
			<section>
				<div class="d-flex justify-content-between align-items-center mb-2">
					<h5 class="mb-0">Recently Added</h5>
				</div>
				<!-- Render recent books or an empty state. -->
				<?php if ($recentBooks): ?>
				<div class="book-row">
					<!-- Loop through recent books. -->
					<?php foreach ($recentBooks as $row): ?>
					<?php
						// Prepare cover data for the recent book card.
						$bookId = (int) ($row['book_id'] ?? 0);
						$cover = !empty($row['book_cover_path']) ? htmlspecialchars($row['book_cover_path']) : 'assets/img/book-cover.jpg';
					?>
					<div class="book-card">
						<img src="<?php echo $cover; ?>">
						<div class="book-overlay">
							<i class="bi bi-eye" data-bs-toggle="modal" data-bs-target="#bookModal<?php echo $bookId; ?>" title="View details"></i>
							<i class="bi bi-collection" title="Add to shelf"></i>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				<?php else: ?>
				<p class="text-muted">No books found.</p>
				<?php endif; ?>
			</section>

			<!-- Category shelves. -->
			<?php if ($displayCategories): ?>
				<?php foreach ($displayCategories as $category): ?>
					<?php
						// Prepare category section data.
						$categoryId = (int) ($category['id'] ?? 0);
						$categoryName = $category['name'] ?? 'Category';
						$books = $categoryBooks[$categoryId] ?? [];
					?>
					<section>
						<div class="d-flex justify-content-between align-items-center mb-2">
							<h5 class="mb-0"><?php echo htmlspecialchars($categoryName); ?></h5>
							<a href="<?php echo BASE_URL; ?>category_view.php?category_id=<?php echo $categoryId; ?>" class="btn btn-sm btn-outline-light">View All</a>
						</div>
						<!-- Render books for the category or an empty state. -->
						<?php if ($books): ?>
							<div class="book-row">
								<!-- Loop through books in this category. -->
								<?php foreach ($books as $row): ?>
								<?php
									// Prepare cover data for the category card.
									$bookId = (int) ($row['book_id'] ?? 0);
									$cover = !empty($row['book_cover_path']) ? htmlspecialchars($row['book_cover_path']) : 'assets/img/book-cover.jpg';
								?>
								<div class="book-card">
									<img src="<?php echo $cover; ?>">
									<div class="book-overlay">
										<i class="bi bi-eye" data-bs-toggle="modal" data-bs-target="#bookModal<?php echo $bookId; ?>" title="View details"></i>
										<i class="bi bi-collection" title="Add to shelf"></i>
									</div>
								</div>
								<?php endforeach; ?>
							</div>
						<?php else: ?>
							<p class="text-muted">No books yet.</p>
						<?php endif; ?>
					</section>
				<?php endforeach; ?>
			<?php else: ?>
				<p class="text-muted">No categories available.</p>
			<?php endif; ?>

			<!-- Ebook shelf. -->
			<?php if ($ebookResources): ?>
			<section>
				<div class="d-flex justify-content-between align-items-center mb-2">
					<h5 class="mb-0">Ebooks</h5>
					<a href="<?php echo BASE_URL; ?>category_view.php?type=ebook" class="btn btn-sm btn-outline-light">View All</a>
				</div>
				<div class="book-row">
					<!-- Loop through ebook resources. -->
					<?php foreach ($ebookResources as $row): ?>
					<div class="book-card">
						<img src="assets/img/book-cover.jpg" alt="Ebook cover">
					</div>
					<?php endforeach; ?>
				</div>
			</section>
			<?php endif; ?>

		</main>
	</div>

	<!-- Category filter modal. -->
	<div class="modal fade report-config-modal" id="categoryFilterModal" tabindex="-1" aria-labelledby="categoryFilterModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<form action="<?php echo BASE_URL; ?>home.php" method="get">
					<div class="modal-header">
						<h5 class="modal-title d-flex align-items-center gap-2" id="categoryFilterModalLabel">
							<i class="bi bi-funnel"></i>
							Category Filters
						</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="d-flex flex-wrap gap-3 mb-4">
							<button class="btn btn-glass btn-enable flex-fill" type="button" data-category-action="enable-all">
								<i class="bi bi-check-lg me-1"></i> Enable All
							</button>
							<button class="btn btn-glass btn-disable flex-fill" type="button" data-category-action="disable-all">
								<i class="bi bi-x-lg me-1"></i> Disable All
							</button>
							<button class="btn btn-glass btn-reset flex-fill" type="button" data-category-action="reset">
								<i class="bi bi-arrow-clockwise me-1"></i> Reset
							</button>
						</div>
						<div class="row g-4">
							<div class="col-md-7">
								<div class="section-title">CATEGORIES</div>
								<div class="divider"></div>
								<?php if ($allCategories): ?>
									<?php foreach ($allCategories as $category): ?>
										<?php
											// Prepare category option data for the filter.
											$categoryId = (int) ($category['id'] ?? 0);
											$categoryName = $category['name'] ?? 'Category';
											$bookCount = (int) ($category['book_count'] ?? 0);
											$isSelected = isset($selectedCategoryIds[$categoryId]);
										?>
										<div class="form-check mb-2">
											<input class="form-check-input category-check" type="checkbox" name="categories[]"
												id="category-option-<?php echo $categoryId; ?>" value="<?php echo $categoryId; ?>"
												<?php echo $isSelected ? 'checked' : ''; ?>>
											<label class="form-check-label" for="category-option-<?php echo $categoryId; ?>">
												<?php echo htmlspecialchars($categoryName); ?> (<?php echo $bookCount; ?>)
											</label>
										</div>
									<?php endforeach; ?>
								<?php else: ?>
									<p class="text-muted mb-0">No categories available.</p>
								<?php endif; ?>
							</div>
							<div class="col-md-5">
								<div class="section-title">DISPLAY</div>
								<div class="divider"></div>
								<label for="categoryLimit" class="form-label">Category limit</label>
								<select id="categoryLimit" name="category_limit" class="form-select">
									<?php foreach ($categoryLimitOptions as $limitOption): ?>
										<?php $label = $limitOption === 0 ? 'All' : (string) $limitOption; ?>
										<option value="<?php echo $limitOption; ?>" <?php echo $categoryLimit === $limitOption ? 'selected' : ''; ?>>
											<?php echo $label; ?>
										</option>
									<?php endforeach; ?>
								</select>
								<div class="form-text mt-2">
									Leave categories unchecked to show the top categories with books.
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-glass btn-enable" type="submit">Apply Filters</button>
						<a class="btn btn-glass btn-reset" href="<?php echo BASE_URL; ?>home.php">Reset</a>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Detail modals for each unique book. -->
	<?php foreach ($uniqueBooks as $row): ?>
	<?php
	// Compute per-book display values for the modal.
	$bookId = (int) ($row['book_id'] ?? 0);
	$cover = !empty($row['book_cover_path']) ? htmlspecialchars($row['book_cover_path']) : 'assets/img/book-cover.jpg';
	$reservedForUser = $reservedByBook[$bookId] ?? 0;
	$availableCopies = ($availableByBook[$bookId] ?? 0) + $reservedForUser;
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
									<h2 class="mb-0"><?php echo display_value($row["title"] ?? null); ?></h2>
									<i class="bi bi-unlock text-success fs-5"></i>
								</div>

								<a href="#" class="author-name"><?php echo display_value($row["author"] ?? null); ?></a>

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
										<div><strong>Category:</strong> <span class="text-success"><?php echo display_value($row["category_name"] ?? null); ?></span></div>
										<div><strong>Available Copies:</strong> <?php echo (int) $availableCopies; ?></div>
										<!-- Display reservation note when applicable. -->
										<?php if ($reservedForUser > 0): ?>
											<div class="text-warning fw-semibold">Reserved for you</div>
										<?php endif; ?>
										<div><strong>Published:</strong> <?php echo display_value($row["publication_year"] ?? null); ?></div>
										<div><strong>File Type:</strong> <span class="badge bg-primary"><?php echo $fileType !== '' ? $fileType : '-'; ?></span></div>
										<div><strong>Read Status:</strong> <span class="badge bg-secondary">UNSET</span> <i class="bi bi-pencil"></i></div>
										<div><strong>File Size:</strong> <span class="text-success"><?php echo $fileSize; ?></span></div>
										<div><strong>File Path:</strong> <?php echo $filePathLabel; ?></div>
									</div>

									<div class="col-md-6">
										<div><strong>Publisher:</strong> <?php echo display_value($row["publisher"] ?? null); ?></div>
										<div><strong>Language:</strong> <span class="text-success">-</span></div>
										<div><strong>ISBN:</strong> <?php echo display_value($row["isbn"] ?? null); ?></div>
										<div><strong>Excerpt:</strong> <?php echo display_value($row["book_excerpt"] ?? null); ?></div>
									</div>
								</div>

								<div class="book-actions mt-4">
									<!-- Loan request action. -->
									<form action="<?php echo BASE_URL; ?>actions/request_loan.php" method="post" class="d-inline">
										<input type="hidden" name="book_id" value="<?php echo $bookId; ?>">
										<button class="btn btn-outline-info" type="submit" <?php echo $bookId <= 0 ? 'disabled' : ''; ?>>
											<i class="bi bi-box-arrow-in-right"></i> Request Loan
										</button>
									</form>
									<!-- Reservation request action. -->
									<form action="<?php echo BASE_URL; ?>actions/request_reservation.php" method="post" class="d-inline">
										<input type="hidden" name="book_id" value="<?php echo $bookId; ?>">
										<button class="btn btn-outline-secondary" type="submit" <?php echo ($bookId <= 0 || $availableCopies > 0) ? 'disabled' : ''; ?>>
											<i class="bi bi-bookmark-plus"></i> Reserve
										</button>
									</form>
									<!-- Read and shelf quick actions. -->
									<button class="btn btn-outline-success" <?php echo $canRead ? '' : 'disabled'; ?>>
										<i class="bi bi-book"></i> Read
									</button>
									<button class="btn btn-outline-primary">
										<i class="bi bi-folder"></i> Shelf
									</button>
									<!-- Conditional download action for ebooks. -->
									<?php if ($canDownload): ?>
									<a class="btn btn-outline-success" href="<?php echo BASE_URL; ?>actions/download_ebook.php?book_id=<?php echo $bookId; ?>">
										<i class="bi bi-download"></i> Download
									</a>
									<?php else: ?>
									<button class="btn btn-outline-success" disabled>
										<i class="bi bi-download"></i> Download
									</button>
									<?php endif; ?>
									<!-- Utility actions dropdown. -->
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
	</div>
	<?php endforeach; ?>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

	<!-- App JS -->
	<script src="assets/js/home.js"></script>
</body>

</html>
