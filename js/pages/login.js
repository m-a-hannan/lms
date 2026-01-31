document.addEventListener('DOMContentLoaded', () => {
  const body = document.body;
  const toastEl = document.getElementById('logoutToast');
  if (toastEl && window.bootstrap?.Toast) {
    window.bootstrap.Toast.getOrCreateInstance(toastEl).show();
  }

  const resetForm = document.getElementById('forgotPasswordForm');
  if (!resetForm) {
    return;
  }

  const baseUrl = body?.dataset.baseUrl || '/';

  const showToast = (message, variant = 'success') => {
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
    toastContainer.innerHTML = `
      <div class="toast text-bg-${variant} border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">${message}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    `;
    document.body.appendChild(toastContainer);
    const toast = toastContainer.querySelector('.toast');
    if (toast && window.bootstrap?.Toast) {
      window.bootstrap.Toast.getOrCreateInstance(toast, { delay: 3500 }).show();
    }
  };

  resetForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = new FormData(resetForm);

    try {
      const response = await fetch(resetForm.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          Accept: 'application/json',
        },
      });
      const rawText = await response.text();
      let data = {};
      try {
        data = JSON.parse(rawText);
      } catch (jsonError) {
        data = { status: 'error', message: rawText || 'Invalid response from server.' };
      }

      if (data.status === 'sent') {
        const modalEl = document.getElementById('forgotPasswordModal');
        const modalInstance = modalEl && window.bootstrap?.Modal ? window.bootstrap.Modal.getInstance(modalEl) : null;
        if (modalInstance) {
          modalInstance.hide();
        }
        showToast('Password reset request submitted successfully.');
        setTimeout(() => {
          window.location.href = `${baseUrl}login.php`;
        }, 1200);
        return;
      }

      showToast(data.message || 'Unable to send request.', 'danger');
    } catch (err) {
      showToast('Unable to send request. Please try again.', 'danger');
    }
  });
});
