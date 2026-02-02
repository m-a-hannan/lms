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
  const fallbackStatusUrl = deployContainer.dataset.fallbackStatusUrl || statusUrl;
  const fallbackShaUrl = deployContainer.dataset.fallbackShaUrl || shaUrl;

  const fetchDeploy = (statusPath, shaPath) => Promise.all([
    fetch(statusPath, { cache: 'no-store' }).then((res) => {
      if (!res.ok) {
        throw new Error('status fetch failed');
      }
      return res.json();
    }),
    fetch(shaPath, { cache: 'no-store' }).then((res) => {
      if (!res.ok) {
        throw new Error('sha fetch failed');
      }
      return res.text();
    }),
  ]);

  fetchDeploy(statusUrl, shaUrl)
    .catch(() => fetchDeploy(fallbackStatusUrl, fallbackShaUrl))
    .then(([status, shaText]) => {
      deployContainer.textContent = '';
      const sha = shaText.trim();
      const rows = [
        `Last deploy: ${status.time || 'unknown'}`,
        `SHA: ${sha || 'unknown'}`,
        `DB: ${status.dump || 'unknown'}`,
        `Result: ${status.result || 'unknown'}`,
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
