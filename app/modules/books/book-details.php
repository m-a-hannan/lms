<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book Details</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/book-details.css">
</head>
<body class="text-light p-4">
<!-- Static book details layout (demo markup) -->

<div class="book-details-card">

  <!-- Header -->
  <div class="book-details-header">
    <div class="d-flex align-items-center gap-3">
      <span class="text-success fw-semibold">
        <i class="bi bi-journal-text"></i> Book Details
      </span>
      <span class="text-muted">
        <i class="bi bi-pencil"></i> Edit Metadata
      </span>
      <span class="text-muted">
        <i class="bi bi-search"></i> Search Metadata
      </span>
    </div>
  </div>

  <!-- Body -->
  <div class="book-details-body row g-4">

    <!-- Cover -->
    <div class="col-md-3 text-center">
      <img src="assets/img/book-cover.jpg" class="book-cover" alt="Book cover">
    </div>

    <!-- Metadata -->
    <div class="col-md-9">

      <div class="d-flex align-items-center gap-2">
        <h2 class="mb-0">Passage to anywhere</h2>
        <i class="bi bi-unlock text-success fs-5"></i>
      </div>

      <a href="#" class="author-name">Sam Merwin Jr.</a>

      <!-- Rating -->
      <div class="rating-row mt-2">
        <i class="bi bi-hand-thumbs-up-fill text-warning"></i>
        <div class="stars">
          <i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i>
          <i class="bi bi-star"></i><i class="bi bi-star"></i>
          <i class="bi bi-star"></i><i class="bi bi-star"></i>
          <i class="bi bi-star"></i><i class="bi bi-star"></i>
        </div>
        <i class="bi bi-arrow-clockwise text-warning"></i>
      </div>

      <!-- Info Grid -->
      <div class="row mt-4 small">
        <div class="col-md-6">
          <div><strong>Library:</strong> <span class="text-success">Novels</span></div>
          <div><strong>Published:</strong> -</div>
          <div><strong>File Type:</strong> <span class="badge bg-primary">MOBI</span></div>
          <div><strong>Metadata Match:</strong> <span class="badge bg-success">24%</span></div>
          <div><strong>Read Status:</strong> <span class="badge bg-secondary">UNSET</span> <i class="bi bi-pencil"></i></div>
          <div><strong>File Size:</strong> <span class="text-success">0.58 MB</span></div>
          <div><strong>File Path:</strong> <i class="bi bi-eye"></i></div>
        </div>

        <div class="col-md-6">
          <div><strong>Publisher:</strong> -</div>
          <div><strong>Language:</strong> <span class="text-success">en</span></div>
          <div><strong>BookLore Progress:</strong> <span class="badge bg-secondary">N/A</span></div>
          <div><strong>ISBN:</strong> -</div>
          <div><strong>Page Count:</strong> -</div>
        </div>
      </div>

      <!-- Actions -->
      <div class="book-actions mt-4">
        <button class="btn btn-outline-success">
          <i class="bi bi-book"></i> Read
        </button>
        <button class="btn btn-outline-primary">
          <i class="bi bi-folder"></i> Shelf
        </button>
        <button class="btn btn-outline-success">
          <i class="bi bi-download"></i> Download
        </button>
        <div class="btn-group">
          <button class="btn btn-outline-warning">
            <i class="bi bi-lightning"></i> Fetch
          </button>
          <button class="btn btn-outline-warning dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="#">Fetch Cover</a></li>
            <li><a class="dropdown-item" href="#">Fetch Metadata</a></li>
          </ul>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Bootstrap JS for dropdowns and components -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
