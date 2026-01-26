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
			<a class="navbar-brand fw-bold" href="home.php">
				<span class="logo-dot"></span> Library
			</a>

			<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navMenu">
				<ul class="navbar-nav mx-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
					<li class="nav-item"><a class="nav-link" href="home.php">Library</a></li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							Catalog
						</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="book_list.php">Books</a></li>
							<li><a class="dropdown-item" href="category_list.php">Categories</a></li>
							<li><a class="dropdown-item" href="book_edition_list.php">Editions</a></li>
							<li><a class="dropdown-item" href="book_copy_list.php">Copies</a></li>
						</ul>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							Users
						</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="user_list.php">Users</a></li>
							<li><a class="dropdown-item" href="user_profile_list.php">Profiles</a></li>
							<li><a class="dropdown-item" href="role_list.php">Roles</a></li>
						</ul>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							Operations
						</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="loan_list.php">Loans</a></li>
							<li><a class="dropdown-item" href="reservation_list.php">Reservations</a></li>
							<li><a class="dropdown-item" href="return_list.php">Returns</a></li>
						</ul>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							Systems
						</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="erd.php">ERD</a></li>
							<li><a class="dropdown-item" href="library_rbac_matrix.php">RBAC</a></li>
							<li><a class="dropdown-item" href="audit_log_list.php">Audit Logs</a></li>
						</ul>
					</li>
				</ul>
				<div class="d-flex align-items-center gap-2">
					<button class="btn btn-gradient px-4" id="loginOpen" type="button">
						<i class="bi bi-box-arrow-in-right me-1"></i>
						Login
					</button>
				</div>
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

		<!--  -->
		<section class="container my-5">
			<div class="row g-4">
				<!-- New Arrivals -->
				<div class="col-md-6">
					<h4 class="mb-3">Latest Transactions</h4>
					<ul class="list-group">
						<li class="list-group-item">
							<span class="badge bg-success me-2">Issued</span>
							"The Alchemist" to Rohan
						</li>
						<li class="list-group-item">
							<span class="badge bg-secondary me-2">Returned</span>
							"Data Science Basics" by Sneha
						</li>
						<li class="list-group-item">
							<span class="badge bg-success me-2">Issued</span>
							"Rich Dad Poor Dad" to Ankit
						</li>
						<li class="list-group-item">
							<span class="badge bg-secondary me-2">Returned</span>
							"Python Programming" by Asha
						</li>
					</ul>
				</div>

				<!-- Transactions -->
				<div class="col-md-6">

					<h5 class="mt-4">Announcements</h5>
					<ul class="list-group">
						<li class="list-group-item">
							<span class="badge bg-danger me-2"><i class="bi bi-megaphone-fill"></i></span>
							Library will remain closed on upcoming holiday
						</li>
						<li class="list-group-item">
							<span class="badge bg-success me-2"><i class="bi bi-megaphone-fill"></i></span>
							Library will remain closed on upcoming holiday
						</li>
						<li class="list-group-item">
							<span class="badge bg-danger me-2"><i class="bi bi-megaphone-fill"></i></span>
							Library will remain closed on upcoming holiday
						</li>
						<li class="list-group-item">
							<span class="badge bg-success me-2"><i class="bi bi-megaphone-fill"></i></span>
							Library will remain closed on upcoming holiday
						</li>
					</ul>
				</div>
			</div>
		</section>


	<!-- Floating Add Button -->
	<button class="add-btn">+</button>
	</section>

	<!-- Login Modal -->
	<div class="login-modal" id="loginModal" aria-hidden="true" hidden>
		<div class="login-modal__overlay" data-login-close></div>
		<div class="login-modal__panel" role="dialog" aria-modal="true" aria-label="Login">
			<iframe class="login-modal__frame" src="login.php" title="Login"></iframe>
		</div>
	</div>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

	<!-- Custom JS -->
	<!-- <script src="script.js"></script> -->
	<script>
	// Placeholder for future interactions
	document.querySelector('.add-btn').addEventListener('click', () => {
		alert("Add new book feature coming soon!");
	});

	// Close dropdown after selecting a menu item
	document.querySelectorAll('.dropdown-menu .dropdown-item').forEach((item) => {
		item.addEventListener('click', () => {
			const dropdown = item.closest('.dropdown');
			if (!dropdown) {
				return;
			}
			const toggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
			if (toggle) {
				toggle.click();
			}
		});
	});

	const loginModal = document.getElementById('loginModal');
	const loginOpen = document.getElementById('loginOpen');
	const loginCloseEls = document.querySelectorAll('[data-login-close]');
	const loginPanel = loginModal ? loginModal.querySelector('.login-modal__panel') : null;

	if (loginModal && loginOpen) {
		loginOpen.addEventListener('click', () => {
			loginModal.classList.add('is-open');
			loginModal.setAttribute('aria-hidden', 'false');
			loginModal.removeAttribute('hidden');
			document.body.style.overflow = 'hidden';

			if (loginPanel) {
				loginPanel.style.top = '';
				loginPanel.style.left = '';
			}
		});

		loginCloseEls.forEach((el) => {
			el.addEventListener('click', () => {
				loginModal.classList.remove('is-open');
				loginModal.setAttribute('aria-hidden', 'true');
				loginModal.setAttribute('hidden', '');
				document.body.style.overflow = '';
				if (loginPanel) {
					loginPanel.style.top = '';
					loginPanel.style.left = '';
				}
			});
		});
	}
	</script>
</body>

</html>
