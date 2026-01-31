<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

$query = trim($_GET['q'] ?? '');
if ($query === '' || mb_strlen($query) < 2) {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode([]);
	exit;
}

$like = '%' . $query . '%';
$stmt = $conn->prepare(
	"SELECT book_id, title, author
	 FROM books
	 WHERE title LIKE ? OR author LIKE ?
	 ORDER BY title ASC
	 LIMIT 8"
);

$results = [];
if ($stmt) {
	$stmt->bind_param('ss', $like, $like);
	$stmt->execute();
	$result = $stmt->get_result();
	while ($result && ($row = $result->fetch_assoc())) {
		$results[] = [
			'id' => (int) ($row['book_id'] ?? 0),
			'title' => $row['title'] ?? '',
			'author' => $row['author'] ?? '',
		];
	}
	$stmt->close();
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($results);