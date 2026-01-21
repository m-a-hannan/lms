<?php
require_once dirname(__DIR__) . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';

if (isset($_POST['save'])) {
    $user_id = (int) $_POST['user_id'];
    $role_id = (int) $_POST['role_id'];

    $sql = "INSERT INTO user_roles (user_id, role_id) VALUES ($user_id, $role_id)";
    $result = $conn->query($sql);

    if ($result) {
        header("Location: " . BASE_URL . "user_role_list.php");
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
						<h3>Add User Role</h3>
						<a href="<?php echo BASE_URL; ?>user_role_list.php" class="btn btn-secondary btn-sm">Back</a>
					</div>
					<div class="row mt">
						<div class="col-md-6">
							<div class="card card-primary card-outline mb-4">
								<form action="<?php echo BASE_URL; ?>crud_files/add_user_role.php" method="post">
									<div class="card-body">
						<div class="mb-3">
							<label class="form-label">User Id</label>
							<input type="number" class="form-control" name="user_id" />
						</div>
						<div class="mb-3">
							<label class="form-label">Role Id</label>
							<input type="number" class="form-control" name="role_id" />
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
