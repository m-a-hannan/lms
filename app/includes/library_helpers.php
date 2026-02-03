<?php

// Store the current user id in the DB session for auditing triggers.
function library_set_current_user(mysqli $conn, int $userId): void
{
	// Only set a valid positive user id.
	if ($userId > 0) {
		$conn->query('SET @current_user_id = ' . (int) $userId);
	}
}

// Fetch the current policy day count, falling back to a default.
function library_get_policy_days(mysqli $conn, string $policyName, int $defaultDays = 14): int
{
	// Escape the policy name for safe SQL usage.
	$policyName = $conn->real_escape_string($policyName);
	// Query the latest policy record with a value if possible.
	$result = $conn->query(
		"SELECT policy_value, description
		 FROM library_policies
		 WHERE name = '$policyName'
		   AND deleted_date IS NULL
		 ORDER BY effective_date DESC, policy_id DESC
		 LIMIT 1"
	);
	// Fall back to description-only query if policy_value is missing.
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
	// Return default when no policy rows are available.
	if (!$result || $result->num_rows === 0) {
		return $defaultDays;
	}
	$row = $result->fetch_assoc();
	// Use explicit policy_value when provided and valid.
	if (isset($row['policy_value']) && $row['policy_value'] !== null && (int) $row['policy_value'] > 0) {
		return (int) $row['policy_value'];
	}
	// Parse numeric days from the description text if needed.
	$raw = trim((string) ($row['description'] ?? ''));
	$digits = (int) preg_replace('/[^0-9]/', '', $raw);
	return $digits > 0 ? $digits : $defaultDays;
}

// Insert a notification row for a specific user.
function library_notify_user(mysqli $conn, int $userId, string $title, string $message, ?int $createdBy = null): void
{
	$title = trim($title);
	$message = trim($message);
	// Abort when required values are missing.
	if ($userId <= 0 || $title === '' || $message === '') {
		return;
	}

	// Normalize created_by to null for invalid ids.
	$createdBy = $createdBy !== null ? (int) $createdBy : null;
	if ($createdBy !== null && $createdBy <= 0) {
		$createdBy = null;
	}

	// Insert the notification with a prepared statement.
	$stmt = $conn->prepare(
		"INSERT INTO notifications (user_id, title, message, created_at, read_status, created_by)
		 VALUES (?, ?, ?, NOW(), 0, ?)"
	);
	// Abort if the statement cannot be prepared.
	if (!$stmt) {
		return;
	}
	$stmt->bind_param('issi', $userId, $title, $message, $createdBy);
	$stmt->execute();
	$stmt->close();
}

// Send a notification to every user in the given roles.
function library_notify_roles(mysqli $conn, array $roles, string $title, string $message, ?int $createdBy = null): void
{
	// Normalize and filter role names.
	$roles = array_values(array_filter(array_map('trim', $roles)));
	if (!$roles) {
		return;
	}
	// Build placeholders for the IN clause.
	$placeholders = implode(',', array_fill(0, count($roles), '?'));
	$types = str_repeat('s', count($roles));

	// Query all users that have any of the given roles.
	$stmt = $conn->prepare(
		"SELECT DISTINCT ur.user_id
		 FROM user_roles ur
		 JOIN roles r ON ur.role_id = r.role_id
		 WHERE r.role_name IN ($placeholders)"
	);
	// Abort if the statement cannot be prepared.
	if (!$stmt) {
		return;
	}
	$stmt->bind_param($types, ...$roles);
	$stmt->execute();
	$result = $stmt->get_result();
	$userIds = [];
	// Collect user ids from the role query results.
	while ($row = $result->fetch_assoc()) {
		$userIds[] = (int) $row['user_id'];
	}
	$stmt->close();

	// Notify each user found for the role set.
	foreach ($userIds as $userId) {
		library_notify_user($conn, $userId, $title, $message, $createdBy);
	}
}

// Treat blank or "available" statuses as available.
function library_is_copy_available(?string $status): bool
{
	$status = strtolower(trim((string) $status));
	return $status === '' || $status === 'available';
}

// Build a map of user ids to display names.
function library_user_map(mysqli $conn): array
{
	$map = [];
	// Pull user identifiers for display mapping.
	$result = $conn->query("SELECT user_id, username, email FROM users");
	if ($result) {
		// Convert each row into a user id -> label entry.
		while ($row = $result->fetch_assoc()) {
			$userId = (int) ($row['user_id'] ?? 0);
			// Skip invalid ids.
			if ($userId <= 0) {
				continue;
			}
			$name = trim((string) ($row['username'] ?? ''));
			// Fall back to email when username is missing.
			if ($name === '') {
				$name = trim((string) ($row['email'] ?? ''));
			}
			// Use a generic label when no name is available.
			if ($name === '') {
				$name = 'User #' . $userId;
			}
			$map[$userId] = $name;
		}
	}

	return $map;
}

// Format a user id or label value for display.
function library_user_label($value, array $map): string
{
	// Return a placeholder for null input.
	if ($value === null) {
		return '-';
	}

	$value = trim((string) $value);
	// Return a placeholder for empty strings.
	if ($value === '') {
		return '-';
	}

	// Attempt to resolve numeric values to user ids.
	if (is_numeric($value)) {
		$userId = (int) $value;
		// Reject non-positive ids.
		if ($userId <= 0) {
			return '-';
		}
		return $map[$userId] ?? ('User #' . $userId);
	}

	// Return non-numeric labels as-is.
	return $value;
}
