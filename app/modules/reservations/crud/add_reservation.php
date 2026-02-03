<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 3) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';

// Handle form submission for creating a reservation.
if (isset($_POST['save'])) {
    // Read and sanitize incoming form values.
    $user_id = (int) $_POST['user_id'];
    $book_id = (int) ($_POST['book_id'] ?? 0);
    $copy_id = (int) $_POST['copy_id'];
    $reservation_date = $conn->real_escape_string(trim($_POST['reservation_date']));
    $expiry_date = $conn->real_escape_string(trim($_POST['expiry_date']));

    // Resolve the book id from the copy when needed.
    if ($book_id <= 0 && $copy_id > 0) {
        $bookResult = $conn->query(
            "SELECT e.book_id
             FROM book_copies c
             JOIN book_editions e ON c.edition_id = e.edition_id
             WHERE c.copy_id = $copy_id
             LIMIT 1"
        );
        // Apply the resolved book id when found.
        if ($bookResult && $bookResult->num_rows === 1) {
            $bookRow = $bookResult->fetch_assoc();
            $book_id = (int) ($bookRow['book_id'] ?? 0);
        }
    }

    // Normalize nullable ids for insertion.
    $bookValue = $book_id > 0 ? $book_id : 'NULL';
    $copyValue = $copy_id > 0 ? $copy_id : 'NULL';

    // Insert the reservation record into the database.
    $sql = "INSERT INTO reservations (user_id, book_id, copy_id, reservation_date, expiry_date) VALUES ($user_id, $bookValue, $copyValue, '$reservation_date', '$expiry_date')";
    $result = $conn->query($sql);

    if ($result) {
        // Redirect back to the list after saving.
        header("Location: " . BASE_URL . "reservation_list.php");
        exit;
    } else {
        die("Database error: " . $conn->error);
    }
}
?>
<?php // Shared header resources and layout chrome. ?>
<?php include(ROOT_PATH . '/app/includes/header_resources.php') ?>
<?php include(ROOT_PATH . '/app/includes/header.php') ?>
<?php include(ROOT_PATH . '/app/views/sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<!-- Page header with title and navigation. -->
					<div class="mb-4 d-flex justify-content-between">
						<h3>Add Reservation</h3>
						<a href="<?php echo BASE_URL; ?>reservation_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<!-- Reservation creation form card. -->
							<div class="card card-primary card-outline mb-4">
								<!-- Submission form for a new reservation. -->
								<form action="<?php echo BASE_URL; ?>crud_files/add_reservation.php" method="post">
									<div class="card-body">
										<!-- User reference input. -->
										<div class="mb-3">
											<label class="form-label">User Id</label>
											<input type="number" class="form-control" name="user_id" />
										</div>
										<!-- Book reference input. -->
										<div class="mb-3">
											<label class="form-label">Book Id</label>
											<input type="number" class="form-control" name="book_id" />
										</div>
										<!-- Copy reference input. -->
										<div class="mb-3">
											<label class="form-label">Copy Id</label>
											<input type="number" class="form-control" name="copy_id" />
										</div>
										<!-- Reservation date input. -->
										<div class="mb-3">
											<label class="form-label">Reservation Date</label>
											<input type="date" class="form-control" name="reservation_date" />
										</div>
										<!-- Expiry date input. -->
										<div class="mb-3">
											<label class="form-label">Expiry Date</label>
											<input type="date" class="form-control" name="expiry_date" />
										</div>
									</div>
									<!-- Form actions. -->
									<div class="card-footer">
										<button type="submit" name="save" class="btn btn-primary">Submit</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
<!--end::App Main-->
<?php // Shared footer layout and scripts. ?>
<?php include(ROOT_PATH . '/app/includes/footer.php') ?>
<?php include(ROOT_PATH . '/app/includes/footer_resources.php') ?>
