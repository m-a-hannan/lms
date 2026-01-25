<?php
require_once dirname(__DIR__) . "/include/config.php";
require_once ROOT_PATH . "/include/connection.php";

// Fetch categories for dropdown.
$categoryResult = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
if ($categoryResult === false) {
    die("Category query failed: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Basic input handling
    $title            = $conn->real_escape_string(trim($_POST["title"]));
    $book_excerpt     = $conn->real_escape_string(trim($_POST["book_excerpt"]));
    $author           = $conn->real_escape_string(trim($_POST["author"]));
    $isbn             = $conn->real_escape_string(trim($_POST["isbn"]));
    $publisher        = $conn->real_escape_string(trim($_POST["publisher"]));
    $publication_year = (int) $_POST["publication_year"];
    $category_id      = (int) $_POST["category_id"];

    $uploadDir = ROOT_PATH . "/uploads/book_cover/";
    $imagePath = null;

    // Handle image upload
    if (!empty($_FILES["book_cover"]["name"])) {
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                die("Upload directory not available.");
            }
        }
        if (!is_writable($uploadDir)) {
            die("Upload directory is not writable.");
        }

        if (!empty($_FILES["book_cover"]["error"]) && $_FILES["book_cover"]["error"] !== UPLOAD_ERR_OK) {
            die("Image upload error: " . (int) $_FILES["book_cover"]["error"]);
        }

        $fileName   = time() . "_" . basename($_FILES["book_cover"]["name"]);
        $targetFile = $uploadDir . $fileName;
        $fileType   = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $allowedTypes = ["jpg", "jpeg", "png", "webp"];

        if (!in_array($fileType, $allowedTypes)) {
            die("Invalid image type.");
        }

        if ($_FILES["book_cover"]["size"] > 2 * 1024 * 1024) {
            die("Image size must be under 2MB.");
        }

        if (!move_uploaded_file($_FILES["book_cover"]["tmp_name"], $targetFile)) {
            die("Failed to upload image.");
        }

        $imagePath = "uploads/book_cover/" . $fileName;
    }

    $imageValue = $imagePath !== null ? "'" . $conn->real_escape_string($imagePath) . "'" : "NULL";
    $sql = "INSERT INTO books (title, book_excerpt, author, isbn, publisher, publication_year, category_id, book_cover_path)
            VALUES ('$title', '$book_excerpt', '$author', '$isbn', '$publisher', $publication_year, $category_id, $imageValue)";

    if ($conn->query($sql)) {
        header("Location: " . BASE_URL . "book_list.php?success=1");
        exit;
    } else {
        die("Database error: " . $conn->error);
    }
}

?>

<?php include(ROOT_PATH . '/include/header_resources.php') ?>

<?php include(ROOT_PATH . '/include/header.php') ?>
<?php include(ROOT_PATH . '/sidebar.php') ?>
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
												<?php while ($category = $categoryResult->fetch_assoc()): ?>
												<option value="<?= (int) $category["category_id"] ?>">
													<?= htmlspecialchars($category["category_name"]) ?>
												</option>
												<?php endwhile; ?>
											</select>
										</div>
										<div class="mb-3">
											<label class="form-label">Book Cover</label>
											<input type="file" id="fileInput" accept="image/*" class="form-control" name="book_cover">
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
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>

<?php if (isset($_GET["success"])): ?>
<div class="alert alert-success">Book added successfully.</div>
<?php endif; ?>
