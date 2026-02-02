<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';

$context = rbac_get_context($conn);
$canDelete = (bool) ($context['is_admin'] ?? false);

function normalize_media_path($path)
{
	$path = trim((string) $path);
	if ($path === '') {
		return '';
	}
	$path = str_replace('\\', '/', $path);
	$path = ltrim($path, '/');
	return $path;
}

function format_media_bytes($bytes)
{
	$bytes = (int) $bytes;
	if ($bytes <= 0) {
		return '0 KB';
	}
	$kb = $bytes / 1024;
	if ($kb < 1024) {
		return number_format($kb, 1) . ' KB';
	}
	$mb = $kb / 1024;
	return number_format($mb, 1) . ' MB';
}

function collect_media_files($directory, $relativePrefix, array $allowedExtensions)
{
	$files = [];
	if (!is_dir($directory)) {
		return $files;
	}

	$relativePrefix = trim((string) $relativePrefix, '/');
	$relativePrefix = $relativePrefix === '' ? '' : $relativePrefix . '/';

	$iterator = new DirectoryIterator($directory);
	foreach ($iterator as $fileinfo) {
		if (!$fileinfo->isFile()) {
			continue;
		}
		$ext = strtolower($fileinfo->getExtension());
		if (!in_array($ext, $allowedExtensions, true)) {
			continue;
		}
		$filename = $fileinfo->getFilename();
		$files[] = [
			'path' => $relativePrefix . $filename,
			'name' => $filename,
			'size' => $fileinfo->getSize(),
			'mtime' => $fileinfo->getMTime(),
		];
	}

	return $files;
}

function media_label(array $item): string
{
	if (($item['category'] ?? '') === 'profile') {
		return 'Profile Picture';
	}
	$subtype = $item['subtype'] ?? 'unknown';
	if ($subtype === 'physical') {
		return 'Physical Cover';
	}
	if ($subtype === 'ebook') {
		return 'Ebook Cover';
	}
	if ($subtype === 'mixed') {
		return 'Mixed Cover';
	}
	return 'Book Cover';
}

$filter = strtolower(trim((string) ($_GET['filter'] ?? 'all')));
$allowedFilters = ['all', 'cover_physical', 'cover_ebook', 'profile', 'unused'];
if (!in_array($filter, $allowedFilters, true)) {
	$filter = 'all';
}

$status = strtolower(trim((string) ($_GET['status'] ?? '')));
$deletedCount = isset($_GET['deleted']) ? (int) $_GET['deleted'] : 0;
$skippedCount = isset($_GET['skipped']) ? (int) $_GET['skipped'] : 0;
$alertMap = [
	'deleted' => ['success', 'Image deleted.'],
	'bulk_deleted' => ['success', 'Selected images deleted.'],
	'in_use' => ['warning', 'This image is used in the database and cannot be deleted.'],
	'missing' => ['warning', 'Image not found on disk.'],
	'invalid' => ['danger', 'Invalid delete request.'],
	'not_writable' => ['danger', 'Uploads folder is not writable. Check server permissions for the uploads directory.'],
	'forbidden' => ['danger', 'You do not have permission to delete images.'],
	'error' => ['danger', 'Unable to delete the image.'],
];
$alert = $alertMap[$status] ?? null;

$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

$usedCovers = [];
$coverResult = $conn->query("SELECT book_cover_path, book_type FROM books WHERE book_cover_path IS NOT NULL AND book_cover_path <> ''");
if ($coverResult) {
	while ($row = $coverResult->fetch_assoc()) {
		$path = normalize_media_path($row['book_cover_path'] ?? '');
		if ($path === '') {
			continue;
		}
		$type = strtolower(trim((string) ($row['book_type'] ?? '')));
		if (!in_array($type, ['physical', 'ebook'], true)) {
			$type = 'unknown';
		}
		if (!isset($usedCovers[$path])) {
			$usedCovers[$path] = [
				'count' => 0,
				'types' => [],
			];
		}
		$usedCovers[$path]['count']++;
		$usedCovers[$path]['types'][$type] = true;
	}
}

$usedProfiles = [];
$profileResult = $conn->query("SELECT profile_picture FROM user_profiles WHERE profile_picture IS NOT NULL AND profile_picture <> ''");
if ($profileResult) {
	while ($row = $profileResult->fetch_assoc()) {
		$path = normalize_media_path($row['profile_picture'] ?? '');
		if ($path === '') {
			continue;
		}
		$usedProfiles[$path] = true;
	}
}

