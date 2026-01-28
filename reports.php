<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/permissions.php';



if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$context = rbac_get_context($conn);
$isLibrarian = strcasecmp($context['role_name'] ?? '', 'Librarian') === 0;
if (!($context['is_admin'] || $isLibrarian)) {
	header('Location: ' . BASE_URL . 'dashboard.php');
	exit;
}

function fetch_kv($conn, $sql, $key, $value)
{
	$data = [];
	$result = $conn->query($sql);
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			$data[$row[$key]] = (int) $row[$value];
		}
	}
	return $data;
}

function column_exists($conn, $table, $column)
{
	$table = $conn->real_escape_string($table);
	$column = $conn->real_escape_string($column);
	$result = $conn->query("SELECT DATABASE() AS db");
	$row = $result ? $result->fetch_assoc() : null;
	$db = $conn->real_escape_string($row['db'] ?? '');
	if ($db === '') {
		return false;
	}
	$sql = "SELECT COUNT(*) AS total FROM information_schema.COLUMNS
		WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = '$table' AND COLUMN_NAME = '$column'";
	$result = $conn->query($sql);
	if ($result && ($row = $result->fetch_assoc())) {
		return (int) $row['total'] > 0;
	}
	return false;
}

$userStatuses = ['pending' => 0, 'approved' => 0, 'blocked' => 0, 'suspended' => 0];
if (column_exists($conn, 'users', 'account_status')) {
	$userStatuses = fetch_kv(
		$conn,
		"SELECT account_status, COUNT(*) AS total FROM users WHERE deleted_date IS NULL GROUP BY account_status",
		'account_status',
		'total'
	) ?: $userStatuses;
}

$inventoryTypes = ['physical' => 0, 'ebook' => 0];
if (column_exists($conn, 'books', 'book_type')) {
	$inventoryTypes = fetch_kv(
		$conn,
		"SELECT book_type, COUNT(*) AS total FROM books WHERE deleted_date IS NULL GROUP BY book_type",
		'book_type',
		'total'
	) ?: $inventoryTypes;
} else {
	$totalBooks = $conn->query("SELECT COUNT(*) AS total FROM books WHERE deleted_date IS NULL");
	if ($totalBooks && ($row = $totalBooks->fetch_assoc())) {
		$inventoryTypes['physical'] = (int) ($row['total'] ?? 0);
	}
}

$loanStatus = ['pending' => 0, 'approved' => 0, 'returned' => 0];
$reservationStatus = ['pending' => 0, 'approved' => 0];
$returnStatus = ['pending' => 0, 'approved' => 0];

if (column_exists($conn, 'loans', 'status')) {
	$loanStatus = fetch_kv(
		$conn,
		"SELECT status, COUNT(*) AS total FROM loans WHERE deleted_date IS NULL GROUP BY status",
		'status',
		'total'
	) ?: $loanStatus;
}
if (column_exists($conn, 'reservations', 'status')) {
	$reservationStatus = fetch_kv(
		$conn,
		"SELECT status, COUNT(*) AS total FROM reservations WHERE deleted_date IS NULL GROUP BY status",
		'status',
		'total'
	) ?: $reservationStatus;
}
if (column_exists($conn, 'returns', 'status')) {
	$returnStatus = fetch_kv(
		$conn,
		"SELECT status, COUNT(*) AS total FROM returns WHERE deleted_date IS NULL GROUP BY status",
		'status',
		'total'
	) ?: $returnStatus;
}

$overdueCount = 0;
if (column_exists($conn, 'loans', 'due_date')) {
	$overdueResult = $conn->query(
		"SELECT COUNT(*) AS total FROM loans WHERE deleted_date IS NULL AND due_date IS NOT NULL AND due_date < CURDATE() AND (return_date IS NULL OR return_date = '')"
	);
	if ($overdueResult && ($row = $overdueResult->fetch_assoc())) {
		$overdueCount = (int) ($row['total'] ?? 0);
	}
}

$pendingApprovals = [
	'loans' => (int) ($loanStatus['pending'] ?? 0),
	'reservations' => (int) ($reservationStatus['pending'] ?? 0),
	'returns' => (int) ($returnStatus['pending'] ?? 0),
];

