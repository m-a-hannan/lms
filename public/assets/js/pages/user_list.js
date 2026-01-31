document.addEventListener('DOMContentLoaded', () => {
  const toastRoot = document.getElementById('userListToasts');
  if (!toastRoot || !window.bootstrap?.Toast) {
    return;
  }
  toastRoot.querySelectorAll('.toast').forEach((toastEl) => {
    window.bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 4000 }).show();
  });
});
