<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$context = rbac_get_context($conn);
$isLibrarian = strcasecmp($context['role_name'] ?? '', 'Librarian') === 0;
if (!($context['is_admin'] || $isLibrarian)) {
	http_response_code(403);
	echo json_encode(['error' => 'Access denied.']);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['error' => 'Method not allowed.']);
	exit;
}

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
if (!$isAjax) {
	http_response_code(400);
	echo json_encode(['error' => 'Invalid request.']);
	exit;
}

$errors = [];
$summary = ['total' => 0, 'inserted' => 0, 'skipped' => 0, 'errors' => 0, 'dry_run' => 0];
$dryRun = !empty($_POST['dry_run']);
$summary['dry_run'] = $dryRun ? 1 : 0;

if (empty($_FILES['csv_file']['name']) || empty($_FILES['zip_file']['name'])) {
	http_response_code(400);
	echo json_encode(['error' => 'CSV and ZIP files are required.']);
	exit;
}

if (!empty($_FILES['csv_file']['error']) || !empty($_FILES['zip_file']['error'])) {
	http_response_code(400);
	echo json_encode(['error' => 'Upload error.']);
	exit;
}

$csvExt = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));
$zipExt = strtolower(pathinfo($_FILES['zip_file']['name'], PATHINFO_EXTENSION));
if ($csvExt !== 'csv' || $zipExt !== 'zip') {
	http_response_code(400);
	echo json_encode(['error' => 'Invalid file types.']);
	exit;
}

$tempBase = sys_get_temp_dir() . '/lms_bulk_' . bin2hex(random_bytes(8));
$tempZip = $tempBase . '/upload.zip';
$tempCsv = $tempBase . '/upload.csv';
$extractDir = $tempBase . '/files';

if (!mkdir($tempBase, 0755, true)) {
	http_response_code(500);
	echo json_encode(['error' => 'Unable to create temp directory.']);
	exit;
}
mkdir($extractDir, 0755, true);

if (!move_uploaded_file($_FILES['zip_file']['tmp_name'], $tempZip)) {
	http_response_code(500);
	echo json_encode(['error' => 'Failed to store ZIP file.']);
	exit;
}
if (!move_uploaded_file($_FILES['csv_file']['tmp_name'], $tempCsv)) {
	http_response_code(500);
	echo json_encode(['error' => 'Failed to store CSV file.']);
	exit;
}

$zip = new ZipArchive();
if ($zip->open($tempZip) !== true) {
	http_response_code(400);
	echo json_encode(['error' => 'Unable to read ZIP file.']);
	exit;
}

for ($i = 0; $i < $zip->numFiles; $i++) {
	$entry = $zip->getNameIndex($i);
	if ($entry !== false) {
		$zip->extractTo($extractDir, [$entry]);
	}
}
$zip->close();

// Build map of filename -> full path
$fileMap = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($extractDir));
foreach ($iterator as $file) {
	if ($file->isFile()) {
		$base = $file->getBasename();
		if (!isset($fileMap[$base])) {
			$fileMap[$base] = $file->getPathname();
		}
	}
}

$coverExtensions = ['jpg', 'jpeg', 'png', 'webp'];
$ebookExtensions = ['pdf', 'epub', 'mobi'];

$existingTitles = [];
$existingIsbns = [];
$existingResult = $conn->query("SELECT title, isbn FROM books WHERE deleted_date IS NULL");
if ($existingResult) {
	while ($row = $existingResult->fetch_assoc()) {
		$title = strtolower(trim((string) ($row['title'] ?? '')));
		$isbn = strtolower(trim((string) ($row['isbn'] ?? '')));
		if ($title !== '') {
			$existingTitles[$title] = true;
		}
		if ($isbn !== '') {
			$existingIsbns[$isbn] = true;
		}
	}
}

$csv = fopen($tempCsv, 'r');
if (!$csv) {
	http_response_code(500);
	echo json_encode(['error' => 'Unable to read CSV file.']);
	exit;
}

$header = fgetcsv($csv);
if (!$header) {
	http_response_code(400);
	echo json_encode(['error' => 'CSV header missing.']);
	exit;
}

$normalized = [];
foreach ($header as $index => $label) {
	$key = strtolower(trim((string) $label));
	$normalized[$key] = $index;
}

$required = ['title', 'description', 'author', 'isbn', 'publisher', 'publish_year', 'category_id', 'book_type', 'ebook_format', 'cover_file', 'ebook_file', 'copy_count'];
foreach ($required as $column) {
	if (!array_key_exists($column, $normalized)) {
		http_response_code(400);
		echo json_encode(['error' => 'Missing CSV column: ' . $column]);
		exit;
	}
}

