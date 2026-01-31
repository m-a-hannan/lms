<?php
$shaFile  = ROOT_PATH . '/DEPLOYED_SHA.txt';
$timeFile = ROOT_PATH . '/DEPLOYED_AT.txt';

$sha  = file_exists($shaFile)  ? trim(file_get_contents($shaFile))  : 'unknown';
$time = file_exists($timeFile) ? trim(file_get_contents($timeFile)) : 'unknown';

$shortSha = $sha !== 'unknown' ? substr($sha, 0, 7) : $sha;
?>
<!--begin::Footer-->
<footer class="app-footer">
	<!--begin::To the end-->
	<div class="float-end d-none d-sm-inline text-muted">
		Build <?= htmlspecialchars($shortSha) ?>
		<?php if ($time !== 'unknown'): ?>
		· <?= htmlspecialchars($time) ?>
		<?php endif; ?>
	</div>
	<!--end::To the end-->

	<!--begin::Copyright-->
	<strong>
		Copyright &copy; 2026&nbsp;
		<a href="#" class="text-decoration-none">PGD-ICT #49 | Group A</a>.
	</strong>
	All rights reserved.
	<!--end::Copyright-->
</footer>
<!--end::Footer-->

<div class="modal fade report-config-modal modal-top-center" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title d-flex align-items-center gap-2" id="confirmDeleteTitle">
					<i class="bi bi-trash"></i>
					Confirm delete
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p class="mb-0" id="confirmDeleteMessage">Are you sure you want to delete this item?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-glass" data-bs-dismiss="modal">Cancel</button>
				<a class="btn btn-glass btn-reset" href="#" data-delete-mode="soft" id="confirmDeleteSoftButton">Hide</a>
				<a class="btn btn-glass btn-disable" href="#" data-delete-mode="hard" id="confirmDeleteHardButton">Delete</a>
			</div>
		</div>
	</div>
</div>
