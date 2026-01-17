<?php
require_once "include/connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitize inputs
    $title             = trim($_POST["title"]);
    $author            = trim($_POST["author"]);
    $isbn              = trim($_POST["isbn"]);
    $publisher         = trim($_POST["publisher"]);
    $publication_year  = (int) $_POST["publication_year"];

    $uploadDir = "uploads/book_cover/";
    $imagePath = null;

    // Handle image upload
    if (!empty($_FILES["book_cover"]["name"])) {

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

        $imagePath = $targetFile;
    }

    // Insert into database
    $stmt = $conn->prepare(
        "INSERT INTO books (title, author, isbn, publisher, publication_year, book_cover_path)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssssis",
        $title,
        $author,
        $isbn,
        $publisher,
        $publication_year,
        $imagePath
    );

    if ($stmt->execute()) {
        header("Location: book_list.php?success=1");
        exit;
    } else {
        die("Database error: " . $stmt->error);
    }
}

?>

<?php include('include/header_resources.php') ?>

<?php include('include/header.php') ?>
<?php include('sidebar.php') ?>


<link rel="stylesheet" href="css/custom.css">
<!--begin::App Main-->
<main class="app-main">
	<!--begin::App Content-->
	<div class="app-content">
		<!--begin::Container-->
		<div class="container-fluid">
			<!--begin::Row-->
			<div class="row">
				<!-- Content title -->
				<h1>Add Book</h1>
				<!-- FORM ELELEMNTS -->
				<div class="row mt">
					<div class="col-md-6">
						<!--begin::Form-->
						<div class="card card-primary card-outline mb-4">
							<!-- must add form action -->
							<form action="add_book.php" method="post" enctype="multipart/form-data">
								<!--begin::Body-->
								<div class="card-body">
									<div class="mb-3">
										<label class="form-label">Book Title</label>
										<input type="text" class="form-control" name="title" aria-describedby="#" />
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
</main>
<!--end::App Main-->
<?php include('include/footer.php') ?>

<script src="js/custom.js"></script>
<?php include('include/footer_resources.php') ?>

<?php if (isset($_GET["success"])): ?>
    <div class="alert alert-success">Book added successfully.</div>
<?php endif; ?>