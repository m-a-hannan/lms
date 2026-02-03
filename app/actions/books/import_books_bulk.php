<?php
// Load core configuration, database connection, and RBAC helpers.
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';

// Ensure session is active for role checks and results storage.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Require admin or librarian access for bulk import.
$context = rbac_get_context($conn);
$isLibrarian = strcasecmp($context['role_name'] ?? '', 'Librarian') === 0;
if (!($context['is_admin'] || $isLibrarian)) {
	http_response_code(403);
	echo json_encode(['error' => 'Access denied.']);
	exit;
}

// Accept only POST requests for bulk import.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['error' => 'Method not allowed.']);
	exit;
}

// Require an AJAX/XHR request.
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
if (!$isAjax) {
	http_response_code(400);
	echo json_encode(['error' => 'Invalid request.']);
	exit;
}

// Initialize error tracking and summary statistics.
$errors = [];
$summary = ['total' => 0, 'inserted' => 0, 'skipped' => 0, 'errors' => 0, 'dry_run' => 0];
$dryRun = !empty($_POST['dry_run']);
$summary['dry_run'] = $dryRun ? 1 : 0;

// Require both CSV and ZIP uploads.
if (empty($_FILES['csv_file']['name']) || empty($_FILES['zip_file']['name'])) {
	http_response_code(400);
	echo json_encode(['error' => 'CSV and ZIP files are required.']);
	exit;
}

// Check for upload errors.
if (!empty($_FILES['csv_file']['error']) || !empty($_FILES['zip_file']['error'])) {
	http_response_code(400);
	echo json_encode(['error' => 'Upload error.']);
	exit;
}

// Validate file extensions.
$csvExt = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));
$zipExt = strtolower(pathinfo($_FILES['zip_file']['name'], PATHINFO_EXTENSION));
if ($csvExt !== 'csv' || $zipExt !== 'zip') {
	http_response_code(400);
	echo json_encode(['error' => 'Invalid file types.']);
	exit;
}

// Create temporary working directories and file paths.
$tempBase = sys_get_temp_dir() . '/lms_bulk_' . bin2hex(random_bytes(8));
$tempZip = $tempBase . '/upload.zip';
$tempCsv = $tempBase . '/upload.csv';
$extractDir = $tempBase . '/files';

// Ensure temp directory exists.
if (!mkdir($tempBase, 0755, true)) {
	http_response_code(500);
	echo json_encode(['error' => 'Unable to create temp directory.']);
	exit;
}
mkdir($extractDir, 0755, true);

// Persist the uploaded ZIP file to disk.
if (!move_uploaded_file($_FILES['zip_file']['tmp_name'], $tempZip)) {
	http_response_code(500);
	echo json_encode(['error' => 'Failed to store ZIP file.']);
	exit;
}
// Persist the uploaded CSV file to disk.
if (!move_uploaded_file($_FILES['csv_file']['tmp_name'], $tempCsv)) {
	http_response_code(500);
	echo json_encode(['error' => 'Failed to store CSV file.']);
	exit;
}

// Open and extract the ZIP archive.
$zip = new ZipArchive();
if ($zip->open($tempZip) !== true) {
	http_response_code(400);
	echo json_encode(['error' => 'Unable to read ZIP file.']);
	exit;
}

// Extract each entry from the ZIP into the temp directory.
for ($i = 0; $i < $zip->numFiles; $i++) {
	$entry = $zip->getNameIndex($i);
	// Skip invalid entries.
	if ($entry !== false) {
		$zip->extractTo($extractDir, [$entry]);
	}
}
$zip->close();

// Build map of filename -> full path.
$fileMap = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($extractDir));
// Record each file by basename for lookup.
foreach ($iterator as $file) {
	// Only index regular files, not directories.
	if ($file->isFile()) {
		$base = $file->getBasename();
		// Prefer the first occurrence of a filename.
		if (!isset($fileMap[$base])) {
			$fileMap[$base] = $file->getPathname();
		}
	}
}

