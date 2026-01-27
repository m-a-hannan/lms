<?php
require_once __DIR__ . '/../include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/permissions.php';

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

if ($affected > 0) {
	header('Location: ' . BASE_URL . 'notification_list.php?remove=success');
	exit;
}

header('Location: ' . BASE_URL . 'notification_list.php?remove=notfound');
exit;
?>
