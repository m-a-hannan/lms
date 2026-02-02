<?php
require_once dirname(__DIR__, 2) . '/includes/config.php'; foreach ($books as $book): ?>
  <div class="book-card">
    <img src="<?= $book['cover'] ?>">
  </div>
<?php endforeach; ?>