$bookInsert = $conn->prepare(
	"INSERT INTO books (title, book_excerpt, author, isbn, publisher, publication_year, category_id, book_cover_path, book_type, ebook_format, ebook_file_path, ebook_file_size)
	 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
if (!$bookInsert) {
	http_response_code(500);
	echo json_encode(['error' => 'Unable to prepare insert.']);
	exit;
}

$editionInsert = $conn->prepare(
	"INSERT INTO book_editions (book_id, edition_number, publication_year) VALUES (?, 1, ?)"
);
$copyInsert = $conn->prepare(
	"INSERT INTO book_copies (edition_id, barcode, status) VALUES (?, ?, 'available')"
);

$rowNumber = 1;
$allowedTypes = ['physical', 'ebook'];
$dupTitles = [];
$dupIsbns = [];

while (($row = fgetcsv($csv)) !== false) {
	$rowNumber++;
	$summary['total']++;

	$get = function ($key) use ($row, $normalized) {
		$index = $normalized[$key] ?? null;
		return $index === null ? '' : trim((string) ($row[$index] ?? ''));
	};

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

	if ($title === '') {
		$errors[] = ['row' => $rowNumber, 'field' => 'title', 'message' => 'Title is required.'];
		$summary['skipped']++;
		continue;
	}

	if (!in_array($bookType, $allowedTypes, true)) {
		$bookType = 'physical';
	}

	$titleKey = strtolower($title);
	$isbnKey = strtolower($isbn);
	if (($isbnKey !== '' && (isset($existingIsbns[$isbnKey]) || isset($dupIsbns[$isbnKey])))
		|| ($titleKey !== '' && (isset($existingTitles[$titleKey]) || isset($dupTitles[$titleKey])))) {
		$errors[] = ['row' => $rowNumber, 'field' => 'isbn/title', 'message' => 'Duplicate ISBN or title.'];
		$summary['skipped']++;
		continue;
	}

	$coverExt = strtolower(pathinfo($coverFile, PATHINFO_EXTENSION));
	if ($coverFile === '' || !isset($fileMap[$coverFile]) || !in_array($coverExt, $coverExtensions, true)) {
		$errors[] = ['row' => $rowNumber, 'field' => 'cover_file', 'message' => 'Cover image is required and must be jpg/png/webp.'];
		$summary['skipped']++;
		continue;
	}

	$ebookPath = null;
	$ebookSize = null;
	if ($bookType === 'ebook') {
		$ebookExt = strtolower(pathinfo($ebookFile, PATHINFO_EXTENSION));
		if ($ebookFile === '' || !isset($fileMap[$ebookFile]) || !in_array($ebookExt, $ebookExtensions, true)) {
			$errors[] = ['row' => $rowNumber, 'field' => 'ebook_file', 'message' => 'Ebook file is required and must be pdf/epub/mobi.'];
			$summary['skipped']++;
			continue;
		}
		if ($ebookFormat !== '' && $ebookExt !== $ebookFormat) {
			$errors[] = ['row' => $rowNumber, 'field' => 'ebook_format', 'message' => 'Ebook format does not match file extension.'];
			$summary['skipped']++;
			continue;
		}
		$ebookFormat = $ebookFormat !== '' ? $ebookFormat : $ebookExt;
	} else {
		$ebookFormat = '';
		$ebookFile = '';
		$copyCount = max(0, $copyCount);
	}

	if ($dryRun) {
		$dupTitles[$titleKey] = true;
		if ($isbnKey !== '') {
			$dupIsbns[$isbnKey] = true;
		}
		$summary['inserted']++;
		continue;
	}

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

	$coverName = time() . '_' . basename($coverFile);
	$coverTarget = $coverDir . $coverName;
	if (!copy($fileMap[$coverFile], $coverTarget)) {
		$errors[] = ['row' => $rowNumber, 'field' => 'cover_file', 'message' => 'Failed to store cover image.'];
		$summary['skipped']++;
		continue;
	}
	$coverPath = 'uploads/book_cover/' . $coverName;

	if ($bookType === 'ebook') {
		$ebookDir = ROOT_PATH . '/uploads/ebooks/';
		if (!is_dir($ebookDir) && !mkdir($ebookDir, 0755, true)) {
			$errors[] = ['row' => $rowNumber, 'field' => 'ebook_file', 'message' => 'Ebook upload directory not available.'];
			$summary['skipped']++;
			continue;
		}
		if (!is_writable($ebookDir)) {
			$errors[] = ['row' => $rowNumber, 'field' => 'ebook_file', 'message' => 'Ebook upload directory is not writable.'];
			$summary['skipped']++;
			continue;
		}
		$ebookName = time() . '_' . basename($ebookFile);
		$ebookTarget = $ebookDir . $ebookName;
		if (!copy($fileMap[$ebookFile], $ebookTarget)) {
			$errors[] = ['row' => $rowNumber, 'field' => 'ebook_file', 'message' => 'Failed to store ebook file.'];
			$summary['skipped']++;
			continue;
		}
		$ebookPath = 'uploads/ebooks/' . $ebookName;
		$ebookSize = (int) filesize($ebookTarget);
	}

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

	$conn->begin_transaction();
	try {
		if (!$bookInsert->execute()) {
			throw new RuntimeException('Insert failed');
		}

		$bookId = (int) $conn->insert_id;
		if ($bookType === 'physical' && $copyCount > 0 && $bookId > 0) {
			$editionYear = $year > 0 ? $year : null;
			if ($editionInsert) {
				$editionInsert->bind_param('ii', $bookId, $editionYear);
				if (!$editionInsert->execute()) {
					throw new RuntimeException('Edition insert failed');
				}
				$editionId = (int) $conn->insert_id;
				if ($copyInsert && $editionId > 0) {
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

		$conn->commit();
		$summary['inserted']++;
		$dupTitles[$titleKey] = true;
		if ($isbnKey !== '') {
			$dupIsbns[$isbnKey] = true;
		}
	} catch (Throwable $e) {
		$conn->rollback();
		$errors[] = ['row' => $rowNumber, 'field' => 'database', 'message' => 'Insert failed.'];
		$summary['skipped']++;
	}
}

fclose($csv);

$summary['errors'] = count($errors);

// Cleanup temp files
$iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator($tempBase, RecursiveDirectoryIterator::SKIP_DOTS),
	RecursiveIteratorIterator::CHILD_FIRST
);
foreach ($iterator as $file) {
	if ($file->isDir()) {
		@rmdir($file->getPathname());
	} else {
		@unlink($file->getPathname());
	}
}
@rmdir($tempBase);

$token = bin2hex(random_bytes(8));
$_SESSION['bulk_import_results'][$token] = [
	'summary' => $summary,
	'errors' => $errors,
];

echo json_encode(['redirect' => BASE_URL . 'book_bulk_import.php?token=' . $token]);
exit;