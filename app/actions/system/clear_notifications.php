<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Ensure session is active for user context.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Require an authenticated user to clear notifications.
$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
if ($userId <= 0) {
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

// Soft-delete all notifications for the current user.
$stmt = $conn->prepare(
	"UPDATE notifications
	 SET deleted_date = NOW(), deleted_by = ?
	 WHERE user_id = ? AND deleted_date IS NULL"
);
if (!$stmt) {
	header('Location: ' . BASE_URL . 'notification_list.php?remove=error');
	exit;
}

$stmt->bind_param('ii', $userId, $userId);
// Redirect with success when the update runs.
if ($stmt->execute()) {
	header('Location: ' . BASE_URL . 'notification_list.php?remove=success');
	$stmt->close();
	exit;
}

$stmt->close();
// Fall back to error when the update fails.
header('Location: ' . BASE_URL . 'notification_list.php?remove=error');
exit;
