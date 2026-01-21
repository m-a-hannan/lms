const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('sidebarToggle');
const themeBtn = document.getElementById('themeToggle');
const html = document.documentElement;

toggleBtn.addEventListener('click', () => {
  if (window.innerWidth <= 768) {
    sidebar.classList.toggle('show');       // mobile slide
  } else {
    sidebar.classList.toggle('collapsed');  // desktop collapse
  }
});

themeBtn.addEventListener('click', () => {
  const current = html.getAttribute('data-theme');
  html.setAttribute('data-theme', current === 'dark' ? 'light' : 'dark');
});
