<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request.');
}

$user_role_id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM user_roles WHERE user_role_id = $user_role_id");
if (!$result || $result->num_rows !== 1) {
    die('Record not found.');
}
$row = $result->fetch_assoc();

if (isset($_POST['save'])) {
    $user_id = (int) $_POST['user_id'];
    $role_id = (int) $_POST['role_id'];

    $sql = "UPDATE user_roles SET user_id = $user_id, role_id = $role_id WHERE user_role_id = $user_role_id";
    $updated = $conn->query($sql);

    if ($updated) {
        header("Location: " . BASE_URL . "user_role_list.php");
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
						<h3>Edit User Role</h3>
						<a href="<?php echo BASE_URL; ?>user_role_list.php" class="btn btn-secondary btn-sm">Back</a>
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
							<label class="form-label">Role Id</label>
							<input type="number" class="form-control" name="role_id" value="<?= htmlspecialchars($row['role_id']) ?>" />
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
