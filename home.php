<?php
require_once __DIR__ . "/include/config.php";
require_once ROOT_PATH . "/include/connection.php";

$book = null;
$bookResult = $conn->query(
	"SELECT books.*, categories.category_name
	 FROM books
	 LEFT JOIN categories ON categories.category_id = books.category_id
	 ORDER BY books.book_id DESC
	 LIMIT 1"
);
if ($bookResult && $bookResult->num_rows > 0) {
	$book = $bookResult->fetch_assoc();
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