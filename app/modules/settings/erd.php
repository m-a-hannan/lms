<?php
// Load app configuration, database connection, and permissions helpers.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';

// Build the dashboard link based on RBAC settings.
$dashboardUrl = BASE_URL . rbac_dashboard_path($conn);
?>
<!-- ERD viewer page layout. -->
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
	<link rel="stylesheet" href="assets/css/style.css">
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
					<li class="nav-item"><a class="nav-link active" href="<?php echo BASE_URL; ?>home.php">Home</a></li>
					<li class="nav-item"><a class="nav-link" href="#">Managers</a></li>
					<li class="nav-item"><a class="nav-link" href="erd.php">ERD</a></li>
					<li class="nav-item"><a class="nav-link" href="library_rbac_matrix.php">RBAC</a></li>
				</ul>

				<a href="<?php echo $dashboardUrl; ?>" class="btn btn-gradient px-4"><i class="nav-icon bi bi-speedometer"></i> Dashboard</a>
			</div>
		</div>
	</nav>

	<!-- ================= ERD ================= -->
	<section class="py-5 bg-light">
		<section class="container">
			<div class="row g-4">
				<div class="col-md-12">
					<!-- ERD header and viewer controls. -->
					<h3 class="mb-4">Database Diagram</h3>
					<div class="erd-toolbar mb-2">
						<button type="button" class="btn btn-sm btn-outline-secondary" id="erd-zoom-out">-</button>
						<button type="button" class="btn btn-sm btn-outline-secondary" id="erd-zoom-in">+</button>
						<button type="button" class="btn btn-sm btn-outline-secondary" id="erd-zoom-reset">Reset</button>
						<button type="button" class="btn btn-sm btn-outline-secondary" id="erd-fullscreen">Fullscreen</button>
					</div>
					<!-- ERD image viewer. -->
					<div class="erd-viewer" id="erd-viewer">
						<img src="assets/img/ER Diagram.svg" alt="ER Diagram" id="erd-image">
					</div>
				</div>
			</div>
			</div>
		</section>


		<!-- Floating Add Button -->
		<button class="add-btn">+</button>
	</section>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	<!-- Custom JS -->
	<!-- <script src="script.js"></script> -->
	<script src="assets/js/pages/erd.js"></script>
</body>

</html>
