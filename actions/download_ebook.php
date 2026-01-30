<?php
require_once __DIR__ . '/../include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$bookId = isset($_GET['book_id']) ? (int) $_GET['book_id'] : 0;

if ($userId <= 0 || $bookId <= 0) {
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

$stmt = $conn->prepare(
	"SELECT title, book_type, ebook_file_path
	 FROM books
	 WHERE book_id = ? AND deleted_date IS NULL"
);
if (!$stmt) {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

$stmt->bind_param('i', $bookId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$row || strtolower($row['book_type'] ?? '') !== 'ebook') {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

$relativePath = (string) ($row['ebook_file_path'] ?? '');
if ($relativePath === '') {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

$fullPath = realpath(ROOT_PATH . '/' . ltrim($relativePath, '/'));
$ebookRoot = realpath(ROOT_PATH . '/uploads/ebooks');

if ($fullPath === false || $ebookRoot === false || strpos($fullPath, $ebookRoot) !== 0) {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

if (!is_file($fullPath) || !is_readable($fullPath)) {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

$logTable = $conn->query("SHOW TABLES LIKE 'audit_logs'");
if ($logTable && $logTable->num_rows > 0) {
	$logStmt = $conn->prepare(
		"INSERT INTO audit_logs (user_id, action, target_table, target_id, time_stamp)
		 VALUES (?, 'download_ebook', 'books', ?, NOW())"
	);
	if ($logStmt) {
		$logStmt->bind_param('ii', $userId, $bookId);
		$logStmt->execute();
		$logStmt->close();
	}
}

$filename = basename($fullPath);
$title = trim((string) ($row['title'] ?? 'ebook'));
if ($title !== '') {
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	$sanitized = preg_replace('/[^a-zA-Z0-9-_ ]+/', '', $title);
	$sanitized = trim(preg_replace('/\s+/', ' ', $sanitized));
	if ($sanitized !== '') {
		$filename = $sanitized . ($ext ? '.' . $ext : '');
	}
}

$mime = 'application/octet-stream';
$ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
if ($ext === 'pdf') {
	$mime = 'application/pdf';
} elseif ($ext === 'epub') {
	$mime = 'application/epub+zip';
} elseif ($ext === 'mobi') {
	$mime = 'application/x-mobipocket-ebook';
}

header('Content-Description: File Transfer');
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: private');

readfile($fullPath);
exit;
