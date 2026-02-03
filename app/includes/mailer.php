<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once ROOT_PATH . '/vendor/autoload.php';

// Load environment configuration from .env and runtime variables.
function mailer_load_env(): array
{
	static $env = null;
	// Return cached values when already loaded.
	if ($env !== null) {
		return $env;
	}

	$env = [];
	$envPath = ROOT_PATH . '/.env';
	// Parse .env file when present on disk.
	if (is_file($envPath)) {
		$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		// Convert each non-comment line into key/value pairs.
		foreach ($lines as $line) {
			$line = trim($line);
			// Skip blank lines and comments.
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
			$env[$key] = $value;
		}
	}

	// Allow runtime env vars to override .env values.
	foreach ($env as $key => $value) {
		$runtime = getenv($key);
		if ($runtime !== false && $runtime !== '') {
			$env[$key] = $runtime;
		}
	}

	return $env;
}

// Retrieve a single mailer environment value with fallback.
function mailer_env(string $key, ?string $default = null): ?string
{
	$env = mailer_load_env();
	// Prefer values from the loaded environment map.
	if (array_key_exists($key, $env) && $env[$key] !== '') {
		return $env[$key];
	}

	// Fall back to runtime environment variables.
	$runtime = getenv($key);
	if ($runtime !== false && $runtime !== '') {
		return $runtime;
	}

	return $default;
}

// Create and configure a PHPMailer instance.
function mailer_create(): PHPMailer
{
	$mail = new PHPMailer(true);
	$mail->isSMTP();
	$mail->SMTPAuth = true;
	$mail->Host = mailer_env('SMTP_HOST', 'live.smtp.mailtrap.io');
	$mail->Port = (int) (mailer_env('SMTP_PORT', '587') ?? 587);
	$mail->Username = mailer_env('SMTP_USER', 'api') ?? 'api';
	$mail->Password = mailer_env('SMTP_PASS', '') ?? '';

	// Configure SMTP encryption based on env setting.
	$encryption = strtolower((string) mailer_env('SMTP_ENCRYPTION', 'tls'));
	if ($encryption === 'ssl' || $encryption === 'smtps') {
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
	} elseif ($encryption === 'tls' || $encryption === 'starttls') {
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	}

	$mail->isHTML(true);
	$mail->CharSet = 'UTF-8';

	return $mail;
}