$coverFiles = collect_media_files(ROOT_PATH . '/public/uploads/book_cover', 'uploads/book_cover', $allowedExtensions);
$profileFiles = collect_media_files(ROOT_PATH . '/public/uploads/profile_picture', 'uploads/profile_picture', $allowedExtensions);

$galleryItems = [];
foreach ($coverFiles as $file) {
	$path = $file['path'];
	$used = isset($usedCovers[$path]);
	$subtype = 'unknown';
	if ($used) {
		$types = $usedCovers[$path]['types'] ?? [];
		$hasPhysical = !empty($types['physical']);
		$hasEbook = !empty($types['ebook']);
		if ($hasPhysical && !$hasEbook) {
			$subtype = 'physical';
		} elseif ($hasEbook && !$hasPhysical) {
			$subtype = 'ebook';
		} elseif ($hasPhysical && $hasEbook) {
			$subtype = 'mixed';
		}
	}
	$galleryItems[] = [
		'category' => 'cover',
		'subtype' => $subtype,
		'path' => $path,
		'url' => BASE_URL . ltrim($path, '/'),
		'name' => $file['name'],
		'size' => $file['size'],
		'size_label' => format_media_bytes($file['size']),
		'mtime' => $file['mtime'],
		'used' => $used,
	];
}

foreach ($profileFiles as $file) {
	$path = $file['path'];
	$galleryItems[] = [
		'category' => 'profile',
		'subtype' => 'profile',
		'path' => $path,
		'url' => BASE_URL . ltrim($path, '/'),
		'name' => $file['name'],
		'size' => $file['size'],
		'size_label' => format_media_bytes($file['size']),
		'mtime' => $file['mtime'],
		'used' => isset($usedProfiles[$path]),
	];
}

usort($galleryItems, function ($a, $b) {
	return ($b['mtime'] ?? 0) <=> ($a['mtime'] ?? 0);
});

$filteredItems = array_values(array_filter($galleryItems, function ($item) use ($filter) {
	if ($filter === 'all') {
		return true;
	}
	if ($filter === 'unused') {
		return empty($item['used']);
	}
	if ($filter === 'profile') {
		return ($item['category'] ?? '') === 'profile';
	}
	if ($filter === 'cover_physical') {
		if (($item['category'] ?? '') !== 'cover') {
			return false;
		}
		if (empty($item['used'])) {
			return true;
		}
		return ($item['subtype'] ?? '') === 'physical';
	}
	if ($filter === 'cover_ebook') {
		if (($item['category'] ?? '') !== 'cover') {
			return false;
		}
		if (empty($item['used'])) {
			return true;
		}
		return ($item['subtype'] ?? '') === 'ebook';
	}
	return true;
}));

