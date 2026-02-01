<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
	$menu_id = (int) $_GET['delete'];
	require_once ROOT_PATH . '/app/includes/library_helpers.php';
	$mode = library_delete_mode();
	$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
	library_set_current_user($conn, $userId);

	$deleted = $mode === 'soft'
		? library_soft_delete($conn, 'menus', 'menu_id', $menu_id, $userId)
		: library_hard_delete($conn, 'menus', 'menu_id', $menu_id);
	if (!$deleted) {
		die('Delete failed: ' . $conn->error);
	}
	header('Location: ' . BASE_URL . 'system_settings/index.php');
	exit;
}

$sql = "SELECT m.menu_id, m.menu_title, m.menu_order, m.icon, m.is_active, p.page_name, p.page_path
	FROM menus m
	LEFT JOIN page_list p ON m.page_id = p.page_id
	WHERE m.deleted_date IS NULL
	ORDER BY m.menu_order ASC, m.menu_title ASC";
$result = $conn->query($sql);
if ($result === false) {
	die('Query failed: ' . $conn->error);
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
					<div class="d-flex justify-content-between align-items-center mb-3">
						<h3 class="mb-0">Menu Items</h3>
						<a href="<?php echo BASE_URL; ?>system_settings/home.php" class="btn btn-primary btn-sm">Add Menu</a>
					</div>

					<?php include(__DIR__ . '/sidebar.php') ?>

					<div class="card shadow-sm">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-light">
										<tr>
											<th>ID</th>
											<th>Title</th>
											<th>Page</th>
											<th>Path</th>
											<th>Order</th>
											<th>Icon</th>
											<th>Status</th>
											<th class="text-center">Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php if ($result->num_rows > 0): ?>
										<?php while ($row = $result->fetch_assoc()): ?>
										<tr>
											<td><?= htmlspecialchars($row['menu_id']) ?></td>
											<td><?= htmlspecialchars($row['menu_title']) ?></td>
											<td><?= htmlspecialchars($row['page_name'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['page_path'] ?? '') ?></td>
											<td><?= htmlspecialchars($row['menu_order']) ?></td>
											<td><?= htmlspecialchars($row['icon']) ?></td>
											<td>
												<span class="badge <?= $row['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
													<?= $row['is_active'] ? 'Active' : 'Inactive' ?>
												</span>
											</td>
											<td class="text-center">
												<a href="<?php echo BASE_URL; ?>system_settings/home.php?id=<?= $row['menu_id'] ?>" class="text-primary me-2" title="Edit">
													<i class="bi bi-pencil-square fs-5"></i>
												</a>
												<a href="<?php echo BASE_URL; ?>system_settings/index.php?delete=<?= $row['menu_id'] ?>" class="text-danger" title="Delete" data-confirm-delete data-delete-label="menu" data-delete-id="<?= (int) $row['menu_id'] ?>">
													<i class="bi bi-trash fs-5"></i>
												</a>
											</td>
										</tr>
										<?php endwhile; ?>
										<?php else: ?>
										<tr>
											<td colspan="8" class="text-center text-muted">No menu items found.</td>
										</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
<!--end::App Main-->
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