$digitalTotals = [
	'resources' => 0,
	'files' => 0,
	'downloads' => 0,
];
if (column_exists($conn, 'digital_resources', 'resource_id')) {
	$digitalResources = $conn->query("SELECT COUNT(*) AS total FROM digital_resources WHERE deleted_date IS NULL");
	if ($digitalResources && ($row = $digitalResources->fetch_assoc())) {
		$digitalTotals['resources'] = (int) ($row['total'] ?? 0);
	}
}
if (column_exists($conn, 'digital_files', 'file_id')) {
	$digitalFiles = $conn->query("SELECT COUNT(*) AS total, COALESCE(SUM(download_count), 0) AS downloads FROM digital_files WHERE deleted_date IS NULL");
	if ($digitalFiles && ($row = $digitalFiles->fetch_assoc())) {
		$digitalTotals['files'] = (int) ($row['total'] ?? 0);
		$digitalTotals['downloads'] = (int) ($row['downloads'] ?? 0);
	}
}

$fineTotals = ['count' => 0, 'amount' => 0];
if (column_exists($conn, 'fines', 'fine_id')) {
	$fineAmountSql = column_exists($conn, 'fines', 'amount')
		? 'COALESCE(SUM(amount), 0) AS amount'
		: '0 AS amount';
	$fineResult = $conn->query("SELECT COUNT(*) AS total, {$fineAmountSql} FROM fines WHERE deleted_date IS NULL");
	if ($fineResult && ($row = $fineResult->fetch_assoc())) {
		$fineTotals['count'] = (int) ($row['total'] ?? 0);
		$fineTotals['amount'] = (float) ($row['amount'] ?? 0);
	}
}

$waiverTotals = ['count' => 0, 'amount' => 0];
if (column_exists($conn, 'fine_waivers', 'waiver_id')) {
	$waiverAmountSql = column_exists($conn, 'fine_waivers', 'amount')
		? 'COALESCE(SUM(amount), 0) AS amount'
		: '0 AS amount';
	$waiverResult = $conn->query("SELECT COUNT(*) AS total, {$waiverAmountSql} FROM fine_waivers WHERE deleted_date IS NULL");
	if ($waiverResult && ($row = $waiverResult->fetch_assoc())) {
		$waiverTotals['count'] = (int) ($row['total'] ?? 0);
		$waiverTotals['amount'] = (float) ($row['amount'] ?? 0);
	}
}

$paymentTotals = ['count' => 0, 'amount' => 0];
if (column_exists($conn, 'payments', 'payment_id')) {
	$paymentAmountSql = column_exists($conn, 'payments', 'amount')
		? 'COALESCE(SUM(amount), 0) AS amount'
		: '0 AS amount';
	$paymentResult = $conn->query("SELECT COUNT(*) AS total, {$paymentAmountSql} FROM payments WHERE deleted_date IS NULL");
	if ($paymentResult && ($row = $paymentResult->fetch_assoc())) {
		$paymentTotals['count'] = (int) ($row['total'] ?? 0);
		$paymentTotals['amount'] = (float) ($row['amount'] ?? 0);
	}
}

$bookCount = 0;
$authorCount = 0;
$publisherCount = 0;
$seriesCount = 0;
$librarySizeBytes = 0;

