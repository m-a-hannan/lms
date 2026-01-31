<?php

function library_set_current_user(mysqli $conn, int $userId): void
{
	if ($userId > 0) {
		$conn->query('SET @current_user_id = ' . (int) $userId);
	}
}

function library_get_policy_days(mysqli $conn, string $policyName, int $defaultDays = 14): int
{
	$policyName = $conn->real_escape_string($policyName);
	$result = $conn->query(
		"SELECT policy_value, description
		 FROM library_policies
		 WHERE name = '$policyName'
		   AND deleted_date IS NULL
		 ORDER BY effective_date DESC, policy_id DESC
		 LIMIT 1"
	);
	if ($result === false) {
		$result = $conn->query(
			"SELECT description
			 FROM library_policies
			 WHERE name = '$policyName'
			   AND deleted_date IS NULL
			 ORDER BY effective_date DESC, policy_id DESC
			 LIMIT 1"
		);
	}
	if (!$result || $result->num_rows === 0) {
		return $defaultDays;
	}
	$row = $result->fetch_assoc();
	if (isset($row['policy_value']) && $row['policy_value'] !== null && (int) $row['policy_value'] > 0) {
		return (int) $row['policy_value'];
	}
	$raw = trim((string) ($row['description'] ?? ''));
	$digits = (int) preg_replace('/[^0-9]/', '', $raw);
	return $digits > 0 ? $digits : $defaultDays;
}

function library_notify_user(mysqli $conn, int $userId, string $title, string $message, ?int $createdBy = null): void
{
	$title = trim($title);
	$message = trim($message);
	if ($userId <= 0 || $title === '' || $message === '') {
		return;
	}

	$createdBy = $createdBy !== null ? (int) $createdBy : null;
	if ($createdBy !== null && $createdBy <= 0) {
		$createdBy = null;
	}

	$stmt = $conn->prepare(
		"INSERT INTO notifications (user_id, title, message, created_at, read_status, created_by)
		 VALUES (?, ?, ?, NOW(), 0, ?)"
	);
	if (!$stmt) {
		return;
	}
	$stmt->bind_param('issi', $userId, $title, $message, $createdBy);
	$stmt->execute();
	$stmt->close();
}

function library_notify_roles(mysqli $conn, array $roles, string $title, string $message, ?int $createdBy = null): void
{
	$roles = array_values(array_filter(array_map('trim', $roles)));
	if (!$roles) {
		return;
	}
	$placeholders = implode(',', array_fill(0, count($roles), '?'));
	$types = str_repeat('s', count($roles));

	$stmt = $conn->prepare(
		"SELECT DISTINCT ur.user_id
		 FROM user_roles ur
		 JOIN roles r ON ur.role_id = r.role_id
		 WHERE r.role_name IN ($placeholders)"
	);
	if (!$stmt) {
		return;
	}
	$stmt->bind_param($types, ...$roles);
	$stmt->execute();
	$result = $stmt->get_result();
	$userIds = [];
	while ($row = $result->fetch_assoc()) {
		$userIds[] = (int) $row['user_id'];
	}
	$stmt->close();

	foreach ($userIds as $userId) {
		library_notify_user($conn, $userId, $title, $message, $createdBy);
	}
}

function library_is_copy_available(?string $status): bool
{
	$status = strtolower(trim((string) $status));
	return $status === '' || $status === 'available';
}

function library_user_map(mysqli $conn): array
{
	$map = [];
	$result = $conn->query("SELECT user_id, username, email FROM users");
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			$userId = (int) ($row['user_id'] ?? 0);
			if ($userId <= 0) {
				continue;
			}
			$name = trim((string) ($row['username'] ?? ''));
			if ($name === '') {
				$name = trim((string) ($row['email'] ?? ''));
			}
			if ($name === '') {
				$name = 'User #' . $userId;
			}
			$map[$userId] = $name;
		}
	}

	return $map;
}

function library_user_label($value, array $map): string
{
	if ($value === null) {
		return '-';
	}

	$value = trim((string) $value);
	if ($value === '') {
		return '-';
	}

	if (is_numeric($value)) {
		$userId = (int) $value;
		if ($userId <= 0) {
			return '-';
		}
		return $map[$userId] ?? ('User #' . $userId);
	}

	return $value;
}
