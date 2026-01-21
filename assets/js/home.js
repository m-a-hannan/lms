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

const searchBox = document.getElementById('searchBox');
const googleIcon = document.getElementById('googleIcon');

if (searchBox) {
  const searchInput = searchBox.querySelector('input');

  const expandSearch = () => {
    searchBox.classList.add('active');
    if (searchInput) {
      searchInput.focus();
    }
  };

  const collapseSearch = () => {
    if (searchInput && searchInput.value.trim() !== '') {
      return;
    }
    searchBox.classList.remove('active');
  };

  if (googleIcon) {
    googleIcon.addEventListener('click', expandSearch);
  }

  if (searchInput) {
    searchInput.addEventListener('focus', expandSearch);
    searchInput.addEventListener('blur', collapseSearch);
  }
}
