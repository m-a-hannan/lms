<?php
// Load app configuration, database connection, and permissions helpers.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';

// Ensure a session is active for RBAC context.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Resolve user context and enforce admin/librarian access.
$context = rbac_get_context($conn);
$isLibrarian = strcasecmp($context['role_name'] ?? '', 'Librarian') === 0;
// Redirect non-privileged users to the dashboard.
if (!($context['is_admin'] || $isLibrarian)) {
	header('Location: ' . BASE_URL . 'dashboard.php');
	exit;
}

// Run a grouped count query and return a key/value map.
function fetch_kv($conn, $sql, $key, $value)
{
	$data = [];
	$result = $conn->query($sql);
	// Build the key/value map when results are available.
	if ($result) {
		// Collect each row into the map.
		while ($row = $result->fetch_assoc()) {
			$data[$row[$key]] = (int) $row[$value];
		}
	}
	return $data;
}

// Check whether a column exists in the current database schema.
function column_exists($conn, $table, $column)
{
	$table = $conn->real_escape_string($table);
	$column = $conn->real_escape_string($column);
	$result = $conn->query("SELECT DATABASE() AS db");
	$row = $result ? $result->fetch_assoc() : null;
	$db = $conn->real_escape_string($row['db'] ?? '');
	// Abort when the database name is unavailable.
	if ($db === '') {
		return false;
	}
	$sql = "SELECT COUNT(*) AS total FROM information_schema.COLUMNS
		WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = '$table' AND COLUMN_NAME = '$column'";
	$result = $conn->query($sql);
	// Return true when the column exists.
	if ($result && ($row = $result->fetch_assoc())) {
		return (int) $row['total'] > 0;
	}
	return false;
}

// Aggregate user account status counts.
$userStatuses = ['pending' => 0, 'approved' => 0, 'blocked' => 0, 'suspended' => 0];
// Load status breakdown when the column exists.
if (column_exists($conn, 'users', 'account_status')) {
	$userStatuses = fetch_kv(
		$conn,
		"SELECT account_status, COUNT(*) AS total FROM users WHERE deleted_date IS NULL GROUP BY account_status",
		'account_status',
		'total'
	) ?: $userStatuses;
}

// Aggregate inventory counts by type.
$inventoryTypes = ['physical' => 0, 'ebook' => 0];
// Load inventory types when the column exists.
if (column_exists($conn, 'books', 'book_type')) {
	$inventoryTypes = fetch_kv(
		$conn,
		"SELECT book_type, COUNT(*) AS total FROM books WHERE deleted_date IS NULL GROUP BY book_type",
		'book_type',
		'total'
	) ?: $inventoryTypes;
} else {
	// Fall back to total books when book_type is missing.
	$totalBooks = $conn->query("SELECT COUNT(*) AS total FROM books WHERE deleted_date IS NULL");
	if ($totalBooks && ($row = $totalBooks->fetch_assoc())) {
		$inventoryTypes['physical'] = (int) ($row['total'] ?? 0);
	}
}

// Aggregate circulation status counts.
$loanStatus = ['pending' => 0, 'approved' => 0, 'returned' => 0];
$reservationStatus = ['pending' => 0, 'approved' => 0];
$returnStatus = ['pending' => 0, 'approved' => 0];

// Load loan status counts when available.
if (column_exists($conn, 'loans', 'status')) {
	$loanStatus = fetch_kv(
		$conn,
		"SELECT status, COUNT(*) AS total FROM loans WHERE deleted_date IS NULL GROUP BY status",
		'status',
		'total'
	) ?: $loanStatus;
}
// Load reservation status counts when available.
if (column_exists($conn, 'reservations', 'status')) {
	$reservationStatus = fetch_kv(
		$conn,
		"SELECT status, COUNT(*) AS total FROM reservations WHERE deleted_date IS NULL GROUP BY status",
		'status',
		'total'
	) ?: $reservationStatus;
}
// Load return status counts when available.
if (column_exists($conn, 'returns', 'status')) {
	$returnStatus = fetch_kv(
		$conn,
		"SELECT status, COUNT(*) AS total FROM returns WHERE deleted_date IS NULL GROUP BY status",
		'status',
		'total'
	) ?: $returnStatus;
}

$overdueCount = 0;
// Count overdue loans when a due date column exists.
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

// Build pending approval metrics for the dashboard.
$pendingApprovals = [
	'loans' => (int) ($loanStatus['pending'] ?? 0),
	'reservations' => (int) ($reservationStatus['pending'] ?? 0),
	'returns' => (int) ($returnStatus['pending'] ?? 0),
];

