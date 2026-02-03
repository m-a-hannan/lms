<?php
// Load core configuration, database connection, and permission helpers.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';

// Ensure session is active for auth context.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Require admin or librarian privileges to manage users.
$context = rbac_get_context($conn);
$isLibrarian = strcasecmp($context['role_name'] ?? '', 'Librarian') === 0;
if (!($context['is_admin'] || $isLibrarian)) {
	header('Location: ' . BASE_URL . 'user_list.php?status=error');
	exit;
}

// Accept only POST requests for user status changes.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'user_list.php');
	exit;
}

// Validate incoming user id and action.
$userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
$action = strtolower(trim($_POST['action'] ?? ''));
if ($userId <= 0 || !in_array($action, ['approve', 'block', 'suspend', 'delete'], true)) {
	header('Location: ' . BASE_URL . 'user_list.php?status=error');
	exit;
}

// Map actions to account status values.
$statusMap = [
	'approve' => 'approved',
	'block' => 'blocked',
	'suspend' => 'suspended'
];
$status = $statusMap[$action] ?? null;
$actorId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

// Handle hard-delete actions in a transaction.
if ($action === 'delete') {
	$conn->begin_transaction();
	try {
		// Remove user-owned records first.
		$conn->query("DELETE FROM user_roles WHERE user_id = $userId");
		$conn->query("DELETE FROM user_profiles WHERE user_id = $userId");
		$conn->query("DELETE FROM notifications WHERE user_id = $userId");
		$conn->query("DELETE FROM loans WHERE user_id = $userId");
		$conn->query("DELETE FROM reservations WHERE user_id = $userId");
		$conn->query("DELETE FROM audit_logs WHERE user_id = $userId");

		// Null out audit/ownership references to avoid FK failures.
		$auditTables = [
			'announcements', 'audit_logs', 'backups', 'book_categories', 'book_copies',
			'book_editions', 'books', 'categories', 'digital_files', 'digital_resources',
			'fine_waivers', 'fines', 'holidays', 'library_policies', 'loans', 'notifications',
			'payments', 'policy_changes', 'reservations', 'returns', 'roles', 'system_settings',
			'user_profiles', 'user_roles'
		];
		// Clear audit columns for each table referencing this user.
		foreach ($auditTables as $table) {
			$conn->query("UPDATE {$table} SET created_by = NULL WHERE created_by = $userId");
			$conn->query("UPDATE {$table} SET modified_by = NULL WHERE modified_by = $userId");
			$conn->query("UPDATE {$table} SET deleted_by = NULL WHERE deleted_by = $userId");
		}

		// Finally remove the user record.
		$conn->query("DELETE FROM users WHERE user_id = $userId");
		$conn->commit();
		header('Location: ' . BASE_URL . 'user_list.php?status=deleted');
		exit;
	} catch (Throwable $e) {
		// Roll back on any failure during deletion.
		$conn->rollback();
		header('Location: ' . BASE_URL . 'user_list.php?status=error');
		exit;
	}
}

// Prepare the update statement for non-delete actions.
$stmt = $conn->prepare(
	"UPDATE users
	 SET account_status = ?, modified_by = ?
	 WHERE user_id = ?"
);
if (!$stmt) {
	header('Location: ' . BASE_URL . 'user_list.php?status=error');
	exit;
}

// Bind parameters and execute the status update.
$stmt->bind_param('sii', $status, $actorId, $userId);
if ($stmt->execute()) {
	$stmt->close();
	// Assign the default "User" role on approval.
	if ($status === 'approved') {
		$userStmt = $conn->prepare("SELECT username FROM users WHERE user_id = ? LIMIT 1");
		if ($userStmt) {
			$userStmt->bind_param('i', $userId);
			$userStmt->execute();
			$userResult = $userStmt->get_result();
			$userRow = $userResult ? $userResult->fetch_assoc() : null;
			$userStmt->close();

			$username = $userRow['username'] ?? '';
			$roleStmt = $conn->prepare("SELECT role_id FROM roles WHERE role_name = 'User' LIMIT 1");
			if ($roleStmt) {
				// Look up the role id for the default User role.
				$roleStmt->execute();
				$roleResult = $roleStmt->get_result();
				$roleRow = $roleResult ? $roleResult->fetch_assoc() : null;
				$roleStmt->close();

				$roleId = (int) ($roleRow['role_id'] ?? 0);
				// Insert or update the user role assignment.
				if ($roleId > 0 && $username !== '') {
					$assignStmt = $conn->prepare(
						"INSERT INTO user_roles (user_id, username, role_id, role_name, created_by)
						 VALUES (?, ?, ?, 'User', ?)
						 ON DUPLICATE KEY UPDATE role_id = VALUES(role_id), role_name = VALUES(role_name), username = VALUES(username), modified_by = VALUES(created_by)"
					);
					if ($assignStmt) {
						$assignStmt->bind_param('isii', $userId, $username, $roleId, $actorId);
						$assignStmt->execute();
						$assignStmt->close();
					}
				}
			}
		}
	}
	// Redirect back to the user list with the resulting status.
	header('Location: ' . BASE_URL . 'user_list.php?status=' . ($status ?? 'error'));
	exit;
}

$stmt->close();
// Fall back to error when the update fails.
header('Location: ' . BASE_URL . 'user_list.php?status=error');
exit;
