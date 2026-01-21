<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <title>Booklore – Demo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/home.css">
</head>

<body class="app-body">

<!-- Top Navbar -->
<nav class="navbar navbar-dark fixed-top px-3">
  <button class="btn btn-icon" id="sidebarToggle">
    <i class="bi bi-list"></i>
  </button>

  <span class="navbar-brand ms-2">Booklore</span>

  <div class="ms-auto d-flex align-items-center gap-2">
    <input type="text" class="form-control form-control-sm search-input"
           placeholder="Title, Author, Genre…" disabled>
    <button class="btn btn-icon" id="themeToggle">
      <i class="bi bi-moon"></i>
    </button>
  </div>
</nav>

<!-- Layout -->
<div class="layout">

  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar collapsed">
    <div class="sidebar-section">
      <small>HOME</small>
      <a class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a><i class="bi bi-book"></i> All Books</a>
    </div>

    <div class="sidebar-section">
      <small>LIBRARIES</small>
      <a><i class="bi bi-journal-bookmark"></i> Novels</a>
      <a><i class="bi bi-cpu"></i> Technology</a>
      <a><i class="bi bi-brush"></i> Comics</a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="content">

    <!-- Section -->
    <section>
      <h5>Continue Reading</h5>
      <div class="book-row">
        <div class="book-card">
          <img src="assets/img/book1.jpg">
          <div class="book-overlay">
            <i class="bi bi-play-fill"></i>
          </div>
          <div class="progress-bar"></div>
        </div>
        <div class="book-card">
          <img src="assets/img/book2.jpg">
          <div class="book-overlay">
            <i class="bi bi-play-fill"></i>
          </div>
          <div class="progress-bar"></div>
        </div>
      </div>
    </section>

    <section>
      <h5>Recently Added</h5>
      <div class="book-row">
        <div class="book-card">
          <img src="assets/img/book3.jpg">
          <div class="book-overlay">
            <i class="bi bi-eye"></i>
          </div>
        </div>
        <div class="book-card">
          <img src="assets/img/book4.jpg">
          <div class="book-overlay">
            <i class="bi bi-eye"></i>
          </div>
        </div>
      </div>
    </section>

    <section>
      <h5>Ebook</h5>
      <div class="book-row">
        <div class="book-card">
          <img src="assets/img/book3.jpg">
          <div class="book-overlay">
            <i class="bi bi-eye"></i>
          </div>
        </div>
        <div class="book-card">
          <img src="assets/img/book4.jpg">
          <div class="book-overlay">
            <i class="bi bi-eye"></i>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<!-- App JS -->
<script src="assets/js/home.js"></script>
</body>
</html>
