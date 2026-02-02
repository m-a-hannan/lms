<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

function redirect_with_status($base, $status)
{
	$separator = strpos($base, '?') === false ? '?' : '&';
	header('Location: ' . $base . $separator . 'status=' . urlencode($status));
	exit;
}

function normalize_media_path($path)
{
	$path = trim((string) $path);
	if ($path === '') {
		return '';
	}
	$path = str_replace('\\', '/', $path);
	return ltrim($path, '/');
}

function db_path_in_use($conn, $table, $column, array $variants)
{
	$placeholders = implode(',', array_fill(0, count($variants), '?'));
	$sql = "SELECT 1 FROM {$table} WHERE {$column} IN ({$placeholders}) LIMIT 1";
	$stmt = $conn->prepare($sql);
	if (!$stmt) {
		return false;
	}
	$types = str_repeat('s', count($variants));
	$stmt->bind_param($types, ...$variants);
	$stmt->execute();
	$stmt->store_result();
	$inUse = $stmt->num_rows > 0;
	$stmt->close();
	return $inUse;
}

$context = rbac_get_context($conn);
if (empty($context['is_admin'])) {
	redirect_with_status(BASE_URL . 'gallery_list.php', 'forbidden');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	redirect_with_status(BASE_URL . 'gallery_list.php', 'invalid');
}

$path = normalize_media_path($_POST['path'] ?? '');
$filter = trim((string) ($_POST['filter'] ?? ''));
$redirect = BASE_URL . 'gallery_list.php';
if ($filter !== '') {
	$redirect .= '?filter=' . urlencode($filter);
}

if ($path === '' || strpos($path, '..') !== false) {
	redirect_with_status($redirect, 'invalid');
}

$allowedDirectories = [
	'uploads/book_cover/' => [
		ROOT_PATH . '/public/uploads/book_cover/',
		ROOT_PATH . '/uploads/book_cover/',
	],
	'uploads/profile_picture/' => [
		ROOT_PATH . '/public/uploads/profile_picture/',
		ROOT_PATH . '/uploads/profile_picture/',
	],
];

$targetDirs = null;
$relativeName = '';
foreach ($allowedDirectories as $prefix => $dir) {
	if (strpos($path, $prefix) === 0) {
		$targetDirs = (array) $dir;
		$relativeName = substr($path, strlen($prefix));
		break;
	}
}

if ($targetDirs === null || $relativeName === '' || strpos($relativeName, '/') !== false) {
	redirect_with_status($redirect, 'invalid');
}

$pathVariants = [
	$path,
	'/' . $path,
	'public/' . $path,
	'/public/' . $path,
];

$inUse = db_path_in_use($conn, 'books', 'book_cover_path', $pathVariants)
	|| db_path_in_use($conn, 'user_profiles', 'profile_picture', $pathVariants);

if ($inUse) {
	redirect_with_status($redirect, 'in_use');
}

$fullPath = '';
$targetDir = '';
foreach ($targetDirs as $dir) {
	$candidate = $dir . $relativeName;
	if (is_file($candidate)) {
		$fullPath = $candidate;
		$targetDir = $dir;
		break;
	}
}

if ($fullPath === '') {
	redirect_with_status($redirect, 'missing');
}

if (!is_writable($targetDir)) {
	@chmod($targetDir, 0775);
}
if (!is_writable($targetDir)) {
	redirect_with_status($redirect, 'not_writable');
}

if (!@unlink($fullPath)) {
	@chmod($fullPath, 0664);
	if (!@unlink($fullPath)) {
		redirect_with_status($redirect, 'error');
	}
}

if (file_exists($fullPath)) {
	redirect_with_status($redirect, 'error');
}

redirect_with_status($redirect, 'deleted');
