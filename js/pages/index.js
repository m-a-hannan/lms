document.addEventListener('DOMContentLoaded', () => {
  const addBtn = document.querySelector('.add-btn');
  if (addBtn) {
    addBtn.addEventListener('click', () => {
      alert('Add new book feature coming soon!');
    });
  }

  document.querySelectorAll('.dropdown-menu .dropdown-item').forEach((item) => {
    item.addEventListener('click', () => {
      const dropdown = item.closest('.dropdown');
      if (!dropdown) {
        return;
      }
      const toggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
      if (toggle) {
        toggle.click();
      }
    });
  });
});
