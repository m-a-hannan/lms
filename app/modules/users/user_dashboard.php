<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';

$context = rbac_get_context($conn);
$roleName = $context['role_name'] ?? '';
$isLibrarian = strcasecmp($roleName, 'Librarian') === 0;
if ($context['is_admin'] || $isLibrarian) {
	header('Location: ' . BASE_URL . 'dashboard.php');
	exit;
}

$userId = (int) ($context['user_id'] ?? 0);

$loans = [];
$reservations = [];
$returns = [];

if ($userId > 0) {
	$perPage = 10;
	$loanPage = max(1, (int) ($_GET['loan_page'] ?? 1));
	$reservationPage = max(1, (int) ($_GET['reservation_page'] ?? 1));
	$returnPage = max(1, (int) ($_GET['return_page'] ?? 1));

	$loanOffset = ($loanPage - 1) * $perPage;
	$reservationOffset = ($reservationPage - 1) * $perPage;
	$returnOffset = ($returnPage - 1) * $perPage;

	$loanCountStmt = $conn->prepare("SELECT COUNT(*) AS total FROM loans WHERE user_id = ?");
	$loanPages = 1;
	if ($loanCountStmt) {
		$loanCountStmt->bind_param('i', $userId);
		$loanCountStmt->execute();
		$loanCountResult = $loanCountStmt->get_result();
		$loanTotal = $loanCountResult ? (int) ($loanCountResult->fetch_assoc()['total'] ?? 0) : 0;
		$loanPages = max(1, (int) ceil($loanTotal / $perPage));
		$loanCountStmt->close();
	}

	$resCountStmt = $conn->prepare("SELECT COUNT(*) AS total FROM reservations WHERE user_id = ?");
	$resPages = 1;
	if ($resCountStmt) {
		$resCountStmt->bind_param('i', $userId);
		$resCountStmt->execute();
		$resCountResult = $resCountStmt->get_result();
		$resTotal = $resCountResult ? (int) ($resCountResult->fetch_assoc()['total'] ?? 0) : 0;
		$resPages = max(1, (int) ceil($resTotal / $perPage));
		$resCountStmt->close();
	}

	$returnCountStmt = $conn->prepare(
		"SELECT COUNT(*) AS total
		 FROM returns r
		 JOIN loans l ON r.loan_id = l.loan_id
		 WHERE l.user_id = ?"
	);
	$returnPages = 1;
	if ($returnCountStmt) {
		$returnCountStmt->bind_param('i', $userId);
		$returnCountStmt->execute();
		$returnCountResult = $returnCountStmt->get_result();
		$returnTotal = $returnCountResult ? (int) ($returnCountResult->fetch_assoc()['total'] ?? 0) : 0;
		$returnPages = max(1, (int) ceil($returnTotal / $perPage));
		$returnCountStmt->close();
	}

	$stmt = $conn->prepare(
		"SELECT l.loan_id, l.status, l.issue_date, l.due_date, l.return_date,
				b.title, c.copy_id,
				(SELECT COUNT(*) FROM returns r WHERE r.loan_id = l.loan_id AND r.status = 'pending') AS pending_return
		 FROM loans l
		 JOIN book_copies c ON l.copy_id = c.copy_id
		 JOIN book_editions e ON c.edition_id = e.edition_id
		 JOIN books b ON e.book_id = b.book_id
		 WHERE l.user_id = ?
		 ORDER BY l.created_date DESC
		 LIMIT $perPage OFFSET $loanOffset"
	);
	$stmt->bind_param('i', $userId);
	$stmt->execute();
	$result = $stmt->get_result();
	while ($result && ($row = $result->fetch_assoc())) {
		$loans[] = $row;
	}
	$stmt->close();

	$hasReservationBookId = false;
	$reservationColumnCheck = $conn->query("SHOW COLUMNS FROM reservations LIKE 'book_id'");
	if ($reservationColumnCheck && $reservationColumnCheck->num_rows > 0) {
		$hasReservationBookId = true;
	}

	if ($hasReservationBookId) {
		$stmt = $conn->prepare(
			"SELECT r.reservation_id, r.status, r.reservation_date, r.expiry_date, b.title, r.copy_id
			 FROM reservations r
			 JOIN books b ON r.book_id = b.book_id
			 WHERE r.user_id = ?
			 ORDER BY r.created_date DESC
			 LIMIT $perPage OFFSET $reservationOffset"
		);
	} else {
		$stmt = $conn->prepare(
			"SELECT r.reservation_id, r.status, r.reservation_date, r.expiry_date, b.title, r.copy_id
			 FROM reservations r
			 JOIN book_copies c ON r.copy_id = c.copy_id
			 JOIN book_editions e ON c.edition_id = e.edition_id
			 JOIN books b ON e.book_id = b.book_id
			 WHERE r.user_id = ?
			 ORDER BY r.created_date DESC
			 LIMIT $perPage OFFSET $reservationOffset"
		);
	}
	$stmt->bind_param('i', $userId);
	$stmt->execute();
	$result = $stmt->get_result();
	while ($result && ($row = $result->fetch_assoc())) {
		$reservations[] = $row;
	}
	$stmt->close();

	$stmt = $conn->prepare(
		"SELECT r.return_id, r.status, r.return_date, l.loan_id, b.title, c.copy_id
		 FROM returns r
		 JOIN loans l ON r.loan_id = l.loan_id
		 JOIN book_copies c ON l.copy_id = c.copy_id
		 JOIN book_editions e ON c.edition_id = e.edition_id
		 JOIN books b ON e.book_id = b.book_id
		 WHERE l.user_id = ?
		 ORDER BY r.created_date DESC
		 LIMIT $perPage OFFSET $returnOffset"
	);
	$stmt->bind_param('i', $userId);
	$stmt->execute();
	$result = $stmt->get_result();
	while ($result && ($row = $result->fetch_assoc())) {
		$returns[] = $row;
	}
	$stmt->close();
}

