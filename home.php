<?php
require_once __DIR__ . "/include/config.php";
require_once ROOT_PATH . "/include/connection.php";

$book = null;
$availableCopies = 0;
$bookResult = $conn->query(
	"SELECT books.*, categories.category_name
	 FROM books
	 LEFT JOIN categories ON categories.category_id = books.category_id
	 ORDER BY books.book_id DESC
	 LIMIT 1"
);
if ($bookResult && $bookResult->num_rows > 0) {
	$book = $bookResult->fetch_assoc();
	$bookId = (int) ($book['book_id'] ?? 0);
	if ($bookId > 0) {
		$copyResult = $conn->query(
			"SELECT COUNT(*) AS available_count
			 FROM book_copies c
			 JOIN book_editions e ON c.edition_id = e.edition_id
			 WHERE e.book_id = $bookId
			   AND (c.status IS NULL OR c.status = '' OR c.status = 'available')"
		);
		if ($copyResult && $copyResult->num_rows > 0) {
			$availableRow = $copyResult->fetch_assoc();
			$availableCopies = (int) ($availableRow['available_count'] ?? 0);
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

$coverPath = $book && !empty($book["book_cover_path"])
	? htmlspecialchars($book["book_cover_path"])
	: "assets/img/book-cover.jpg";
$alerts = [];
$loanStatus = $_GET['loan'] ?? '';
if ($loanStatus !== '') {
	if ($loanStatus === 'success') {
		$alerts[] = ['success', 'Loan request submitted successfully.'];
	} elseif ($loanStatus === 'unavailable') {
		$alerts[] = ['warning', 'No copies are currently available for loan.'];
	} elseif ($loanStatus === 'invalid') {
		$alerts[] = ['warning', 'Invalid loan request.'];
	} elseif ($loanStatus === 'error') {
		$alerts[] = ['danger', 'Loan request failed. Please try again.'];
	}
}

$reserveStatus = $_GET['reserve'] ?? '';
if ($reserveStatus !== '') {
	if ($reserveStatus === 'success') {
		$alerts[] = ['success', 'Reservation request submitted successfully.'];
	} elseif ($reserveStatus === 'available') {
		$alerts[] = ['warning', 'Copies are available. Please request a loan instead of reserving.'];
	} elseif ($reserveStatus === 'unavailable') {
		$alerts[] = ['warning', 'No copies are available to reserve at the moment.'];
	} elseif ($reserveStatus === 'invalid') {
		$alerts[] = ['warning', 'Invalid reservation request.'];
	} elseif ($reserveStatus === 'error') {
		$alerts[] = ['danger', 'Reservation request failed. Please try again.'];
	}
}
?>
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
		<button class="btn btn-icon" id="sidebarToggle">
			<i class="bi bi-list"></i>
		</button>

		<span class="navbar-brand ms-2">LMS</span>

		<div class="mx-auto search-wrap">
			<div id="searchBox" class="search-box">
				<i class="bi bi-binoculars-fill"></i>
				<input type="text" placeholder="Type book or author name">
				<i class="bi bi-mic-fill"></i>
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
				<a class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
				<a><i class="bi bi-book"></i> All Books</a>
			</div>

			<div class="sidebar-section">
				<small>LIBRARIES</small>
				<a><i class="bi bi-journal-bookmark"></i> Novels</a>
				<a><i class="bi bi-cpu"></i> Technology</a>
				<a><i class="bi bi-brush"></i> Comics</a>
				<a href="dashboard.php"><i class="bi bi-brush"></i> Admin Dashboard</a>
			</div>
		</aside>

		<!-- Main Content -->
		<main class="content">
			<?php if ($alerts): ?>
			<div class="mb-3">
				<?php foreach ($alerts as $alert): ?>
				<div class="alert alert-<?php echo htmlspecialchars($alert[0]); ?> mb-2">
					<?php echo htmlspecialchars($alert[1]); ?>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<!-- Recently added book -->
			<section>
				<h5>Recently Added</h5>
				<!-- start::book row -->
				<div class="book-row">
					<!-- start:book card -->
					<div class="book-card">
						<img src="<?php echo $coverPath; ?>">
						<div class="book-overlay">
							<i class="bi bi-eye" data-bs-toggle="modal" data-bs-target="#exampleModal" title="View details"></i>
							<i class="bi bi-collection" title="Add to shelf"></i>
						</div>
					</div>
					<!-- end::book card -->
				</div>
				<!-- end::book row -->
			</section>

		</main>
	</div>

	<!-- Modal -->
	<div class="modal fade book-details-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-body p-0">
					<div class="book-details-card">

						<div class="book-details-header">
							<div class="d-flex align-items-center gap-3">
								<span class="text-success fw-semibold">
									<i class="bi bi-journal-text"></i> Book Details
								</span>
								<!-- <span>
									<i class="bi bi-pencil"></i> Edit Metadata
								</span>
								<span>
									<i class="bi bi-search"></i> Search Metadata
								</span> -->
							</div>
							<button type="button" class="btn-close close-book-detail" data-bs-dismiss="modal"
								aria-label="Close"></button>
						</div>

						<div class="book-details-body row g-4">

							<div class="col-md-3 text-center">
								<img src="<?php echo $coverPath; ?>" class="book-cover" alt="Book cover">
							</div>

							<div class="col-md-9">
								<div class="d-flex align-items-center gap-2">
									<h2 class="mb-0"><?php echo display_value($book["title"] ?? null); ?></h2>
									<i class="bi bi-unlock text-success fs-5"></i>
								</div>

								<a href="#" class="author-name"><?php echo display_value($book["author"] ?? null); ?></a>

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
										<div><strong>Category:</strong> <span
												class="text-success"><?php echo display_value($book["category_name"] ?? null); ?></span></div>
										<div><strong>Available Copies:</strong> <?php echo $availableCopies; ?></div>
										<div><strong>Published:</strong> <?php echo display_value($book["publication_year"] ?? null); ?>
										</div>
										<div><strong>File Type:</strong> <span class="badge bg-primary">-</span></div>
										<div><strong>Read Status:</strong> <span class="badge bg-secondary">UNSET</span> <i
												class="bi bi-pencil"></i></div>
										<div><strong>File Size:</strong> <span class="text-success">-</span></div>
										<div><strong>File Path:</strong> -</div>
									</div>

									<div class="col-md-6">
										<div><strong>Publisher:</strong> <?php echo display_value($book["publisher"] ?? null); ?></div>
										<div><strong>Language:</strong> <span class="text-success">-</span></div>
										<div><strong>ISBN:</strong> <?php echo display_value($book["isbn"] ?? null); ?></div>
										<div><strong>Excerpt:</strong> <?php echo display_value($book["book_excerpt"] ?? null); ?></div>
									</div>
								</div>

								<div class="book-actions mt-4">
									<form action="<?php echo BASE_URL; ?>actions/request_loan.php" method="post" class="d-inline">
										<input type="hidden" name="book_id" value="<?php echo (int) ($book['book_id'] ?? 0); ?>">
										<button class="btn btn-outline-info" type="submit" <?php echo empty($book) ? 'disabled' : ''; ?>>
											<i class="bi bi-box-arrow-in-right"></i> Request Loan
										</button>
									</form>
									<form action="<?php echo BASE_URL; ?>actions/request_reservation.php" method="post" class="d-inline">
										<input type="hidden" name="book_id" value="<?php echo (int) ($book['book_id'] ?? 0); ?>">
										<button class="btn btn-outline-secondary" type="submit" <?php echo (empty($book) || $availableCopies > 0) ? 'disabled' : ''; ?>>
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
										<button class="btn btn-outline-warning dropdown-toggle dropdown-toggle-split"
											data-bs-toggle="dropdown"></button>
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

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

	<!-- App JS -->
	<script src="assets/js/home.js"></script>
</body>

</html>
