document.addEventListener('DOMContentLoaded', () => {
  const sidebarWrapper = document.querySelector('.sidebar-wrapper');
  if (sidebarWrapper && window.OverlayScrollbarsGlobal?.OverlayScrollbars) {
    window.OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
      scrollbars: {
        theme: 'os-theme-light',
        autoHide: 'leave',
        clickScroll: true,
      },
    });
  }

  const alerts = Array.from(document.querySelectorAll('.app-content .alert')).filter(
    (alert) => !alert.hasAttribute('data-no-toast')
  );
  if (!alerts.length) {
    return;
  }

  let container = document.getElementById('app-toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'app-toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    document.body.appendChild(container);
  }

  alerts.forEach((alert) => {
    const typeMatch = Array.from(alert.classList).find((cls) => cls.startsWith('alert-'));
    const type = typeMatch ? typeMatch.replace('alert-', '') : 'secondary';

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${alert.innerHTML}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;

    container.appendChild(toast);
    alert.remove();

    if (window.bootstrap?.Toast) {
      const toastInstance = window.bootstrap.Toast.getOrCreateInstance(toast, { delay: 5000 });
      toastInstance.show();
    }
  });
});
