<?php
// Load app configuration and enable verbose error reporting.
require_once dirname(__DIR__, 2) . '/includes/config.php';
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Load database connection and permission helpers.
require_once ROOT_PATH . '/app/includes/connection.php';
require_once ROOT_PATH . '/app/includes/permissions.php';

// Build the dashboard link based on RBAC settings.
$dashboardUrl = BASE_URL . rbac_dashboard_path($conn);

// Check whether a column type should be treated as numeric.
function is_numeric_type($type)
{
	return (bool) preg_match('/int|decimal|float|double|bit|year/i', $type);
}

// Check whether a column type is a date.
function is_date_type($type)
{
	return (bool) preg_match('/^date$/i', $type);
}

// Check whether a column type is a datetime/timestamp.
function is_datetime_type($type)
{
	return (bool) preg_match('/datetime|timestamp/i', $type);
}

// Build a map of foreign key relationships in the database.
function fetch_fk_map($conn)
{
	$map = [];
	// Query information_schema for foreign key references.
	$sql = "SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
		FROM information_schema.KEY_COLUMN_USAGE
		WHERE TABLE_SCHEMA = DATABASE()
		  AND REFERENCED_TABLE_NAME IS NOT NULL";
	$result = $conn->query($sql);
	// Return an empty map on query failure.
	if ($result === false) {
		return $map;
	}
	// Populate the map with FK column references.
	while ($row = $result->fetch_assoc()) {
		$table = $row['TABLE_NAME'];
		$column = $row['COLUMN_NAME'];
		$map[$table][$column] = [
			'table' => $row['REFERENCED_TABLE_NAME'],
			'column' => $row['REFERENCED_COLUMN_NAME'],
		];
	}
	return $map;
}

// Fetch an existing referenced FK value if one exists.
function get_existing_fk_value($conn, $refTable, $refColumn)
{
	// Grab the first available referenced row.
	$sql = "SELECT `$refColumn` FROM `$refTable` ORDER BY `$refColumn` ASC LIMIT 1";
	$result = $conn->query($sql);
	if ($result && $result->num_rows > 0) {
		$row = $result->fetch_row();
		return $row[0];
	}
	return null;
}

// Resolve a valid FK value, inserting placeholder rows when needed.
function resolve_fk_value($conn, $fkMap, $table, $field, &$creatingTables)
{
	// Exit early when the field has no FK reference.
	if (!isset($fkMap[$table][$field])) {
		return null;
	}

	$refTable = $fkMap[$table][$field]['table'];
	$refColumn = $fkMap[$table][$field]['column'];

	// Use an existing referenced value when possible.
	$existing = get_existing_fk_value($conn, $refTable, $refColumn);
	if ($existing !== null) {
		return $existing;
	}

	// Insert a placeholder row in the referenced table.
	return insert_placeholder_row($conn, $fkMap, $refTable, $creatingTables);
}

// Insert a placeholder row in a table to satisfy FK dependencies.
function insert_placeholder_row($conn, $fkMap, $table, &$creatingTables)
{
	// Prevent recursive insertion loops.
	if (isset($creatingTables[$table])) {
		return null;
	}
	$creatingTables[$table] = true;

	// Inspect table schema to build an insert.
	$describe = $conn->query("DESCRIBE `$table`");
	if ($describe === false) {
		unset($creatingTables[$table]);
		return null;
	}

	$columns = [];
	$pk = null;
	$pkType = null;
	$pkAuto = false;
	// Collect column definitions and primary key metadata.
	while ($row = $describe->fetch_assoc()) {
		$columns[] = $row;
		if ($row['Key'] === 'PRI' && $pk === null) {
			$pk = $row['Field'];
			$pkType = $row['Type'];
			$pkAuto = stripos($row['Extra'], 'auto_increment') !== false;
		}
	}

	$insertCols = [];
	$insertVals = [];
	$insertedValues = [];

	// Build insert columns/values for required fields.
	foreach ($columns as $col) {
		$field = $col['Field'];
		$type = $col['Type'];
		$nullable = $col['Null'] === 'YES';
		$default = $col['Default'];
		$extra = $col['Extra'];

		if (stripos($extra, 'auto_increment') !== false) {
			continue;
		}

		if ($default !== null) {
			continue;
		}

		$insertCols[] = "`$field`";
		// Resolve FK values when needed.
		$fkValue = resolve_fk_value($conn, $fkMap, $table, $field, $creatingTables);
		if ($fkValue !== null) {
			$insertVals[] = sql_literal($conn, $type, $fkValue);
			$insertedValues[$field] = $fkValue;
			continue;
		}

		if ($nullable) {
			$insertVals[] = 'NULL';
			$insertedValues[$field] = null;
			continue;
		}

		// Generate sample values for non-nullable fields.
		$value = sample_value($type, $field);
		$insertVals[] = sql_literal($conn, $type, $value);
		$insertedValues[$field] = $value;
	}

	// Build the final insert SQL statement.
	if ($insertCols) {
		$insertSql = "INSERT INTO `$table` (" . implode(', ', $insertCols) . ") VALUES (" . implode(', ', $insertVals) . ")";
	} else {
		$insertSql = "INSERT INTO `$table` () VALUES ()";
	}

	// Execute the insert and return early on failure.
	if ($conn->query($insertSql) !== true) {
		unset($creatingTables[$table]);
		return null;
	}

	// Resolve the new primary key value for the row.
	if ($pkAuto) {
		$pkValue = $conn->insert_id;
	} elseif (isset($insertedValues[$pk])) {
		$pkValue = $insertedValues[$pk];
	} else {
		$pkValue = null;
	}

	unset($creatingTables[$table]);
	return $pkValue;
}

