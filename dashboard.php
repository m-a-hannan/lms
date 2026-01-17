<?php include('includes/header_resources.php') ?>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
	<!--begin::App Wrapper-->
	<div class="app-wrapper">
		<!--begin::Header-->
		<?php include('includes/dash_header.php') ?>
		<!--end::Header-->
		<!--begin::Sidebar-->
		<?php include('dash_sidebar.php'); ?>
		<!--end::Sidebar-->
		<!--begin::App Main-->
		<main class="app-main">
			<!--begin::App Content Header-->
			<div class="app-content-header">
				<!--begin::Container-->
				<div class="container-fluid">
					<!--begin::Row-->
					<div class="row">
						<div class="col-sm-12">
							<h3 class="mb-0">Dashboard</h3>
						</div>
					</div>
					<!--end::Row-->
				</div>
				<!--end::Container-->
			</div>
			<!--end::App Content-->
		</main>
		<!--end::App Main-->

		<?php include('includes/footer.php') ?>
		<?php include('includes/footer_resources.php') ?>