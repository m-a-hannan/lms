<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
require_once ROOT_PATH . '/include/connection.php';

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$displayName = 'User';
$displayRole = 'Member';
$profileImage = BASE_URL . 'assets/img/user2-160x160.jpg';
$memberSince = '';
$notificationCount = 0;
$notifications = [];

if ($userId > 0) {
	if (!empty($_SESSION['user_username'])) {
		$displayName = $_SESSION['user_username'];
	}

$profileResult = $conn->query("SELECT first_name, last_name, designation, profile_picture, created_date FROM user_profiles WHERE user_id = $userId ORDER BY profile_id DESC LIMIT 1");
if ($profileResult && $profileResult->num_rows === 1) {
	$row = $profileResult->fetch_assoc();
		$fullName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
		if ($fullName !== '') {
			$displayName = $fullName;
		}
		if (!empty($row['designation'])) {
			$displayRole = $row['designation'];
		}
		if (!empty($row['profile_picture'])) {
			$profileImage = BASE_URL . ltrim($row['profile_picture'], '/');
		}
		if (!empty($row['created_date'])) {
			$memberSince = date('M Y', strtotime($row['created_date']));
		}
	}

	if ($displayRole === 'Member') {
		$roleResult = $conn->query("SELECT role_name FROM user_roles WHERE user_id = $userId ORDER BY user_role_id DESC LIMIT 1");
		if ($roleResult && $roleResult->num_rows === 1) {
			$roleRow = $roleResult->fetch_assoc();
			if (!empty($roleRow['role_name'])) {
				$displayRole = $roleRow['role_name'];
			}
		}
	}

	if ($displayName === 'User') {
		$userResult = $conn->query("SELECT username, email, created_date FROM users WHERE user_id = $userId LIMIT 1");
		if ($userResult && $userResult->num_rows === 1) {
			$userRow = $userResult->fetch_assoc();
			if (!empty($userRow['username'])) {
				$displayName = $userRow['username'];
			} elseif (!empty($userRow['email'])) {
				$displayName = $userRow['email'];
			}
			if ($memberSince === '' && !empty($userRow['created_date'])) {
				$memberSince = date('M Y', strtotime($userRow['created_date']));
			}
		}
	}

	if ($memberSince === '') {
		$userResult = $conn->query("SELECT created_date FROM users WHERE user_id = $userId LIMIT 1");
		if ($userResult && $userResult->num_rows === 1) {
			$userRow = $userResult->fetch_assoc();
			if (!empty($userRow['created_date'])) {
				$memberSince = date('M Y', strtotime($userRow['created_date']));
			}
		}
	}

	$countResult = $conn->query(
		"SELECT COUNT(*) AS total\n\t\t FROM notifications\n\t\t WHERE user_id = $userId AND read_status = 0 AND deleted_date IS NULL"
	);
	if ($countResult && $countResult->num_rows === 1) {
		$countRow = $countResult->fetch_assoc();
		$notificationCount = (int) ($countRow['total'] ?? 0);
	}

	$notificationResult = $conn->query(
		"SELECT notification_id, title, message, created_at, read_status\n\t\t FROM notifications\n\t\t WHERE user_id = $userId AND deleted_date IS NULL\n\t\t ORDER BY created_at DESC, notification_id DESC\n\t\t LIMIT 5"
	);
	if ($notificationResult) {
		while ($row = $notificationResult->fetch_assoc()) {
			$notifications[] = $row;
		}
	}
}
?>
	<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
		<!--begin::App Wrapper-->
		<div class="app-wrapper">
			<!--begin::Header-->
			<nav class="app-header navbar navbar-expand bg-body">
				<!--begin::Container-->
				<div class="container-fluid">
					<!--begin::Start Navbar Links-->
					<ul class="navbar-nav">
						<li class="nav-item">
							<a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
								<i class="bi bi-list"></i>
							</a>
						</li>
						<li class="nav-item d-none d-md-block"><a href="<?php echo BASE_URL; ?>home.php" class="nav-link">Library</a></li>
						<li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Contact</a></li>
					</ul>
					<!--end::Start Navbar Links-->
					<!--begin::End Navbar Links-->
					<ul class="navbar-nav ms-auto">
						<!--begin::Navbar Search-->
						<li class="nav-item">
							<a class="nav-link" data-widget="navbar-search" href="#" role="button">
								<i class="bi bi-search"></i>
							</a>
						</li>
						<!--end::Navbar Search-->
						<!--begin::Notifications Dropdown Menu-->
						<li class="nav-item dropdown">
							<a class="nav-link" data-bs-toggle="dropdown" href="#">
								<i class="bi bi-bell-fill"></i>
								<?php if ($notificationCount > 0): ?>
								<span class="navbar-badge badge text-bg-warning"><?php echo $notificationCount; ?></span>
								<?php endif; ?>
							</a>
							<div class="dropdown-menu dropdown-menu-lg dropdown-menu-end notification-dropdown">
								<div class="dropdown-item dropdown-header d-flex justify-content-between align-items-center">
									<span><?php echo $notificationCount; ?> Notifications</span>
									<form method="post" action="<?php echo BASE_URL; ?>actions/clear_notifications.php" class="m-0">
										<button type="submit" class="btn btn-link btn-sm text-danger p-0 notification-clear-all" title="Clear all notifications" <?php echo $notificationCount > 0 ? '' : 'disabled'; ?>>
											Clear All
										</button>
									</form>
								</div>
								<div class="dropdown-divider"></div>
								<?php if ($notifications): ?>
								<?php foreach ($notifications as $note): ?>
								<div class="dropdown-item">
									<div class="d-flex align-items-start gap-2 notification-entry">
										<i class="bi bi-info-circle text-primary mt-1"></i>
										<div class="flex-grow-1">
											<div class="d-flex flex-wrap justify-content-between gap-2">
												<a href="<?php echo BASE_URL; ?>notification_list.php" class="notification-title text-decoration-none">
													<?php echo htmlspecialchars($note['title'] ?? 'Notification'); ?>
												</a>
												<div class="d-flex align-items-center gap-2">
													<span class="notification-date text-secondary">
														<?php echo htmlspecialchars($note['created_at'] ?? ''); ?>
													</span>
													<form method="post" action="<?php echo BASE_URL; ?>actions/remove_notification.php" class="m-0">
														<input type="hidden" name="notification_id" value="<?php echo (int) ($note['notification_id'] ?? 0); ?>">
														<button type="submit" class="btn btn-link text-danger p-0 notification-clear-btn" title="Clear notification">
															<i class="bi bi-x-circle-fill"></i>
														</button>
													</form>
												</div>
											</div>
											<div class="notification-message text-secondary small">
												<?php echo htmlspecialchars($note['message'] ?? ''); ?>
											</div>
										</div>
									</div>
								</div>
								<div class="dropdown-divider"></div>
								<?php endforeach; ?>
								<?php else: ?>
								<span class="dropdown-item text-muted">No notifications yet.</span>
								<div class="dropdown-divider"></div>
								<?php endif; ?>
								<a href="<?php echo BASE_URL; ?>notification_list.php" class="dropdown-item dropdown-footer"> See All Notifications </a>
							</div>
						</li>
						<!--end::Notifications Dropdown Menu-->
						<!--begin::Fullscreen Toggle-->
						<li class="nav-item">
							<a class="nav-link" href="#" data-lte-toggle="fullscreen">
								<i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
								<i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
							</a>
						</li>
						<!--end::Fullscreen Toggle-->
						<!--begin::User Menu Dropdown-->
						<li class="nav-item dropdown user-menu">
							<a href="<?php echo BASE_URL; ?>view_profile.php" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
								<img
									src="<?php echo htmlspecialchars($profileImage); ?>"
									class="user-image rounded-circle shadow"
									alt="User Image"
								/>
								<span class="d-none d-md-inline"><?php echo htmlspecialchars($displayName); ?></span>
							</a>
							<ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
								<!--begin::User Image-->
								<li class="user-header text-bg-primary">
									<img
										src="<?php echo htmlspecialchars($profileImage); ?>"
										class="rounded-circle shadow"
										alt="User Image"
									/>
									<p>
										<?php echo htmlspecialchars($displayName); ?> - <?php echo htmlspecialchars($displayRole); ?>
										<small>Member since <?php echo $memberSince !== "" ? htmlspecialchars($memberSince) : "-"; ?></small>
									</p>
								</li>
								<!--end::User Image-->
								<!--begin::Menu Body-->
								<li class="user-body">
									<!--begin::Row-->
									<div class="row">
										<div class="col-4 text-center"><a href="#">Followers</a></div>
										<div class="col-4 text-center"><a href="#">Sales</a></div>
										<div class="col-4 text-center"><a href="#">Friends</a></div>
									</div>
									<!--end::Row-->
								</li>
								<!--end::Menu Body-->
								<!--begin::Menu Footer-->
								<li class="user-footer">
									<a href="<?php echo BASE_URL; ?>view_profile.php" class="btn btn-default btn-flat">Profile</a>
									<a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
								</li>
								<!--end::Menu Footer-->
							</ul>
						</li>
						<!--end::User Menu Dropdown-->
					</ul>
					<!--end::End Navbar Links-->
				</div>
				<!--end::Container-->
			</nav>
			<!--end::Header-->
