<?php
require_once dirname(__DIR__) . "/include/config.php";
require_once ROOT_PATH . "/include/connection.php";

/* ---------------------------
   Validate & fetch book
---------------------------- */
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid book ID.");
}

$book_id = (int) $_GET["id"];

$result = $conn->query("SELECT * FROM books WHERE book_id = $book_id");

if ($result->num_rows !== 1) {
    die("Book not found.");
}

$book = $result->fetch_assoc();

// Fetch categories for dropdown.
$categoryResult = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
if ($categoryResult === false) {
    die("Category query failed: " . $conn->error);
}

$copyCounts = ['total' => 0, 'available' => 0];
$countResult = $conn->query(
    "SELECT COUNT(c.copy_id) AS total_copies,
        SUM(CASE WHEN c.status IS NULL OR c.status = '' OR c.status = 'available' THEN 1 ELSE 0 END) AS available_copies
     FROM book_copies c
     JOIN book_editions e ON c.edition_id = e.edition_id
     WHERE e.book_id = $book_id"
);
if ($countResult && $countResult->num_rows === 1) {
    $countRow = $countResult->fetch_assoc();
    $copyCounts['total'] = (int) ($countRow['total_copies'] ?? 0);
    $copyCounts['available'] = (int) ($countRow['available_copies'] ?? 0);
}

