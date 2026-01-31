<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
if ($userId <= 0) {
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

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
if ($stmt->execute()) {
	header('Location: ' . BASE_URL . 'notification_list.php?remove=success');
	$stmt->close();
	exit;
}

$stmt->close();
header('Location: ' . BASE_URL . 'notification_list.php?remove=error');
exit;