<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/permissions.php';

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
					<li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
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
					<h3 class="mb-4">Database Diagram</h3>
					<div class="erd-toolbar mb-2">
						<button type="button" class="btn btn-sm btn-outline-secondary" id="erd-zoom-out">-</button>
						<button type="button" class="btn btn-sm btn-outline-secondary" id="erd-zoom-in">+</button>
						<button type="button" class="btn btn-sm btn-outline-secondary" id="erd-zoom-reset">Reset</button>
						<button type="button" class="btn btn-sm btn-outline-secondary" id="erd-fullscreen">Fullscreen</button>
					</div>
					<div class="erd-viewer" id="erd-viewer">
						<img src="DB/ER Diagram.svg" alt="ER Diagram" id="erd-image">
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
	<script>
	// Placeholder for future interactions
	document.querySelector('.add-btn').addEventListener('click', () => {
		alert("Add new book feature coming soon!");
	});

	const viewer = document.getElementById('erd-viewer');
	const image = document.getElementById('erd-image');
	const zoomIn = document.getElementById('erd-zoom-in');
	const zoomOut = document.getElementById('erd-zoom-out');
	const zoomReset = document.getElementById('erd-zoom-reset');
	const fullscreenBtn = document.getElementById('erd-fullscreen');
	let scale = 1;

	function applyZoom(nextScale) {
		scale = Math.min(4, Math.max(0.25, nextScale));
		image.style.transform = `scale(${scale})`;
	}

	zoomIn.addEventListener('click', () => applyZoom(scale + 0.1));
	zoomOut.addEventListener('click', () => applyZoom(scale - 0.1));
	zoomReset.addEventListener('click', () => applyZoom(1));
	fullscreenBtn.addEventListener('click', () => {
		if (!document.fullscreenElement) {
			viewer.requestFullscreen();
		} else {
			document.exitFullscreen();
		}
	});

	document.addEventListener('fullscreenchange', () => {
		const isFullscreen = document.fullscreenElement === viewer;
		fullscreenBtn.textContent = isFullscreen ? 'Exit Fullscreen' : 'Fullscreen';
	});

	viewer.addEventListener('wheel', (event) => {
		if (!event.ctrlKey) {
			return;
		}
		event.preventDefault();
		const direction = event.deltaY > 0 ? -0.1 : 0.1;
		applyZoom(scale + direction);
	}, { passive: false });

	let isPanning = false;
	let startX = 0;
	let startY = 0;
	let startScrollLeft = 0;
	let startScrollTop = 0;

	viewer.addEventListener('mousedown', (event) => {
		isPanning = true;
		startX = event.clientX;
		startY = event.clientY;
		startScrollLeft = viewer.scrollLeft;
		startScrollTop = viewer.scrollTop;
		viewer.classList.add('panning');
	});

	window.addEventListener('mousemove', (event) => {
		if (!isPanning) {
			return;
		}
		const dx = event.clientX - startX;
		const dy = event.clientY - startY;
		viewer.scrollLeft = startScrollLeft - dx;
		viewer.scrollTop = startScrollTop - dy;
	});

	window.addEventListener('mouseup', () => {
		if (!isPanning) {
			return;
		}
		isPanning = false;
		viewer.classList.remove('panning');
	});
	</script>
</body>

</html>