// Allowed file extensions for covers and ebooks.
$coverExtensions = ['jpg', 'jpeg', 'png', 'webp'];
$ebookExtensions = ['pdf', 'epub', 'mobi'];

// Load existing titles and ISBNs to detect duplicates.
$existingTitles = [];
$existingIsbns = [];
$existingResult = $conn->query("SELECT title, isbn FROM books WHERE deleted_date IS NULL");
if ($existingResult) {
	// Normalize and index existing titles and ISBNs.
	while ($row = $existingResult->fetch_assoc()) {
		$title = strtolower(trim((string) ($row['title'] ?? '')));
		$isbn = strtolower(trim((string) ($row['isbn'] ?? '')));
		// Index non-empty titles for duplication checks.
		if ($title !== '') {
			$existingTitles[$title] = true;
		}
		// Index non-empty ISBNs for duplication checks.
		if ($isbn !== '') {
			$existingIsbns[$isbn] = true;
		}
	}
}

// Open the CSV file for reading.
$csv = fopen($tempCsv, 'r');
if (!$csv) {
	http_response_code(500);
	echo json_encode(['error' => 'Unable to read CSV file.']);
	exit;
}

// Read and validate the CSV header row.
$header = fgetcsv($csv);
if (!$header) {
	http_response_code(400);
	echo json_encode(['error' => 'CSV header missing.']);
	exit;
}

// Normalize header labels to column indexes.
$normalized = [];
foreach ($header as $index => $label) {
	$key = strtolower(trim((string) $label));
	$normalized[$key] = $index;
}

// Require all expected columns in the CSV.
$required = ['title', 'description', 'author', 'isbn', 'publisher', 'publish_year', 'category_id', 'book_type', 'ebook_format', 'cover_file', 'ebook_file', 'copy_count'];
foreach ($required as $column) {
	// Stop early when a required CSV column is missing.
	if (!array_key_exists($column, $normalized)) {
		http_response_code(400);
		echo json_encode(['error' => 'Missing CSV column: ' . $column]);
		exit;
	}
}

