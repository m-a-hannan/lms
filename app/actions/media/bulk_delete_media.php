<?php
// Load core configuration, database connection, and permission helpers.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';

// Ensure session is active for permission checks and redirects.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Redirect with a status and optional query parameters.
function redirect_with_status($base, $status, array $params = [])
{
	$separator = strpos($base, '?') === false ? '?' : '&';
	$query = 'status=' . urlencode($status);
	// Append additional parameters to the redirect query string.
	foreach ($params as $key => $value) {
		$query .= '&' . urlencode((string) $key) . '=' . urlencode((string) $value);
	}
	header('Location: ' . $base . $separator . $query);
	exit;
}

// Normalize a media path for safe comparisons and prefix checks.
function normalize_media_path($path)
{
	$path = trim((string) $path);
	// Return early for empty input paths.
	if ($path === '') {
		return '';
	}
	$path = str_replace('\\', '/', $path);
	return ltrim($path, '/');
}

// Check if any of the path variants are referenced in a table column.
function db_path_in_use($conn, $table, $column, array $variants)
{
	// Build a parameterized IN clause for the variants.
	$placeholders = implode(',', array_fill(0, count($variants), '?'));
	$sql = "SELECT 1 FROM {$table} WHERE {$column} IN ({$placeholders}) LIMIT 1";
	$stmt = $conn->prepare($sql);
	// Abort the lookup if the prepared statement fails.
	if (!$stmt) {
		return false;
	}
	// Bind all variants as strings and execute the query.
	$types = str_repeat('s', count($variants));
	$stmt->bind_param($types, ...$variants);
	$stmt->execute();
	$stmt->store_result();
	// Return whether any rows reference the media path.
	$inUse = $stmt->num_rows > 0;
	$stmt->close();
	return $inUse;
}

// Enforce admin-only access to bulk media deletion.
$context = rbac_get_context($conn);
if (empty($context['is_admin'])) {
	redirect_with_status(BASE_URL . 'gallery_list.php', 'forbidden');
}

// Accept only POST requests for destructive actions.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	redirect_with_status(BASE_URL . 'gallery_list.php', 'invalid');
}

// Preserve the current filter in the redirect URL if provided.
$filter = trim((string) ($_POST['filter'] ?? ''));
$redirect = BASE_URL . 'gallery_list.php';
if ($filter !== '') {
	$redirect .= '?filter=' . urlencode($filter);
}

// Validate the incoming list of media paths.
$paths = $_POST['paths'] ?? [];
if (!is_array($paths) || !$paths) {
	redirect_with_status($redirect, 'invalid');
}

// Restrict deletions to known media directories.
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

// Track deletion outcomes and permission issues.
$deleted = 0;
$skipped = 0;
$notWritable = false;

// Process each requested media path for deletion.
foreach ($paths as $rawPath) {
	// Normalize and validate the raw path input.
	$path = normalize_media_path($rawPath);
	// Skip empty paths or traversal attempts.
	if ($path === '' || strpos($path, '..') !== false) {
		$skipped++;
		continue;
	}

	// Resolve the target directories based on allowed prefixes.
	$targetDirs = null;
	$relativeName = '';
	foreach ($allowedDirectories as $prefix => $dirs) {
		// Match the requested path to a known directory prefix.
		if (strpos($path, $prefix) === 0) {
			$targetDirs = (array) $dirs;
			$relativeName = substr($path, strlen($prefix));
			break;
		}
	}

	// Reject paths outside the allowed directory prefixes.
	if ($targetDirs === null || $relativeName === '' || strpos($relativeName, '/') !== false) {
		$skipped++;
		continue;
	}

	// Build path variants to match stored DB references.
	$pathVariants = [
		$path,
		'/' . $path,
		'public/' . $path,
		'/public/' . $path,
	];

	// Skip deletion if any record still references this media.
	$inUse = db_path_in_use($conn, 'books', 'book_cover_path', $pathVariants)
		|| db_path_in_use($conn, 'user_profiles', 'profile_picture', $pathVariants);

	if ($inUse) {
		$skipped++;
		continue;
	}

	// Find the actual file on disk within allowed directories.
	$fullPath = '';
	$targetDir = '';
	foreach ($targetDirs as $dir) {
		$candidate = $dir . $relativeName;
		// Pick the first directory that contains the requested file.
		if (is_file($candidate)) {
			$fullPath = $candidate;
			$targetDir = $dir;
			break;
		}
	}

	// Skip when the file cannot be located.
	if ($fullPath === '') {
		$skipped++;
		continue;
	}

	// Ensure target directory is writable before deletion.
	if (!is_writable($targetDir)) {
		@chmod($targetDir, 0775);
	}
	// Re-check writability after attempting to adjust permissions.
	if (!is_writable($targetDir)) {
		$notWritable = true;
		$skipped++;
		continue;
	}

	// Attempt to delete the file, adjusting permissions if needed.
	if (!@unlink($fullPath)) {
		@chmod($fullPath, 0664);
		// Retry deletion after adjusting file permissions.
		if (!@unlink($fullPath)) {
			$skipped++;
			continue;
		}
	}

	// Double-check removal to avoid false positives.
	if (file_exists($fullPath)) {
		$skipped++;
		continue;
	}

	// Count successful deletions.
	$deleted++;
}

// Report non-writable directories distinctly for admin action.
if ($notWritable) {
	redirect_with_status($redirect, 'not_writable', ['deleted' => $deleted, 'skipped' => $skipped]);
}

// Report success when at least one deletion occurred.
if ($deleted > 0) {
	redirect_with_status($redirect, 'bulk_deleted', ['deleted' => $deleted, 'skipped' => $skipped]);
}

// Fall back to error status when nothing was deleted.
redirect_with_status($redirect, 'error', ['deleted' => $deleted, 'skipped' => $skipped]);
