<?php
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/permissions.php';

$crudTemplatePages = [
	'templates/blank_page.php',
	'templates/create_or_add_page.php',
	'templates/read_or_view_page.php',
	'templates/edit_or_update_page.php',
	'templates/delete_page.php',
];
$libraryPages = [
	'book_list.php',
	'category_list.php',
	'book_category_list.php',
	'book_edition_list.php',
	'book_copy_list.php',
	'loan_list.php',
	'reservation_list.php',
	'return_list.php',
];
$userRolePages = [
	'user_list.php',
	'user_profile_list.php',
	'user_role_list.php',
	'manage_user_role.php',
	'role_list.php',
	'permission_management.php',
];
$digitalPages = [
	'digital_resource_list.php',
	'digital_file_list.php',
];
$alertPages = [
	'announcement_list.php',
	'notification_list.php',
];
$policyPages = [
	'library_policy_list.php',
	'policy_change_list.php',
	'audit_log_list.php',
	'system_setting_list.php',
];
$operationsPages = [
	'holiday_list.php',
	'backup_list.php',
];
$finePages = [
	'fine_list.php',
	'fine_waiver_list.php',
	'payment_list.php',
];
$systemSettingsPages = [
	'system_settings/index.php',
	'system_settings/home.php',
];

$canDashboard = rbac_can_access($conn, 'dashboard.php');
$canCrudTemplates = rbac_any_access($conn, $crudTemplatePages);
$canLibrary = rbac_any_access($conn, $libraryPages);
$canUsersRoles = rbac_any_access($conn, $userRolePages);
$canDigital = rbac_any_access($conn, $digitalPages);
$canAlerts = rbac_any_access($conn, $alertPages);
$canPolicies = rbac_any_access($conn, $policyPages);
$canOperations = rbac_any_access($conn, $operationsPages);
$canFines = rbac_any_access($conn, $finePages);
$canSystemSettings = rbac_any_access($conn, $systemSettingsPages);
?>
			<!--begin::Sidebar-->
			<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
				<!--begin::Sidebar Brand-->
				<div class="sidebar-brand">
					<!--begin::Brand Link-->
					<a href="<?php echo BASE_URL; ?>index.php" class="brand-link">
						<!--begin::Brand Image-->
						<img src="<?php echo BASE_URL; ?>assets/img/AdminLTELogo.png" alt="AdminLTE Logo"
							class="brand-image opacity-75 shadow" />
						<!--end::Brand Image-->
						<!--begin::Brand Text-->
						<span class="brand-text fw-light">AdminLTE 4</span>
						<!--end::Brand Text-->
					</a>
					<!--end::Brand Link-->
				</div>
				<!--end::Sidebar Brand-->
				<!--begin::Sidebar Wrapper-->
				<div class="sidebar-wrapper">
					<nav class="mt-2">
						<!--begin::Sidebar Menu-->
						<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
							aria-label="Main navigation" data-accordion="false" id="navigation">
							<?php if ($canDashboard): ?>
							<li class="nav-item menu-open">
								<a href="<?php echo BASE_URL; ?>dashboard.php" class="nav-link active">
									<i class="nav-icon bi bi-speedometer"></i>
									<p>Dashboard</p>
								</a>
							</li>
							<?php endif; ?>
							<?php if (rbac_can_access($conn, 'user_dashboard.php')): ?>
							<li class="nav-item">
								<a href="<?php echo BASE_URL; ?>user_dashboard.php" class="nav-link">
									<i class="nav-icon bi bi-person-badge"></i>
									<p>User Dashboard</p>
								</a>
							</li>
							<?php endif; ?>

							<?php if ($canCrudTemplates): ?>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="bi bi-ui-checks"></i>
									<p>
										CRUD Templates
										<i class="nav-arrow bi bi-chevron-right"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<?php if (rbac_can_access($conn, 'templates/blank_page.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>templates/blank_page.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Blank Page Template</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'templates/create_or_add_page.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>templates/create_or_add_page.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Create Page Template</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'templates/read_or_view_page.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>templates/read_or_view_page.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>View Page Template</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'templates/edit_or_update_page.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>templates/edit_or_update_page.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Update Page Template</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'templates/delete_page.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>templates/delete_page.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Delete Page Template</p>
										</a>
									</li>
									<?php endif; ?>
								</ul>
							</li>
							<?php endif; ?>

							<?php if ($canLibrary): ?>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="bi bi-diagram-3-fill"></i>
									<p>
										Manage Library
										<i class="nav-arrow bi bi-chevron-right"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<?php if (rbac_can_access($conn, 'book_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>book_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Books List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'category_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>category_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Category List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'book_category_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>book_category_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Book Category List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'book_edition_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>book_edition_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Book Edition List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'book_copy_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>book_copy_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Book Copy List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'loan_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>loan_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Loan List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'reservation_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>reservation_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Reservation List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'return_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>return_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Return List</p>
										</a>
									</li>
									<?php endif; ?>
								</ul>
							</li>
							<?php endif; ?>

							<?php if ($canUsersRoles): ?>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="bi bi-people-fill"></i>
									<p>
										Users & Roles
										<i class="nav-arrow bi bi-chevron-right"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<?php if (rbac_can_access($conn, 'user_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>user_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>User List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'user_profile_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>user_profile_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>User Profile List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'user_role_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>user_role_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>User Role List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'manage_user_role.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>manage_user_role.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Manage User Roles</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'role_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>role_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Role List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'permission_management.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>permission_management.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Permission Management</p>
										</a>
									</li>
									<?php endif; ?>
								</ul>
							</li>
							<?php endif; ?>

							<?php if ($canDigital): ?>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="bi bi-cloud-arrow-down-fill"></i>
									<p>
										Digital Library
										<i class="nav-arrow bi bi-chevron-right"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<?php if (rbac_can_access($conn, 'digital_resource_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>digital_resource_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Digital Resource List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'digital_file_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>digital_file_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Digital File List</p>
										</a>
									</li>
									<?php endif; ?>
								</ul>
							</li>
							<?php endif; ?>

							<?php if ($canAlerts): ?>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="bi bi-megaphone-fill"></i>
									<p>
										Announcements & Alerts
										<i class="nav-arrow bi bi-chevron-right"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<?php if (rbac_can_access($conn, 'announcement_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>announcement_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Announcement List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'notification_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>notification_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Notification List</p>
										</a>
									</li>
									<?php endif; ?>
								</ul>
							</li>
							<?php endif; ?>

							<?php if ($canPolicies): ?>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="bi bi-journal-text"></i>
									<p>
										Policies & Compliance
										<i class="nav-arrow bi bi-chevron-right"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<?php if (rbac_can_access($conn, 'library_policy_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>library_policy_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Library Policy List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'policy_change_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>policy_change_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Policy Change List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'audit_log_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>audit_log_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Audit Log List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'system_setting_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>system_setting_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>System Setting List</p>
										</a>
									</li>
									<?php endif; ?>
								</ul>
							</li>
							<?php endif; ?>

							<?php if ($canOperations): ?>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="bi bi-calendar-event"></i>
									<p>
										Operations
										<i class="nav-arrow bi bi-chevron-right"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<?php if (rbac_can_access($conn, 'holiday_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>holiday_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Holiday List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'backup_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>backup_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Backup List</p>
										</a>
									</li>
									<?php endif; ?>
								</ul>
							</li>
							<?php endif; ?>

							<?php if ($canFines): ?>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="bi bi-cash-stack"></i>
									<p>
										Fines & Payments
										<i class="nav-arrow bi bi-chevron-right"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<?php if (rbac_can_access($conn, 'fine_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>fine_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Fine List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'fine_waiver_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>fine_waiver_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Fine Waiver List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'payment_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>payment_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Payment List</p>
										</a>
									</li>
									<?php endif; ?>
								</ul>
							</li>
							<?php endif; ?>

							<?php if ($canSystemSettings): ?>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="bi bi-cash-stack"></i>
									<p>
										System Settings
										<i class="nav-arrow bi bi-chevron-right"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<?php if (rbac_can_access($conn, 'system_settings/index.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>system_settings/index.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Menu Items</p>
										</a>
									</li>
									<?php endif; ?>
									<?php if (rbac_can_access($conn, 'system_settings/home.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>system_settings/home.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Add Menu</p>
										</a>
									</li>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>system_settings/home.php?refresh=1" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Refresh Page List</p>
										</a>
									</li>
									<?php endif; ?>
								</ul>
							</li>
							<?php endif; ?>

























						</ul>
						<!--end::Sidebar Menu-->
					</nav>
				</div>
				<!--end::Sidebar Wrapper-->
			</aside>
			<!--end::Sidebar-->
