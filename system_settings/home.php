<?php
require_once __DIR__ . '/../include/config.php';
require_once ROOT_PATH . '/include/connection.php';

function normalize_page_name($path)
{
	$name = basename($path, '.php');
	$name = str_replace(['_', '-'], ' ', $name);
	return ucwords($name);
}

function scan_pages($root)
{
	$paths = [];
	$directory = new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS);
	$iterator = new RecursiveIteratorIterator($directory);
	foreach ($iterator as $file) {
		if (!$file->isFile()) {
			continue;
		}
		if ($file->getExtension() !== 'php') {
			continue;
		}
		$relative = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
		$relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
		if (preg_match('#^(include|crud_files|templates|DB|system_settings)/#', $relative)) {
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

function refresh_page_list($conn)
{
	$root = dirname(__DIR__);
	$paths = scan_pages($root);
	$conn->query('DELETE FROM page_list');

	foreach ($paths as $path) {
		$name = normalize_page_name($path);
		$pathEsc = $conn->real_escape_string($path);
		$nameEsc = $conn->real_escape_string($name);
		$conn->query("INSERT INTO page_list (page_name, page_path, is_active) VALUES ('$nameEsc', '$pathEsc', 1)");
	}
}

if (isset($_GET['refresh'])) {
	refresh_page_list($conn);
	header('Location: ' . BASE_URL . 'system_settings/home.php');
	exit;
}

$menu_id = null;
$menu = [
	'menu_title' => '',
	'page_id' => '',
	'menu_order' => 0,
	'icon' => '',
	'is_active' => 1,
];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$menu_id = (int) $_GET['id'];
	$result = $conn->query("SELECT * FROM menus WHERE menu_id = $menu_id");
	if ($result && $result->num_rows === 1) {
		$menu = $result->fetch_assoc();
	} else {
		die('Menu item not found.');
	}
}

if (isset($_POST['save'])) {
	$menu_title = $conn->real_escape_string(trim($_POST['menu_title']));
	$page_id = (int) $_POST['page_id'];
	$menu_order = (int) $_POST['menu_order'];
	$icon = $conn->real_escape_string(trim($_POST['icon']));
	$is_active = isset($_POST['is_active']) ? 1 : 0;

	if ($menu_id) {
		$sql = "UPDATE menus SET menu_title = '$menu_title', page_id = $page_id, menu_order = $menu_order, icon = '$icon', is_active = $is_active WHERE menu_id = $menu_id";
	} else {
		$sql = "INSERT INTO menus (menu_title, page_id, menu_order, icon, is_active) VALUES ('$menu_title', $page_id, $menu_order, '$icon', $is_active)";
	}

	if ($conn->query($sql)) {
		header('Location: ' . BASE_URL . 'system_settings/index.php');
		exit;
	}

	die('Save failed: ' . $conn->error);
}

$pageList = [];
$pageResult = $conn->query('SELECT page_id, page_name, page_path FROM page_list ORDER BY page_name ASC');
if ($pageResult) {
	while ($row = $pageResult->fetch_assoc()) {
		$pageList[] = $row;
	}
}
?>
<?php include(ROOT_PATH . '/include/header_resources.php') ?>
<?php include(ROOT_PATH . '/include/header.php') ?>
<?php include(ROOT_PATH . '/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<div class="d-flex justify-content-between align-items-center mb-3">
						<h3 class="mb-0"><?= $menu_id ? 'Edit Menu' : 'Add Menu' ?></h3>
						<a href="<?php echo BASE_URL; ?>system_settings/index.php" class="btn btn-secondary btn-sm">Back</a>
					</div>

					<?php include(__DIR__ . '/sidebar.php') ?>

					<div class="card shadow-sm">
						<div class="card-body">
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
										<div class="mb-3">
											<label class="form-label">Menu Title</label>
											<input type="text" class="form-control" name="menu_title" value="<?= htmlspecialchars($menu['menu_title']) ?>" required />
										</div>

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
											<?php if (!$pageList): ?>
											<p class="text-muted small mt-2">No pages found. Use "Refresh Page List".</p>
											<?php endif; ?>
										</div>

										<div class="mb-3">
											<label class="form-label">Menu Order</label>
											<input type="number" class="form-control" name="menu_order" value="<?= htmlspecialchars((string) $menu['menu_order']) ?>" />
										</div>

										<div class="mb-3">
											<label class="form-label">Icon (Bootstrap class)</label>
											<input type="text" class="form-control" name="icon" value="<?= htmlspecialchars($menu['icon']) ?>" placeholder="bi bi-house" />
										</div>

										<div class="form-check mb-3">
											<input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= $menu['is_active'] ? 'checked' : '' ?> />
											<label class="form-check-label" for="is_active">Active</label>
										</div>

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
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>
