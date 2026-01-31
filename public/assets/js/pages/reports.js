document.addEventListener('DOMContentLoaded', () => {
  const dataTag = document.getElementById('reports-data');
  if (!dataTag) {
    return;
  }

  let reportData = {};
  try {
    reportData = JSON.parse(dataTag.textContent || '{}');
  } catch (err) {
    reportData = {};
  }

  const themeButtons = document.querySelectorAll('[data-chart-theme]');
  let chartTheme = 'default';

  const themePalettes = {
    default: ['#0d6efd', '#20c997', '#ffc107', '#dc3545', '#6f42c1'],
    muted: ['#6c757d', '#adb5bd', '#ced4da', '#868e96', '#495057'],
  };

  function renderCharts() {
    if (!window.ApexCharts) {
      return;
    }

    const palette = themePalettes[chartTheme] || themePalettes.default;

    const userStatus = reportData.userStatuses || {};
    new ApexCharts(document.querySelector('#userStatusChart'), {
      chart: {
        type: 'donut',
        height: 250,
      },
      labels: Object.keys(userStatus),
      series: Object.values(userStatus),
      colors: palette,
      legend: {
        position: 'bottom',
      },
    }).render();

    const inventory = reportData.inventoryTypes || {};
    new ApexCharts(document.querySelector('#inventoryChart'), {
      chart: {
        type: 'bar',
        height: 250,
      },
      series: [
        {
          name: 'Books',
          data: Object.values(inventory),
        },
      ],
      xaxis: {
        categories: Object.keys(inventory),
      },
      colors: [palette[0]],
    }).render();

    const circulation = reportData.circulation || {
      Loans: {},
      Reservations: {},
      Returns: {},
    };
    const circulationLabels = Array.from(
      new Set([
        ...Object.keys(circulation.Loans || {}),
        ...Object.keys(circulation.Reservations || {}),
        ...Object.keys(circulation.Returns || {}),
      ])
    );

    new ApexCharts(document.querySelector('#circulationChart'), {
      chart: {
        type: 'bar',
        height: 250,
        stacked: true,
      },
      series: [
        {
          name: 'Loans',
          data: circulationLabels.map((label) => circulation.Loans?.[label] || 0),
        },
        {
          name: 'Reservations',
          data: circulationLabels.map((label) => circulation.Reservations?.[label] || 0),
        },
        {
          name: 'Returns',
          data: circulationLabels.map((label) => circulation.Returns?.[label] || 0),
        },
      ],
      xaxis: {
        categories: circulationLabels,
      },
      colors: palette,
    }).render();

    const approvals = reportData.pendingApprovals || {};
    new ApexCharts(document.querySelector('#approvalChart'), {
      chart: {
        type: 'radar',
        height: 250,
      },
      series: [
        {
          name: 'Pending',
          data: Object.values(approvals),
        },
      ],
      xaxis: {
        categories: Object.keys(approvals),
      },
      colors: [palette[2]],
    }).render();

    const digital = reportData.digitalTotals || {};
    new ApexCharts(document.querySelector('#digitalChart'), {
      chart: {
        type: 'bar',
        height: 250,
      },
      series: [
        {
          name: 'Total',
          data: Object.values(digital),
        },
      ],
      xaxis: {
        categories: ['Resources', 'Files', 'Downloads'],
      },
      colors: [palette[1]],
    }).render();

    const finance = reportData.finance || { Fines: {}, Waivers: {}, Payments: {} };
    new ApexCharts(document.querySelector('#financeChart'), {
      chart: {
        type: 'bar',
        height: 250,
      },
      series: [
        {
          name: 'Count',
          data: [finance.Fines?.count || 0, finance.Waivers?.count || 0, finance.Payments?.count || 0],
        },
        {
          name: 'Amount',
          data: [finance.Fines?.amount || 0, finance.Waivers?.amount || 0, finance.Payments?.amount || 0],
        },
      ],
      xaxis: {
        categories: ['Fines', 'Waivers', 'Payments'],
      },
      colors: [palette[3], palette[0]],
    }).render();

    const searches = reportData.searchTop || [];
    new ApexCharts(document.querySelector('#searchChart'), {
      chart: {
        type: 'bar',
        height: 250,
      },
      series: [
        {
          name: 'Searches',
          data: searches.map((item) => item.total),
        },
      ],
      xaxis: {
        categories: searches.map((item) => item.query || '-'),
      },
      colors: [palette[4]],
    }).render();
  }

  function renderHeatmap() {
    const container = document.getElementById('activityHeatmap');
    const grid = document.getElementById('heatmapGrid');
    const monthsEl = document.getElementById('heatmapMonths');
    const userSelect = document.getElementById('activityUserFilter');
    const typeSelect = document.getElementById('activityTypeFilter');
    if (!container || !grid || !monthsEl || !userSelect || !typeSelect) return;

    const data = JSON.parse(container.dataset.activity || '{}');
    const totalDays = 53 * 7;
    const today = new Date();
    const start = new Date(today);
    start.setDate(today.getDate() - (totalDays - 1));

    grid.innerHTML = '';
    monthsEl.innerHTML = '';

    const monthLabels = [];
    for (let i = 0; i < 12; i++) {
      monthLabels.push(
        new Intl.DateTimeFormat('en', {
          month: 'short',
        }).format(new Date(today.getFullYear(), (today.getMonth() - 11 + i + 12) % 12, 1))
      );
    }
    monthLabels.forEach((label) => {
      const span = document.createElement('span');
      span.textContent = label;
      monthsEl.appendChild(span);
    });

    const selectedUser = userSelect.value;
    const selectedType = typeSelect.value;
    const source =
      selectedUser === 'all'
        ? data.all || { loan: {}, reserve: {}, return: {}, download: {} }
        : (data.users && data.users[selectedUser]) || { loan: {}, reserve: {}, return: {}, download: {} };

    const types = ['loan', 'reserve', 'return', 'download'];
    const maxByType = {
      loan: 1,
      reserve: 1,
      return: 1,
      download: 1,
    };

    types.forEach((type) => {
      const values = [];
      for (let i = 0; i < totalDays; i++) {
        const date = new Date(start);
        date.setDate(start.getDate() + i);
        const key = date.toISOString().slice(0, 10);
        values.push(source[type]?.[key] || 0);
      }
      maxByType[type] = Math.max(1, ...values);
    });

    const levelFor = (count, max) => {
      if (count <= 0) return 0;
      const ratio = count / max;
      if (ratio <= 0.25) return 1;
      if (ratio <= 0.5) return 2;
      if (ratio <= 0.75) return 3;
      return 4;
    };

    for (let i = 0; i < totalDays; i++) {
      const date = new Date(start);
      date.setDate(start.getDate() + i);
      const key = date.toISOString().slice(0, 10);

      const counts = {
        loan: source.loan?.[key] || 0,
        reserve: source.reserve?.[key] || 0,
        return: source.return?.[key] || 0,
        download: source.download?.[key] || 0,
      };

      let activity = selectedType === 'all' ? 'loan' : selectedType;
      if (selectedType === 'all') {
        activity = Object.entries(counts).sort((a, b) => b[1] - a[1])[0][0];
      }

      const count = counts[activity] || 0;
      const level = levelFor(count, maxByType[activity] || 1);
      const cell = document.createElement('div');
      cell.className = `day activity-${activity} level-${level}`;
      cell.title = `${key}: ${count} ${activity}`;
      grid.appendChild(cell);
    }
  }

  renderCharts();
  renderHeatmap();

  themeButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      chartTheme = btn.dataset.chartTheme || 'default';
      document.querySelectorAll('.chart-box').forEach((box) => (box.innerHTML = ''));
      renderCharts();
    });
  });

  const reportGroupItems = document.querySelectorAll('.report-group-item');
  const reportGroupToggle = document.querySelector('.report-group-toggle');
  const applyGroupFilter = (value) => {
    document.querySelectorAll('.report-section').forEach((section) => {
      if (value === 'all') {
        section.classList.remove('report-hidden');
        return;
      }
      const group = section.dataset.reportGroup;
      section.classList.toggle('report-hidden', group !== value);
    });
  };

  if (reportGroupItems.length) {
    reportGroupItems.forEach((item) => {
      item.addEventListener('click', (event) => {
        event.preventDefault();
        const value = item.dataset.reportGroup || 'all';
        if (reportGroupToggle) {
          reportGroupToggle.textContent = item.textContent.trim();
        }
        applyGroupFilter(value);
      });
    });
  }

  const reportToggles = Array.from(document.querySelectorAll('.chart-check[data-report-target]'));
  const reportCards = Array.from(document.querySelectorAll('.report-card[data-report-id]'));
  const reportGrids = Array.from(document.querySelectorAll('.reports-grid'));

  const applyReportVisibility = () => {
    reportToggles.forEach((toggle) => {
      const target = toggle.dataset.reportTarget;
      const card = reportCards.find((item) => item.dataset.reportId === target);
      if (!card) return;
      card.classList.toggle('report-card-hidden', !toggle.checked);
    });
  };

  const resetReportOrder = () => {
    reportGrids.forEach((grid) => {
      const cards = Array.from(grid.querySelectorAll('.report-card[data-report-order]'));
      cards.sort((a, b) => Number(a.dataset.reportOrder) - Number(b.dataset.reportOrder));
      cards.forEach((card) => grid.appendChild(card));
    });
  };

  const setAll = (value) => {
    reportToggles.forEach((toggle) => {
      toggle.checked = value;
    });
    applyReportVisibility();
  };

  document.querySelectorAll('[data-report-action]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const action = btn.dataset.reportAction;
      if (action === 'enable-all') {
        setAll(true);
      } else if (action === 'disable-all') {
        setAll(false);
      } else if (action === 'reset-order') {
        setAll(true);
        resetReportOrder();
      }
    });
  });

  reportToggles.forEach((toggle) => {
    toggle.addEventListener('change', applyReportVisibility);
  });

  applyReportVisibility();

  const activityUserFilter = document.getElementById('activityUserFilter');
  const activityTypeFilter = document.getElementById('activityTypeFilter');
  if (activityUserFilter && activityTypeFilter) {
    activityUserFilter.addEventListener('change', renderHeatmap);
    activityTypeFilter.addEventListener('change', renderHeatmap);
  }
});
