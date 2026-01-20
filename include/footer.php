<?php
$shaFile  = __DIR__ . '/../DEPLOYED_SHA.txt';
$timeFile = __DIR__ . '/../DEPLOYED_AT.txt';

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
		Copyright &copy; 2014–2025&nbsp;
		<a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
	</strong>
	All rights reserved.
	<!--end::Copyright-->
</footer>
<!--end::Footer-->