<?php
// Shared top navigation bar for book-facing pages.
?>
<!-- Top Navbar -->
<nav class="navbar navbar-dark fixed-top px-3">
	<!-- Sidebar toggle button. -->
	<button class="btn btn-icon" id="sidebarToggle">
		<i class="bi bi-list"></i>
	</button>

	<!-- Brand label. -->
	<span class="navbar-brand ms-2">LMS</span>

	<!-- Search bar with live suggestions. -->
	<div class="mx-auto search-wrap">
		<div class="search-container">
			<!-- Search form targets the results page. -->
			<form id="searchBox" class="search-box" action="<?php echo BASE_URL; ?>search_results.php" method="get" data-suggest-url="<?php echo BASE_URL; ?>actions/search_suggest.php" autocomplete="off">
				<i class="bi bi-binoculars-fill"></i>
				<input type="text" name="q" id="searchInput" placeholder="Type book or author name">
				<i class="bi bi-mic-fill"></i>
			</form>
			<!-- Suggestion dropdown container. -->
			<div id="searchSuggest" class="search-suggest"></div>
		</div>
	</div>

	<!-- User actions: logout and theme toggle. -->
	<div class="d-flex align-items-center gap-2">
		<a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-outline-light btn-sm">Logout</a>
		<button class="btn btn-icon" id="themeToggle">
			<i class="bi bi-moon"></i>
		</button>
	</div>
</nav>
