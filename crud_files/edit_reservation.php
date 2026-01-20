<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$reservation_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM reservations WHERE reservation_id = $reservation_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

if (isset($_POST['save'])) {
    $user_id = (int) $_POST['user_id'];
    $copy_id = (int) $_POST['copy_id'];
    $reservation_date = $conn->real_escape_string(trim($_POST['reservation_date']));
    $expiry_date = $conn->real_escape_string(trim($_POST['expiry_date']));

    $sql = "UPDATE reservations SET user_id = $user_id, copy_id = $copy_id, reservation_date = '$reservation_date', expiry_date = '$expiry_date' WHERE reservation_id = $reservation_id";
    $updated = $conn->query($sql);

    if ($updated) {
        header("Location: " . BASE_URL . "reservation_list.php");
        exit;
    } else {
        die("Update failed: " . $conn->error);
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
						<h3>Edit Reservation</h3>
						<a href="<?php echo BASE_URL; ?>reservation_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="card shadow-sm">
						<div class="card-body">
							<form method="post">
								<div class="row g-4">
									<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">User Id</label>
							<input type="number" class="form-control" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Copy Id</label>
							<input type="number" class="form-control" name="copy_id" value="<?= htmlspecialchars($row['copy_id']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Reservation Date</label>
							<input type="date" class="form-control" name="reservation_date" value="<?= htmlspecialchars($row['reservation_date']) ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Expiry Date</label>
							<input type="date" class="form-control" name="expiry_date" value="<?= htmlspecialchars($row['expiry_date']) ?>" />
						</div>
										<button type="submit" name="save" class="btn btn-primary">Update</button>
									</div>
								</div>
							</form>
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
