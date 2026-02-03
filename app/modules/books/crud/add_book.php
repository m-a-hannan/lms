<?php
// Load core configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . "/app/includes/connection.php";

// Fetch categories for the category dropdown.
$categoryResult = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
if ($categoryResult === false) {
    die("Category query failed: " . $conn->error);
}

// Handle book creation submission.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Basic input handling
    $title            = $conn->real_escape_string(trim($_POST["title"]));
    $book_excerpt     = $conn->real_escape_string(trim($_POST["book_excerpt"]));
    $author           = $conn->real_escape_string(trim($_POST["author"]));
    $isbn             = $conn->real_escape_string(trim($_POST["isbn"]));
    $publisher        = $conn->real_escape_string(trim($_POST["publisher"]));
    $publication_year = (int) $_POST["publication_year"];
    $category_id      = (int) $_POST["category_id"];
    $initial_copies   = (int) ($_POST["initial_copies"] ?? 0);
    $book_type        = strtolower(trim($_POST["book_type"] ?? "physical"));
    $ebook_format     = strtolower(trim($_POST["ebook_format"] ?? ""));

    // Normalize book type and ebook format values.
    $allowedTypes = ["physical", "ebook"];
    if (!in_array($book_type, $allowedTypes, true)) {
        $book_type = "physical";
    }

    $allowedFormats = ["pdf", "epub", "mobi"];
    if ($book_type === "ebook") {
        if (!in_array($ebook_format, $allowedFormats, true)) {
            $ebook_format = "";
        }
        $initial_copies = 0;
    } else {
        $ebook_format = "";
    }

    // Initialize upload paths for cover and ebook files.
    $uploadDir = ROOT_PATH . "/public/uploads/book_cover/";
    $imagePath = null;
    $ebookFilePath = null;
    $ebookFileSize = null;

    // Handle image upload
    if (!empty($_FILES["book_cover"]["name"])) {
        // Ensure the cover upload directory exists.
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                die("Upload directory not available.");
            }
        }
        // Require writable upload directory.
        if (!is_writable($uploadDir)) {
            die("Upload directory is not writable.");
        }

        // Validate upload error state.
        if (!empty($_FILES["book_cover"]["error"]) && $_FILES["book_cover"]["error"] !== UPLOAD_ERR_OK) {
            die("Image upload error: " . (int) $_FILES["book_cover"]["error"]);
        }

        $fileName   = time() . "_" . basename($_FILES["book_cover"]["name"]);
        $targetFile = $uploadDir . $fileName;
        $fileType   = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $allowedTypes = ["jpg", "jpeg", "png", "webp"];

        // Enforce allowed cover image extensions.
        if (!in_array($fileType, $allowedTypes)) {
            die("Invalid image type.");
        }

        // Enforce max cover image size.
        if ($_FILES["book_cover"]["size"] > 2 * 1024 * 1024) {
            die("Image size must be under 2MB.");
        }

        // Move the uploaded cover into place.
        if (!move_uploaded_file($_FILES["book_cover"]["tmp_name"], $targetFile)) {
            die("Failed to upload image.");
        }

        $imagePath = "uploads/book_cover/" . $fileName;
    }

    // Handle ebook upload when book type is ebook.
    if ($book_type === "ebook") {
        if (empty($_FILES["ebook_file"]["name"])) {
            die("Ebook file is required.");
        }

        $ebookDir = ROOT_PATH . "/public/uploads/ebooks/";
        if (!is_dir($ebookDir)) {
            if (!mkdir($ebookDir, 0755, true)) {
                die("Ebook upload directory not available.");
            }
        }
        if (!is_writable($ebookDir)) {
            die("Ebook upload directory is not writable.");
        }

        // Validate ebook upload error state.
        if (!empty($_FILES["ebook_file"]["error"]) && $_FILES["ebook_file"]["error"] !== UPLOAD_ERR_OK) {
            die("Ebook upload error: " . (int) $_FILES["ebook_file"]["error"]);
        }

        $ebookName = time() . "_" . basename($_FILES["ebook_file"]["name"]);
        $ebookTarget = $ebookDir . $ebookName;
        $ebookExt = strtolower(pathinfo($ebookTarget, PATHINFO_EXTENSION));

        // Enforce allowed ebook extensions.
        if (!in_array($ebookExt, $allowedFormats, true)) {
            die("Invalid ebook file type.");
        }
        // Ensure selected format matches uploaded file.
        if ($ebook_format !== "" && $ebookExt !== $ebook_format) {
            die("Selected ebook format does not match uploaded file.");
        }

        // Enforce max ebook file size.
        if ($_FILES["ebook_file"]["size"] > 50 * 1024 * 1024) {
            die("Ebook file size must be under 50MB.");
        }

        // Move the uploaded ebook into place.
        if (!move_uploaded_file($_FILES["ebook_file"]["tmp_name"], $ebookTarget)) {
            die("Failed to upload ebook file.");
        }

        $ebookFilePath = "uploads/ebooks/" . $ebookName;
        $ebookFileSize = (int) $_FILES["ebook_file"]["size"];
    }

    // Build values for nullable book fields.
    $imageValue = $imagePath !== null ? "'" . $conn->real_escape_string($imagePath) . "'" : "NULL";
    $bookTypeValue = "'" . $conn->real_escape_string($book_type) . "'";
    $ebookFormatValue = $ebook_format !== "" ? "'" . $conn->real_escape_string($ebook_format) . "'" : "NULL";
    $ebookPathValue = $ebookFilePath !== null ? "'" . $conn->real_escape_string($ebookFilePath) . "'" : "NULL";
    $ebookSizeValue = $ebookFileSize !== null ? (int) $ebookFileSize : "NULL";
    $sql = "INSERT INTO books (title, book_excerpt, author, isbn, publisher, publication_year, category_id, book_cover_path, book_type, ebook_format, ebook_file_path, ebook_file_size)
            VALUES ('$title', '$book_excerpt', '$author', '$isbn', '$publisher', $publication_year, $category_id, $imageValue, $bookTypeValue, $ebookFormatValue, $ebookPathValue, $ebookSizeValue)";

    // Insert book and any related records in a transaction.
    $conn->begin_transaction();
    try {
        if (!$conn->query($sql)) {
            throw new RuntimeException("Database error: " . $conn->error);
        }

        $bookId = (int) $conn->insert_id;

        if ($initial_copies > 0 && $bookId > 0) {
            // Create the initial edition and requested copies.
            $yearValue = $publication_year > 0 ? $publication_year : "NULL";
            $editionSql = "INSERT INTO book_editions (book_id, edition_number, publication_year)
                           VALUES ($bookId, 1, $yearValue)";
            if (!$conn->query($editionSql)) {
                throw new RuntimeException("Edition insert failed: " . $conn->error);
            }

            $editionId = (int) $conn->insert_id;
            for ($i = 1; $i <= $initial_copies; $i++) {
                $barcode = $conn->real_escape_string("B{$bookId}-E{$editionId}-" . date('YmdHis') . "-{$i}");
                $copySql = "INSERT INTO book_copies (edition_id, barcode, status)
                            VALUES ($editionId, '$barcode', 'available')";
                if (!$conn->query($copySql)) {
                    throw new RuntimeException("Copy insert failed: " . $conn->error);
                }
            }
        }

        // Commit and redirect on success.
        $conn->commit();
        header("Location: " . BASE_URL . "book_list.php?success=1");
        exit;
    } catch (Throwable $e) {
        // Roll back on any failure.
        $conn->rollback();
        die($e->getMessage());
    }
}

