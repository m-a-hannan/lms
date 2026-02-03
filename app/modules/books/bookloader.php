<?php
// Load base configuration for book loader snippets.
require_once dirname(__DIR__, 2) . '/includes/config.php';
// Render each book card in the provided list.
foreach ($books as $book): ?>
  <div class="book-card">
    <!-- Cover thumbnail -->
    <img src="<?= $book['cover'] ?>">
  </div>
<?php endforeach; ?>
