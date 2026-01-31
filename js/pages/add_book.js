document.addEventListener('DOMContentLoaded', () => {
  const bookTypeSelect = document.getElementById('bookTypeSelect');
  const ebookFormatGroup = document.getElementById('ebookFormatGroup');
  const ebookFormatSelect = document.getElementById('ebookFormatSelect');
  const initialCopiesInput = document.getElementById('initialCopiesInput');
  const ebookFileGroup = document.getElementById('ebookFileGroup');
  const ebookFileInput = document.getElementById('ebookFileInput');

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
    if (initialCopiesInput) {
      initialCopiesInput.disabled = isEbook;
      if (isEbook) {
        initialCopiesInput.value = 0;
      }
    }
    if (ebookFileGroup) {
      ebookFileGroup.classList.toggle('d-none', !isEbook);
    }
    if (ebookFileInput) {
      ebookFileInput.required = isEbook;
      if (!isEbook) {
        ebookFileInput.value = '';
      }
    }
  };

  if (bookTypeSelect) {
    bookTypeSelect.addEventListener('change', updateBookTypeFields);
    updateBookTypeFields();
  }
});
