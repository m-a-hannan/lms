<?php
// Load core configuration, database connection, and permission helpers.
require_once dirname(__DIR__) . '/includes/config.php';
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';

// Group CRUD template pages for section-level access checks.
$crudTemplatePages = [
	'templates/blank_page.php',
	'templates/create_or_add_page.php',
	'templates/read_or_view_page.php',
	'templates/edit_or_update_page.php',
	'templates/delete_page.php',
];
// Library-related pages for the Manage Library navigation section.
$libraryPages = [
	'book_list.php',
	'category_list.php',
	'book_category_list.php',
	'book_edition_list.php',
	'book_copy_list.php',
	'loan_list.php',
	'reservation_list.php',
	'return_list.php',
	'gallery_list.php',
];
// User and role management pages for RBAC checks.
$userRolePages = [
	'user_list.php',
	'user_profile_list.php',
	'user_role_list.php',
	'manage_user_role.php',
	'role_list.php',
	'permission_management.php',
];
// Digital library pages to gate the digital menu section.
$digitalPages = [
	'digital_resource_list.php',
	'digital_file_list.php',
];
// Alert-related pages for announcements and notifications.
$alertPages = [
	'announcement_list.php',
	'notification_list.php',
];
// Policy/compliance pages for the policies section.
$policyPages = [
	'library_policy_list.php',
	'policy_change_list.php',
	'audit_log_list.php',
	'system_setting_list.php',
];
// Operational pages for holidays and backups.
$operationsPages = [
	'holiday_list.php',
	'backup_list.php',
];
// Fine and payment pages for the fines section.
$finePages = [
	'fine_list.php',
	'fine_waiver_list.php',
	'payment_list.php',
];
// System settings pages for menu management.
$systemSettingsPages = [
	'system_settings/index.php',
	'system_settings/home.php',
];

// Resolve per-section access flags for the current user.
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
			<!-- Sidebar container and branding -->
			<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
				<!--begin::Sidebar Brand-->
				<div class="sidebar-brand">
					<!--begin::Brand Link-->
					<a href="<?php echo BASE_URL; ?>home.php" class="brand-link">
						<!--begin::Brand Image-->
						<img src="<?php echo BASE_URL; ?>assets/img/LMS_Logo.png" alt="LMS Logo"
							class="brand-image opacity-75 shadow" />
						<!--end::Brand Image-->
						<!--begin::Brand Text-->
						<span class="brand-text fw-light">LMS</span>
						<!--end::Brand Text-->
					</a>
					<!--end::Brand Link-->
				</div>
				<!--end::Sidebar Brand-->
				<!--begin::Sidebar Wrapper-->
				<div class="sidebar-wrapper">
					<!-- Sidebar search controls -->
					<div class="sidebar-search px-3 pt-3">
						<div class="input-group input-group-sm sidebar-search-group">
							<span class="input-group-text"><i class="bi bi-search"></i></span>
							<input type="text" class="form-control" id="sidebarSearchInput" placeholder="Search menu item" autocomplete="off">
						</div>
						<div id="sidebarSearchSuggest" class="sidebar-search-suggest"></div>
					</div>
					<!-- Primary navigation tree -->
					<nav class="mt-2">
						<!--begin::Sidebar Menu-->
						<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
							aria-label="Main navigation" data-accordion="false" id="sidebar-navigation">
							<?php // Show the main Dashboard link when access is allowed. ?>
							<?php if ($canDashboard): ?>
							<li class="nav-item menu-open">
								<a href="<?php echo BASE_URL; ?>dashboard.php" class="nav-link active">
									<i class="nav-icon bi bi-speedometer"></i>
									<p>Dashboard</p>
								</a>
							</li>
							<?php endif; ?>
							<?php // Show the User Dashboard link when access is allowed. ?>
							<?php if (rbac_can_access($conn, 'user_dashboard.php')): ?>
							<li class="nav-item">
								<a href="<?php echo BASE_URL; ?>user_dashboard.php" class="nav-link">
									<i class="nav-icon bi bi-person-badge"></i>
									<p>User Dashboard</p>
								</a>
							</li>
							<?php endif; ?>

							<?php // Gate the Manage Library menu section by RBAC. ?>
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
									<?php // Show menu item for book_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'book_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>book_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Books List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for category_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'category_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>category_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Category List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for book_category_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'book_category_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>book_category_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Book Category List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for book_edition_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'book_edition_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>book_edition_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Book Edition List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for book_copy_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'book_copy_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>book_copy_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Book Copy List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for book_bulk_import.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'book_bulk_import.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>book_bulk_import.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Bulk Book Import</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for loan_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'loan_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>loan_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Loan List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for reservation_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'reservation_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>reservation_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Reservation List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for return_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'return_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>return_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Return List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for gallery_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'gallery_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>gallery_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Media Gallery</p>
										</a>
									</li>
									<?php endif; ?>
								</ul>
							</li>
							<?php endif; ?>

							<?php // Gate the Users & Roles menu section by RBAC. ?>
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
									<?php // Show menu item for user_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'user_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>user_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>User List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for user_profile_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'user_profile_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>user_profile_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>User Profile List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for user_role_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'user_role_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>user_role_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>User Role List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for manage_user_role.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'manage_user_role.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>manage_user_role.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Manage User Roles</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for role_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'role_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>role_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Role List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for permission_management.php when access is allowed. ?>
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

							<?php // Gate the Digital Library menu section by RBAC. ?>
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
									<?php // Show menu item for digital_resource_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'digital_resource_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>digital_resource_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Digital Resource List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for digital_file_list.php when access is allowed. ?>
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

							<?php // Gate the Announcements & Alerts menu section by RBAC. ?>
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
									<?php // Show menu item for announcement_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'announcement_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>announcement_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Announcement List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for notification_list.php when access is allowed. ?>
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

							<?php // Gate the Policies & Compliance menu section by RBAC. ?>
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
									<?php // Show menu item for library_policy_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'library_policy_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>library_policy_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Library Policy List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for policy_change_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'policy_change_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>policy_change_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Policy Change List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for audit_log_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'audit_log_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>audit_log_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Audit Log List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for system_setting_list.php when access is allowed. ?>
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

							<?php // Gate the Operations menu section by RBAC. ?>
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
									<?php // Show menu item for holiday_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'holiday_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>holiday_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Holiday List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for backup_list.php when access is allowed. ?>
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

							<?php // Gate the Fines & Payments menu section by RBAC. ?>
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
									<?php // Show menu item for fine_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'fine_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>fine_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Fine List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for fine_waiver_list.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'fine_waiver_list.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>fine_waiver_list.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Fine Waiver List</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for payment_list.php when access is allowed. ?>
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

							<?php // Gate the System Settings menu section by RBAC. ?>
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
									<?php // Show menu item for system_settings/index.php when access is allowed. ?>
									<?php if (rbac_can_access($conn, 'system_settings/index.php')): ?>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>system_settings/index.php" class="nav-link">
											<i class="bi bi-link-45deg"></i>
											<p>Menu Items</p>
										</a>
									</li>
									<?php endif; ?>
									<?php // Show menu item for system_settings/home.php when access is allowed. ?>
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