// Generate a sample value based on a column type.
function sample_value($type, $field)
{
	// Choose a default enum option when applicable.
	if (preg_match('/^enum\((.+)\)$/i', $type, $match)) {
		$parts = str_getcsv($match[1], ',', "'");
		return $parts[0] ?? 'test';
	}

	// Provide date values for date columns.
	if (is_date_type($type)) {
		return date('Y-m-d');
	}

	// Provide datetime values for datetime columns.
	if (is_datetime_type($type)) {
		return date('Y-m-d H:i:s');
	}

	// Provide numeric values for numeric columns.
	if (is_numeric_type($type)) {
		return 1;
	}

	// Default fallback for text-like fields.
	return $field . '_test';
}

// Convert a PHP value into a SQL literal by column type.
function sql_literal($conn, $type, $value)
{
	// Return NULL literal for null values.
	if ($value === null) {
		return 'NULL';
	}

	// Avoid quotes for numeric types.
	if (is_numeric_type($type)) {
		return (string) $value;
	}

	// Escape and quote strings for SQL.
	return "'" . $conn->real_escape_string((string) $value) . "'";
}

// Load all tables in the current database.
$tables = [];
$tablesResult = $conn->query('SHOW TABLES');
if ($tablesResult === false) {
	die('Failed to load tables: ' . $conn->error);
}
// Build the tables list from the result set.
while ($row = $tablesResult->fetch_row()) {
	$tables[] = $row[0];
}
sort($tables);

// Initialize results and FK helpers.
$results = [];
$fkMap = fetch_fk_map($conn);
$creatingTables = [];

