<?php

function rbac_normalize_path($path)
{
	$path = strtok((string) $path, '?');
	$path = trim($path);
	if ($path === '') {
		return '';
	}

	if (defined('BASE_URL')) {
		$base = (string) BASE_URL;
		if ($base !== '' && $base !== '/' && strpos($path, $base) === 0) {
			$path = substr($path, strlen($base));
		}
	}

	return ltrim($path, '/');
}

function rbac_get_context($conn)
{
	static $context = null;
	if ($context !== null) {
		return $context;
	}

	$context = [
		'user_id' => 0,
		'role_id' => 0,
		'role_name' => '',
		'is_admin' => false,
		'permissions' => [],
	];

	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
	$context['user_id'] = $userId;
	if ($userId <= 0) {
		return $context;
	}

	$roleResult = $conn->query(
		"SELECT r.role_id, r.role_name
		 FROM user_roles ur
		 JOIN roles r ON ur.role_id = r.role_id
		 WHERE ur.user_id = $userId
		 ORDER BY ur.user_role_id DESC
		 LIMIT 1"
	);

	if ($roleResult && $roleResult->num_rows === 1) {
		$row = $roleResult->fetch_assoc();
		$roleId = (int) ($row['role_id'] ?? 0);
		$roleName = trim((string) ($row['role_name'] ?? ''));

		$context['role_id'] = $roleId;
		$context['role_name'] = $roleName;
		$context['is_admin'] = strcasecmp($roleName, 'Admin') === 0;

		if ($roleId > 0) {
			$permResult = $conn->query(
				"SELECT pl.page_path, p.can_read, p.can_write, p.deny
				 FROM permissions p
				 JOIN page_list pl ON p.page_id = pl.page_id
				 WHERE p.role_id = $roleId"
			);
			if ($permResult) {
				while ($perm = $permResult->fetch_assoc()) {
					$pagePath = rbac_normalize_path($perm['page_path'] ?? '');
					if ($pagePath === '') {
						continue;
					}
					$context['permissions'][$pagePath] = [
						'can_read' => (int) ($perm['can_read'] ?? 0),
						'can_write' => (int) ($perm['can_write'] ?? 0),
						'deny' => (int) ($perm['deny'] ?? 0),
					];
				}
			}
		}
	}

	return $context;
}

function rbac_can_access($conn, $path, $action = 'read')
{
	$context = rbac_get_context($conn);
	if ($context['user_id'] <= 0) {
		return false;
	}

	$pagePath = rbac_normalize_path($path);
	if ($pagePath === '') {
		return false;
	}

	$entry = $context['permissions'][$pagePath] ?? null;
	if ($entry && (int) $entry['deny'] === 1) {
		return false;
	}

	if ($context['is_admin']) {
		return true;
	}

	if (!$entry) {
		return false;
	}

	if ($action === 'write') {
		return (int) $entry['can_write'] === 1;
	}

	return (int) $entry['can_read'] === 1 || (int) $entry['can_write'] === 1;
}

function rbac_any_access($conn, array $paths)
{
	foreach ($paths as $path) {
		if (rbac_can_access($conn, $path, 'read')) {
			return true;
		}
	}

	return false;
}

?>
