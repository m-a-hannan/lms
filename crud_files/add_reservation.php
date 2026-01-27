<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (isset($_POST['save'])) {
    $user_id = (int) $_POST['user_id'];
    $book_id = (int) ($_POST['book_id'] ?? 0);
    $copy_id = (int) $_POST['copy_id'];
    $reservation_date = $conn->real_escape_string(trim($_POST['reservation_date']));
    $expiry_date = $conn->real_escape_string(trim($_POST['expiry_date']));

    if ($book_id <= 0 && $copy_id > 0) {
        $bookResult = $conn->query(
            "SELECT e.book_id
             FROM book_copies c
             JOIN book_editions e ON c.edition_id = e.edition_id
             WHERE c.copy_id = $copy_id
             LIMIT 1"
        );
        if ($bookResult && $bookResult->num_rows === 1) {
            $bookRow = $bookResult->fetch_assoc();
            $book_id = (int) ($bookRow['book_id'] ?? 0);
        }
    }

    $bookValue = $book_id > 0 ? $book_id : 'NULL';
    $copyValue = $copy_id > 0 ? $copy_id : 'NULL';

    $sql = "INSERT INTO reservations (user_id, book_id, copy_id, reservation_date, expiry_date) VALUES ($user_id, $bookValue, $copyValue, '$reservation_date', '$expiry_date')";
    $result = $conn->query($sql);

    if ($result) {
        header("Location: " . BASE_URL . "reservation_list.php");
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
	<div class="app-content">
		<div class="container-fluid">
			<div class="row">
				<div class="container py-5">
					<div class="mb-4 d-flex justify-content-between">
						<h3>Add Reservation</h3>
						<a href="<?php echo BASE_URL; ?>reservation_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_reservation.php" method="post">
									<div class="card-body">
						<div class="mb-3">
							<label class="form-label">User Id</label>
							<input type="number" class="form-control" name="user_id" />
						</div>
						<div class="mb-3">
							<label class="form-label">Book Id</label>
							<input type="number" class="form-control" name="book_id" />
						</div>
						<div class="mb-3">
							<label class="form-label">Copy Id</label>
							<input type="number" class="form-control" name="copy_id" />
						</div>
						<div class="mb-3">
							<label class="form-label">Reservation Date</label>
							<input type="date" class="form-control" name="reservation_date" />
						</div>
						<div class="mb-3">
							<label class="form-label">Expiry Date</label>
							<input type="date" class="form-control" name="expiry_date" />
						</div>
									</div>
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
<?php include(ROOT_PATH . '/include/footer.php') ?>
<?php include(ROOT_PATH . '/include/footer_resources.php') ?>
