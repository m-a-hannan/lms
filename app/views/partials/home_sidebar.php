<?php
// Shared sidebar for book-facing pages.
$sidebarCategories = $sidebarCategories ?? [];
$sidebarShowFilter = $sidebarShowFilter ?? false;
$sidebarFilterTarget = $sidebarFilterTarget ?? '#categoryFilterModal';
?>
<!-- Sidebar -->
<aside id="sidebar" class="sidebar">
	<!-- Home navigation links. -->
	<div class="sidebar-section">
		<small>HOME</small>
		<a href="<?php echo $dashboardUrl; ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
		<a class="active"><i class="bi bi-book"></i> All Books</a>
	</div>

	<!-- Category shortcuts. -->
	<div class="sidebar-section">
		<div class="d-flex align-items-center justify-content-between">
			<small>CATEGORIES</small>
			<?php if ($sidebarShowFilter): ?>
				<!-- Category filter trigger. -->
				<button class="btn btn-icon btn-sm sidebar-filter-btn" type="button" data-bs-toggle="modal" data-bs-target="<?php echo htmlspecialchars($sidebarFilterTarget); ?>" aria-label="Filter categories">
					<i class="bi bi-funnel"></i>
				</button>
			<?php endif; ?>
		</div>
		<?php if ($sidebarCategories): ?>
			<!-- Render category links based on the current filter. -->
			<?php foreach ($sidebarCategories as $category): ?>
				<?php
					// Prepare sidebar link data for the category.
					$categoryId = (int) ($category['id'] ?? 0);
					$categoryName = $category['name'] ?? 'Category';
					$bookCount = (int) ($category['book_count'] ?? 0);
				?>
				<a href="<?php echo BASE_URL; ?>category_view.php?category_id=<?php echo $categoryId; ?>">
					<i class="bi bi-tag"></i> <?php echo htmlspecialchars($categoryName); ?> (<?php echo $bookCount; ?>)
				</a>
			<?php endforeach; ?>
		<?php else: ?>
			<span class="text-muted small">No categories available.</span>
		<?php endif; ?>
	</div>
</aside>
