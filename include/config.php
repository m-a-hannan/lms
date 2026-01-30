<?php
if (!defined('ROOT_PATH')) {
	define('ROOT_PATH', dirname(__DIR__));
}
if (!defined('BASE_URL')) {
	define('BASE_URL', '/lms/');
}

if (!defined('AUTH_ENABLED')) {
	$authEnabled = true;
	$envPath = ROOT_PATH . '/.env';
	$envValue = null;

	if (is_file($envPath)) {
		$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line) {
			$line = trim($line);
			if ($line === '' || $line[0] === '#' || $line[0] === ';') {
				continue;
			}
			$parts = explode('=', $line, 2);
			if (count($parts) !== 2) {
				continue;
			}
			if (trim($parts[0]) === 'AUTH_ENABLED') {
				$envValue = trim($parts[1], " \t\n\r\0\x0B\"'");
				break;
			}
		}
	}

	$runtimeEnv = getenv('AUTH_ENABLED');
	if ($runtimeEnv !== false && $runtimeEnv !== '') {
		$envValue = $runtimeEnv;
	}

	if ($envValue !== null) {
		$envValue = strtolower(trim($envValue));
		$authEnabled = in_array($envValue, ['1', 'true', 'yes', 'on'], true);
	}

	define('AUTH_ENABLED', $authEnabled);
}

$script = basename($_SERVER['SCRIPT_NAME'] ?? '');
$authSkip = [
	'index.php',
	'login.php',
	'register.php',
	'request_password_reset.php',
	'actions/request_password_reset.php',
	'reset_password.php',
	'process_reset_password.php'
];
if (AUTH_ENABLED && !in_array($script, $authSkip, true)) {
	require_once ROOT_PATH . '/include/auth.php';
	require_once ROOT_PATH . '/include/permission_guard.php';
}

?>
