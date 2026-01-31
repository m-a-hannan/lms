document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-auto-toast]').forEach((container) => {
    container.querySelectorAll('.toast').forEach((toastEl) => {
      if (window.bootstrap?.Toast) {
        window.bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 4000 }).show();
      }
    });
  });

  const deployContainer = document.getElementById('deployStatus');
  if (!deployContainer) {
    return;
  }

  const statusUrl = deployContainer.dataset.statusUrl || '/deploy/status.json';
  const shaUrl = deployContainer.dataset.shaUrl || '/DEPLOYED_SHA.txt';

  Promise.all([
    fetch(statusUrl, { cache: 'no-store' }).then((res) => res.json()),
    fetch(shaUrl, { cache: 'no-store' }).then((res) => res.text()),
  ])
    .then(([status, shaText]) => {
      deployContainer.textContent = '';
      const sha = shaText.trim();
      const rows = [
        `Last deploy: ${status.time}`,
        `SHA: ${sha}`,
        `DB: ${status.dump}`,
        `Result: ${status.result}`,
      ];
      rows.forEach((text) => {
        const div = document.createElement('div');
        div.textContent = text;
        deployContainer.appendChild(div);
      });
    })
    .catch(() => {
      deployContainer.textContent = 'Deploy status unavailable';
    });
});
