<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/permissions.php';

$context = rbac_get_context($conn);
$roleName = $context['role_name'] ?? '';
$isLibrarian = strcasecmp($roleName, 'Librarian') === 0;
if (!$context['is_admin'] && !$isLibrarian) {
	header('Location: ' . BASE_URL . 'user_dashboard.php');
	exit;
}

$perPage = 10;
$loanPage = max(1, (int) ($_GET['loan_page'] ?? 1));
$reservationPage = max(1, (int) ($_GET['reservation_page'] ?? 1));
$returnPage = max(1, (int) ($_GET['return_page'] ?? 1));

$loanOffset = ($loanPage - 1) * $perPage;
$reservationOffset = ($reservationPage - 1) * $perPage;
$returnOffset = ($returnPage - 1) * $perPage;

$loanTotalRow = $conn->query("SELECT COUNT(*) AS total FROM loans WHERE status = 'pending'");
$loanTotal = $loanTotalRow ? (int) ($loanTotalRow->fetch_assoc()['total'] ?? 0) : 0;
$loanPages = max(1, (int) ceil($loanTotal / $perPage));

$pendingLoans = $conn->query(
	"SELECT l.loan_id, l.created_date, u.username, u.email, b.title, c.copy_id
	 FROM loans l
	 JOIN book_copies c ON l.copy_id = c.copy_id
	 JOIN book_editions e ON c.edition_id = e.edition_id
	 JOIN books b ON e.book_id = b.book_id
	 JOIN users u ON l.user_id = u.user_id
	 WHERE l.status = 'pending'
	 ORDER BY l.created_date ASC
	 LIMIT $perPage OFFSET $loanOffset"
);

$hasReservationBookId = false;
$reservationColumnCheck = $conn->query("SHOW COLUMNS FROM reservations LIKE 'book_id'");
if ($reservationColumnCheck && $reservationColumnCheck->num_rows > 0) {
	$hasReservationBookId = true;
}

if ($hasReservationBookId) {
	$resCountRow = $conn->query("SELECT COUNT(*) AS total FROM reservations WHERE status = 'pending'");
	$resTotal = $resCountRow ? (int) ($resCountRow->fetch_assoc()['total'] ?? 0) : 0;
	$resPages = max(1, (int) ceil($resTotal / $perPage));
	$pendingReservations = $conn->query(
		"SELECT r.reservation_id, r.created_date, u.username, u.email, b.title, r.copy_id
		 FROM reservations r
		 JOIN books b ON r.book_id = b.book_id
		 JOIN users u ON r.user_id = u.user_id
		 WHERE r.status = 'pending'
		 ORDER BY r.created_date ASC
		 LIMIT $perPage OFFSET $reservationOffset"
	);
} else {
	$resCountRow = $conn->query("SELECT COUNT(*) AS total FROM reservations WHERE status = 'pending'");
	$resTotal = $resCountRow ? (int) ($resCountRow->fetch_assoc()['total'] ?? 0) : 0;
	$resPages = max(1, (int) ceil($resTotal / $perPage));
	$pendingReservations = $conn->query(
		"SELECT r.reservation_id, r.created_date, u.username, u.email, b.title, r.copy_id
		 FROM reservations r
		 JOIN book_copies c ON r.copy_id = c.copy_id
		 JOIN book_editions e ON c.edition_id = e.edition_id
		 JOIN books b ON e.book_id = b.book_id
		 JOIN users u ON r.user_id = u.user_id
		 WHERE r.status = 'pending'
		 ORDER BY r.created_date ASC
		 LIMIT $perPage OFFSET $reservationOffset"
	);
}

$returnTotalRow = $conn->query("SELECT COUNT(*) AS total FROM returns WHERE status = 'pending'");
$returnTotal = $returnTotalRow ? (int) ($returnTotalRow->fetch_assoc()['total'] ?? 0) : 0;
$returnPages = max(1, (int) ceil($returnTotal / $perPage));

$pendingReturns = $conn->query(
	"SELECT r.return_id, r.created_date, l.loan_id, u.username, u.email, b.title, c.copy_id
	 FROM returns r
	 JOIN loans l ON r.loan_id = l.loan_id
	 JOIN book_copies c ON l.copy_id = c.copy_id
	 JOIN book_editions e ON c.edition_id = e.edition_id
	 JOIN books b ON e.book_id = b.book_id
	 JOIN users u ON l.user_id = u.user_id
	 WHERE r.status = 'pending'
	 ORDER BY r.created_date ASC
	 LIMIT $perPage OFFSET $returnOffset"
);

