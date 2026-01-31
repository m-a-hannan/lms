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
	header('Location: ' . BASE_URL . 'book_list.php');
	exit;
}

$token = $_GET['token'] ?? '';
$resultPayload = null;
if ($token !== '' && isset($_SESSION['bulk_import_results'][$token])) {
	$resultPayload = $_SESSION['bulk_import_results'][$token];
	unset($_SESSION['bulk_import_results'][$token]);
}
?>
<?php include(ROOT_PATH . '/include/header_resources.php') ?>
<?php include(ROOT_PATH . '/include/header.php') ?>
<?php include(ROOT_PATH . '/sidebar.php') ?>
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h3 class="mb-0">Bulk Book Import (CSV + ZIP)</h3>
						<a href="<?php echo BASE_URL; ?>book_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>

					<?php if ($resultPayload): ?>
						<?php
							$summary = $resultPayload['summary'] ?? [];
							$errors = $resultPayload['errors'] ?? [];
						?>
						<div class="card shadow-sm mb-4">
							<div class="card-body">
								<h5 class="mb-3">Import Summary</h5>
								<div class="row g-3">
									<div class="col-md-3">
										<div class="border rounded p-3 text-center">
											<div class="text-muted small">Total Rows</div>
											<div class="fs-4 fw-bold"><?php echo (int) ($summary['total'] ?? 0); ?></div>
										</div>
									</div>
									<div class="col-md-3">
										<div class="border rounded p-3 text-center">
											<div class="text-muted small">Inserted</div>
											<div class="fs-4 fw-bold text-success"><?php echo (int) ($summary['inserted'] ?? 0); ?></div>
										</div>
									</div>
									<div class="col-md-3">
										<div class="border rounded p-3 text-center">
											<div class="text-muted small">Skipped</div>
											<div class="fs-4 fw-bold text-warning"><?php echo (int) ($summary['skipped'] ?? 0); ?></div>
										</div>
									</div>
									<div class="col-md-3">
										<div class="border rounded p-3 text-center">
											<div class="text-muted small">Errors</div>
											<div class="fs-4 fw-bold text-danger"><?php echo (int) ($summary['errors'] ?? 0); ?></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<?php if (!empty($errors)): ?>
							<div class="card shadow-sm mb-4">
								<div class="card-body">
									<div class="d-flex justify-content-between align-items-center mb-3">
										<h5 class="mb-0">Error Report</h5>
										<button type="button" class="btn btn-outline-primary btn-sm" id="downloadErrorReport">Download Errors CSV</button>
									</div>
									<div class="table-responsive">
										<table class="table table-bordered table-hover align-middle">
											<thead class="table-light">
												<tr>
													<th>#</th>
													<th>Row</th>
													<th>Field</th>
													<th>Message</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($errors as $index => $error): ?>
													<tr>
														<td><?php echo (int) ($index + 1); ?></td>
														<td><?php echo htmlspecialchars((string) ($error['row'] ?? '')); ?></td>
														<td><?php echo htmlspecialchars((string) ($error['field'] ?? '')); ?></td>
														<td><?php echo htmlspecialchars((string) ($error['message'] ?? '')); ?></td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
									<script>
										window.bulkImportErrors = <?php echo json_encode($errors); ?>;
									</script>
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<div class="card shadow-sm">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center mb-3">
								<h5 class="mb-0">Upload CSV + ZIP</h5>
								<a class="btn btn-outline-secondary btn-sm" href="<?php echo BASE_URL; ?>actions/download_sample_books_csv.php">Sample CSV</a>
							</div>
							<form id="bulkImportForm" method="post" enctype="multipart/form-data" action="<?php echo BASE_URL; ?>actions/import_books_bulk.php">
								<div class="row g-3">
									<div class="col-md-6">
										<label class="form-label">CSV File</label>
										<input type="file" name="csv_file" class="form-control" accept=".csv" required>
										<div class="form-text">CSV columns: title, description, author, isbn, publisher, publish_year, category_id, book_type, ebook_format, cover_file, ebook_file, copy_count</div>
									</div>
									<div class="col-md-6">
										<label class="form-label">ZIP File</label>
										<input type="file" name="zip_file" class="form-control" accept=".zip" required>
										<div class="form-text">ZIP contains cover images and ebook files referenced by filename in CSV.</div>
									</div>
									<div class="col-md-12">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="dryRunToggle" name="dry_run" value="1">
											<label class="form-check-label" for="dryRunToggle">Dry run (validate only, no inserts or file moves)</label>
										</div>
									</div>
								</div>

								<div class="mt-4">
									<div class="progress mb-2" style="height: 8px; display: none;" id="uploadProgressWrap">
										<div class="progress-bar" role="progressbar" style="width: 0%;" id="uploadProgressBar"></div>
									</div>
									<div class="small text-muted" id="uploadStatus"></div>
								</div>

								<div class="mt-4">
									<button type="submit" class="btn btn-primary" id="startImportBtn">Start Import</button>
								</div>
							</form>
						</div>
					</div>

					<div class="card shadow-sm mt-4">
						<div class="card-body">
							<h6 class="mb-2">Rules</h6>
							<ul class="mb-0">
								<li>Physical books require a cover image.</li>
								<li>Ebooks require both cover image and ebook file.</li>
								<li>Duplicate ISBN or title rows are skipped.</li>
								<li>Copy count creates physical copies (ebooks ignore copy count).</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>
