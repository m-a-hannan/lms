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
const binocularsIcon = searchBox ? searchBox.querySelector('.bi-binoculars-fill') : null;
const micIcon = searchBox ? searchBox.querySelector('.bi-mic-fill') : null;
const searchInput = searchBox ? searchBox.querySelector('input') : null;
const searchSuggest = document.getElementById('searchSuggest');
const suggestUrl = searchBox ? searchBox.dataset.suggestUrl : '';

if (searchBox) {
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

  const bindExpandTrigger = (element) => {
    if (!element) {
      return;
    }
    element.addEventListener('pointerdown', expandSearch);
    element.addEventListener('click', expandSearch);
  };

  bindExpandTrigger(binocularsIcon);
  bindExpandTrigger(micIcon);
  bindExpandTrigger(searchBox);

  if (searchInput) {
    searchInput.addEventListener('focus', expandSearch);
    searchInput.addEventListener('blur', collapseSearch);
  }
}

let suggestTimer;

const clearSuggestions = () => {
  if (searchSuggest) {
    searchSuggest.innerHTML = '';
    searchSuggest.classList.remove('show');
  }
};

const renderSuggestions = (items) => {
  if (!searchSuggest) {
    return;
  }
  if (!items.length) {
    clearSuggestions();
    return;
  }

  searchSuggest.innerHTML = items
    .map((item) => {
      const title = item.title || 'Untitled';
      const author = item.author || 'Unknown author';
      const safeTitle = title.replace(/"/g, '&quot;');
      return `
        <button type="button" class="suggest-item" data-value="${safeTitle}">
          <span class="suggest-title">${title}</span>
          <span class="suggest-meta">${author}</span>
        </button>
      `;
    })
    .join('');
  searchSuggest.classList.add('show');
};

const fetchSuggestions = async (term) => {
  if (!suggestUrl) {
    return;
  }
  try {
    const url = `${suggestUrl}?q=${encodeURIComponent(term)}`;
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) {
      clearSuggestions();
      return;
    }
    const data = await res.json();
    renderSuggestions(Array.isArray(data) ? data : []);
  } catch (err) {
    clearSuggestions();
  }
};

if (searchInput) {
  searchInput.addEventListener('input', () => {
    const term = searchInput.value.trim();
    if (term.length < 2) {
      clearSuggestions();
      return;
    }
    clearTimeout(suggestTimer);
    suggestTimer = setTimeout(() => fetchSuggestions(term), 250);
  });

  searchInput.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      clearSuggestions();
      searchInput.blur();
    }
  });
}

document.addEventListener('click', (event) => {
  if (!searchSuggest || !searchBox) {
    return;
  }
  if (!searchBox.contains(event.target) && !searchSuggest.contains(event.target)) {
    clearSuggestions();
  }
});

if (searchSuggest && searchInput && searchBox) {
  searchSuggest.addEventListener('click', (event) => {
    const target = event.target.closest('.suggest-item');
    if (!target) {
      return;
    }
    const value = target.getAttribute('data-value') || '';
    searchInput.value = value;
    clearSuggestions();
    searchBox.submit();
  });
}

const categoryModal = document.getElementById('categoryFilterModal');
if (categoryModal) {
  const categoryForm = categoryModal.querySelector('form');
  const categoryChecks = Array.from(categoryModal.querySelectorAll('.category-check'));
  const categoryLimitSelect = categoryModal.querySelector('#categoryLimit');

  const setAllCategories = (checked) => {
    categoryChecks.forEach((input) => {
      input.checked = checked;
    });
  };

  const resetCategoryFilters = () => {
    setAllCategories(false);
    if (categoryLimitSelect) {
      categoryLimitSelect.value = '10';
    }
    if (categoryForm) {
      categoryForm.submit();
    }
  };

  categoryModal.addEventListener('click', (event) => {
    const actionButton = event.target.closest('[data-category-action]');
    if (!actionButton) {
      return;
    }
    const action = actionButton.getAttribute('data-category-action');
    if (action === 'enable-all') {
      setAllCategories(true);
    } else if (action === 'disable-all') {
      setAllCategories(false);
    } else if (action === 'reset') {
      resetCategoryFilters();
    }
  });
}