$alerts = [];
$loanStatus = $_GET['loan'] ?? '';
if ($loanStatus !== '') {
	if ($loanStatus === 'success') {
		$alerts[] = ['success', 'Loan request updated successfully.'];
	} elseif ($loanStatus === 'invalid') {
		$alerts[] = ['warning', 'Loan request is no longer pending.'];
	} elseif ($loanStatus === 'error') {
		$alerts[] = ['danger', 'Loan update failed.'];
	}
}

$reservationStatus = $_GET['reservation'] ?? '';
if ($reservationStatus !== '') {
	if ($reservationStatus === 'success') {
		$alerts[] = ['success', 'Reservation updated successfully.'];
	} elseif ($reservationStatus === 'invalid') {
		$alerts[] = ['warning', 'Reservation request is no longer pending.'];
	} elseif ($reservationStatus === 'unavailable') {
		$alerts[] = ['warning', 'No available copies to approve this reservation.'];
	} elseif ($reservationStatus === 'error') {
		$alerts[] = ['danger', 'Reservation update failed.'];
	}
}

$returnStatus = $_GET['return'] ?? '';
if ($returnStatus !== '') {
	if ($returnStatus === 'success') {
		$alerts[] = ['success', 'Return request updated successfully.'];
	} elseif ($returnStatus === 'invalid') {
		$alerts[] = ['warning', 'Return request is no longer pending.'];
	} elseif ($returnStatus === 'error') {
		$alerts[] = ['danger', 'Return update failed.'];
	}
}
?>

<?php include('include/header_resources.php') ?>

