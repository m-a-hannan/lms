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
		"SELECT COUNT(*) AS total
		 FROM loans
		 WHERE deleted_date IS NULL
		   AND CAST(due_date AS CHAR) <> ''
		   AND CAST(due_date AS CHAR) <> '0000-00-00'
		   AND STR_TO_DATE(CAST(due_date AS CHAR), '%Y-%m-%d') < CURDATE()
		   AND (
				return_date IS NULL
				OR CAST(return_date AS CHAR) = ''
				OR CAST(return_date AS CHAR) = '0000-00-00'
		   )"
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
if (column_exists($conn, 'books', 'book_type')) {
	$ebookTotals = $conn->query(
		"SELECT COUNT(*) AS total,
			SUM(CASE WHEN ebook_file_path IS NOT NULL AND ebook_file_path <> '' THEN 1 ELSE 0 END) AS files
		 FROM books
		 WHERE deleted_date IS NULL AND book_type = 'ebook'"
	);
	if ($ebookTotals && ($row = $ebookTotals->fetch_assoc())) {
		$digitalTotals['resources'] += (int) ($row['total'] ?? 0);
		$digitalTotals['files'] += (int) ($row['files'] ?? 0);
	}
}
if (column_exists($conn, 'digital_resources', 'resource_id')) {
	$digitalResources = $conn->query("SELECT COUNT(*) AS total FROM digital_resources WHERE deleted_date IS NULL");
	if ($digitalResources && ($row = $digitalResources->fetch_assoc())) {
		$digitalTotals['resources'] += (int) ($row['total'] ?? 0);
	}
}
if (column_exists($conn, 'digital_files', 'file_id')) {
	$digitalFiles = $conn->query("SELECT COUNT(*) AS total, COALESCE(SUM(download_count), 0) AS downloads FROM digital_files WHERE deleted_date IS NULL");
	if ($digitalFiles && ($row = $digitalFiles->fetch_assoc())) {
		$digitalTotals['files'] += (int) ($row['total'] ?? 0);
		$digitalTotals['downloads'] = (int) ($row['downloads'] ?? 0);
	}
}

if (column_exists($conn, 'audit_logs', 'log_id')) {
	$downloadLogs = $conn->query(
		"SELECT COUNT(*) AS total
		 FROM audit_logs
		 WHERE action = 'download_ebook'
		   AND deleted_date IS NULL"
	);
	if ($downloadLogs && ($row = $downloadLogs->fetch_assoc())) {
		$digitalTotals['downloads'] += (int) ($row['total'] ?? 0);
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

$activityUsers = [];
$activityData = [
	'all' => ['loan' => [], 'reserve' => [], 'return' => [], 'download' => []],
	'users' => [],
];

$userResult = $conn->query(
	"SELECT user_id, username, email
	 FROM users
	 WHERE deleted_date IS NULL
	 ORDER BY username ASC"
);
if ($userResult) {
	while ($row = $userResult->fetch_assoc()) {
		$activityUsers[] = $row;
	}
}

$addActivity = function (&$store, string $type, string $day, int $userId, int $count) {
	if (!isset($store['users'][$userId])) {
		$store['users'][$userId] = ['loan' => [], 'reserve' => [], 'return' => [], 'download' => []];
	}
	$store['users'][$userId][$type][$day] = ($store['users'][$userId][$type][$day] ?? 0) + $count;
	$store['all'][$type][$day] = ($store['all'][$type][$day] ?? 0) + $count;
};

$activityQueries = [
	'loan' => "SELECT DATE(created_date) AS day, user_id, COUNT(*) AS total
		FROM loans
		WHERE created_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)
		GROUP BY DATE(created_date), user_id",
	'reserve' => "SELECT DATE(created_date) AS day, user_id, COUNT(*) AS total
		FROM reservations
		WHERE created_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)
		GROUP BY DATE(created_date), user_id",
	'return' => "SELECT DATE(created_date) AS day, created_by AS user_id, COUNT(*) AS total
		FROM returns
		WHERE created_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)
		GROUP BY DATE(created_date), created_by",
	'download' => "SELECT DATE(COALESCE(time_stamp, created_date)) AS day, user_id, COUNT(*) AS total
		FROM audit_logs
		WHERE action = 'download_ebook'
		  AND COALESCE(time_stamp, created_date) >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)
		  AND user_id IS NOT NULL
		GROUP BY DATE(COALESCE(time_stamp, created_date)), user_id",
];

