document.addEventListener('DOMContentLoaded', () => {
  const addBtn = document.querySelector('.add-btn');
  if (addBtn) {
    addBtn.addEventListener('click', () => {
      alert('Add new book feature coming soon!');
    });
  }

  const headerRow = document.getElementById('tableHeader');
  const tbody = document.getElementById('tableBody');
  const jsonOutput = document.getElementById('jsonOutput');
  if (!headerRow || !tbody) {
    return;
  }

  const rbacData = {
    roles: ['user', 'librarian', 'admin'],
    resources: [
      'account registration',
      'profile customization',
      'view library',
      'request book requisition',
      'return book',
      'pay fine',
      'see fine',
      'see notification',
      'see requisitioned book',
      'download book',
      'request book',
      'CRUD Operations',
      'see pending requisition',
      'approve requisition',
      'reject requisition',
      'waive fine',
      'accept fine',
      'make announcement',
      'set holidays',
      'create backup',
      'restore backup',
      'manage role',
      'approve user',
      'user CRUD',
      'Policy CRUD',
    ],
    permissions: {
      user: {
        'account registration': true,
        'profile customization': true,
        'view library': true,
        'request book requisition': true,
        'return book': true,
        'pay fine': true,
        'see fine': true,
        'see notification': true,
        'see requisitioned book': true,
        'download book': true,
        'request book': true,
      },
      librarian: {
        'account registration': true,
        'profile customization': true,
        'view library': true,
        'see fine': true,
        'see notification': true,
        'see requisitioned book': true,
        'download book': true,
        'CRUD Operations': true,
        'see pending requisition': true,
        'approve requisition': true,
        'reject requisition': true,
        'waive fine': true,
        'accept fine': true,
        'make announcement': true,
        'set holidays': true,
      },
      admin: {
        'profile customization': true,
        'view library': true,
        'see fine': true,
        'see notification': true,
        'see requisitioned book': true,
        'download book': true,
        'CRUD Operations': true,
        'see pending requisition': true,
        'approve requisition': true,
        'reject requisition': true,
        'waive fine': true,
        'accept fine': true,
        'make announcement': true,
        'set holidays': true,
        'create backup': true,
        'restore backup': true,
        'manage role': true,
        'approve user': true,
        'user CRUD': true,
        'Policy CRUD': true,
      },
    },
  };

  rbacData.roles.forEach((role) => {
    const th = document.createElement('th');
    th.textContent = role.toUpperCase();
    headerRow.appendChild(th);
  });

  rbacData.resources.forEach((resource) => {
    const tr = document.createElement('tr');

    const resourceTd = document.createElement('td');
    resourceTd.textContent = resource;
    tr.appendChild(resourceTd);

    rbacData.roles.forEach((role) => {
      const td = document.createElement('td');
      const checkbox = document.createElement('input');

      checkbox.type = 'checkbox';
      checkbox.checked = rbacData.permissions[role]?.[resource] === true;

      checkbox.addEventListener('change', () => {
        if (!rbacData.permissions[role]) {
          rbacData.permissions[role] = {};
        }

        rbacData.permissions[role][resource] = checkbox.checked;
        if (jsonOutput) {
          jsonOutput.textContent = JSON.stringify(rbacData.permissions, null, 2);
        }
      });

      td.appendChild(checkbox);
      tr.appendChild(td);
    });

    tbody.appendChild(tr);
  });
});
