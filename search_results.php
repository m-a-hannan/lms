<?php
require_once __DIR__ . "/include/config.php";
require_once ROOT_PATH . "/include/connection.php";
require_once ROOT_PATH . "/include/permissions.php";

$dashboardUrl = BASE_URL . rbac_dashboard_path($conn);
$context = rbac_get_context($conn);
$currentUserId = (int) ($context['user_id'] ?? 0);

$query = trim($_GET['q'] ?? '');
$results = [];
$availableByBook = [];

if ($query !== '') {
	$like = '%' . $query . '%';
	$stmt = $conn->prepare(
		"SELECT books.*, categories.category_name
		 FROM books
		 LEFT JOIN categories ON categories.category_id = books.category_id
		 WHERE books.title LIKE ? OR books.author LIKE ?
		 ORDER BY books.title ASC
		 LIMIT 60"
	);
	if ($stmt) {
		$stmt->bind_param('ss', $like, $like);
		$stmt->execute();
		$result = $stmt->get_result();
		while ($result && ($row = $result->fetch_assoc())) {
			$results[] = $row;
		}
		$stmt->close();
	}

	if ($results) {
		$bookIds = array_map(
			static fn($row) => (int) ($row['book_id'] ?? 0),
			$results
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

	$logCheck = $conn->query("SHOW TABLES LIKE 'search_logs'");
	if ($logCheck && $logCheck->num_rows > 0) {
		$logStmt = $conn->prepare(
			"INSERT INTO search_logs (user_id, query_text, results_count, created_at)
			 VALUES (?, ?, ?, NOW())"
		);
		if ($logStmt) {
			$resultsCount = count($results);
			$userId = $currentUserId > 0 ? $currentUserId : null;
			$logStmt->bind_param('isi', $userId, $query, $resultsCount);
			$logStmt->execute();
			$logStmt->close();
		}
	}
}

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
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
	<meta charset="UTF-8" />
	<title>LMS - Search</title>
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
					<input type="text" name="q" id="searchInput" value="<?php echo htmlspecialchars($query); ?>" placeholder="Type book or author name">
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
				<h5>Search Results</h5>
				<?php if ($query === ''): ?>
					<p class="text-muted">Type a book title or author name to search.</p>
				<?php elseif (!$results): ?>
					<p class="text-muted">No results found for "<?php echo htmlspecialchars($query); ?>".</p>
				<?php else: ?>
					<p class="text-muted">Found <?php echo count($results); ?> result(s) for "<?php echo htmlspecialchars($query); ?>".</p>
					<div class="book-grid">
						<?php foreach ($results as $row): ?>
						<?php
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
			</section>
		</main>
	</div>

	<?php if ($results): ?>
	<?php foreach ($results as $row): ?>
	<?php
		$bookId = (int) ($row['book_id'] ?? 0);
		$availableCopies = $availableByBook[$bookId] ?? 0;
		$cover = !empty($row['book_cover_path']) ? htmlspecialchars($row['book_cover_path']) : 'assets/img/book-cover.jpg';
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
										<div><strong>File Type:</strong> <span class="badge bg-primary">-</span></div>
										<div><strong>Read Status:</strong> <span class="badge bg-secondary">UNSET</span> <i class="bi bi-pencil"></i></div>
										<div><strong>File Size:</strong> <span class="text-success">-</span></div>
										<div><strong>File Path:</strong> -</div>
									</div>

									<div class="col-md-6">
										<div><strong>Publisher:</strong> <?php echo display_value($row['publisher'] ?? null); ?></div>
										<div><strong>Language:</strong> <span class="text-success">-</span></div>
										<div><strong>ISBN:</strong> <?php echo display_value($row['isbn'] ?? null); ?></div>
										<div><strong>Excerpt:</strong> <?php echo display_value($row['book_excerpt'] ?? null); ?></div>
									</div>
								</div>

								<div class="book-actions mt-4">
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
									<button class="btn btn-outline-success">
										<i class="bi bi-book"></i> Read
									</button>
									<button class="btn btn-outline-primary">
										<i class="bi bi-folder"></i> Shelf
									</button>
									<button class="btn btn-outline-success">
										<i class="bi bi-download"></i> Download
									</button>
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
	<?php endif; ?>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
	<!-- App JS -->
	<script src="assets/js/home.js"></script>
</body>

</html>