$bookTotalResult = $conn->query("SELECT COUNT(*) AS total FROM books WHERE deleted_date IS NULL");
if ($bookTotalResult && ($row = $bookTotalResult->fetch_assoc())) {
	$bookCount = (int) ($row['total'] ?? 0);
}
if (column_exists($conn, 'books', 'author')) {
	$authorResult = $conn->query("SELECT COUNT(DISTINCT author) AS total FROM books WHERE deleted_date IS NULL AND author IS NOT NULL AND author <> ''");
	if ($authorResult && ($row = $authorResult->fetch_assoc())) {
		$authorCount = (int) ($row['total'] ?? 0);
	}
}
if (column_exists($conn, 'books', 'publisher')) {
	$publisherResult = $conn->query("SELECT COUNT(DISTINCT publisher) AS total FROM books WHERE deleted_date IS NULL AND publisher IS NOT NULL AND publisher <> ''");
	if ($publisherResult && ($row = $publisherResult->fetch_assoc())) {
		$publisherCount = (int) ($row['total'] ?? 0);
	}
}
if (column_exists($conn, 'books', 'ebook_file_size')) {
	$sizeResult = $conn->query("SELECT COALESCE(SUM(ebook_file_size), 0) AS total FROM books WHERE deleted_date IS NULL");
	if ($sizeResult && ($row = $sizeResult->fetch_assoc())) {
		$librarySizeBytes += (int) ($row['total'] ?? 0);
	}
}
if (column_exists($conn, 'digital_files', 'file_size')) {
	$digitalSize = $conn->query("SELECT COALESCE(SUM(file_size), 0) AS total FROM digital_files WHERE deleted_date IS NULL");
	if ($digitalSize && ($row = $digitalSize->fetch_assoc())) {
		$librarySizeBytes += (int) ($row['total'] ?? 0);
	}
}

function format_kb($bytes)
{
	if ($bytes <= 0) {
		return '0 KB';
	}
	$kb = $bytes / 1024;
	if ($kb < 1024) {
		return number_format($kb, 0) . ' KB';
	}
	$mb = $kb / 1024;
	return number_format($mb, 1) . ' MB';
}

$librarySizeLabel = format_kb($librarySizeBytes);

$searchTop = [];
if (column_exists($conn, 'search_logs', 'query_text')) {
	$searchLogs = $conn->query(
		"SELECT query_text, COUNT(*) AS total FROM search_logs GROUP BY query_text ORDER BY total DESC LIMIT 8"
	);
	if ($searchLogs) {
		while ($row = $searchLogs->fetch_assoc()) {
			$searchTop[] = [
				'query' => $row['query_text'] ?? '',
				'total' => (int) ($row['total'] ?? 0),
			];
		}
	}
}

$activityDays = [];
if (column_exists($conn, 'audit_logs', 'created_date')) {
	$activityResult = $conn->query(
		"SELECT DATE(created_date) AS day, COUNT(*) AS total
		 FROM audit_logs
		 WHERE created_date >= DATE_SUB(CURDATE(), INTERVAL 120 DAY)
		 GROUP BY DATE(created_date)
		 ORDER BY day ASC"
	);
	if ($activityResult) {
		while ($row = $activityResult->fetch_assoc()) {
			$activityDays[$row['day']] = (int) ($row['total'] ?? 0);
		}
	}
}