// Aggregate digital library counts and downloads.
$digitalTotals = [
	'resources' => 0,
	'files' => 0,
	'downloads' => 0,
];
// Include ebook totals from books when available.
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
// Include digital resources table totals.
if (column_exists($conn, 'digital_resources', 'resource_id')) {
	$digitalResources = $conn->query("SELECT COUNT(*) AS total FROM digital_resources WHERE deleted_date IS NULL");
	if ($digitalResources && ($row = $digitalResources->fetch_assoc())) {
		$digitalTotals['resources'] += (int) ($row['total'] ?? 0);
	}
}
// Include digital files and downloads totals.
if (column_exists($conn, 'digital_files', 'file_id')) {
	$digitalFiles = $conn->query("SELECT COUNT(*) AS total, COALESCE(SUM(download_count), 0) AS downloads FROM digital_files WHERE deleted_date IS NULL");
	if ($digitalFiles && ($row = $digitalFiles->fetch_assoc())) {
		$digitalTotals['files'] += (int) ($row['total'] ?? 0);
		$digitalTotals['downloads'] = (int) ($row['downloads'] ?? 0);
	}
}

// Include download audit logs when available.
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

// Aggregate fines totals.
$fineTotals = ['count' => 0, 'amount' => 0];
// Load fine totals when the table exists.
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

// Aggregate fine waiver totals.
$waiverTotals = ['count' => 0, 'amount' => 0];
// Load waiver totals when the table exists.
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

// Aggregate payment totals.
$paymentTotals = ['count' => 0, 'amount' => 0];
// Load payment totals when the table exists.
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

// Aggregate library catalog totals.
$bookCount = 0;
$authorCount = 0;
$publisherCount = 0;
$seriesCount = 0;
$librarySizeBytes = 0;

// Count total books in the catalog.
$bookTotalResult = $conn->query("SELECT COUNT(*) AS total FROM books WHERE deleted_date IS NULL");
if ($bookTotalResult && ($row = $bookTotalResult->fetch_assoc())) {
	$bookCount = (int) ($row['total'] ?? 0);
}
// Count distinct authors when available.
if (column_exists($conn, 'books', 'author')) {
	$authorResult = $conn->query("SELECT COUNT(DISTINCT author) AS total FROM books WHERE deleted_date IS NULL AND author IS NOT NULL AND author <> ''");
	if ($authorResult && ($row = $authorResult->fetch_assoc())) {
		$authorCount = (int) ($row['total'] ?? 0);
	}
}
// Count distinct publishers when available.
if (column_exists($conn, 'books', 'publisher')) {
	$publisherResult = $conn->query("SELECT COUNT(DISTINCT publisher) AS total FROM books WHERE deleted_date IS NULL AND publisher IS NOT NULL AND publisher <> ''");
	if ($publisherResult && ($row = $publisherResult->fetch_assoc())) {
		$publisherCount = (int) ($row['total'] ?? 0);
	}
}
// Sum ebook file sizes when available.
if (column_exists($conn, 'books', 'ebook_file_size')) {
	$sizeResult = $conn->query("SELECT COALESCE(SUM(ebook_file_size), 0) AS total FROM books WHERE deleted_date IS NULL");
	if ($sizeResult && ($row = $sizeResult->fetch_assoc())) {
		$librarySizeBytes += (int) ($row['total'] ?? 0);
	}
}
// Sum digital file sizes when available.
if (column_exists($conn, 'digital_files', 'file_size')) {
	$digitalSize = $conn->query("SELECT COALESCE(SUM(file_size), 0) AS total FROM digital_files WHERE deleted_date IS NULL");
	if ($digitalSize && ($row = $digitalSize->fetch_assoc())) {
		$librarySizeBytes += (int) ($row['total'] ?? 0);
	}
}

// Format the total library size for display.
function format_kb($bytes)
{
	// Return a default label for empty sizes.
	if ($bytes <= 0) {
		return '0 KB';
	}
	// Convert bytes to KB/MB for display.
	$kb = $bytes / 1024;
	if ($kb < 1024) {
		return number_format($kb, 0) . ' KB';
	}
	$mb = $kb / 1024;
	return number_format($mb, 1) . ' MB';
}

$librarySizeLabel = format_kb($librarySizeBytes);

