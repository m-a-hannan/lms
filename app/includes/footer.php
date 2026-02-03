<?php
// Locate deployment metadata files for footer display.
$shaFile  = ROOT_PATH . '/DEPLOYED_SHA.txt';
$timeFile = ROOT_PATH . '/DEPLOYED_AT.txt';

// Read deployment values with safe fallbacks.
$sha  = file_exists($shaFile)  ? trim(file_get_contents($shaFile))  : 'unknown';
$time = file_exists($timeFile) ? trim(file_get_contents($timeFile)) : 'unknown';

// Shorten the SHA for a compact footer label.
$shortSha = $sha !== 'unknown' ? substr($sha, 0, 7) : $sha;
?>
<!--begin::Footer-->
<footer class="app-footer">
	<!--begin::To the end-->
	<div class="float-end d-none d-sm-inline text-muted">
		Build <?= htmlspecialchars($shortSha) ?>
		<?php // Only show deploy time when available. ?>
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
