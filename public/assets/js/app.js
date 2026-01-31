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
  if (alerts.length) {
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
  }

  const deleteModal = document.getElementById('confirmDeleteModal');
  const deleteTitle = document.getElementById('confirmDeleteTitle');
  const deleteMessage = document.getElementById('confirmDeleteMessage');
  const deleteSoftButton = document.getElementById('confirmDeleteSoftButton');
  const deleteHardButton = document.getElementById('confirmDeleteHardButton');
  let pendingForm = null;

  const appendMode = (url, mode) => {
    if (!url) {
      return '';
    }
    try {
      const parsed = new URL(url, window.location.origin);
      parsed.searchParams.set('mode', mode);
      return `${parsed.pathname}${parsed.search}${parsed.hash}`;
    } catch (error) {
      const separator = url.includes('?') ? '&' : '?';
      return `${url}${separator}mode=${encodeURIComponent(mode)}`;
    }
  };

  const deriveLabelFromUrl = (url) => {
    if (!url) {
      return '';
    }
    const file = url.split('?')[0].split('/').pop() || '';
    const match = file.match(/^delete_(.+)\.php$/i);
    if (match) {
      return match[1].replace(/_/g, ' ');
    }
    return '';
  };

  const deriveIdFromUrl = (url) => {
    if (!url) {
      return '';
    }
    try {
      const parsed = new URL(url, window.location.origin);
      return parsed.searchParams.get('id') || parsed.searchParams.get('delete') || '';
    } catch (error) {
      return '';
    }
  };

  const buildTitle = ({ title, label }) => {
    if (title) {
      return title;
    }
    if (label) {
      return `Delete ${label}`;
    }
    return 'Confirm delete';
  };

  const buildMessage = ({ message, label, id }) => {
    if (message) {
      return message;
    }
    if (label && id) {
      return `Delete ${label} #${id}?`;
    }
    if (label) {
      return `Delete ${label}?`;
    }
    return 'Are you sure you want to delete this item?';
  };

  const showDeleteModal = ({ title, message, label, id, url, form }) => {
    if (!deleteModal || !deleteMessage || !deleteSoftButton || !deleteHardButton) {
      return;
    }

    pendingForm = form || null;
    if (deleteTitle) {
      deleteTitle.textContent = buildTitle({ title, label });
    }
    deleteMessage.textContent = buildMessage({ message, label, id });

    if (pendingForm) {
      deleteSoftButton.href = '#';
      deleteHardButton.href = '#';
    } else {
      const softUrl = appendMode(url, 'soft');
      const hardUrl = appendMode(url, 'hard');
      deleteSoftButton.href = softUrl || '#';
      deleteHardButton.href = hardUrl || '#';
    }

    if (window.bootstrap?.Modal) {
      window.bootstrap.Modal.getOrCreateInstance(deleteModal).show();
    } else if (url) {
      window.location.href = appendMode(url, 'hard');
    } else if (form) {
      const modeInput = form.querySelector('input[name="mode"]');
      if (modeInput) {
        modeInput.value = 'hard';
      }
      form.submit();
    }
  };

  document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-confirm-delete]');
    if (!trigger) {
      return;
    }

    const tagName = trigger.tagName.toLowerCase();
    if (tagName === 'form') {
      return;
    }

    event.preventDefault();

    const url = trigger.getAttribute('href') || trigger.dataset.deleteUrl || '';
    const title = trigger.dataset.deleteTitle || '';
    const message = trigger.dataset.deleteMessage || '';
    const label = trigger.dataset.deleteLabel || deriveLabelFromUrl(url);
    const id = trigger.dataset.deleteId || deriveIdFromUrl(url);
    pendingForm = null;
    showDeleteModal({ title, message, label, id, url });
  });

  document.addEventListener('submit', (event) => {
    const form = event.target.closest('form[data-confirm-delete]');
    if (!form) {
      return;
    }

    event.preventDefault();
    const title = form.dataset.deleteTitle || '';
    const message = form.dataset.deleteMessage || '';
    const label = form.dataset.deleteLabel || '';
    const id = form.dataset.deleteId || '';
    showDeleteModal({ title, message, label, id, form });
  });

  const submitPendingForm = (mode) => {
    if (!pendingForm) {
      return false;
    }
    let input = pendingForm.querySelector('input[name="mode"]');
    if (!input) {
      input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'mode';
      pendingForm.appendChild(input);
    }
    input.value = mode;
    const form = pendingForm;
    pendingForm = null;
    form.submit();
    return true;
  };

  if (deleteSoftButton) {
    deleteSoftButton.addEventListener('click', (event) => {
      if (submitPendingForm('soft')) {
        event.preventDefault();
      }
    });
  }

  if (deleteHardButton) {
    deleteHardButton.addEventListener('click', (event) => {
      if (submitPendingForm('hard')) {
        event.preventDefault();
      }
    });
  }
});
