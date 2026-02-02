<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';

$dashboardUrl = BASE_URL . rbac_dashboard_path($conn);
?>
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
					<li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
					<li class="nav-item"><a class="nav-link" href="#">Managers</a></li>
					<li class="nav-item"><a class="nav-link" href="erd.php">ERD</a></li>
					<li class="nav-item"><a class="nav-link" href="library_rbac_matrix.php">RBAC</a></li>
				</ul>

				<a href="<?php echo $dashboardUrl; ?>" class="btn btn-gradient px-4"><i class="nav-icon bi bi-speedometer"></i> Dashboard</a>
			</div>
		</div>
	</nav>

	<!-- ================= RBAC Matrix ================= -->
	<section class="py-5 bg-light">
		<section class="container">
			<div class="row g-4">
				<div class="col-md-12">
					<h3 class="mb-4">Role-Based Access Control (RBAC) Matrix for LMS</h3>

					<div class="table-responsive">
						<table class="table table-striped table-bordered" id="rbacTable">
							<thead class="table-dark">
								<tr id="tableHeader">
									<th>Resource / Permission</th>
								</tr>
							</thead>
							<tbody id="tableBody"></tbody>
						</table>
						<!-- Debug output (optional) -->
						<pre class="mt-4 bg-light p-3" id="jsonOutput"></pre>
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

	<script src="assets/js/pages/library_rbac_matrix.js"></script>
</body>

</html>