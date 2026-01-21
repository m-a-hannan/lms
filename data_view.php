<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

$tables = [];
$tablesResult = $conn->query('SHOW TABLES');
if ($tablesResult === false) {
	die('Failed to load tables: ' . $conn->error);
}
while ($row = $tablesResult->fetch_row()) {
	$tables[] = $row[0];
}
sort($tables);

$selectedTable = null;
$rows = [];
$columns = [];
$error = '';

if (isset($_GET['table'])) {
	$table = $_GET['table'];
	if (in_array($table, $tables, true)) {
		$selectedTable = $table;
		$result = $conn->query("SELECT * FROM `$selectedTable`");
		if ($result === false) {
			$error = $conn->error;
		} else {
			$fields = $result->fetch_fields();
			foreach ($fields as $field) {
				$columns[] = $field->name;
			}
			while ($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
		}
	} else {
		$error = 'Invalid table selection.';
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Library Management System - Data Viewer</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap 5 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Bootstrap 5 Icons -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

	<!-- Custom CSS -->
	<link rel="stylesheet" href="style.css">

	<style>
		body { padding-top: 88px; background: #f6f7fb; }
		.page-title { font-weight: 700; }
		.table-grid {
			display: grid;
			grid-template-columns: repeat(5, minmax(0, 1fr));
			gap: 10px;
		}
		.table-grid a { width: 100%; }
		@media (max-width: 992px) {
			.table-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
		}
		@media (max-width: 576px) {
			.table-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
		}
	</style>
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
					<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
					<li class="nav-item"><a class="nav-link" href="#">Managers</a></li>
					<li class="nav-item"><a class="nav-link" href="erd.php">ERD</a></li>
					<li class="nav-item"><a class="nav-link" href="library_rbac_matrix.php">RBAC</a></li>
				</ul>

				<a href="dashboard.php" class="btn btn-gradient px-4"><i class="nav-icon bi bi-speedometer"></i> Dashboard</a>
			</div>
		</div>
	</nav>

	<section class="py-4">
		<div class="container">
			<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
				<div>
					<h2 class="page-title mb-1">Database Viewer</h2>
					<p class="text-muted mb-0">Select a table to view its data.</p>
				</div>
			</div>

			<div class="card shadow-sm mb-4">
				<div class="card-body">
					<div class="table-grid">
						<?php foreach ($tables as $tableName): ?>
						<a href="?table=<?= urlencode($tableName) ?>" class="btn <?= $selectedTable === $tableName ? 'btn-gradient' : 'btn-outline-secondary' ?> btn-sm">
							<?= htmlspecialchars($tableName) ?>
						</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div class="card shadow-sm">
				<div class="card-body">
					<?php if ($error): ?>
					<div class="alert alert-danger mb-0"><?= htmlspecialchars($error) ?></div>
					<?php elseif ($selectedTable === null): ?>
					<p class="text-muted mb-0">Choose a table above to see its data.</p>
					<?php else: ?>
					<h5 class="mb-3">Table: <?= htmlspecialchars($selectedTable) ?></h5>
					<?php if (!$rows): ?>
					<p class="text-muted mb-0">No records found.</p>
					<?php else: ?>
					<div class="table-responsive">
						<table class="table table-bordered table-hover align-middle">
							<thead class="table-light">
								<tr>
									<?php foreach ($columns as $col): ?>
									<th><?= htmlspecialchars($col) ?></th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($rows as $row): ?>
								<tr>
									<?php foreach ($columns as $col): ?>
									<td><?= htmlspecialchars((string) ($row[$col] ?? '')) ?></td>
									<?php endforeach; ?>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