/* ---------------------------
   Handle update submission
---------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title            = $conn->real_escape_string(trim($_POST["title"]));
    $author           = $conn->real_escape_string(trim($_POST["author"]));
    $isbn             = $conn->real_escape_string(trim($_POST["isbn"]));
    $publisher        = $conn->real_escape_string(trim($_POST["publisher"]));
    $publication_year = (int) $_POST["publication_year"];
    $category_id      = (int) $_POST["category_id"];
    $add_copies       = (int) ($_POST["add_copies"] ?? 0);

    $uploadDir = ROOT_PATH . "/uploads/book_cover/";
    $imagePath = $book["book_cover_path"]; // default: keep existing

    if (!empty($_FILES["book_cover"]["name"])) {

        $fileName = time() . "_" . basename($_FILES["book_cover"]["name"]);
        $target   = $uploadDir . $fileName;
        $ext      = strtolower(pathinfo($target, PATHINFO_EXTENSION));

        $allowed = ["jpg", "jpeg", "png", "webp"];

        if (!in_array($ext, $allowed)) {
            die("Invalid image format.");
        }

        if ($_FILES["book_cover"]["size"] > 2 * 1024 * 1024) {
            die("Image must be under 2MB.");
        }

        if (move_uploaded_file($_FILES["book_cover"]["tmp_name"], $target)) {

            // delete old image if exists
            if (!empty($book["book_cover_path"])) {
                $oldPath = ROOT_PATH . '/' . ltrim($book["book_cover_path"], '/');
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $imagePath = "uploads/book_cover/" . $fileName;
        } else {
            die("Image upload failed.");
        }
    }

    $imageValue = $conn->real_escape_string($imagePath);
    $sql = "UPDATE books
            SET title = '$title',
                author = '$author',
                isbn = '$isbn',
                publisher = '$publisher',
                publication_year = $publication_year,
                category_id = $category_id,
                book_cover_path = '$imageValue'
            WHERE book_id = $book_id";

    $conn->begin_transaction();
    try {
        if (!$conn->query($sql)) {
            throw new RuntimeException("Update failed: " . $conn->error);
        }

        if ($add_copies > 0) {
            $editionResult = $conn->query(
                "SELECT edition_id
                 FROM book_editions
                 WHERE book_id = $book_id
                 ORDER BY edition_id DESC
                 LIMIT 1"
            );
            if ($editionResult && $editionResult->num_rows === 1) {
                $editionRow = $editionResult->fetch_assoc();
                $editionId = (int) ($editionRow['edition_id'] ?? 0);
            } else {
                $yearValue = $publication_year > 0 ? $publication_year : "NULL";
                $editionSql = "INSERT INTO book_editions (book_id, edition_number, publication_year)
                               VALUES ($book_id, 1, $yearValue)";
                if (!$conn->query($editionSql)) {
                    throw new RuntimeException("Edition insert failed: " . $conn->error);
                }
                $editionId = (int) $conn->insert_id;
            }

            if (!empty($editionId)) {
                for ($i = 1; $i <= $add_copies; $i++) {
                    $barcode = $conn->real_escape_string("B{$book_id}-E{$editionId}-" . date('YmdHis') . "-{$i}");
                    $copySql = "INSERT INTO book_copies (edition_id, barcode, status)
                                VALUES ($editionId, '$barcode', 'available')";
                    if (!$conn->query($copySql)) {
                        throw new RuntimeException("Copy insert failed: " . $conn->error);
                    }
                }
            }
        }

        $conn->commit();
        header("Location: " . BASE_URL . "book_list.php");
        exit;
    } catch (Throwable $e) {
        $conn->rollback();
        die($e->getMessage());
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
						<h3>Edit Book</h3>
						<a href="<?php echo BASE_URL; ?>book_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>

					<div class="card shadow-sm">
						<div class="card-body">

							<form method="post" enctype="multipart/form-data">
								<div class="row g-4">

									<div class="col-md-6">
										<div class="mb-3">
											<label class="form-label">Total Copies</label>
											<input type="text" class="form-control" value="<?= htmlspecialchars((string) $copyCounts['total']) ?>" readonly>
										</div>
										<div class="mb-3">
											<label class="form-label">Available Copies</label>
											<input type="text" class="form-control" value="<?= htmlspecialchars((string) $copyCounts['available']) ?>" readonly>
										</div>
										<div class="mb-3">
											<label class="form-label">Book Title</label>
											<input type="text" name="title" class="form-control"
												value="<?= htmlspecialchars($book["title"]) ?>" required>
										</div>

										<div class="mb-3">
											<label class="form-label">Author</label>
											<input type="text" name="author" class="form-control"
												value="<?= htmlspecialchars($book["author"]) ?>" required>
										</div>

										<div class="mb-3">
											<label class="form-label">ISBN</label>
											<input type="text" name="isbn" class="form-control"
												value="<?= htmlspecialchars($book["isbn"]) ?>">
										</div>

										<div class="mb-3">
											<label class="form-label">Publisher</label>
											<input type="text" name="publisher" class="form-control"
												value="<?= htmlspecialchars($book["publisher"]) ?>">
										</div>

										<div class="mb-3">
											<label class="form-label">Publication Year</label>
											<input type="number" name="publication_year" class="form-control"
												value="<?= htmlspecialchars($book["publication_year"]) ?>">
										</div>
										<div class="mb-3">
											<label class="form-label">Category</label>
											<select class="form-select" name="category_id" required>
												<option value="" disabled>Select a category</option>
												<?php while ($category = $categoryResult->fetch_assoc()): ?>
												<option value="<?= (int) $category["category_id"] ?>"
													<?php if ((int) $book["category_id"] === (int) $category["category_id"]) echo 'selected'; ?>>
													<?= htmlspecialchars($category["category_name"]) ?>
												</option>
												<?php endwhile; ?>
											</select>
										</div>

										<div class="mb-3">
											<label class="form-label">Replace Book Cover</label>
											<input type="file" name="book_cover" class="form-control" accept="image/*"
												onchange="previewNewCover(event)">
										</div>
										<div class="mb-3">
											<label class="form-label">Add Copies</label>
											<input type="number" name="add_copies" class="form-control" min="0" value="0">
										</div>

										<button type="submit" class="btn btn-primary">Update Book</button>
									</div>

									<div class="col-md-6">
										<div class="card card-primary card-outline mb-4">
											<div class="preview-box">
												<h6 class="mb-3">Current Cover</h6>
												<div class="preview-area">

													<?php if (!empty($book["book_cover_path"])): ?>
													<img id="previewImage" src="<?= htmlspecialchars(BASE_URL . $book["book_cover_path"]) ?>"
														class="img-fluid cover-preview" alt="Book Cover">
													<?php else: ?>
													<p class="text-muted">No cover uploaded</p>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					<!-- row end -->
				</div>
			</div>
</main>
<!--end::App Main-->
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>