$totalCount = count($galleryItems);
$unusedCount = 0;
foreach ($galleryItems as $item) {
	if (empty($item['used'])) {
		$unusedCount++;
	}
}
$shownCount = count($filteredItems);
?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php include(ROOT_PATH . '/app/views/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<!--begin::App Content-->
	<div class="app-content">
		<!--begin::Container-->
		<div class="container-fluid">
			<!--begin::Row-->
			<div class="row">
				<div class="container py-5">
					<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
						<div>
							<h3 class="mb-1">Media Gallery</h3>
							<p class="text-muted mb-0">Used images are locked. Only unused images can be deleted.</p>
						</div>
						<div class="text-muted small">
							<span class="me-3">Total: <strong><?= $totalCount ?></strong></span>
							<span>Unused: <strong><?= $unusedCount ?></strong></span>
						</div>
					</div>

					<?php if ($alert): ?>
						<div class="alert alert-<?= htmlspecialchars($alert[0]) ?>" role="alert">
							<?= htmlspecialchars($alert[1]) ?>
							<?php if ($status === 'bulk_deleted'): ?>
								<span class="ms-2 text-muted">(Deleted: <?= $deletedCount ?>, Skipped: <?= $skippedCount ?>)</span>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<div class="card shadow-sm mb-4">
						<div class="card-body">
							<form method="get" class="row g-3 align-items-end">
								<div class="col-sm-6 col-md-4 col-lg-3">
									<label class="form-label">Filter</label>
									<select name="filter" class="form-select" onchange="this.form.submit()">
										<option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Media</option>
										<option value="cover_physical" <?= $filter === 'cover_physical' ? 'selected' : '' ?>>Book Covers - Physical</option>
										<option value="cover_ebook" <?= $filter === 'cover_ebook' ? 'selected' : '' ?>>Book Covers - Ebook</option>
										<option value="profile" <?= $filter === 'profile' ? 'selected' : '' ?>>Profile Pictures</option>
										<option value="unused" <?= $filter === 'unused' ? 'selected' : '' ?>>Unused Only</option>
									</select>
								</div>
								<div class="col-sm-6 col-md-4 col-lg-3 text-muted small">
									Showing <strong><?= $shownCount ?></strong> of <strong><?= $totalCount ?></strong> images
								</div>
							</form>
						</div>
					</div>

					<form method="post" action="<?= BASE_URL; ?>actions/bulk_delete_media.php" class="media-bulk-form">
						<input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
						<?php if ($canDelete): ?>
							<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
								<div class="form-check me-2">
									<input class="form-check-input" type="checkbox" id="mediaSelectAll">
									<label class="form-check-label" for="mediaSelectAll">Select all unused</label>
								</div>
								<button type="submit" class="btn btn-danger btn-sm" id="bulkDeleteBtn" disabled>Delete selected</button>
							</div>
						<?php endif; ?>

						<div class="media-gallery-grid">
							<?php if (!$filteredItems): ?>
								<div class="media-empty text-muted">No images found for this filter.</div>
							<?php else: ?>
								<?php foreach ($filteredItems as $item): ?>
									<div class="media-card<?= !empty($item['used']) ? ' is-locked' : '' ?>">
										<div class="media-thumb">
											<img src="<?= htmlspecialchars($item['url']) ?>" alt="<?= htmlspecialchars(media_label($item)) ?>">
											<span class="media-label"><?= htmlspecialchars(media_label($item)) ?></span>
											<?php if (!empty($item['used'])): ?>
												<div class="media-overlay" title="Used in database">
													<i class="bi bi-lock"></i>
												</div>
											<?php elseif ($canDelete): ?>
												<label class="media-check">
													<input type="checkbox" class="media-select" name="paths[]" value="<?= htmlspecialchars($item['path']) ?>">
													<span class="media-check-icon"><i class="bi bi-check2"></i></span>
												</label>
											<?php endif; ?>
										</div>
										<div class="media-meta">
											<div class="media-name text-truncate" title="<?= htmlspecialchars($item['name']) ?>"><?= htmlspecialchars($item['name']) ?></div>
											<div class="media-sub">
												<?= htmlspecialchars($item['size_label']) ?>
												·
												<?= htmlspecialchars(date('M j, Y', (int) ($item['mtime'] ?? time()))) ?>
											</div>
										</div>
										<div class="media-actions">
											<?php if (empty($item['used']) && $canDelete): ?>
												<form method="post" action="<?= BASE_URL; ?>actions/delete_media.php" onsubmit="return confirm('Delete this image?');">
													<input type="hidden" name="path" value="<?= htmlspecialchars($item['path']) ?>">
													<input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
													<button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
												</form>
											<?php else: ?>
												<span class="badge text-bg-light">No delete access</span>
											<?php endif; ?>
										</div>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</form>
				</div>
			</div>
			<!-- row end -->
		</div>
	</div>
</main>
<!--end::App Main-->
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
	const selectAll = document.getElementById('mediaSelectAll');
	const bulkBtn = document.getElementById('bulkDeleteBtn');
	const checkboxes = Array.from(document.querySelectorAll('.media-select'));

	const updateBulkState = () => {
		if (!bulkBtn) return;
		const anyChecked = checkboxes.some((box) => box.checked);
		bulkBtn.disabled = !anyChecked;
	};

	if (selectAll) {
		selectAll.addEventListener('change', () => {
			checkboxes.forEach((box) => { box.checked = selectAll.checked; });
			updateBulkState();
		});
	}

	checkboxes.forEach((box) => {
		box.addEventListener('change', () => {
			if (selectAll) {
				selectAll.checked = checkboxes.length > 0 && checkboxes.every((item) => item.checked);
			}
			updateBulkState();
		});
	});
});
</script>
