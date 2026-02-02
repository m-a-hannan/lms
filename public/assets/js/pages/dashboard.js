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

  const shaUrl = deployContainer.dataset.shaUrl || '/DEPLOYED_SHA.txt';
  const timeUrl = deployContainer.dataset.timeUrl || '/DEPLOYED_AT.txt';
  const fallbackShaUrl = deployContainer.dataset.fallbackShaUrl || shaUrl;
  const fallbackTimeUrl = deployContainer.dataset.fallbackTimeUrl || timeUrl;

  const fetchText = (url) => fetch(url, { cache: 'no-store' }).then((res) => {
    if (!res.ok) {
      throw new Error('fetch failed');
    }
    return res.text();
  });

  const fetchDeploy = (shaPath, timePath) => Promise.all([
    fetchText(shaPath),
    fetchText(timePath),
  ]);

  fetchDeploy(shaUrl, timeUrl)
    .catch(() => fetchDeploy(fallbackShaUrl, fallbackTimeUrl))
    .then(([shaText, timeText]) => {
      deployContainer.textContent = '';
      const sha = shaText.trim();
      const time = timeText.trim();
      const rows = [
        `Last deploy: ${time || 'unknown'}`,
        `SHA: ${sha || 'unknown'}`,
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
