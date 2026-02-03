<?php
// Load core configuration for base settings.
require_once dirname(__DIR__, 2) . '/includes/config.php';

// Ensure session is active for consistent behavior.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Define sample CSV rows for bulk import.
$rows = [
	['title', 'description', 'author', 'isbn', 'publisher', 'publish_year', 'category_id', 'book_type', 'ebook_format', 'cover_file', 'ebook_file', 'copy_count'],
	[
		'The Hobbit',
		'Fantasy adventure novel.',
		'J.R.R. Tolkien',
		'9780261103344',
		'George Allen & Unwin',
		'1937',
		'1',
		'physical',
		'',
		'hobbit-cover.jpg',
		'',
		'5',
	],
	[
		'Digital Fortress',
		'Tech thriller novel.',
		'Dan Brown',
		'9780312180877',
		'St. Martin\'s Press',
		'1998',
		'1',
		'ebook',
		'pdf',
		'digital-fortress-cover.jpg',
		'digital-fortress.pdf',
		'0',
	],
];

// Send CSV headers to trigger file download.
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="bulk-books-sample.csv"');

// Stream CSV output to the response body.
$out = fopen('php://output', 'w');
// Write each sample row to the CSV.
foreach ($rows as $row) {
	fputcsv($out, $row);
}
fclose($out);
exit;