?>

<?php // Shared CSS/JS resources for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>

<?php // Top navigation bar for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php // Sidebar navigation for admin sections. ?>
<?php include(ROOT_PATH . '/app/views/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<!--begin::App Content-->
	<div class="app-content">
		<!--begin::Container-->
		<div class="container-fluid">
			<!--begin::Row-->
			<div class="row">
				<div class="container py-5">
					<!-- Add contents Below-->
					<div class="mb-4 d-flex justify-content-between">
						<h3>Add Book</h3>
						<a href="<?php echo BASE_URL; ?>book_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<!-- FORM ELELEMNTS -->
					<div class="row mt">
						<div class="col-md-6">
							<!--begin::Form-->
							<div class="card card-primary card-outline mb-4">
								<!-- must add form action -->
								<form action="<?php echo BASE_URL; ?>crud_files/add_book.php" method="post"
									enctype="multipart/form-data">
									<!--begin::Body-->
									<div class="card-body">
										<div class="mb-3">
											<label class="form-label">Book Title</label>
											<input type="text" class="form-control" name="title" aria-describedby="#" />
										</div>
										<div class="mb-3">
											<label class="form-label">Book Excerpt</label>
											<textarea type="text" class="form-control" name="book_excerpt" rows="2" cols="40" maxlength="150" placeholder="Excerpt should be within 150 character" aria-describedby="#"></textarea>
										</div>
										<div class="mb-3">
											<label class="form-label">Author Name</label>
											<input type="text" class="form-control" name="author" aria-describedby="#" />
										</div>
										<div class="mb-3">
											<label class="form-label">ISBN</label>
											<input type="text" class="form-control" name="isbn" aria-describedby="#" />
										</div>
										<div class="mb-3">
											<label class="form-label">Publisher</label>
											<input type="text" class="form-control" name="publisher" aria-describedby="#" />
										</div>
										<div class="mb-3">
											<label class="form-label">Publication Year</label>
											<input type="text" class="form-control" name="publication_year" aria-describedby="#" />
										</div>
										<div class="mb-3">
											<label class="form-label">Category</label>
											<select class="form-select" name="category_id" required>
												<option value="" selected disabled>Select a category</option>
												<?php // Render category options from the query. ?>
												<?php while ($category = $categoryResult->fetch_assoc()): ?>
												<option value="<?= (int) $category["category_id"] ?>">
													<?= htmlspecialchars($category["category_name"]) ?>
												</option>
												<?php endwhile; ?>
											</select>
										</div>
										<div class="mb-3">
											<label class="form-label">Select Book Type</label>
											<select class="form-select" name="book_type" id="bookTypeSelect" required>
												<option value="physical" selected>Physical Copy/Book</option>
												<option value="ebook">Digital Copy/Ebook</option>
											</select>
										</div>
										<div class="mb-3 d-none" id="ebookFormatGroup">
											<label class="form-label">Select Ebook Format</label>
											<select class="form-select" name="ebook_format" id="ebookFormatSelect">
												<option value="" selected disabled>Select format</option>
												<option value="pdf">PDF</option>
												<option value="epub">EPUB</option>
												<option value="mobi">MOBI</option>
											</select>
										</div>
										<div class="mb-3">
											<label class="form-label">Initial Copies</label>
											<input type="number" class="form-control" name="initial_copies" id="initialCopiesInput" min="0" value="0" />
										</div>
										<div class="mb-3">
											<label class="form-label">Book Cover</label>
											<input type="file" id="fileInput" accept="image/*" class="form-control" name="book_cover">
										</div>
										<div class="mb-3 d-none" id="ebookFileGroup">
											<label class="form-label">Upload Ebook File</label>
											<input type="file" class="form-control" name="ebook_file" id="ebookFileInput" accept=".pdf,.epub,.mobi">
										</div>
									</div>
									<!--end::Body-->
									<!--begin::Footer-->
									<div class="card-footer">
										<button type="submit" class="btn btn-primary">Submit</button>
									</div>
									<!--end::Footer-->
								</form>
								<!--end::Form-->
							</div>
							<!--end::Form-->
						</div>
						<!-- col-md-6-->
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<div class="preview-box">
									<h6 class="mb-3">Preview</h6>
									<div class="preview-area">
										<img id="previewImage" class="img-fluid d-none" alt="Preview">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- row end -->
			</div>
		</div>
	</div>
</main>
<!--end::App Main-->
<?php // Shared footer markup for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<!-- Page-specific behavior for file inputs -->
<script src="<?php echo BASE_URL; ?>assets/js/pages/add_book.js"></script>
<?php // Shared JS resources for the admin layout. ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>

<?php // Show success alert after adding a book. ?>
<?php if (isset($_GET["success"])): ?>
<div class="alert alert-success">Book added successfully.</div>
<?php endif; ?>
