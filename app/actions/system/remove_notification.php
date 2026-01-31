<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';
require_once ROOT_PATH . '/app/includes/library_helpers.php';

$context = rbac_get_context($conn);
$userId = (int) ($context['user_id'] ?? 0);
if ($userId <= 0) {
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

$notificationId = isset($_POST['notification_id']) ? (int) $_POST['notification_id'] : 0;
if ($notificationId <= 0) {
	header('Location: ' . BASE_URL . 'notification_list.php?remove=notfound');
	exit;
}

$mode = library_delete_mode();
library_set_current_user($conn, $userId);

if ($mode === 'hard') {
	$stmt = $conn->prepare("DELETE FROM notifications WHERE notification_id = ? AND user_id = ?");
	if (!$stmt) {
		header('Location: ' . BASE_URL . 'notification_list.php?remove=error');
		exit;
	}
	$stmt->bind_param('ii', $notificationId, $userId);
	$stmt->execute();
	$affected = $stmt->affected_rows;
	$stmt->close();
} else {
	$stmt = $conn->prepare(
		"UPDATE notifications
		 SET deleted_date = NOW(), deleted_by = ?
		 WHERE notification_id = ? AND user_id = ? AND deleted_date IS NULL"
	);
	if (!$stmt) {
		header('Location: ' . BASE_URL . 'notification_list.php?remove=error');
		exit;
	}
	$stmt->bind_param('iii', $userId, $notificationId, $userId);
	$stmt->execute();
	$affected = $stmt->affected_rows;
	$stmt->close();
}

if ($affected > 0) {
	header('Location: ' . BASE_URL . 'notification_list.php?remove=success');
	exit;
}

header('Location: ' . BASE_URL . 'notification_list.php?remove=notfound');
exit;
?>
