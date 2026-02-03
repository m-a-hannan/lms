<?php
// Define env file path and required DB keys.
$envPath = ROOT_PATH . '/.env';
$required = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
// Load database configuration from .env.
$env = loadEnvFile($envPath);

// Validate required DB environment variables.
foreach ($required as $key) {
	if (!isset($env[$key]) || $env[$key] === '') {
		die('Missing required database env var(s): ' . $key);
	}
}

// Establish MySQLi connection using env settings.
$conn = new mysqli($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME']);
if ($conn->connect_error) {
	die('Database connection failed: ' . $conn->connect_error);
}

// Parse a simple KEY=VALUE .env file into an array.
function loadEnvFile($path)
{
	$values = [];
	$lines = file_exists($path) ? file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	// Extract non-empty, non-comment lines into key/value pairs.
	foreach ($lines as $line) {
		$line = trim($line);
		// Skip comments and blank lines.
		if ($line === '' || $line[0] === '#' || $line[0] === ';') {
			continue;
		}
		$parts = explode('=', $line, 2);
		// Skip malformed lines without a key/value separator.
		if (count($parts) !== 2) {
			continue;
		}
		$key = trim($parts[0]);
		$value = trim($parts[1], " \t\n\r\0\x0B\"'");
		$values[$key] = $value;
	}

	return $values;
}

?>
