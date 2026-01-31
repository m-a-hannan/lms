document.addEventListener('DOMContentLoaded', () => {
  const customReportBtn = document.getElementById('toggleCustomReport');
  const customReportRow = document.getElementById('customReportRow');
  const customReportClose = document.getElementById('customReportClose');
  const clearDateFilters = document.getElementById('clearDateFilters');
  const startDateInput = document.getElementById('startDate');
  const endDateInput = document.getElementById('endDate');

  if (customReportBtn && customReportRow) {
    customReportBtn.addEventListener('click', () => {
      customReportRow.classList.remove('d-none');
    });
  }
  if (customReportClose && customReportRow) {
    customReportClose.addEventListener('click', () => {
      customReportRow.classList.add('d-none');
    });
  }
  if (clearDateFilters && startDateInput && endDateInput) {
    clearDateFilters.addEventListener('click', () => {
      startDateInput.value = '';
      endDateInput.value = '';
    });
  }
});