?>
<?php include(ROOT_PATH . '/include/header_resources.php') ?>
<?php include(ROOT_PATH . '/include/header.php') ?>
<?php include(ROOT_PATH . '/sidebar.php') ?>
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid py-4">
			<div class="report-hero mb-4">
				<div class="report-hero-header">
					<h2 class="mb-0">Library Statistics</h2>
					<div class="report-hero-controls">
						<select class="form-select form-select-sm report-group-select" id="reportGroupSelect">
							<option value="all" selected>All Reports</option>
							<option value="library">Library Reports</option>
							<option value="user">User Reports</option>
						</select>
						<button class="btn btn-outline-light btn-sm" type="button" aria-label="Settings">
							<i class="bi bi-gear"></i>
						</button>
					</div>
				</div>
				<div class="report-hero-stats">
					<div class="stat-pill">
						<i class="bi bi-book"></i>
						<span>Books</span>
						<strong><?php echo (int) $bookCount; ?></strong>
					</div>
					<div class="stat-pill">
						<i class="bi bi-people"></i>
						<span>Authors</span>
						<strong><?php echo (int) $authorCount; ?></strong>
					</div>
					<div class="stat-pill">
						<i class="bi bi-journal-bookmark"></i>
						<span>Series</span>
						<strong><?php echo (int) $seriesCount; ?></strong>
					</div>
					<div class="stat-pill">
						<i class="bi bi-building"></i>
						<span>Publishers</span>
						<strong><?php echo (int) $publisherCount; ?></strong>
					</div>
					<div class="stat-pill">
						<i class="bi bi-hdd"></i>
						<span>Library Size</span>
						<strong><?php echo htmlspecialchars($librarySizeLabel); ?></strong>
					</div>
				</div>
			</div>

			<div class="d-flex align-items-center justify-content-between mb-3">
				<div>
					<h3 class="mb-1">Reports Dashboard</h3>
					<p class="text-muted mb-0">Operational snapshot across users, inventory, circulation, and finance.</p>
				</div>
				<div class="report-controls">
					<button class="btn btn-outline-primary btn-sm" type="button" data-chart-theme="default">Default Theme</button>
					<button class="btn btn-outline-secondary btn-sm" type="button" data-chart-theme="muted">Muted Theme</button>
				</div>
			</div>

			<div class="report-section" data-report-group="library">
				<div class="report-section-header">Library Reports</div>
				<div class="reports-grid">
					<div class="report-card">
						<div class="report-title">Physical vs Ebook Inventory</div>
						<div id="inventoryChart" class="chart-box"></div>
					</div>

					<div class="report-card">
						<div class="report-title">Circulation Status</div>
						<div id="circulationChart" class="chart-box"></div>
					</div>

					<div class="report-card">
						<div class="report-title">Pending Approvals</div>
						<div id="approvalChart" class="chart-box"></div>
					</div>

					<div class="report-card">
						<div class="report-title">Overdue Loans</div>
						<div class="overdue-tile">
							<span class="overdue-count"><?php echo (int) $overdueCount; ?></span>
							<span class="overdue-label">Loans overdue</span>
						</div>
					</div>

					<div class="report-card">
						<div class="report-title">Digital Library</div>
						<div id="digitalChart" class="chart-box"></div>
					</div>

					<div class="report-card">
						<div class="report-title">Fines & Payments</div>
						<div id="financeChart" class="chart-box"></div>
					</div>
				</div>
			</div>

			<div class="report-section" data-report-group="user">
				<div class="report-section-header">User Reports</div>
				<div class="reports-grid">
					<div class="report-card">
						<div class="report-title">User Activity Heatmap</div>
						<div class="report-subtitle">Activity from audit logs (last 120 days)</div>
						<div class="activity-heatmap" id="activityHeatmap" data-activity='<?php echo htmlspecialchars(json_encode($activityDays)); ?>'></div>
					</div>

					<div class="report-card">
						<div class="report-title">User Account Status</div>
						<div id="userStatusChart" class="chart-box"></div>
					</div>

					<div class="report-card">
						<div class="report-title">Top Searches</div>
						<div id="searchChart" class="chart-box"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>

