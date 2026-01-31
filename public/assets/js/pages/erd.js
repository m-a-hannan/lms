document.addEventListener('DOMContentLoaded', () => {
  const addBtn = document.querySelector('.add-btn');
  if (addBtn) {
    addBtn.addEventListener('click', () => {
      alert('Add new book feature coming soon!');
    });
  }

  const viewer = document.getElementById('erd-viewer');
  const image = document.getElementById('erd-image');
  const zoomIn = document.getElementById('erd-zoom-in');
  const zoomOut = document.getElementById('erd-zoom-out');
  const zoomReset = document.getElementById('erd-zoom-reset');
  const fullscreenBtn = document.getElementById('erd-fullscreen');

  if (!viewer || !image || !zoomIn || !zoomOut || !zoomReset || !fullscreenBtn) {
    return;
  }

  let scale = 1;

  function applyZoom(nextScale) {
    scale = Math.min(4, Math.max(0.25, nextScale));
    image.style.transform = `scale(${scale})`;
  }

  zoomIn.addEventListener('click', () => applyZoom(scale + 0.1));
  zoomOut.addEventListener('click', () => applyZoom(scale - 0.1));
  zoomReset.addEventListener('click', () => applyZoom(1));
  fullscreenBtn.addEventListener('click', () => {
    if (!document.fullscreenElement) {
      viewer.requestFullscreen();
    } else {
      document.exitFullscreen();
    }
  });

  document.addEventListener('fullscreenchange', () => {
    const isFullscreen = document.fullscreenElement === viewer;
    fullscreenBtn.textContent = isFullscreen ? 'Exit Fullscreen' : 'Fullscreen';
  });

  viewer.addEventListener(
    'wheel',
    (event) => {
      if (!event.ctrlKey) {
        return;
      }
      event.preventDefault();
      const direction = event.deltaY > 0 ? -0.1 : 0.1;
      applyZoom(scale + direction);
    },
    { passive: false }
  );

  let isPanning = false;
  let startX = 0;
  let startY = 0;
  let startScrollLeft = 0;
  let startScrollTop = 0;

  viewer.addEventListener('mousedown', (event) => {
    isPanning = true;
    startX = event.clientX;
    startY = event.clientY;
    startScrollLeft = viewer.scrollLeft;
    startScrollTop = viewer.scrollTop;
    viewer.classList.add('panning');
  });

  window.addEventListener('mousemove', (event) => {
    if (!isPanning) {
      return;
    }
    const dx = event.clientX - startX;
    const dy = event.clientY - startY;
    viewer.scrollLeft = startScrollLeft - dx;
    viewer.scrollTop = startScrollTop - dy;
  });

  window.addEventListener('mouseup', () => {
    if (!isPanning) {
      return;
    }
    isPanning = false;
    viewer.classList.remove('panning');
  });
});
