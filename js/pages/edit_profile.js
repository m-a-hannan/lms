document.addEventListener('DOMContentLoaded', () => {
  const profileInput = document.getElementById('profileInput');
  const profilePreview = document.getElementById('profilePreview');

  if (profileInput && profilePreview) {
    profileInput.addEventListener('change', () => {
      const file = profileInput.files && profileInput.files[0];
      if (!file) {
        return;
      }
      const reader = new FileReader();
      reader.onload = (event) => {
        profilePreview.src = event.target.result;
      };
      reader.readAsDataURL(file);
    });
  }
});
