<?php

// Normalize a request path for RBAC comparisons.
function rbac_normalize_path($path)
{
	// Strip query string and trim whitespace.
	$path = strtok((string) $path, '?');
	$path = trim($path);
	// Return empty when no path is provided.
	if ($path === '') {
		return '';
	}

	// Remove the BASE_URL prefix when present.
	if (defined('BASE_URL')) {
		$base = (string) BASE_URL;
		if ($base !== '' && $base !== '/' && strpos($path, $base) === 0) {
			$path = substr($path, strlen($base));
		}
	}

	// Strip the public/ prefix when the rewrite target is exposed.
	if (strpos($path, 'public/') === 0) {
		$path = substr($path, strlen('public/'));
	}

	// Return a normalized, leading-slash-free path.
	return ltrim($path, '/');
}

// Build and cache the RBAC context for the current session.
function rbac_get_context($conn)
{
	static $context = null;
	// Return cached context for subsequent calls.
	if ($context !== null) {
		return $context;
	}

	// Initialize the default context shape.
	$context = [
		'user_id' => 0,
		'role_id' => 0,
		'role_name' => '',
		'is_admin' => false,
		'permissions' => [],
	];

	// Ensure session is active before reading user data.
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	// Load the current user id from the session.
	$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
	$context['user_id'] = $userId;
	// Return defaults when no user is logged in.
	if ($userId <= 0) {
		return $context;
	}

	// Validate the user account is active and approved.
	$userResult = $conn->query("SELECT account_status, deleted_date FROM users WHERE user_id = $userId LIMIT 1");
	if (!$userResult || $userResult->num_rows !== 1) {
		session_unset();
		session_destroy();
		$context['user_id'] = 0;
		return $context;
	}
	$userRow = $userResult->fetch_assoc();
	$accountStatus = $userRow['account_status'] ?? 'pending';
	// Clear session if the account is deleted or not approved.
	if (!empty($userRow['deleted_date']) || $accountStatus !== 'approved') {
		session_unset();
		session_destroy();
		$context['user_id'] = 0;
		return $context;
	}

	// Look up the most recent role assigned to the user.
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

		// Store role details and admin flag in the context.
		$context['role_id'] = $roleId;
		$context['role_name'] = $roleName;
		$context['is_admin'] = strcasecmp($roleName, 'Admin') === 0;

		// Load page-level permissions for the current role.
		if ($roleId > 0) {
			$permResult = $conn->query(
				"SELECT pl.page_path, p.can_read, p.can_write, p.deny
				 FROM permissions p
				 JOIN page_list pl ON p.page_id = pl.page_id
				 WHERE p.role_id = $roleId"
			);
			if ($permResult) {
				// Index permissions by normalized page path.
				while ($perm = $permResult->fetch_assoc()) {
					$pagePath = rbac_normalize_path($perm['page_path'] ?? '');
					// Skip invalid or empty page paths.
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

// Determine whether the user can access a path for a given action.
function rbac_can_access($conn, $path, $action = 'read')
{
	// Resolve context and require an authenticated user.
	$context = rbac_get_context($conn);
	if ($context['user_id'] <= 0) {
		return false;
	}

	// Normalize the path for lookups.
	$pagePath = rbac_normalize_path($path);
	if ($pagePath === '') {
		return false;
	}

	// Respect explicit deny rules.
	$entry = $context['permissions'][$pagePath] ?? null;
	if ($entry && (int) $entry['deny'] === 1) {
		return false;
	}

	// Admins can access all pages.
	if ($context['is_admin']) {
		return true;
	}

	// Non-admins require a permission entry.
	if (!$entry) {
		return false;
	}

	// Enforce write vs read permissions.
	if ($action === 'write') {
		return (int) $entry['can_write'] === 1;
	}

	return (int) $entry['can_read'] === 1 || (int) $entry['can_write'] === 1;
}

// Check whether the user can access any of the given paths.
function rbac_any_access($conn, array $paths)
{
	// Return true as soon as any readable path is found.
	foreach ($paths as $path) {
		if (rbac_can_access($conn, $path, 'read')) {
			return true;
		}
	}

	return false;
}

// Resolve the correct dashboard route for the current user.
function rbac_dashboard_path($conn): string
{
	$context = rbac_get_context($conn);
	// Send unauthenticated users to the login page.
	if (($context['user_id'] ?? 0) <= 0) {
		return 'login.php';
	}

	// Route librarians and admins to the full dashboard.
	$roleName = $context['role_name'] ?? '';
	$isLibrarian = strcasecmp($roleName, 'Librarian') === 0;
	if ($context['is_admin'] || $isLibrarian) {
		return 'dashboard.php';
	}

	// Default to the user dashboard for all other roles.
	return 'user_dashboard.php';
}

?>
