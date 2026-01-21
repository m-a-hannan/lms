<?php
$envPath = dirname(__DIR__) . '/.env';
$required = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
$env = loadEnvFile($envPath);

foreach ($required as $key) {
	if (!isset($env[$key]) || $env[$key] === '') {
		die('Missing required database env var(s): ' . $key);
	}
}

$conn = new mysqli($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME']);
if ($conn->connect_error) {
	die('Database connection failed: ' . $conn->connect_error);
}

function loadEnvFile($path)
{
	$values = [];
	$lines = file_exists($path) ? file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	foreach ($lines as $line) {
		$line = trim($line);
		if ($line === '' || $line[0] === '#' || $line[0] === ';') {
			continue;
		}
		$parts = explode('=', $line, 2);
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