$searchTop = [];
// Fetch top search queries when logging is available.
if (column_exists($conn, 'search_logs', 'query_text')) {
	$searchLogs = $conn->query(
		"SELECT query_text, COUNT(*) AS total FROM search_logs GROUP BY query_text ORDER BY total DESC LIMIT 8"
	);
	// Build the top search list for the chart.
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

// Load users for the activity filters.
$userResult = $conn->query(
	"SELECT user_id, username, email
	 FROM users
	 WHERE deleted_date IS NULL
	 ORDER BY username ASC"
);
if ($userResult) {
	// Collect users for the activity filter dropdown.
	while ($row = $userResult->fetch_assoc()) {
		$activityUsers[] = $row;
	}
}

// Helper to accumulate activity counts into the store.
$addActivity = function (&$store, string $type, string $day, int $userId, int $count) {
	if (!isset($store['users'][$userId])) {
		$store['users'][$userId] = ['loan' => [], 'reserve' => [], 'return' => [], 'download' => []];
	}
	$store['users'][$userId][$type][$day] = ($store['users'][$userId][$type][$day] ?? 0) + $count;
	$store['all'][$type][$day] = ($store['all'][$type][$day] ?? 0) + $count;
};

// SQL definitions for activity aggregation by type.
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

// Execute each activity query and aggregate results.
foreach ($activityQueries as $type => $sql) {
	$result = $conn->query($sql);
	// Skip empty or failed result sets.
	if (!$result) {
		continue;
	}
	// Aggregate valid activity rows into the store.
	while ($row = $result->fetch_assoc()) {
		$day = $row['day'] ?? null;
		$userId = (int) ($row['user_id'] ?? 0);
		$count = (int) ($row['total'] ?? 0);
		// Ignore invalid or empty activity rows.
		if (!$day || $userId <= 0 || $count <= 0) {
			continue;
		}
		$addActivity($activityData, $type, $day, $userId, $count);
	}
}

?>
<?php // Shared header resources and layout chrome. ?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php include(ROOT_PATH . '/app/views/sidebar.php') ?>
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid py-4">
			<!-- Report hero with summary stats and controls. -->
			<div class="report-hero mb-4">
				<div class="report-hero-header">
					<h2 class="mb-0">Library Statistics</h2>
					<div class="report-hero-controls">
						<!-- Report group filter dropdown. -->
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
						<!-- Chart settings trigger. -->
						<button class="btn btn-outline-light btn-sm" type="button" aria-label="Settings" data-bs-toggle="modal"
							data-bs-target="#chartConfigModal">
							<i class="bi bi-gear"></i>
						</button>
					</div>
				</div>
				<!-- Key library stats. -->
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

			<!-- Report dashboard header. -->
			<div class="d-flex align-items-center justify-content-between mb-3">
				<div>
					<h3 class="mb-1">Reports Dashboard</h3>
					<p class="text-muted mb-0">Operational snapshot across users, inventory, circulation, and finance.</p>
				</div>
				<!-- Theme toggle buttons for charts. -->
				<div class="report-controls">
					<button class="btn btn-outline-primary btn-sm" type="button" data-chart-theme="default">Default Theme</button>
					<button class="btn btn-outline-secondary btn-sm" type="button" data-chart-theme="muted">Muted Theme</button>
				</div>
			</div>

			<!-- Library-focused report cards. -->
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

			<!-- User-focused report cards. -->
			<div class="report-section" data-report-group="user">
				<div class="report-section-header">User Reports</div>
				<div class="reports-grid">
					<div class="report-card report-card-wide" data-report-id="user-activity" data-report-order="7">
						<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
							<div class="report-title mb-0">User Activity Heatmap</div>
							<div class="activity-controls d-flex align-items-center gap-2">
								<select class="form-select form-select-sm activity-filter" id="activityUserFilter">
									<option value="all" selected>All Users</option>
									<!-- User options for the activity filter. -->
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

<!-- Chart configuration modal. -->
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

<?php // Shared footer layout and scripts. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>

<?php
// Bundle report metrics for the frontend charts.
$reportPayload = [
	'userStatuses' => $userStatuses,
	'inventoryTypes' => $inventoryTypes,
	'pendingApprovals' => $pendingApprovals,
	'digitalTotals' => $digitalTotals,
	'searchTop' => $searchTop,
	'finance' => [
		'Fines' => $fineTotals,
		'Waivers' => $waiverTotals,
		'Payments' => $paymentTotals,
	],
	'circulation' => [
		'Loans' => $loanStatus,
		'Reservations' => $reservationStatus,
		'Returns' => $returnStatus,
	],
];
?>
<!-- Embed report data for the charts. -->
<script id="reports-data" type="application/json"><?php echo json_encode($reportPayload); ?></script>
<!-- Page-specific JS for report charts. -->
<script src="<?php echo BASE_URL; ?>assets/js/pages/reports.js"></script>
