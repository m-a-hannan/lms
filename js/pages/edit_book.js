document.addEventListener('DOMContentLoaded', () => {
  const bookTypeSelect = document.getElementById('bookTypeSelect');
  const ebookFormatGroup = document.getElementById('ebookFormatGroup');
  const ebookFormatSelect = document.getElementById('ebookFormatSelect');
  const ebookFileGroup = document.getElementById('ebookFileGroup');
  const ebookFileInput = document.getElementById('ebookFileInput');
  const addCopiesInput = document.getElementById('addCopiesInput');

  const updateBookTypeFields = () => {
    const isEbook = bookTypeSelect && bookTypeSelect.value === 'ebook';
    if (ebookFormatGroup) {
      ebookFormatGroup.classList.toggle('d-none', !isEbook);
    }
    if (ebookFormatSelect) {
      ebookFormatSelect.required = isEbook;
      if (!isEbook) {
        ebookFormatSelect.value = '';
      }
    }
    if (ebookFileGroup) {
      ebookFileGroup.classList.toggle('d-none', !isEbook);
    }
    if (ebookFileInput) {
      ebookFileInput.required = false;
      if (!isEbook) {
        ebookFileInput.value = '';
      }
    }
    if (addCopiesInput) {
      addCopiesInput.disabled = isEbook;
      if (isEbook) {
        addCopiesInput.value = 0;
      }
    }
  };

  if (bookTypeSelect) {
    bookTypeSelect.addEventListener('change', updateBookTypeFields);
    updateBookTypeFields();
  }
});
