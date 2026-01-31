document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('bulkImportForm');
  const progressWrap = document.getElementById('uploadProgressWrap');
  const progressBar = document.getElementById('uploadProgressBar');
  const statusEl = document.getElementById('uploadStatus');
  const startBtn = document.getElementById('startImportBtn');

  if (form && progressWrap && progressBar && statusEl && startBtn) {
    form.addEventListener('submit', (event) => {
      event.preventDefault();
      const formData = new FormData(form);
      const xhr = new XMLHttpRequest();

      progressWrap.style.display = 'block';
      progressBar.style.width = '0%';
      statusEl.textContent = 'Uploading...';
      startBtn.disabled = true;

      xhr.upload.addEventListener('progress', (e) => {
        if (!e.lengthComputable) return;
        const percent = Math.round((e.loaded / e.total) * 100);
        progressBar.style.width = `${percent}%`;
        statusEl.textContent = `Uploading... ${percent}%`;
      });

      xhr.onreadystatechange = () => {
        if (xhr.readyState !== 4) return;
        startBtn.disabled = false;
        if (xhr.status === 200) {
          try {
            const payload = JSON.parse(xhr.responseText);
            if (payload.redirect) {
              window.location.href = payload.redirect;
              return;
            }
          } catch (err) {
            // fallthrough
          }
          statusEl.textContent = 'Import completed.';
        } else {
          statusEl.textContent = 'Import failed. Please try again.';
        }
      };

      xhr.open('POST', form.action, true);
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      xhr.send(formData);
    });
  }

  const downloadErrorsBtn = document.getElementById('downloadErrorReport');
  const errorsEl = document.getElementById('bulkImportErrors');
  if (downloadErrorsBtn && errorsEl) {
    const errorsRaw = errorsEl.dataset.errors || '[]';
    let errors = [];
    try {
      errors = JSON.parse(errorsRaw);
    } catch (err) {
      errors = [];
    }

    if (errors.length) {
      downloadErrorsBtn.addEventListener('click', () => {
        const rows = [['row', 'field', 'message']];
        errors.forEach((err) => {
          rows.push([err.row ?? '', err.field ?? '', err.message ?? '']);
        });
        const csv = rows
          .map((line) =>
            line
              .map((cell) => {
                const text = String(cell ?? '');
                return `"${text.replace(/"/g, '""')}"`;
              })
              .join(',')
          )
          .join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'bulk-import-errors.csv';
        link.click();
        URL.revokeObjectURL(url);
      });
    }
  }
});
