<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Ensure session is active for user context.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Read user and book identifiers.
$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$bookId = isset($_GET['book_id']) ? (int) $_GET['book_id'] : 0;

// Require a logged-in user and valid book id.
if ($userId <= 0 || $bookId <= 0) {
	header('Location: ' . BASE_URL . 'login.php');
	exit;
}

// Look up the ebook file path for the requested book.
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

// Ensure the book is an ebook before allowing download.
if (!$row || strtolower($row['book_type'] ?? '') !== 'ebook') {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

// Require a stored ebook file path.
$relativePath = (string) ($row['ebook_file_path'] ?? '');
if ($relativePath === '') {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

// Resolve and validate the file path inside the ebooks directory.
$fullPath = realpath(ROOT_PATH . '/' . ltrim($relativePath, '/'));
$ebookRoot = realpath(ROOT_PATH . '/uploads/ebooks');

if ($fullPath === false || $ebookRoot === false || strpos($fullPath, $ebookRoot) !== 0) {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

// Ensure the file exists and is readable.
if (!is_file($fullPath) || !is_readable($fullPath)) {
	header('Location: ' . BASE_URL . 'home.php');
	exit;
}

// Log the download to audit logs when the table exists.
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

// Build a friendly filename from the book title.
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

// Set MIME type based on file extension.
$mime = 'application/octet-stream';
$ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
if ($ext === 'pdf') {
	$mime = 'application/pdf';
} elseif ($ext === 'epub') {
	$mime = 'application/epub+zip';
} elseif ($ext === 'mobi') {
	$mime = 'application/x-mobipocket-ebook';
}

// Send file download headers and stream the file.
header('Content-Description: File Transfer');
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: private');

readfile($fullPath);
exit;