foreach ($activityQueries as $type => $sql) {
	$result = $conn->query($sql);
	if (!$result) {
		continue;
	}
	while ($row = $result->fetch_assoc()) {
		$day = $row['day'] ?? null;
		$userId = (int) ($row['user_id'] ?? 0);
		$count = (int) ($row['total'] ?? 0);
		if (!$day || $userId <= 0 || $count <= 0) {
			continue;
		}
		$addActivity($activityData, $type, $day, $userId, $count);
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
						<div class="dropdown">
							<button class="btn dropdown-toggle report-group-toggle" type="button" data-bs-toggle="dropdown"
								aria-expanded="false" id="reportGroupDropdown">
								All Reports
							</button>
							<ul class="dropdown-menu" aria-labelledby="reportGroupDropdown">
								<li><a class="dropdown-item report-group-item" href="#" data-report-group="all">All Reports</a></li>
								<li><a class="dropdown-item report-group-item" href="#" data-report-group="library">Library Reports</a></li>
								<li><a class="dropdown-item report-group-item" href="#" data-report-group="user">User Reports</a></li>
							</ul>
						</div>
						<button class="btn btn-outline-light btn-sm" type="button" aria-label="Settings" data-bs-toggle="modal"
							data-bs-target="#chartConfigModal">
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
					<div class="report-card" data-report-id="physical-ebook" data-report-order="1">
						<div class="report-title">Physical vs Ebook Inventory</div>
						<div id="inventoryChart" class="chart-box"></div>
					</div>

					<div class="report-card" data-report-id="circulation-status" data-report-order="2">
						<div class="report-title">Circulation Status</div>
						<div id="circulationChart" class="chart-box"></div>
					</div>

					<div class="report-card" data-report-id="pending-approvals" data-report-order="3">
						<div class="report-title">Pending Approvals</div>
						<div id="approvalChart" class="chart-box"></div>
					</div>

					<div class="report-card" data-report-id="overdue-loans" data-report-order="4">
						<div class="report-title">Overdue Loans</div>
						<div class="overdue-tile">
							<span class="overdue-count"><?php echo (int) $overdueCount; ?></span>
							<span class="overdue-label">Loans overdue</span>
						</div>
					</div>

					<div class="report-card" data-report-id="digital-library" data-report-order="5">
						<div class="report-title">Digital Library</div>
						<div id="digitalChart" class="chart-box"></div>
					</div>

					<div class="report-card" data-report-id="fines-payments" data-report-order="6">
						<div class="report-title">Fines & Payments</div>
						<div id="financeChart" class="chart-box"></div>
					</div>
				</div>
			</div>

			<div class="report-section" data-report-group="user">
				<div class="report-section-header">User Reports</div>
				<div class="reports-grid">
					<div class="report-card report-card-wide" data-report-id="user-activity" data-report-order="7">
						<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
							<div class="report-title mb-0">User Activity Heatmap</div>
							<div class="activity-controls d-flex align-items-center gap-2">
								<select class="form-select form-select-sm activity-filter" id="activityUserFilter">
									<option value="all" selected>All Users</option>
									<?php foreach ($activityUsers as $user): ?>
									<option value="<?php echo (int) $user['user_id']; ?>">
										<?php echo htmlspecialchars($user['username'] ?: $user['email']); ?>
									</option>
									<?php endforeach; ?>
								</select>
								<select class="form-select form-select-sm activity-filter" id="activityTypeFilter">
									<option value="all" selected>All Activities</option>
									<option value="loan">Loans</option>
									<option value="reserve">Reservations</option>
									<option value="return">Returns</option>
									<option value="download">Downloads</option>
								</select>
							</div>
						</div>
						<div class="report-subtitle">Loan, reservation, and return activity (last 12 months)</div>
						<div class="contribution-card contribution-card-light" id="activityHeatmap"
							data-activity='<?php echo htmlspecialchars(json_encode($activityData)); ?>'>
							<div class="months" id="heatmapMonths"></div>
							<div class="graph-wrapper">
								<div class="day-labels">
									<span>Mon</span>
									<span>Tue</span>
									<span>Wed</span>
									<span>Thu</span>
									<span>Fri</span>
									<span>Sat</span>
									<span>Sun</span>
								</div>
								<div class="heatmap" id="heatmapGrid"></div>
							</div>
							<div class="d-flex justify-content-between align-items-center mt-3">
								<div class="legend legend-stack">
									<div class="legend-row">
										<span class="legend-label">Loans</span>
										<div class="day activity-loan level-1"></div>
										<div class="day activity-loan level-2"></div>
										<div class="day activity-loan level-3"></div>
										<div class="day activity-loan level-4"></div>
									</div>
									<div class="legend-row">
										<span class="legend-label">Reservations</span>
										<div class="day activity-reserve level-1"></div>
										<div class="day activity-reserve level-2"></div>
										<div class="day activity-reserve level-3"></div>
										<div class="day activity-reserve level-4"></div>
									</div>
									<div class="legend-row">
										<span class="legend-label">Returns</span>
										<div class="day activity-return level-1"></div>
										<div class="day activity-return level-2"></div>
										<div class="day activity-return level-3"></div>
										<div class="day activity-return level-4"></div>
									</div>
									<div class="legend-row">
										<span class="legend-label">Downloads</span>
										<div class="day activity-download level-1"></div>
										<div class="day activity-download level-2"></div>
										<div class="day activity-download level-3"></div>
										<div class="day activity-download level-4"></div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="report-card" data-report-id="user-status" data-report-order="8">
						<div class="report-title">User Account Status</div>
						<div id="userStatusChart" class="chart-box"></div>
					</div>

					<div class="report-card" data-report-id="top-searches" data-report-order="9">
						<div class="report-title">Top Searches</div>
						<div id="searchChart" class="chart-box"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<div class="modal fade report-config-modal" id="chartConfigModal" tabindex="-1">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title d-flex align-items-center gap-2">
					<i class="bi bi-gear"></i>
					Chart Configuration
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="d-flex flex-wrap gap-3 mb-4">
					<button class="btn btn-glass btn-enable flex-fill" type="button" data-report-action="enable-all">
						<i class="bi bi-check-lg me-1"></i> Enable All
					</button>
					<button class="btn btn-glass btn-disable flex-fill" type="button" data-report-action="disable-all">
						<i class="bi bi-x-lg me-1"></i> Disable All
					</button>
					<button class="btn btn-glass btn-reset flex-fill" type="button" data-report-action="reset-order">
						<i class="bi bi-arrow-clockwise me-1"></i> Reset Order
					</button>
				</div>

				<div class="row g-4">
					<div class="col-md-6">
						<div class="section-title">SMALL CHARTS</div>
						<div class="divider"></div>
						<div class="form-check mb-2">
							<input class="form-check-input chart-check" type="checkbox" data-report-target="physical-ebook" checked>
							<label class="form-check-label">Physical vs Ebook Inventory</label>
						</div>
						<div class="form-check mb-2">
							<input class="form-check-input chart-check" type="checkbox" data-report-target="circulation-status"
								checked>
							<label class="form-check-label">Circulation Status</label>
						</div>
						<div class="form-check mb-2">
							<input class="form-check-input chart-check" type="checkbox" data-report-target="pending-approvals"
								checked>
							<label class="form-check-label">Pending Approvals</label>
						</div>
						<div class="form-check mb-2">
							<input class="form-check-input chart-check" type="checkbox" data-report-target="digital-library" checked>
							<label class="form-check-label">Digital Library</label>
						</div>
						<div class="form-check mb-2">
							<input class="form-check-input chart-check" type="checkbox" data-report-target="fines-payments" checked>
							<label class="form-check-label">Fines & Payments</label>
						</div>
						<div class="form-check mb-2">
							<input class="form-check-input chart-check" type="checkbox" data-report-target="user-status" checked>
							<label class="form-check-label">User Account Status</label>
						</div>
						<div class="form-check mb-2">
							<input class="form-check-input chart-check" type="checkbox" data-report-target="top-searches" checked>
							<label class="form-check-label">Top Searches</label>
						</div>
					</div>

					<div class="col-md-6">
						<div class="section-title">LARGE CHARTS</div>
						<div class="divider"></div>
						<div class="form-check mb-2">
							<input class="form-check-input chart-check" type="checkbox" data-report-target="user-activity" checked>
							<label class="form-check-label">User Activity Heatmap</label>
						</div>
						<div class="form-check mb-2">
							<input class="form-check-input chart-check" type="checkbox" data-report-target="overdue-loans" checked>
							<label class="form-check-label">Overdue Loans</label>
						</div>
					</div>

					<div class="col-12 mt-2">
						<div class="section-title">EXTRA LARGE CHARTS</div>
						<div class="divider"></div>
						<div class="form-check mb-2">
							<input class="form-check-input chart-check" type="checkbox" data-report-target="overdue-loans" checked
								disabled>
							<label class="form-check-label disabled-label">No extra large charts available</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

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
			chart: {
				type: 'donut',
				height: 250
			},
			labels: Object.keys(userStatus),
			series: Object.values(userStatus),
			colors: palette,
			legend: {
				position: 'bottom'
			},
		}).render();

		const inventory = <?php echo json_encode($inventoryTypes); ?>;
		new ApexCharts(document.querySelector('#inventoryChart'), {
			chart: {
				type: 'bar',
				height: 250
			},
			series: [{
				name: 'Books',
				data: Object.values(inventory)
			}],
			xaxis: {
				categories: Object.keys(inventory)
			},
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
			chart: {
				type: 'bar',
				height: 250,
				stacked: true
			},
			series: [{
					name: 'Loans',
					data: circulationLabels.map((label) => circulation.Loans[label] || 0)
				},
				{
					name: 'Reservations',
					data: circulationLabels.map((label) => circulation.Reservations[label] || 0)
				},
				{
					name: 'Returns',
					data: circulationLabels.map((label) => circulation.Returns[label] || 0)
				},
			],
			xaxis: {
				categories: circulationLabels
			},
			colors: palette,
		}).render();

		const approvals = <?php echo json_encode($pendingApprovals); ?>;
		new ApexCharts(document.querySelector('#approvalChart'), {
			chart: {
				type: 'radar',
				height: 250
			},
			series: [{
				name: 'Pending',
				data: Object.values(approvals)
			}],
			xaxis: {
				categories: Object.keys(approvals)
			},
			colors: [palette[2]],
		}).render();

		const digital = <?php echo json_encode($digitalTotals); ?>;
		const digitalValues = Object.values(digital);
		const digitalBox = document.querySelector('#digitalChart');
		if (digitalBox && digitalValues.every((value) => Number(value) === 0)) {
			digitalBox.innerHTML = '<div class="text-muted small d-flex align-items-center justify-content-center h-100">No digital library activity yet.</div>';
		} else {
			new ApexCharts(digitalBox, {
				chart: {
					type: 'bar',
					height: 250
				},
				series: [{
					name: 'Total',
					data: digitalValues
				}],
				xaxis: {
					categories: ['Resources', 'Files', 'Downloads']
				},
				colors: [palette[1]],
			}).render();
		}

		const finance = {
			Fines: <?php echo json_encode($fineTotals); ?>,
			Waivers: <?php echo json_encode($waiverTotals); ?>,
			Payments: <?php echo json_encode($paymentTotals); ?>,
		};
		new ApexCharts(document.querySelector('#financeChart'), {
			chart: {
				type: 'bar',
				height: 250
			},
			series: [{
					name: 'Count',
					data: [finance.Fines.count, finance.Waivers.count, finance.Payments.count]
				},
				{
					name: 'Amount',
					data: [finance.Fines.amount, finance.Waivers.amount, finance.Payments.amount]
				},
			],
			xaxis: {
				categories: ['Fines', 'Waivers', 'Payments']
			},
			colors: [palette[3], palette[0]],
		}).render();

		const searches = <?php echo json_encode($searchTop); ?>;
		new ApexCharts(document.querySelector('#searchChart'), {
			chart: {
				type: 'bar',
				height: 250
			},
			series: [{
				name: 'Searches',
				data: searches.map((item) => item.total)
			}],
			xaxis: {
				categories: searches.map((item) => item.query || '-')
			},
			colors: [palette[4]],
		}).render();
	}

	function renderHeatmap() {
		const container = document.getElementById('activityHeatmap');
		const grid = document.getElementById('heatmapGrid');
		const monthsEl = document.getElementById('heatmapMonths');
		const userSelect = document.getElementById('activityUserFilter');
		const typeSelect = document.getElementById('activityTypeFilter');
		if (!container || !grid || !monthsEl || !userSelect || !typeSelect) return;

		const data = JSON.parse(container.dataset.activity || '{}');
		const totalDays = 53 * 7;
		const today = new Date();
		const start = new Date(today);
		start.setDate(today.getDate() - (totalDays - 1));

		grid.innerHTML = '';
		monthsEl.innerHTML = '';

		const monthLabels = [];
		for (let i = 0; i < 12; i++) {
			monthLabels.push(new Intl.DateTimeFormat('en', {
				month: 'short'
			}).format(
				new Date(today.getFullYear(), (today.getMonth() - 11 + i + 12) % 12, 1)
			));
		}
		monthLabels.forEach((label) => {
			const span = document.createElement('span');
			span.textContent = label;
			monthsEl.appendChild(span);
		});

		const selectedUser = userSelect.value;
		const selectedType = typeSelect.value;
		const source = selectedUser === 'all' ?
			(data.all || {
				loan: {},
				reserve: {},
				return: {}
			}) :
			((data.users && data.users[selectedUser]) || {
				loan: {},
				reserve: {},
				return: {}
			});

		const types = ['loan', 'reserve', 'return', 'download'];
		const maxByType = {
			loan: 1,
			reserve: 1,
			return: 1,
			download: 1
		};

		types.forEach((type) => {
			const values = [];
			for (let i = 0; i < totalDays; i++) {
				const date = new Date(start);
				date.setDate(start.getDate() + i);
				const key = date.toISOString().slice(0, 10);
				values.push(source[type]?. [key] || 0);
			}
			maxByType[type] = Math.max(1, ...values);
		});

		const levelFor = (count, max) => {
			if (count <= 0) return 0;
			const ratio = count / max;
			if (ratio <= 0.25) return 1;
			if (ratio <= 0.5) return 2;
			if (ratio <= 0.75) return 3;
			return 4;
		};

		for (let i = 0; i < totalDays; i++) {
			const date = new Date(start);
			date.setDate(start.getDate() + i);
			const key = date.toISOString().slice(0, 10);

			const counts = {
			loan: source.loan?. [key] || 0,
			reserve: source.reserve?. [key] || 0,
			return: source.return?. [key] || 0,
			download: source.download?. [key] || 0,
			};

			let activity = selectedType === 'all' ? 'loan' : selectedType;
			if (selectedType === 'all') {
				activity = Object.entries(counts).sort((a, b) => b[1] - a[1])[0][0];
			}

			const count = counts[activity] || 0;
			const level = levelFor(count, maxByType[activity] || 1);
			const cell = document.createElement('div');
			cell.className = `day activity-${activity} level-${level}`;
			cell.title = `${key}: ${count} ${activity}`;
			grid.appendChild(cell);
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

	const reportGroupItems = document.querySelectorAll('.report-group-item');
	const reportGroupToggle = document.querySelector('.report-group-toggle');
	const applyGroupFilter = (value) => {
		document.querySelectorAll('.report-section').forEach((section) => {
			if (value === 'all') {
				section.classList.remove('report-hidden');
				return;
			}
			const group = section.dataset.reportGroup;
			section.classList.toggle('report-hidden', group !== value);
		});
	};
	if (reportGroupItems.length) {
		reportGroupItems.forEach((item) => {
			item.addEventListener('click', (event) => {
				event.preventDefault();
				const value = item.dataset.reportGroup || 'all';
				if (reportGroupToggle) {
					reportGroupToggle.textContent = item.textContent.trim();
				}
				applyGroupFilter(value);
			});
		});
	}

	const reportToggles = Array.from(document.querySelectorAll('.chart-check[data-report-target]'));
	const reportCards = Array.from(document.querySelectorAll('.report-card[data-report-id]'));
	const reportGrids = Array.from(document.querySelectorAll('.reports-grid'));

	const applyReportVisibility = () => {
		reportToggles.forEach((toggle) => {
			const target = toggle.dataset.reportTarget;
			const card = reportCards.find((item) => item.dataset.reportId === target);
			if (!card) return;
			card.classList.toggle('report-card-hidden', !toggle.checked);
		});
	};

	const resetReportOrder = () => {
		reportGrids.forEach((grid) => {
			const cards = Array.from(grid.querySelectorAll('.report-card[data-report-order]'));
			cards.sort((a, b) => Number(a.dataset.reportOrder) - Number(b.dataset.reportOrder));
			cards.forEach((card) => grid.appendChild(card));
		});
	};

	const setAll = (value) => {
		reportToggles.forEach((toggle) => {
			toggle.checked = value;
		});
		applyReportVisibility();
	};

	document.querySelectorAll('[data-report-action]').forEach((btn) => {
		btn.addEventListener('click', () => {
			const action = btn.dataset.reportAction;
			if (action === 'enable-all') {
				setAll(true);
			} else if (action === 'disable-all') {
				setAll(false);
			} else if (action === 'reset-order') {
				setAll(true);
				resetReportOrder();
			}
		});
	});

	reportToggles.forEach((toggle) => {
		toggle.addEventListener('change', applyReportVisibility);
	});

	applyReportVisibility();

	const activityUserFilter = document.getElementById('activityUserFilter');
	const activityTypeFilter = document.getElementById('activityTypeFilter');
	if (activityUserFilter && activityTypeFilter) {
		activityUserFilter.addEventListener('change', renderHeatmap);
		activityTypeFilter.addEventListener('change', renderHeatmap);
	}
})();
</script>