<script>
(() => {
	const form = document.getElementById('bulkImportForm');
	const progressWrap = document.getElementById('uploadProgressWrap');
	const progressBar = document.getElementById('uploadProgressBar');
	const statusEl = document.getElementById('uploadStatus');
	const startBtn = document.getElementById('startImportBtn');

	if (form) {
		form.addEventListener('submit', (event) => {
			event.preventDefault();
			const formData = new FormData(form);
			const xhr = new XMLHttpRequest();

			progressWrap.style.display = 'block';
			progressBar.style.width = '0%';
			statusEl.textContent = 'Uploading...';
			startBtn.disabled = true;

			xhr.upload.addEventListener('progress', (e) => {
				if (!e.lengthComputable) return;
				const percent = Math.round((e.loaded / e.total) * 100);
				progressBar.style.width = `${percent}%`;
				statusEl.textContent = `Uploading... ${percent}%`;
			});

			xhr.onreadystatechange = () => {
				if (xhr.readyState !== 4) return;
				startBtn.disabled = false;
				if (xhr.status === 200) {
					try {
						const payload = JSON.parse(xhr.responseText);
						if (payload.redirect) {
							window.location.href = payload.redirect;
							return;
						}
					} catch (err) {
						// fallthrough
					}
					statusEl.textContent = 'Import completed.';
				} else {
					statusEl.textContent = 'Import failed. Please try again.';
				}
			};

			xhr.open('POST', form.action, true);
			xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xhr.send(formData);
		});
	}

	const downloadErrorsBtn = document.getElementById('downloadErrorReport');
	if (downloadErrorsBtn && window.bulkImportErrors) {
		downloadErrorsBtn.addEventListener('click', () => {
			const rows = [['row', 'field', 'message']];
			window.bulkImportErrors.forEach((err) => {
				rows.push([err.row ?? '', err.field ?? '', err.message ?? '']);
			});
			const csv = rows.map((line) => line.map((cell) => {
				const text = String(cell ?? '');
				return `"${text.replace(/"/g, '""')}"`;
			}).join(',')).join('\n');
			const blob = new Blob([csv], { type: 'text/csv' });
			const url = URL.createObjectURL(blob);
			const link = document.createElement('a');
			link.href = url;
			link.download = 'bulk-import-errors.csv';
			link.click();
			URL.revokeObjectURL(url);
		});
	}
})();
</script>