<?php include('include/header.php') ?>
<?php include('sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<!--begin::App Content-->
	<div class="app-content">
		<!--begin::Container-->
		<div class="container-fluid">
			<!--begin::Row-->
			<div class="row">

				<div class="container py-5">
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h1 class="mb-0">Librarian Dashboard</h1>
						<div class="row">
							<div class="col-md-12">
								<div id="deployStatus" class="small text-muted"></div>
							</div>
						</div>
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
						return BASE_URL . 'dashboard.php?' . http_build_query($query);
					};
					?>

					<div class="row g-4">
						<div class="col-12">
							<div class="card shadow-sm">
								<div class="card-header d-flex justify-content-between align-items-center">
									<h5 class="mb-0">Pending Loan Requests</h5>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-bordered table-hover align-middle">
											<thead class="table-light">
												<tr>
													<th>#</th>
													<th>Book</th>
													<th>Copy ID</th>
													<th>User</th>
													<th>Requested</th>
													<th class="text-center">Actions</th>
												</tr>
											</thead>
											<tbody>
												<?php if ($pendingLoans && $pendingLoans->num_rows > 0): ?>
												<?php while ($row = $pendingLoans->fetch_assoc()): ?>
												<tr>
													<td><?= htmlspecialchars($row['loan_id']) ?></td>
													<td><?= htmlspecialchars($row['title']) ?></td>
													<td><?= htmlspecialchars($row['copy_id'] ?: '-') ?></td>
													<td><?= htmlspecialchars($row['username'] ?: $row['email']) ?></td>
													<td><?= htmlspecialchars($row['created_date']) ?></td>
													<td class="text-center">
														<form method="post" action="<?php echo BASE_URL; ?>actions/admin_process_loan.php" class="d-inline">
															<input type="hidden" name="loan_id" value="<?= (int) $row['loan_id'] ?>">
															<button class="btn btn-sm btn-success" name="action" value="approve">Approve</button>
														</form>
														<form method="post" action="<?php echo BASE_URL; ?>actions/admin_process_loan.php" class="d-inline">
															<input type="hidden" name="loan_id" value="<?= (int) $row['loan_id'] ?>">
															<button class="btn btn-sm btn-danger" name="action" value="reject">Reject</button>
														</form>
													</td>
												</tr>
												<?php endwhile; ?>
												<?php else: ?>
												<tr>
													<td colspan="6" class="text-center text-muted">No pending loan requests.</td>
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
								<div class="card-header d-flex justify-content-between align-items-center">
									<h5 class="mb-0">Pending Reservation Requests</h5>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-bordered table-hover align-middle">
											<thead class="table-light">
												<tr>
													<th>#</th>
													<th>Book</th>
													<th>Copy ID</th>
													<th>User</th>
													<th>Requested</th>
													<th class="text-center">Actions</th>
												</tr>
											</thead>
											<tbody>
												<?php if ($pendingReservations && $pendingReservations->num_rows > 0): ?>
												<?php while ($row = $pendingReservations->fetch_assoc()): ?>
												<tr>
													<td><?= htmlspecialchars($row['reservation_id']) ?></td>
													<td><?= htmlspecialchars($row['title']) ?></td>
													<td><?= htmlspecialchars($row['copy_id']) ?></td>
													<td><?= htmlspecialchars($row['username'] ?: $row['email']) ?></td>
													<td><?= htmlspecialchars($row['created_date']) ?></td>
													<td class="text-center">
														<form method="post" action="<?php echo BASE_URL; ?>actions/admin_process_reservation.php" class="d-inline">
															<input type="hidden" name="reservation_id" value="<?= (int) $row['reservation_id'] ?>">
															<button class="btn btn-sm btn-success" name="action" value="approve">Approve</button>
														</form>
														<form method="post" action="<?php echo BASE_URL; ?>actions/admin_process_reservation.php" class="d-inline">
															<input type="hidden" name="reservation_id" value="<?= (int) $row['reservation_id'] ?>">
															<button class="btn btn-sm btn-danger" name="action" value="reject">Reject</button>
														</form>
													</td>
												</tr>
												<?php endwhile; ?>
												<?php else: ?>
												<tr>
													<td colspan="6" class="text-center text-muted">No pending reservation requests.</td>
												</tr>
												<?php endif; ?>
											</tbody>
										</table>
									</div>
									<?php if (!empty($resPages) && $resPages > 1): ?>
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
								<div class="card-header d-flex justify-content-between align-items-center">
									<h5 class="mb-0">Pending Return Requests</h5>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-bordered table-hover align-middle">
											<thead class="table-light">
												<tr>
													<th>#</th>
													<th>Book</th>
													<th>Copy ID</th>
													<th>User</th>
													<th>Requested</th>
													<th class="text-center">Actions</th>
												</tr>
											</thead>
											<tbody>
												<?php if ($pendingReturns && $pendingReturns->num_rows > 0): ?>
												<?php while ($row = $pendingReturns->fetch_assoc()): ?>
												<tr>
													<td><?= htmlspecialchars($row['return_id']) ?></td>
													<td><?= htmlspecialchars($row['title']) ?></td>
													<td><?= htmlspecialchars($row['copy_id']) ?></td>
													<td><?= htmlspecialchars($row['username'] ?: $row['email']) ?></td>
													<td><?= htmlspecialchars($row['created_date']) ?></td>
													<td class="text-center">
														<form method="post" action="<?php echo BASE_URL; ?>actions/admin_process_return.php" class="d-inline">
															<input type="hidden" name="return_id" value="<?= (int) $row['return_id'] ?>">
															<button class="btn btn-sm btn-success" name="action" value="approve">Approve</button>
														</form>
														<form method="post" action="<?php echo BASE_URL; ?>actions/admin_process_return.php" class="d-inline">
															<input type="hidden" name="return_id" value="<?= (int) $row['return_id'] ?>">
															<button class="btn btn-sm btn-danger" name="action" value="reject">Reject</button>
														</form>
													</td>
												</tr>
												<?php endwhile; ?>
												<?php else: ?>
												<tr>
													<td colspan="6" class="text-center text-muted">No pending return requests.</td>
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
						<?php
							$status = $_GET['status'] ?? '';
							$alerts = [];
							if ($status === 'temp_password') {
								$alerts[] = ['success', 'Temporary password saved. Let the user know to log in and change it.'];
							} elseif ($status === 'error') {
								$alerts[] = ['danger', 'Unable to update password. Please try again.'];
							}
						?>
						<?php if ($alerts): ?>
							<div class="toast-container position-fixed top-0 end-0 p-3">
								<?php foreach ($alerts as $alert): ?>
									<div class="toast text-bg-<?php echo htmlspecialchars($alert[0]); ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
										<div class="d-flex">
											<div class="toast-body"><?php echo htmlspecialchars($alert[1]); ?></div>
											<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
							<script>
								document.querySelectorAll('.toast').forEach((toastEl) => {
									if (window.bootstrap) {
										new bootstrap.Toast(toastEl, { delay: 4000 }).show();
									}
								});
							</script>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<!-- row end -->
		</div>
	</div>
</main>
<!--end::App Main-->
<?php include('include/footer.php') ?>
<!-- Deployment Details -->
<script>
Promise.all([
  fetch('/deploy/status.json', { cache: 'no-store' }).then(r => r.json()),
  fetch('/DEPLOYED_SHA.txt', { cache: 'no-store' }).then(r => r.text())
])
  .then(([status, shaText]) => {
    const container = document.getElementById('deployStatus');
    container.textContent = '';

    const sha = shaText.trim(); // important: remove newline

    const rows = [
      `Last deploy: ${status.time}`,
      `SHA: ${sha}`,
      `DB: ${status.dump}`,
      `Result: ${status.result}`
    ];

    rows.forEach(text => {
      const div = document.createElement('div');
      div.textContent = text;
      container.appendChild(div);
    });
  })
  .catch(() => {
    const container = document.getElementById('deployStatus');
    container.textContent = 'Deploy status unavailable';
  });
</script>

<?php include('include/footer_resources.php') ?>
