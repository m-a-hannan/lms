document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.perm-deny').forEach((checkbox) => {
    checkbox.addEventListener('change', () => {
      const row = checkbox.closest('tr');
      if (!row) {
        return;
      }
      if (checkbox.checked) {
        const readBox = row.querySelector('.perm-read');
        const writeBox = row.querySelector('.perm-write');
        if (readBox) {
          readBox.checked = false;
        }
        if (writeBox) {
          writeBox.checked = false;
        }
      }
    });
  });
});