// Run CRUD checks table-by-table.
foreach ($tables as $table) {
	// Initialize the result entry for this table.
	$entry = [
		'table' => $table,
		'create' => false,
		'read' => false,
		'update' => false,
		'delete' => false,
		'error' => '',
	];

	// Inspect table schema.
	$describe = $conn->query("DESCRIBE `$table`");
	if ($describe === false) {
		$entry['error'] = $conn->error;
		$results[] = $entry;
		continue;
	}

	$columns = [];
	$pk = null;
	$pkType = null;
	$pkAuto = false;
	// Collect columns and primary key metadata.
	while ($row = $describe->fetch_assoc()) {
		$columns[] = $row;
		if ($row['Key'] === 'PRI' && $pk === null) {
			$pk = $row['Field'];
			$pkType = $row['Type'];
			$pkAuto = stripos($row['Extra'], 'auto_increment') !== false;
		}
	}

	if ($pk === null) {
		$entry['error'] = 'No primary key found.';
		$results[] = $entry;
		continue;
	}

	$insertCols = [];
	$insertVals = [];
	$insertedValues = [];

	// Build insertable columns/values for required fields.
	foreach ($columns as $col) {
		$field = $col['Field'];
		$type = $col['Type'];
		$nullable = $col['Null'] === 'YES';
		$default = $col['Default'];
		$extra = $col['Extra'];

		if (stripos($extra, 'auto_increment') !== false) {
			continue;
		}

		if ($default !== null) {
			continue;
		}

		$insertCols[] = "`$field`";

		// Resolve FK values when needed.
		$fkValue = resolve_fk_value($conn, $fkMap, $table, $field, $creatingTables);
		if ($fkValue !== null) {
			$insertVals[] = sql_literal($conn, $type, $fkValue);
			$insertedValues[$field] = $fkValue;
			continue;
		}

		if ($nullable) {
			$insertVals[] = 'NULL';
			$insertedValues[$field] = null;
			continue;
		}

		// Generate sample values for required columns.
		$value = sample_value($type, $field);
		$insertVals[] = sql_literal($conn, $type, $value);
		$insertedValues[$field] = $value;
	}

	// Build the insert SQL for the CREATE check.
	if ($insertCols) {
		$insertSql = "INSERT INTO `$table` (" . implode(', ', $insertCols) . ") VALUES (" . implode(', ', $insertVals) . ")";
	} else {
		$insertSql = "INSERT INTO `$table` () VALUES ()";
	}

	// Attempt to create a row.
	if ($conn->query($insertSql) === true) {
		$entry['create'] = true;
	} else {
		$entry['error'] = $conn->error;
		$results[] = $entry;
		continue;
	}

	$pkValue = null;
	// Resolve the primary key value for follow-up checks.
	if ($pkAuto) {
		$pkValue = $conn->insert_id;
	} elseif (array_key_exists($pk, $insertedValues)) {
		$pkValue = $insertedValues[$pk];
	} else {
		$pkValue = 1;
	}

	// Attempt to read the created row.
	$selectSql = "SELECT * FROM `$table` WHERE `$pk` = " . sql_literal($conn, $pkType, $pkValue);
	$selectResult = $conn->query($selectSql);
	if ($selectResult && $selectResult->num_rows === 1) {
		$entry['read'] = true;
	} else {
		$entry['error'] = $conn->error ?: 'Record not found.';
		$results[] = $entry;
		continue;
	}

	$updateColumn = null;
	$updateType = null;
	// Select the first non-PK column to update.
	foreach ($columns as $col) {
		if ($col['Field'] === $pk) {
			continue;
		}
		$updateColumn = $col['Field'];
		$updateType = $col['Type'];
		break;
	}

	if ($updateColumn === null) {
		// Mark update as passed when no updatable column exists.
		$entry['update'] = true;
	} else {
		// Build a new value for the update check.
		$newValue = sample_value($updateType, $updateColumn);
		$isFk = isset($fkMap[$table][$updateColumn]);
		// Resolve FK values when updating FK columns.
		if ($isFk) {
			$fkValue = resolve_fk_value($conn, $fkMap, $table, $updateColumn, $creatingTables);
			if ($fkValue !== null) {
				$newValue = $fkValue;
			} elseif (array_key_exists($updateColumn, $insertedValues)) {
				$newValue = $insertedValues[$updateColumn];
			}
		}
		// Adjust new values for non-FK columns.
		if (!$isFk) {
			if (is_numeric_type($updateType)) {
				$newValue = 2;
			} elseif (is_date_type($updateType)) {
				$newValue = date('Y-m-d');
			} elseif (is_datetime_type($updateType)) {
				$newValue = date('Y-m-d H:i:s');
			} else {
				$newValue = $newValue . '_updated';
			}
		}

		// Attempt to update the created row.
		$updateSql = "UPDATE `$table` SET `$updateColumn` = " . sql_literal($conn, $updateType, $newValue) . " WHERE `$pk` = " . sql_literal($conn, $pkType, $pkValue);
		if ($conn->query($updateSql) === true) {
			$entry['update'] = true;
		} else {
			$entry['error'] = $conn->error;
			$results[] = $entry;
			continue;
		}
	}

	// Attempt to delete the created row.
	$deleteSql = "DELETE FROM `$table` WHERE `$pk` = " . sql_literal($conn, $pkType, $pkValue);
	if ($conn->query($deleteSql) === true) {
		$entry['delete'] = true;
	} else {
		$entry['error'] = $conn->error;
		$results[] = $entry;
		continue;
	}

	$results[] = $entry;
}

$passCount = 0;
// Count tables that passed all CRUD checks.
foreach ($results as $row) {
	if ($row['create'] && $row['read'] && $row['update'] && $row['delete']) {
		$passCount++;
	}
}
?>
<!-- CRUD health check page layout. -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Library Management System - CRUD Check</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body class="page-crud-check">
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
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Managers</a></li>
                    <li class="nav-item"><a class="nav-link" href="erd.php">ERD</a></li>
                    <li class="nav-item"><a class="nav-link" href="library_rbac_matrix.php">RBAC</a></li>
                </ul>

                <a href="<?php echo $dashboardUrl; ?>" class="btn btn-gradient px-4"><i class="nav-icon bi bi-speedometer"></i> Dashboard</a>
            </div>
        </div>
    </nav>

    <!-- Main content section with results. -->
    <section class="py-4">
        <div class="container">
            <!-- Page header and summary. -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="page-title mb-1">CRUD Health Check</h2>
                    <p class="text-muted mb-0">This page inserts, reads, updates, and deletes one row per table.</p>
                </div>
                <div class="mt-2 mt-md-0">
                    <span class="badge bg-dark status-badge">Passed: <?= $passCount ?> / <?= count($results) ?></span>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <!-- CRUD results table. -->
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Table</th>
                                    <th>Create</th>
                                    <th>Read</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                    <th>Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Render each table's CRUD status row. -->
                                <?php foreach ($results as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['table']) ?></td>
                                    <td>
                                        <span class="badge status-badge <?= $row['create'] ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $row['create'] ? 'OK' : 'FAIL' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge status-badge <?= $row['read'] ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $row['read'] ? 'OK' : 'FAIL' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge status-badge <?= $row['update'] ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $row['update'] ? 'OK' : 'FAIL' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge status-badge <?= $row['delete'] ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $row['delete'] ? 'OK' : 'FAIL' ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted"><?= htmlspecialchars($row['error']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