$alerts = [];
$returnStatus = $_GET['return'] ?? '';
if ($returnStatus !== '') {
	if ($returnStatus === 'success') {
		$alerts[] = ['success', 'Return request submitted successfully.'];
	} elseif ($returnStatus === 'pending') {
		$alerts[] = ['warning', 'A return request for this loan is already pending.'];
	} elseif ($returnStatus === 'invalid') {
		$alerts[] = ['warning', 'This loan cannot be returned at the moment.'];
	} elseif ($returnStatus === 'error') {
		$alerts[] = ['danger', 'Return request failed. Please try again.'];
	}
}

function status_badge(string $status): string
{
	$status = strtolower(trim($status));
	$map = [
		'pending' => 'warning',
		'approved' => 'success',
		'rejected' => 'danger',
		'returned' => 'secondary',
	];
	$color = $map[$status] ?? 'secondary';
	$label = $status !== '' ? ucfirst($status) : 'Unknown';
	return '<span class="badge text-bg-' . $color . '">' . htmlspecialchars($label) . '</span>';
}
?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php include(ROOT_PATH . '/app/views/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h1 class="mb-0">User Dashboard</h1>
						<a href="<?php echo BASE_URL; ?>home.php" class="btn btn-outline-primary btn-sm">Browse Books</a>
					</div>

					<?php if ($alerts): ?>
					<div class="mb-3">
						<?php foreach ($alerts as $alert): ?>
						<div class="alert alert-<?php echo htmlspecialchars($alert[0]); ?> mb-2">
							<?php echo htmlspecialchars($alert[1]); ?>
						</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<?php
					$buildPageLink = function (string $param, int $page) {
						$query = $_GET;
						$query[$param] = $page;
						return BASE_URL . 'user_dashboard.php?' . http_build_query($query);
					};
					?>

					<div class="row g-4">
						<div class="col-12">
							<div class="card shadow-sm">
								<div class="card-header">
									<h5 class="mb-0">My Loans</h5>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-bordered table-hover align-middle">
											<thead class="table-light">
												<tr>
													<th>#</th>
													<th>Book</th>
													<th>Copy ID</th>
													<th>Issue Date</th>
													<th>Due Date</th>
													<th>Status</th>
													<th class="text-center">Actions</th>
												</tr>
											</thead>
											<tbody>
												<?php if ($loans): ?>
												<?php foreach ($loans as $row): ?>
												<?php
													$canReturn = $row['status'] === 'approved'
														&& empty($row['return_date'])
														&& (int) $row['pending_return'] === 0;
												?>
												<tr>
													<td><?= htmlspecialchars($row['loan_id']) ?></td>
													<td><?= htmlspecialchars($row['title']) ?></td>
												<td><?= htmlspecialchars($row['copy_id'] ?: '-') ?></td>
													<td><?= htmlspecialchars($row['issue_date'] ?: '-') ?></td>
													<td><?= htmlspecialchars($row['due_date'] ?: '-') ?></td>
													<td><?= status_badge((string) $row['status']) ?></td>
													<td class="text-center">
														<?php if ($canReturn): ?>
														<form method="post" action="<?php echo BASE_URL; ?>actions/request_return.php" class="d-inline">
															<input type="hidden" name="loan_id" value="<?= (int) $row['loan_id'] ?>">
															<button class="btn btn-sm btn-warning">Request Return</button>
														</form>
														<?php else: ?>
														<span class="text-muted">-</span>
														<?php endif; ?>
													</td>
												</tr>
												<?php endforeach; ?>
												<?php else: ?>
												<tr>
													<td colspan="7" class="text-center text-muted">No loans found.</td>
												</tr>
												<?php endif; ?>
											</tbody>
										</table>
									</div>
									<?php if ($loanPages > 1): ?>
									<nav>
										<ul class="pagination pagination-sm mb-0">
											<li class="page-item <?php echo $loanPage <= 1 ? 'disabled' : ''; ?>">
												<a class="page-link" href="<?php echo $buildPageLink('loan_page', max(1, $loanPage - 1)); ?>">Prev</a>
											</li>
											<?php for ($i = 1; $i <= $loanPages; $i++): ?>
											<li class="page-item <?php echo $i === $loanPage ? 'active' : ''; ?>">
												<a class="page-link" href="<?php echo $buildPageLink('loan_page', $i); ?>"><?php echo $i; ?></a>
											</li>
											<?php endfor; ?>
											<li class="page-item <?php echo $loanPage >= $loanPages ? 'disabled' : ''; ?>">
												<a class="page-link" href="<?php echo $buildPageLink('loan_page', min($loanPages, $loanPage + 1)); ?>">Next</a>
											</li>
										</ul>
									</nav>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<div class="col-12">
							<div class="card shadow-sm">
								<div class="card-header">
									<h5 class="mb-0">My Reservations</h5>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-bordered table-hover align-middle">
											<thead class="table-light">
												<tr>
													<th>#</th>
													<th>Book</th>
													<th>Copy ID</th>
													<th>Reserved On</th>
													<th>Expiry</th>
													<th>Status</th>
												</tr>
											</thead>
											<tbody>
												<?php if ($reservations): ?>
												<?php foreach ($reservations as $row): ?>
												<tr>
													<td><?= htmlspecialchars($row['reservation_id']) ?></td>
													<td><?= htmlspecialchars($row['title']) ?></td>
													<td><?= htmlspecialchars($row['copy_id']) ?></td>
													<td><?= htmlspecialchars($row['reservation_date'] ?: '-') ?></td>
													<td><?= htmlspecialchars($row['expiry_date'] ?: '-') ?></td>
													<td><?= status_badge((string) $row['status']) ?></td>
												</tr>
												<?php endforeach; ?>
												<?php else: ?>
												<tr>
													<td colspan="6" class="text-center text-muted">No reservations found.</td>
												</tr>
												<?php endif; ?>
											</tbody>
										</table>
									</div>
									<?php if ($resPages > 1): ?>
									<nav>
										<ul class="pagination pagination-sm mb-0">
											<li class="page-item <?php echo $reservationPage <= 1 ? 'disabled' : ''; ?>">
												<a class="page-link" href="<?php echo $buildPageLink('reservation_page', max(1, $reservationPage - 1)); ?>">Prev</a>
											</li>
											<?php for ($i = 1; $i <= $resPages; $i++): ?>
											<li class="page-item <?php echo $i === $reservationPage ? 'active' : ''; ?>">
												<a class="page-link" href="<?php echo $buildPageLink('reservation_page', $i); ?>"><?php echo $i; ?></a>
											</li>
											<?php endfor; ?>
											<li class="page-item <?php echo $reservationPage >= $resPages ? 'disabled' : ''; ?>">
												<a class="page-link" href="<?php echo $buildPageLink('reservation_page', min($resPages, $reservationPage + 1)); ?>">Next</a>
											</li>
										</ul>
									</nav>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<div class="col-12">
							<div class="card shadow-sm">
								<div class="card-header">
									<h5 class="mb-0">My Return Requests</h5>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-bordered table-hover align-middle">
											<thead class="table-light">
												<tr>
													<th>#</th>
													<th>Book</th>
													<th>Copy ID</th>
													<th>Return Date</th>
													<th>Status</th>
												</tr>
											</thead>
											<tbody>
												<?php if ($returns): ?>
												<?php foreach ($returns as $row): ?>
												<tr>
													<td><?= htmlspecialchars($row['return_id']) ?></td>
													<td><?= htmlspecialchars($row['title']) ?></td>
													<td><?= htmlspecialchars($row['copy_id']) ?></td>
													<td><?= htmlspecialchars($row['return_date'] ?: '-') ?></td>
													<td><?= status_badge((string) $row['status']) ?></td>
												</tr>
												<?php endforeach; ?>
												<?php else: ?>
												<tr>
													<td colspan="5" class="text-center text-muted">No return requests found.</td>
												</tr>
												<?php endif; ?>
											</tbody>
										</table>
									</div>
									<?php if ($returnPages > 1): ?>
									<nav>
										<ul class="pagination pagination-sm mb-0">
											<li class="page-item <?php echo $returnPage <= 1 ? 'disabled' : ''; ?>">
												<a class="page-link" href="<?php echo $buildPageLink('return_page', max(1, $returnPage - 1)); ?>">Prev</a>
											</li>
											<?php for ($i = 1; $i <= $returnPages; $i++): ?>
											<li class="page-item <?php echo $i === $returnPage ? 'active' : ''; ?>">
												<a class="page-link" href="<?php echo $buildPageLink('return_page', $i); ?>"><?php echo $i; ?></a>
											</li>
											<?php endfor; ?>
											<li class="page-item <?php echo $returnPage >= $returnPages ? 'disabled' : ''; ?>">
												<a class="page-link" href="<?php echo $buildPageLink('return_page', min($returnPages, $returnPage + 1)); ?>">Next</a>
											</li>
										</ul>
									</nav>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>