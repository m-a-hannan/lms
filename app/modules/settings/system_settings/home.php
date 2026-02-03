<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Normalize a PHP file path into a friendly display name.
function normalize_page_name($path)
{
	$name = basename($path, '.php');
	$name = str_replace(['_', '-'], ' ', $name);
	return ucwords($name);
}

// Scan the app directory for PHP pages to register.
function scan_pages($root)
{
	$paths = [];
	$directory = new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS);
	$iterator = new RecursiveIteratorIterator($directory);
	// Walk the filesystem to collect PHP files.
	foreach ($iterator as $file) {
		if (!$file->isFile()) {
			continue;
		}
		if ($file->getExtension() !== 'php') {
			continue;
		}
		$relative = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
		$relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
		if (preg_match('#^(include|crud_files|DB)/#', $relative)) {
			continue;
		}
		if (strpos($relative, '.git/') !== false) {
			continue;
		}
		$paths[] = $relative;
	}
	$paths = array_values(array_unique($paths));
	sort($paths);
	return $paths;
}

// Refresh the page_list table based on current files.
function refresh_page_list($conn)
{
	$root = dirname(__DIR__);
	$paths = scan_pages($root);
	$existing = [];
	$existingResult = $conn->query('SELECT page_id, page_path FROM page_list');
	if ($existingResult) {
		while ($row = $existingResult->fetch_assoc()) {
			$existing[$row['page_path']] = (int) $row['page_id'];
		}
	}

	$seen = [];
	// Insert or update active page entries.
	foreach ($paths as $path) {
		$seen[$path] = true;
		$name = normalize_page_name($path);
		$pathEsc = $conn->real_escape_string($path);
		$nameEsc = $conn->real_escape_string($name);

		if (isset($existing[$path])) {
			$pageId = (int) $existing[$path];
			$conn->query("UPDATE page_list SET page_name = '$nameEsc', is_active = 1 WHERE page_id = $pageId");
		} else {
			$conn->query("INSERT INTO page_list (page_name, page_path, is_active) VALUES ('$nameEsc', '$pathEsc', 1)");
		}
	}

	// Disable pages that no longer exist.
	foreach ($existing as $path => $pageId) {
		if (!isset($seen[$path])) {
			$pageId = (int) $pageId;
			$conn->query("UPDATE page_list SET is_active = 0 WHERE page_id = $pageId");
		}
	}
}

// Refresh page_list when requested.
if (isset($_GET['refresh'])) {
	refresh_page_list($conn);
	header('Location: ' . BASE_URL . 'system_settings/home.php');
	exit;
}

// Default menu state for the form.
$menu_id = null;
$menu = [
	'menu_title' => '',
	'page_id' => '',
	'menu_order' => 0,
	'icon' => '',
	'is_active' => 1,
];

// Load an existing menu item when editing.
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$menu_id = (int) $_GET['id'];
	$result = $conn->query("SELECT * FROM menus WHERE menu_id = $menu_id");
	if ($result && $result->num_rows === 1) {
		$menu = $result->fetch_assoc();
	} else {
		die('Menu item not found.');
	}
}

// Handle menu form submission.
if (isset($_POST['save'])) {
	$menu_title = $conn->real_escape_string(trim($_POST['menu_title']));
	$page_id = (int) $_POST['page_id'];
	$menu_order = (int) $_POST['menu_order'];
	$icon = $conn->real_escape_string(trim($_POST['icon']));
	$is_active = isset($_POST['is_active']) ? 1 : 0;

	// Build insert/update SQL based on edit mode.
	if ($menu_id) {
		$sql = "UPDATE menus SET menu_title = '$menu_title', page_id = $page_id, menu_order = $menu_order, icon = '$icon', is_active = $is_active WHERE menu_id = $menu_id";
	} else {
		$sql = "INSERT INTO menus (menu_title, page_id, menu_order, icon, is_active) VALUES ('$menu_title', $page_id, $menu_order, '$icon', $is_active)";
	}

	// Persist the menu record.
	if ($conn->query($sql)) {
		header('Location: ' . BASE_URL . 'system_settings/index.php');
		exit;
	}

	die('Save failed: ' . $conn->error);
}

// Load pages for the menu dropdown.
$pageList = [];
$pageResult = $conn->query('SELECT page_id, page_name, page_path FROM page_list ORDER BY page_name ASC');
if ($pageResult) {
	while ($row = $pageResult->fetch_assoc()) {
		$pageList[] = $row;
	}
}
?>
<?php // Shared header resources and layout chrome. ?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php include(ROOT_PATH . '/app/views/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<!-- Page header with title and navigation. -->
					<div class="d-flex justify-content-between align-items-center mb-3">
						<h3 class="mb-0"><?= $menu_id ? 'Edit Menu' : 'Add Menu' ?></h3>
						<a href="<?php echo BASE_URL; ?>system_settings/index.php" class="btn btn-secondary btn-sm">Back</a>
					</div>

					<!-- System settings sidebar tabs. -->
					<?php include(__DIR__ . '/sidebar.php') ?>

					<!-- Menu edit form card. -->
					<div class="card shadow-sm">
						<div class="card-body">
							<!-- Submission form for menu items. -->
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
										<!-- Menu title input. -->
										<div class="mb-3">
											<label class="form-label">Menu Title</label>
											<input type="text" class="form-control" name="menu_title" value="<?= htmlspecialchars($menu['menu_title']) ?>" required />
										</div>

										<!-- Page selection input. -->
										<div class="mb-3">
											<label class="form-label">Page</label>
											<select class="form-select" name="page_id" required>
												<option value="">Select page</option>
												<?php foreach ($pageList as $page): ?>
												<option value="<?= $page['page_id'] ?>" <?= (string) $menu['page_id'] === (string) $page['page_id'] ? 'selected' : '' ?>>
													<?= htmlspecialchars($page['page_name']) ?> (<?= htmlspecialchars($page['page_path']) ?>)
												</option>
												<?php endforeach; ?>
											</select>
											<!-- Empty state when no pages exist. -->
											<?php if (!$pageList): ?>
											<p class="text-muted small mt-2">No pages found. Use "Refresh Page List".</p>
											<?php endif; ?>
										</div>

										<!-- Menu order input. -->
										<div class="mb-3">
											<label class="form-label">Menu Order</label>
											<input type="number" class="form-control" name="menu_order" value="<?= htmlspecialchars((string) $menu['menu_order']) ?>" />
										</div>

										<!-- Icon input. -->
										<div class="mb-3">
											<label class="form-label">Icon (Bootstrap class)</label>
											<input type="text" class="form-control" name="icon" value="<?= htmlspecialchars($menu['icon']) ?>" placeholder="bi bi-house" />
										</div>

										<!-- Active flag checkbox. -->
										<div class="form-check mb-3">
											<input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= $menu['is_active'] ? 'checked' : '' ?> />
											<label class="form-check-label" for="is_active">Active</label>
										</div>

										<!-- Submit button. -->
										<button type="submit" name="save" class="btn btn-primary">Save</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
<!--end::App Main-->
<?php // Shared footer layout and scripts. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
