<?php
require_once dirname(__DIR__, 3) . '/includes/config.php';
// Local navigation for system settings pages.
?>
<div class="d-flex flex-wrap gap-2 mb-3">
	<a href="<?php echo BASE_URL; ?>system_settings/index.php" class="btn btn-outline-secondary btn-sm">Menu Items</a>
	<a href="<?php echo BASE_URL; ?>system_settings/home.php" class="btn btn-outline-secondary btn-sm">Add Menu</a>
	<a href="<?php echo BASE_URL; ?>system_settings/home.php?refresh=1" class="btn btn-outline-secondary btn-sm">Refresh Page List</a>
</div>