<script>
(function() {
	const themeButtons = document.querySelectorAll('[data-chart-theme]');
	let chartTheme = 'default';

	const themePalettes = {
		default: ['#0d6efd', '#20c997', '#ffc107', '#dc3545', '#6f42c1'],
		muted: ['#6c757d', '#adb5bd', '#ced4da', '#868e96', '#495057'],
	};

	function renderCharts() {
		const palette = themePalettes[chartTheme] || themePalettes.default;

		const userStatus = <?php echo json_encode($userStatuses); ?>;
		new ApexCharts(document.querySelector('#userStatusChart'), {
			chart: { type: 'donut', height: 250 },
			labels: Object.keys(userStatus),
			series: Object.values(userStatus),
			colors: palette,
			legend: { position: 'bottom' },
		}).render();

		const inventory = <?php echo json_encode($inventoryTypes); ?>;
		new ApexCharts(document.querySelector('#inventoryChart'), {
			chart: { type: 'bar', height: 250 },
			series: [{ name: 'Books', data: Object.values(inventory) }],
			xaxis: { categories: Object.keys(inventory) },
			colors: [palette[0]],
		}).render();

		const circulation = {
			Loans: <?php echo json_encode($loanStatus); ?>,
			Reservations: <?php echo json_encode($reservationStatus); ?>,
			Returns: <?php echo json_encode($returnStatus); ?>,
		};
		const circulationLabels = Array.from(new Set([
			...Object.keys(circulation.Loans),
			...Object.keys(circulation.Reservations),
			...Object.keys(circulation.Returns),
		]));
		new ApexCharts(document.querySelector('#circulationChart'), {
			chart: { type: 'bar', height: 250, stacked: true },
			series: [
				{ name: 'Loans', data: circulationLabels.map((label) => circulation.Loans[label] || 0) },
				{ name: 'Reservations', data: circulationLabels.map((label) => circulation.Reservations[label] || 0) },
				{ name: 'Returns', data: circulationLabels.map((label) => circulation.Returns[label] || 0) },
			],
			xaxis: { categories: circulationLabels },
			colors: palette,
		}).render();

		const approvals = <?php echo json_encode($pendingApprovals); ?>;
		new ApexCharts(document.querySelector('#approvalChart'), {
			chart: { type: 'radar', height: 250 },
			series: [{ name: 'Pending', data: Object.values(approvals) }],
			xaxis: { categories: Object.keys(approvals) },
			colors: [palette[2]],
		}).render();

		const digital = <?php echo json_encode($digitalTotals); ?>;
		new ApexCharts(document.querySelector('#digitalChart'), {
			chart: { type: 'bar', height: 250 },
			series: [{ name: 'Total', data: Object.values(digital) }],
			xaxis: { categories: ['Resources', 'Files', 'Downloads'] },
			colors: [palette[1]],
		}).render();

		const finance = {
			Fines: <?php echo json_encode($fineTotals); ?>,
			Waivers: <?php echo json_encode($waiverTotals); ?>,
			Payments: <?php echo json_encode($paymentTotals); ?>,
		};
		new ApexCharts(document.querySelector('#financeChart'), {
			chart: { type: 'bar', height: 250 },
			series: [
				{ name: 'Count', data: [finance.Fines.count, finance.Waivers.count, finance.Payments.count] },
				{ name: 'Amount', data: [finance.Fines.amount, finance.Waivers.amount, finance.Payments.amount] },
			],
			xaxis: { categories: ['Fines', 'Waivers', 'Payments'] },
			colors: [palette[3], palette[0]],
		}).render();

		const searches = <?php echo json_encode($searchTop); ?>;
		new ApexCharts(document.querySelector('#searchChart'), {
			chart: { type: 'bar', height: 250 },
			series: [{ name: 'Searches', data: searches.map((item) => item.total) }],
			xaxis: { categories: searches.map((item) => item.query || '-') },
			colors: [palette[4]],
		}).render();
	}

	function renderHeatmap() {
		const heatmap = document.getElementById('activityHeatmap');
		if (!heatmap) return;
		const data = JSON.parse(heatmap.dataset.activity || '{}');
		const days = 120;
		heatmap.innerHTML = '';
		const today = new Date();
		for (let i = days - 1; i >= 0; i--) {
			const date = new Date(today);
			date.setDate(today.getDate() - i);
			const key = date.toISOString().slice(0, 10);
			const count = data[key] || 0;
			const cell = document.createElement('div');
			cell.className = 'heatmap-cell';
			cell.dataset.count = count;
			cell.title = `${key}: ${count} activities`;
			heatmap.appendChild(cell);
		}
	}

	renderCharts();
	renderHeatmap();

	themeButtons.forEach((btn) => {
		btn.addEventListener('click', () => {
			chartTheme = btn.dataset.chartTheme || 'default';
			document.querySelectorAll('.chart-box').forEach((box) => (box.innerHTML = ''));
			renderCharts();
		});
	});

	const reportGroupSelect = document.getElementById('reportGroupSelect');
	if (reportGroupSelect) {
		reportGroupSelect.addEventListener('change', () => {
			const value = reportGroupSelect.value;
			document.querySelectorAll('.report-section').forEach((section) => {
				if (value === 'all') {
					section.classList.remove('report-hidden');
					return;
				}
				const group = section.dataset.reportGroup;
				section.classList.toggle('report-hidden', group !== value);
			});
		});
	}
})();
</script>
