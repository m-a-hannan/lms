<?php
require_once __DIR__ . '/../include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/permissions.php';
require_once ROOT_PATH . '/include/library_helpers.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'dashboard.php');
	exit;
}

$context = rbac_get_context($conn);
$roleName = $context['role_name'] ?? '';
$isLibrarian = strcasecmp($roleName, 'Librarian') === 0;
if (!$context['is_admin'] && !$isLibrarian) {
	http_response_code(403);
	exit('Access denied.');
}

$adminId = $context['user_id'] ?? 0;
$loanId = isset($_POST['loan_id']) ? (int) $_POST['loan_id'] : 0;
$action = $_POST['action'] ?? '';

if ($adminId <= 0 || $loanId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
	header('Location: ' . BASE_URL . 'dashboard.php');
	exit;
}

library_set_current_user($conn, $adminId);

$conn->begin_transaction();

try {
	$stmt = $conn->prepare(
		"SELECT l.loan_id, l.copy_id, l.user_id, l.status, b.title
		 FROM loans l
		 JOIN book_copies c ON l.copy_id = c.copy_id
		 JOIN book_editions e ON c.edition_id = e.edition_id
		 JOIN books b ON e.book_id = b.book_id
		 WHERE l.loan_id = ?
		 LIMIT 1
		 FOR UPDATE"
	);
	$stmt->bind_param('i', $loanId);
	$stmt->execute();
	$result = $stmt->get_result();
	$loan = $result ? $result->fetch_assoc() : null;
	$stmt->close();

	if (!$loan || $loan['status'] !== 'pending') {
		$conn->rollback();
		header('Location: ' . BASE_URL . 'dashboard.php?loan=invalid');
		exit;
	}

	$copyId = (int) $loan['copy_id'];
	$userId = (int) $loan['user_id'];
	$title = trim((string) ($loan['title'] ?? 'Book'));

	if ($action === 'approve') {
		$days = library_get_policy_days($conn, 'loan_period_days', 14);
		$dueDate = (new DateTimeImmutable('today'))->modify('+' . $days . ' days')->format('Y-m-d');

		$stmt = $conn->prepare(
			"UPDATE loans
			 SET status = 'approved',
			     issue_date = CURDATE(),
			     due_date = ?,
			     modified_by = ?
			 WHERE loan_id = ?"
		);
		$stmt->bind_param('sii', $dueDate, $adminId, $loanId);
		if (!$stmt->execute()) {
			throw new RuntimeException('Failed to approve loan.');
		}
		$stmt->close();

		$stmt = $conn->prepare("UPDATE book_copies SET status = 'loaned', modified_by = ? WHERE copy_id = ?");
		$stmt->bind_param('ii', $adminId, $copyId);
		if (!$stmt->execute()) {
			throw new RuntimeException('Failed to update copy status.');
		}
		$stmt->close();

		library_notify_user(
			$conn,
			$userId,
			'Loan request approved',
			"Your loan request for \"{$title}\" has been approved.",
			$adminId
		);
	} else {
		$stmt = $conn->prepare("UPDATE loans SET status = 'rejected', modified_by = ? WHERE loan_id = ?");
		$stmt->bind_param('ii', $adminId, $loanId);
		if (!$stmt->execute()) {
			throw new RuntimeException('Failed to reject loan.');
		}
		$stmt->close();

		$stmt = $conn->prepare(
			"UPDATE book_copies
			 SET status = 'available', modified_by = ?
			 WHERE copy_id = ? AND status = 'hold_loan'"
		);
		$stmt->bind_param('ii', $adminId, $copyId);
		$stmt->execute();
		$stmt->close();

		library_notify_user(
			$conn,
			$userId,
			'Loan request rejected',
			"Your loan request for \"{$title}\" has been rejected.",
			$adminId
		);
	}

	$conn->commit();
} catch (Throwable $e) {
	$conn->rollback();
	header('Location: ' . BASE_URL . 'dashboard.php?loan=error');
	exit;
}

header('Location: ' . BASE_URL . 'dashboard.php?loan=success');
exit;
