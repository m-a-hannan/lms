document.addEventListener('DOMContentLoaded', () => {
  const toggles = document.querySelectorAll('.password-toggle');
  if (!toggles.length) {
    return;
  }

  const resolveInput = (toggle) => {
    const targetId = toggle.getAttribute('data-target') || toggle.getAttribute('data-toggle-target');
    if (targetId) {
      return document.getElementById(targetId);
    }
    const group = toggle.closest('.password-toggle-group');
    if (group) {
      return group.querySelector('input');
    }
    const wrapper = toggle.closest('.inputbox') || toggle.closest('.mb-3') || toggle.parentElement;
    return wrapper ? wrapper.querySelector('input') : null;
  };

  const resolveIcon = (toggle) => {
    if (toggle.tagName === 'I') {
      return toggle;
    }
    return toggle.querySelector('i');
  };

  const togglePassword = (toggle) => {
    const input = resolveInput(toggle);
    if (!input) {
      return;
    }
    const icon = resolveIcon(toggle);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    if (icon) {
      icon.classList.remove(isHidden ? 'bi-lock' : 'bi-unlock');
      icon.classList.add(isHidden ? 'bi-unlock' : 'bi-lock');
    }
    toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
  };

  toggles.forEach((toggle) => {
    toggle.addEventListener('click', () => togglePassword(toggle));
    toggle.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        togglePassword(toggle);
      }
    });
  });
});
