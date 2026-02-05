document.addEventListener('DOMContentLoaded', () => {
  const readBoxes = Array.from(document.querySelectorAll('.perm-read'));
  const writeBoxes = Array.from(document.querySelectorAll('.perm-write'));
  const denyBoxes = Array.from(document.querySelectorAll('.perm-deny'));

  const selectAllRead = document.getElementById('selectAllRead');
  const selectAllWrite = document.getElementById('selectAllWrite');
  const selectAllDeny = document.getElementById('selectAllDeny');

  const updateSelectAllState = (headerBox, boxes) => {
    if (!headerBox) {
      return;
    }
    if (boxes.length === 0) {
      headerBox.checked = false;
      headerBox.indeterminate = false;
      return;
    }
    const checkedCount = boxes.filter((box) => box.checked).length;
    headerBox.checked = checkedCount === boxes.length;
    headerBox.indeterminate = checkedCount > 0 && checkedCount < boxes.length;
  };

  const syncAllHeaders = () => {
    updateSelectAllState(selectAllRead, readBoxes);
    updateSelectAllState(selectAllWrite, writeBoxes);
    updateSelectAllState(selectAllDeny, denyBoxes);
  };

  const clearRowReadWrite = (row) => {
    if (!row) {
      return;
    }
    const readBox = row.querySelector('.perm-read');
    const writeBox = row.querySelector('.perm-write');
    if (readBox) {
      readBox.checked = false;
    }
    if (writeBox) {
      writeBox.checked = false;
    }
  };

  const handleSelectAll = (headerBox, boxes, opts = {}) => {
    if (!headerBox) {
      return;
    }
    headerBox.addEventListener('change', () => {
      const checked = headerBox.checked;
      boxes.forEach((box) => {
        box.checked = checked;
        if (checked && opts.clearRowReadWriteOnCheck) {
          clearRowReadWrite(box.closest('tr'));
        }
      });
      if (checked && opts.clearOtherHeaders) {
        opts.clearOtherHeaders();
      }
      syncAllHeaders();
    });
  };

  handleSelectAll(selectAllRead, readBoxes);
  handleSelectAll(selectAllWrite, writeBoxes);
  handleSelectAll(selectAllDeny, denyBoxes, {
    clearRowReadWriteOnCheck: true,
    clearOtherHeaders: () => {
      if (selectAllRead) {
        selectAllRead.checked = false;
        selectAllRead.indeterminate = false;
      }
      if (selectAllWrite) {
        selectAllWrite.checked = false;
        selectAllWrite.indeterminate = false;
      }
    },
  });

  const attachRowHandler = (boxes, onChecked) => {
    boxes.forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        if (checkbox.checked && onChecked) {
          onChecked(checkbox);
        }
        syncAllHeaders();
      });
    });
  };

  attachRowHandler(denyBoxes, (checkbox) => {
    clearRowReadWrite(checkbox.closest('tr'));
  });

  attachRowHandler(readBoxes);
  attachRowHandler(writeBoxes);

  syncAllHeaders();
});
