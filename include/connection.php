<?php

$envPath = dirname(__DIR__) . '/.env';
$requiredKeys = ['DB_HOST', 'DB_NAME', 'DB_USER'];
$env = [];

foreach ($requiredKeys as $key) {
	$value = getenv($key);
	if ($value !== false) {
		$env[$key] = $value;
	}
}

if (count($env) < count($requiredKeys) && file_exists($envPath)) {
	$env = array_merge($env, loadEnvFile($envPath));
}

$missing = [];
foreach ($requiredKeys as $key) {
	if (!isset($env[$key]) || $env[$key] === '') {
		$missing[] = $key;
	}
}

if ($missing) {
	die('Missing required database env var(s): ' . implode(', ', $missing));
}

$servername = $env['DB_HOST'];
$username = $env['DB_USER'];
$password = isset($env['DB_PASS']) ? $env['DB_PASS'] : '';
$dbname = $env['DB_NAME'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die('Database connection failed: ' . $conn->connect_error);
}

function loadEnvFile($path)
{
	$values = [];
	$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if ($lines === false) {
		return $values;
	}

	foreach ($lines as $line) {
		$line = trim($line);
		if ($line === '' || $line[0] === '#' || $line[0] === ';') {
			continue;
		}
		if (strpos($line, '=') === false) {
			continue;
		}
		list($key, $value) = explode('=', $line, 2);
		$key = trim($key);
		$value = trim($value);
		if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
			$quote = $value[0];
			if (substr($value, -1) === $quote) {
				$value = substr($value, 1, -1);
			}
		}
		$values[$key] = $value;
	}

	return $values;
}

?>
