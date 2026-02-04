<?php
// Define core path and base URL constants.
if (!defined('ROOT_PATH')) {
	define('ROOT_PATH', dirname(__DIR__, 2));
}
if (!defined('BASE_URL')) {
	define('BASE_URL', '/lms/');
}

// Resolve AUTH_ENABLED from .env or runtime environment.
if (!defined('AUTH_ENABLED')) {
	$authEnabled = true;
	$envPath = ROOT_PATH . '/.env';
	$envValue = null;

	// Read AUTH_ENABLED from .env when available.
	if (is_file($envPath)) {
		$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		// Scan .env for the AUTH_ENABLED key.
		foreach ($lines as $line) {
			$line = trim($line);
			// Skip empty lines and comments.
			if ($line === '' || $line[0] === '#' || $line[0] === ';') {
				continue;
			}
			$parts = explode('=', $line, 2);
			// Skip malformed entries.
			if (count($parts) !== 2) {
				continue;
			}
			// Capture AUTH_ENABLED value when found.
			if (trim($parts[0]) === 'AUTH_ENABLED') {
				$envValue = trim($parts[1], " \t\n\r\0\x0B\"'");
				break;
			}
		}
	}

	// Allow runtime env var to override .env.
	$runtimeEnv = getenv('AUTH_ENABLED');
	if ($runtimeEnv !== false && $runtimeEnv !== '') {
		$envValue = $runtimeEnv;
	}

	// Normalize truthy/falsey values for AUTH_ENABLED.
	if ($envValue !== null) {
		$envValue = strtolower(trim($envValue));
		$authEnabled = in_array($envValue, ['1', 'true', 'yes', 'on'], true);
	}

	// Define the final AUTH_ENABLED constant.
	define('AUTH_ENABLED', $authEnabled);
}

// Skip auth guards for public-facing pages.
$script = basename($_SERVER['SCRIPT_NAME'] ?? '');
$authSkip = [
	'login.php',
	'register.php',
	'request_password_reset.php',
	'actions/request_password_reset.php',
	'reset_password.php',
	'process_reset_password.php'
];
// Require auth and permission guards for protected pages.
if (AUTH_ENABLED && !in_array($script, $authSkip, true)) {
	require_once ROOT_PATH . '/app/includes/auth.php';
	require_once ROOT_PATH . '/app/includes/permission_guard.php';
}

?>
