		<!--begin::Script-->
		<!--begin::Third Party Plugin(OverlayScrollbars)-->
		<script
			src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
			crossorigin="anonymous"
		></script>
		<!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
		<script
			src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
			crossorigin="anonymous"
		></script>
		<!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
		<script
			src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
			crossorigin="anonymous"
		></script>
		<!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
		<script src="<?php echo BASE_URL; ?>js/adminlte.js"></script>
		<!--end::Required Plugin(AdminLTE)-->
		<script src="<?php echo BASE_URL; ?>js/custom.js"></script>
		<!--begin::OverlayScrollbars Configure-->
		<script>
			const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
			const Default = {
				scrollbarTheme: 'os-theme-light',
				scrollbarAutoHide: 'leave',
				scrollbarClickScroll: true,
			};
			document.addEventListener('DOMContentLoaded', function () {
				const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
				if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
					OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
						scrollbars: {
							theme: Default.scrollbarTheme,
							autoHide: Default.scrollbarAutoHide,
							clickScroll: Default.scrollbarClickScroll,
						},
					});
				}
			});
		</script>
		<!--end::OverlayScrollbars Configure-->
		<!-- OPTIONAL SCRIPTS -->
		<!-- apexcharts -->
		<script
			src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
			integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8="
			crossorigin="anonymous"
		></script>
		<script>
			document.addEventListener('DOMContentLoaded', () => {
				const alerts = Array.from(document.querySelectorAll('.app-content .alert'));
				if (!alerts.length) {
					return;
				}

				let container = document.getElementById('app-toast-container');
				if (!container) {
					container = document.createElement('div');
					container.id = 'app-toast-container';
					container.className = 'toast-container position-fixed top-0 end-0 p-3';
					document.body.appendChild(container);
				}

				alerts.forEach((alert) => {
					const typeMatch = Array.from(alert.classList).find((cls) => cls.startsWith('alert-'));
					const type = typeMatch ? typeMatch.replace('alert-', '') : 'secondary';

					const toast = document.createElement('div');
					toast.className = `toast align-items-center text-bg-${type} border-0`;
					toast.setAttribute('role', 'alert');
					toast.setAttribute('aria-live', 'assertive');
					toast.setAttribute('aria-atomic', 'true');
					toast.innerHTML = `
						<div class="d-flex">
							<div class="toast-body">${alert.innerHTML}</div>
							<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
						</div>
					`;

					container.appendChild(toast);
					alert.remove();

					if (window.bootstrap?.Toast) {
						const toastInstance = bootstrap.Toast.getOrCreateInstance(toast, { delay: 5000 });
						toastInstance.show();
					}
				});
			});
		</script>
		<!--end::Script-->
	</body>
	<!--end::Body-->
</html>
