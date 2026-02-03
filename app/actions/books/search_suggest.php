<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Read the search query from the request.
$query = trim($_GET['q'] ?? '');
// Return empty results for short or missing queries.
if ($query === '' || mb_strlen($query) < 2) {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode([]);
	exit;
}

// Prepare the LIKE pattern for title/author matching.
$like = '%' . $query . '%';
// Query a small set of matching books for suggestions.
$stmt = $conn->prepare(
	"SELECT book_id, title, author
	 FROM books
	 WHERE title LIKE ? OR author LIKE ?
	 ORDER BY title ASC
	 LIMIT 8"
);

$results = [];
// Execute the prepared statement when available.
if ($stmt) {
	$stmt->bind_param('ss', $like, $like);
	$stmt->execute();
	$result = $stmt->get_result();
	// Collect each matching book into the response list.
	while ($result && ($row = $result->fetch_assoc())) {
		$results[] = [
			'id' => (int) ($row['book_id'] ?? 0),
			'title' => $row['title'] ?? '',
			'author' => $row['author'] ?? '',
		];
	}
	$stmt->close();
}

// Return results as JSON.
header('Content-Type: application/json; charset=utf-8');
echo json_encode($results);