// Prepare the book insert statement.
$bookInsert = $conn->prepare(
	"INSERT INTO books (title, book_excerpt, author, isbn, publisher, publication_year, category_id, book_cover_path, book_type, ebook_format, ebook_file_path, ebook_file_size)
	 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
if (!$bookInsert) {
	http_response_code(500);
	echo json_encode(['error' => 'Unable to prepare insert.']);
	exit;
}

// Prepare edition and copy insert statements.
$editionInsert = $conn->prepare(
	"INSERT INTO book_editions (book_id, edition_number, publication_year) VALUES (?, 1, ?)"
);
$copyInsert = $conn->prepare(
	"INSERT INTO book_copies (edition_id, barcode, status) VALUES (?, ?, 'available')"
);

// Iterate through each CSV row and process it.
$rowNumber = 1;
$allowedTypes = ['physical', 'ebook'];
$dupTitles = [];
$dupIsbns = [];

while (($row = fgetcsv($csv)) !== false) {
	// Track the current row number and increment totals.
	$rowNumber++;
	$summary['total']++;

	// Helper to read a normalized column from the CSV row.
	$get = function ($key) use ($row, $normalized) {
		$index = $normalized[$key] ?? null;
		return $index === null ? '' : trim((string) ($row[$index] ?? ''));
	};

	// Read and normalize all expected CSV fields.
	$title = $get('title');
	$description = $get('description');
	$author = $get('author');
	$isbn = $get('isbn');
	$publisher = $get('publisher');
	$year = (int) $get('publish_year');
	$categoryId = (int) $get('category_id');
	$bookType = strtolower($get('book_type'));
	$ebookFormat = strtolower($get('ebook_format'));
	$coverFile = $get('cover_file');
	$ebookFile = $get('ebook_file');
	$copyCount = (int) $get('copy_count');

	// Enforce required title field.
	if ($title === '') {
		$errors[] = ['row' => $rowNumber, 'field' => 'title', 'message' => 'Title is required.'];
		$summary['skipped']++;
		continue;
	}

	// Normalize book type to allowed values.
	if (!in_array($bookType, $allowedTypes, true)) {
		$bookType = 'physical';
	}

	// Skip duplicates by ISBN or title.
	$titleKey = strtolower($title);
	$isbnKey = strtolower($isbn);
	if (($isbnKey !== '' && (isset($existingIsbns[$isbnKey]) || isset($dupIsbns[$isbnKey])))
		|| ($titleKey !== '' && (isset($existingTitles[$titleKey]) || isset($dupTitles[$titleKey])))) {
		$errors[] = ['row' => $rowNumber, 'field' => 'isbn/title', 'message' => 'Duplicate ISBN or title.'];
		$summary['skipped']++;
		continue;
	}

	// Validate cover file presence and extension.
	$coverExt = strtolower(pathinfo($coverFile, PATHINFO_EXTENSION));
	if ($coverFile === '' || !isset($fileMap[$coverFile]) || !in_array($coverExt, $coverExtensions, true)) {
		$errors[] = ['row' => $rowNumber, 'field' => 'cover_file', 'message' => 'Cover image is required and must be jpg/png/webp.'];
		$summary['skipped']++;
		continue;
	}

	// Validate ebook-specific fields when book type is ebook.
	$ebookPath = null;
	$ebookSize = null;
	if ($bookType === 'ebook') {
		$ebookExt = strtolower(pathinfo($ebookFile, PATHINFO_EXTENSION));
		// Validate ebook file existence and extension.
		if ($ebookFile === '' || !isset($fileMap[$ebookFile]) || !in_array($ebookExt, $ebookExtensions, true)) {
			$errors[] = ['row' => $rowNumber, 'field' => 'ebook_file', 'message' => 'Ebook file is required and must be pdf/epub/mobi.'];
			$summary['skipped']++;
			continue;
		}
		// Ensure declared format matches the file extension.
		if ($ebookFormat !== '' && $ebookExt !== $ebookFormat) {
			$errors[] = ['row' => $rowNumber, 'field' => 'ebook_format', 'message' => 'Ebook format does not match file extension.'];
			$summary['skipped']++;
			continue;
		}
		$ebookFormat = $ebookFormat !== '' ? $ebookFormat : $ebookExt;
	} else {
		// Clear ebook-related fields for physical books.
		$ebookFormat = '';
		$ebookFile = '';
		$copyCount = max(0, $copyCount);
	}

	// Short-circuit for dry-run mode without inserting.
	if ($dryRun) {
		$dupTitles[$titleKey] = true;
		// Track ISBN duplicates during dry-run.
		if ($isbnKey !== '') {
			$dupIsbns[$isbnKey] = true;
		}
		$summary['inserted']++;
		continue;
	}

	// Ensure the cover directory exists and is writable.
	$coverDir = ROOT_PATH . '/uploads/book_cover/';
	if (!is_dir($coverDir) && !mkdir($coverDir, 0755, true)) {
		$errors[] = ['row' => $rowNumber, 'field' => 'cover_file', 'message' => 'Cover upload directory not available.'];
		$summary['skipped']++;
		continue;
	}
	if (!is_writable($coverDir)) {
		$errors[] = ['row' => $rowNumber, 'field' => 'cover_file', 'message' => 'Cover upload directory is not writable.'];
		$summary['skipped']++;
		continue;
	}

	// Copy the cover image into place.
	$coverName = time() . '_' . basename($coverFile);
	$coverTarget = $coverDir . $coverName;
	if (!copy($fileMap[$coverFile], $coverTarget)) {
		$errors[] = ['row' => $rowNumber, 'field' => 'cover_file', 'message' => 'Failed to store cover image.'];
		$summary['skipped']++;
		continue;
	}
	$coverPath = 'uploads/book_cover/' . $coverName;

	// Handle ebook file upload when needed.
	if ($bookType === 'ebook') {
		$ebookDir = ROOT_PATH . '/uploads/ebooks/';
		// Ensure the ebook directory exists.
		if (!is_dir($ebookDir) && !mkdir($ebookDir, 0755, true)) {
			$errors[] = ['row' => $rowNumber, 'field' => 'ebook_file', 'message' => 'Ebook upload directory not available.'];
			$summary['skipped']++;
			continue;
		}
		// Require the ebook directory to be writable.
		if (!is_writable($ebookDir)) {
			$errors[] = ['row' => $rowNumber, 'field' => 'ebook_file', 'message' => 'Ebook upload directory is not writable.'];
			$summary['skipped']++;
			continue;
		}
		$ebookName = time() . '_' . basename($ebookFile);
		$ebookTarget = $ebookDir . $ebookName;
		// Copy the ebook file into place.
		if (!copy($fileMap[$ebookFile], $ebookTarget)) {
			$errors[] = ['row' => $rowNumber, 'field' => 'ebook_file', 'message' => 'Failed to store ebook file.'];
			$summary['skipped']++;
			continue;
		}
		$ebookPath = 'uploads/ebooks/' . $ebookName;
		$ebookSize = (int) filesize($ebookTarget);
	}

	// Bind parameters for the book insert statement.
$bookInsert->bind_param(
	'sssssisssssi',
	$title,
	$description,
	$author,
	$isbn,
	$publisher,
		$year,
		$categoryId,
		$coverPath,
		$bookType,
		$ebookFormat,
		$ebookPath,
		$ebookSize
	);

	// Insert book/edition/copies in a transaction per row.
	$conn->begin_transaction();
	try {
		// Insert the book record.
		if (!$bookInsert->execute()) {
			throw new RuntimeException('Insert failed');
		}

		$bookId = (int) $conn->insert_id;
		// For physical books, create edition and copies.
		if ($bookType === 'physical' && $copyCount > 0 && $bookId > 0) {
			$editionYear = $year > 0 ? $year : null;
			// Insert the initial edition row when prepared.
			if ($editionInsert) {
				$editionInsert->bind_param('ii', $bookId, $editionYear);
				// Validate edition insert succeeded.
				if (!$editionInsert->execute()) {
					throw new RuntimeException('Edition insert failed');
				}
				$editionId = (int) $conn->insert_id;
				// Insert the requested number of copies for the edition.
				if ($copyInsert && $editionId > 0) {
					// Insert requested number of copies with unique barcodes.
					for ($i = 1; $i <= $copyCount; $i++) {
						$barcode = "B{$bookId}-E{$editionId}-" . date('YmdHis') . "-{$i}";
						$copyInsert->bind_param('is', $editionId, $barcode);
						if (!$copyInsert->execute()) {
							throw new RuntimeException('Copy insert failed');
						}
					}
				}
			}
		}

		// Commit inserts and update summary counts.
		$conn->commit();
		$summary['inserted']++;
		$dupTitles[$titleKey] = true;
		// Track ISBN duplicates after insert.
		if ($isbnKey !== '') {
			$dupIsbns[$isbnKey] = true;
		}
	} catch (Throwable $e) {
		// Roll back on any insert failure and record the error.
		$conn->rollback();
		$errors[] = ['row' => $rowNumber, 'field' => 'database', 'message' => 'Insert failed.'];
		$summary['skipped']++;
	}
}

// Close the CSV handle after processing.
fclose($csv);

// Finalize error count in the summary.
$summary['errors'] = count($errors);

// Cleanup temp files.
$iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator($tempBase, RecursiveDirectoryIterator::SKIP_DOTS),
	RecursiveIteratorIterator::CHILD_FIRST
);
// Remove extracted files and directories.
foreach ($iterator as $file) {
	// Remove directories after their contents are deleted.
	if ($file->isDir()) {
		@rmdir($file->getPathname());
	} else {
		@unlink($file->getPathname());
	}
}
@rmdir($tempBase);

// Store the import results in session for later display.
$token = bin2hex(random_bytes(8));
$_SESSION['bulk_import_results'][$token] = [
	'summary' => $summary,
	'errors' => $errors,
];

// Respond with a redirect target for results page.
echo json_encode(['redirect' => BASE_URL . 'book_bulk_import.php?token=' . $token]);
exit;
