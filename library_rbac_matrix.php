<?php
require_once __DIR__ . '/include/config.php';
require_once ROOT_PATH . '/include/connection.php';
require_once ROOT_PATH . '/include/permissions.php';

$dashboardUrl = BASE_URL . rbac_dashboard_path($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Library Management System</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap 5 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Bootstrap 5 Icons -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

	<!-- Custom CSS -->
	<link rel="stylesheet" href="style.css">
</head>

<body>

	<!-- ================= NAVBAR ================= -->
	<nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top">
		<div class="container">
			<a class="navbar-brand fw-bold" href="#">
				<span class="logo-dot"></span> Library
			</a>

			<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navMenu">
				<ul class="navbar-nav mx-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
					<li class="nav-item"><a class="nav-link" href="#">Managers</a></li>
					<li class="nav-item"><a class="nav-link" href="erd.php">ERD</a></li>
					<li class="nav-item"><a class="nav-link" href="library_rbac_matrix.php">RBAC</a></li>
				</ul>

				<a href="<?php echo $dashboardUrl; ?>" class="btn btn-gradient px-4"><i class="nav-icon bi bi-speedometer"></i> Dashboard</a>
			</div>
		</div>
	</nav>

	<!-- ================= RBAC Matrix ================= -->
	<section class="py-5 bg-light">
		<section class="container">
			<div class="row g-4">
				<div class="col-md-12">
					<h3 class="mb-4">Role-Based Access Control (RBAC) Matrix for LMS</h3>

					<div class="table-responsive">
						<table class="table table-striped table-bordered" id="rbacTable">
							<thead class="table-dark">
								<tr id="tableHeader">
									<th>Resource / Permission</th>
								</tr>
							</thead>
							<tbody id="tableBody"></tbody>
						</table>
						<!-- Debug output (optional) -->
						<pre class="mt-4 bg-light p-3" id="jsonOutput"></pre>
					</div>

				</div>
			</div>
			</div>
		</section>


		<!-- Floating Add Button -->
		<button class="add-btn">+</button>
	</section>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

	<script>
	// ===== RBAC JSON DATA =====
	const rbacData = {
		roles: ["user", "librarian", "admin"],
		resources: [
			"account registration",
			"profile customization",
			"view library",
			"request book requisition",
			"return book",
			"pay fine",
			"see fine",
			"see notification",
			"see requisitioned book",
			"download book",
			"request book",
			"CRUD Operations",
			"see pending requisition",
			"approve requisition",
			"reject requisition",
			"waive fine",
			"accept fine",
			"make announcement",
			"set holidays",
			"create backup",
			"restore backup",
			"manage role",
			"approve user",
			"user CRUD",
			"Policy CRUD"
		],
		permissions: {
			user: {
				"account registration": true,
				"profile customization": true,
				"view library": true,
				"request book requisition": true,
				"return book": true,
				"pay fine": true,
				"see fine": true,
				"see notification": true,
				"see requisitioned book": true,
				"download book": true,
				"request book": true
			},
			librarian: {
				"account registration": true,
				"profile customization": true,
				"view library": true,
				"see fine": true,
				"see notification": true,
				"see requisitioned book": true,
				"download book": true,
				"CRUD Operations": true,
				"see pending requisition": true,
				"approve requisition": true,
				"reject requisition": true,
				"waive fine": true,
				"accept fine": true,
				"make announcement": true,
				"set holidays": true
			},
			admin: {
				"profile customization": true,
				"view library": true,
				"see fine": true,
				"see notification": true,
				"see requisitioned book": true,
				"download book": true,
				"CRUD Operations": true,
				"see pending requisition": true,
				"approve requisition": true,
				"reject requisition": true,
				"waive fine": true,
				"accept fine": true,
				"make announcement": true,
				"set holidays": true,
				"create backup": true,
				"restore backup": true,
				"manage role": true,
				"approve user": true,
				"user CRUD": true,
				"Policy CRUD": true
			}
		}
	};

	const headerRow = document.getElementById("tableHeader");
	const tbody = document.getElementById("tableBody");
	const jsonOutput = document.getElementById("jsonOutput");

	// ===== BUILD HEADER =====
	rbacData.roles.forEach(role => {
		const th = document.createElement("th");
		th.textContent = role.toUpperCase();
		headerRow.appendChild(th);
	});

	// ===== BUILD BODY =====
	rbacData.resources.forEach(resource => {
		const tr = document.createElement("tr");

		const resourceTd = document.createElement("td");
		resourceTd.textContent = resource;
		tr.appendChild(resourceTd);

		rbacData.roles.forEach(role => {
			const td = document.createElement("td");
			const checkbox = document.createElement("input");

			checkbox.type = "checkbox";
			checkbox.checked =
				rbacData.permissions[role]?. [resource] === true;

			checkbox.addEventListener("change", () => {
				if (!rbacData.permissions[role]) {
					rbacData.permissions[role] = {};
				}

				rbacData.permissions[role][resource] = checkbox.checked;
				renderJson();
			});

			td.appendChild(checkbox);
			tr.appendChild(td);
		});

		tbody.appendChild(tr);
	});

	// ===== SHOW UPDATED JSON (optional) =====
	//function renderJson() {
	//  jsonOutput.textContent = JSON.stringify(rbacData.permissions, null, 2);
	//}

	//renderJson();
	</script>
	<!-- Custom JS -->
	<!-- <script src="script.js"></script> -->
	<script>
	// Placeholder for future interactions
	document.querySelector('.add-btn').addEventListener('click', () => {
		alert("Add new book feature coming soon!");
	});
	</script>
</body>

</html>
