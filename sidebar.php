			<!--begin::Sidebar-->
			<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
				<!--begin::Sidebar Brand-->
				<div class="sidebar-brand">
					<!--begin::Brand Link-->
					<a href="<?php echo BASE_URL; ?>index.php" class="brand-link">
						<!--begin::Brand Image-->
						<img src="<?php echo BASE_URL; ?>assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
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
							<li class="nav-item menu-open">
								<a href="<?php echo BASE_URL; ?>dashboard.php" class="nav-link active">
									<i class="nav-icon bi bi-speedometer"></i>
									<p>Dashboard</p>
								</a>
							</li>

							<li class="nav-item">
								<a href="#" class="nav-link">
								<i class="bi bi-ui-checks"></i>
									<p>
										CRUD Templates
										<i class="nav-arrow bi bi-chevron-right"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>templates/template_blank_page.php" class="nav-link">
										<i class="bi bi-link-45deg"></i>
											<p>Blank  Page Template</p>
										</a>
									</li>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>templates/template_create_page.php" class="nav-link">
										<i class="bi bi-link-45deg"></i>
											<p>Create Page Template</p>
										</a>
									</li>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>templates/template_read_page.php" class="nav-link">
										<i class="bi bi-link-45deg"></i>
											<p>View Page Template</p>
										</a>
									</li>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>templates/template_update_page.php" class="nav-link">
										<i class="bi bi-link-45deg"></i>
											<p>Update Page Template</p>
										</a>
									</li>
									<li class="nav-item">
										<a href="<?php echo BASE_URL; ?>templates/template_delete_page.php" class="nav-link">
										<i class="bi bi-link-45deg"></i>
											<p>Delete Page Template</p>
										</a>
									</li>
								</ul>
							</li>

							<li class="nav-item">
								<a href="#" class="nav-link">
								<i class="bi bi-diagram-3-fill"></i>
									<p>
										Add New Links Below 👇
									</p>
								</a>
							</li>

							<!-- Copy and Paste this one to create new links -->
							<!-- 👇 -->
							<li class="nav-item">
								<a href="<?php echo BASE_URL; ?>book_list.php" class="nav-link">
									<i class="bi bi-link-45deg"></i>
									<p>Books List</p>
								</a>
							</li>

							<li class="nav-item">
								<a href="<?php echo BASE_URL; ?>category_list.php" class="nav-link">
									<i class="bi bi-link-45deg"></i>
									<p>Category List</p>
								</a>
							</li>
							
























						</ul>
						<!--end::Sidebar Menu-->
					</nav>
				</div>
				<!--end::Sidebar Wrapper-->
			</aside>
			<!--end::Sidebar-->