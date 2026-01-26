<?php include('include/header_resources.php') ?>

<?php include('include/header.php') ?>
<?php include('sidebar.php') ?>
<!--begin::App Main-->
<main class="app-main">
	<!--begin::App Content-->
	<div class="app-content">
		<!--begin::Container-->
		<div class="container-fluid">
			<!--begin::Row-->
			<div class="row">

				<div class="container py-5">
					<div class="d-flex justify-content-between align-items-center mb-4">
					<a href="dashboard.php" class="btn btn-primary btn-sm dashboard-link">Dashboard</a>
						<!-- Content will be added here-->
						 <h1>Dashboard content will be added here</h1>
						 <div id="deployStatus" class="small text-muted"></div>



					</div>
				</div>
			</div>
			<!-- row end -->
		</div>
	</div>
</main>
<!--end::App Main-->
<?php include('include/footer.php') ?>
<script>
fetch('/deploy/status.json', { cache: 'no-store' })
  .then(r => r.json())
  .then(s => {
    document.getElementById('deployStatus').innerText =
      `Last deploy: ${s.time} | SHA: ${s.sha} | DB: ${s.dump} | Result: ${s.result}`;
  })
  .catch(() => {
    document.getElementById('deployStatus').innerText = 'Deploy status unavailable';
  });
</script>
<?php include('include/footer_resources.php') ?>