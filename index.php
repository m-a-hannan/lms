<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Library Management System</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap 5 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Bootstrap 5 Icons -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

	<!-- Custom CSS -->
	<link rel="stylesheet" href="style.css">
</head>

<body>

	<!-- ================= NAVBAR ================= -->
	<nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top">
		<div class="container">
			<a class="navbar-brand fw-bold" href="#">
				<span class="logo-dot"></span> Library
			</a>

			<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navMenu">
				<ul class="navbar-nav mx-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
					<li class="nav-item"><a class="nav-link" href="#">Managers</a></li>
					<li class="nav-item"><a class="nav-link" href="#">Members</a></li>
					<li class="nav-item"><a class="nav-link" href="#">Sale</a></li>
				</ul>

				<a href="dashboard.php" class="btn btn-gradient px-4"><i class="nav-icon bi bi-speedometer"></i> Dashboard</a>
			</div>
		</div>
	</nav>

	<!-- ================= HERO SECTION ================= -->
	<section class="hero-section">
		<div class="overlay"></div>

		<div class="container hero-content text-center">
			<h1 class="fw-bold">
				Welcome to Library<br>Management System,
			</h1>
			<p class="text-light mt-3">
				Your gateway to knowledge and learning.
			</p>
			<a href="dashboard.php" class="btn btn-gradient btn-lg mt-3"><i class="nav-icon bi bi-speedometer"></i>
				Dashboard</a>

			<!-- Action Pills -->
			<div class="action-bar mt-5 shadow">
				<div class="action-pill">
					🔍 <span>Search Books</span>
				</div>
				<div class="action-pill green">
					👤 <span>Manage Members</span>
				</div>
				<div class="action-pill red">
					⚖️ <span>Issue & Return</span>
				</div>
				<div class="action-pill">
					📊 <span>Reports & Analytics</span>
				</div>
			</div>
		</div>
	</section>

	<!-- ================= BOOK GRID ================= -->
	<section class="py-5 bg-light">
		<div class="container">
			<div class="row g-4">

				<!-- Book Card -->
				<div class="col-md-3 col-sm-6">
					<div class="book-card">
						<img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c" alt="">
					</div>
				</div>

				<div class="col-md-3 col-sm-6">
					<div class="book-card">
						<img src="https://images.unsplash.com/photo-1512820790803-83ca734da794" alt="">
					</div>
				</div>

				<div class="col-md-3 col-sm-6">
					<div class="book-card">
						<img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f" alt="">
					</div>
				</div>

				<div class="col-md-3 col-sm-6">
					<div class="book-card">
						<img src="https://images.unsplash.com/photo-1516979187457-637abb4f9353" alt="">
					</div>
				</div>

			</div>
		</div>

		<!-- Floating Add Button -->
		<button class="add-btn">+</button>
	</section>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

	<!-- Custom JS -->
	<!-- <script src="script.js"></script> -->
	<script>
	// Placeholder for future interactions
	document.querySelector('.add-btn').addEventListener('click', () => {
		alert("Add new book feature coming soon!");
	});
	</script>
</body>

</html>