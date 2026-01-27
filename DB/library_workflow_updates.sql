-- Library workflow updates (idempotent)
-- Note: Uses information_schema checks to avoid adding existing columns/indexes.

SET @schema = DATABASE();

-- Policy numeric value column
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'library_policies' AND COLUMN_NAME = 'policy_value';
SET @sql = IF(@col_exists = 0,
	'ALTER TABLE library_policies ADD COLUMN policy_value INT(11) DEFAULT NULL AFTER description',
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE library_policies
SET policy_value = CAST(description AS UNSIGNED)
WHERE (policy_value IS NULL OR policy_value = 0) AND description REGEXP '^[0-9]+';

-- Status columns for workflow tracking
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'loans' AND COLUMN_NAME = 'status';
SET @sql = IF(@col_exists = 0,
	"ALTER TABLE loans ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'pending'",
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'reservations' AND COLUMN_NAME = 'status';
SET @sql = IF(@col_exists = 0,
	"ALTER TABLE reservations ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'pending'",
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'returns' AND COLUMN_NAME = 'status';
SET @sql = IF(@col_exists = 0,
	"ALTER TABLE returns ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'pending'",
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Remarks for approval/rejection notes
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'loans' AND COLUMN_NAME = 'remarks';
SET @sql = IF(@col_exists = 0,
	'ALTER TABLE loans ADD COLUMN remarks TEXT DEFAULT NULL',
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'reservations' AND COLUMN_NAME = 'remarks';
SET @sql = IF(@col_exists = 0,
	'ALTER TABLE reservations ADD COLUMN remarks TEXT DEFAULT NULL',
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'returns' AND COLUMN_NAME = 'remarks';
SET @sql = IF(@col_exists = 0,
	'ALTER TABLE returns ADD COLUMN remarks TEXT DEFAULT NULL',
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Reservation book queue support
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'reservations' AND COLUMN_NAME = 'book_id';
SET @sql = IF(@col_exists = 0,
	'ALTER TABLE reservations ADD COLUMN book_id INT(11) DEFAULT NULL AFTER reservation_id',
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Ensure reservations.book_id matches books.book_id type
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'reservations' AND COLUMN_NAME = 'book_id';
SELECT COLUMN_TYPE INTO @book_id_type
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'books' AND COLUMN_NAME = 'book_id';
SET @sql = IF(@col_exists = 1 AND @book_id_type IS NOT NULL,
	CONCAT('ALTER TABLE reservations MODIFY book_id ', @book_id_type, ' NULL'),
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Ensure books.book_id is indexed before adding FK
SELECT COUNT(*) INTO @idx_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'books' AND COLUMN_NAME = 'book_id';
SET @sql = IF(@idx_exists = 0,
	'ALTER TABLE books ADD INDEX idx_books_book_id (book_id)',
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @fk_exists
FROM information_schema.REFERENTIAL_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = @schema
  AND TABLE_NAME = 'reservations'
  AND CONSTRAINT_NAME = 'reservations_book_fk';
SET @sql = IF(@fk_exists = 0,
	'ALTER TABLE reservations ADD CONSTRAINT reservations_book_fk FOREIGN KEY (book_id) REFERENCES books(book_id)',
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE reservations r
JOIN book_copies c ON r.copy_id = c.copy_id
JOIN book_editions e ON c.edition_id = e.edition_id
SET r.book_id = e.book_id
WHERE r.book_id IS NULL;

-- Initialize status values where missing
UPDATE loans
SET status = CASE
	WHEN return_date IS NOT NULL THEN 'returned'
	WHEN issue_date IS NOT NULL THEN 'approved'
	ELSE 'pending'
END
WHERE status IS NULL OR status = '';

UPDATE reservations
SET status = CASE
	WHEN expiry_date IS NOT NULL THEN 'approved'
	ELSE 'pending'
END
WHERE status IS NULL OR status = '';

UPDATE returns
SET status = CASE
	WHEN return_date IS NOT NULL THEN 'approved'
	ELSE 'pending'
END
WHERE status IS NULL OR status = '';

-- Optional indexes for dashboards
SELECT COUNT(*) INTO @idx_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'loans' AND INDEX_NAME = 'idx_loans_user_status';
SET @sql = IF(@idx_exists = 0,
	'CREATE INDEX idx_loans_user_status ON loans(user_id, status)',
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @idx_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'reservations' AND INDEX_NAME = 'idx_reservations_user_status';
SET @sql = IF(@idx_exists = 0,
	'CREATE INDEX idx_reservations_user_status ON reservations(user_id, status)',
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @idx_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'reservations' AND INDEX_NAME = 'idx_reservations_book_status';
SET @sql = IF(@idx_exists = 0,
	'CREATE INDEX idx_reservations_book_status ON reservations(book_id, status, created_date)',
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @idx_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'returns' AND INDEX_NAME = 'idx_returns_status';
SET @sql = IF(@idx_exists = 0,
	'CREATE INDEX idx_returns_status ON returns(status)',
	'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Policy defaults
INSERT INTO library_policies (name, description, effective_date)
SELECT 'loan_period_days', '14', CURDATE()
WHERE NOT EXISTS (
	SELECT 1 FROM library_policies WHERE name = 'loan_period_days' AND deleted_date IS NULL
);

INSERT INTO library_policies (name, description, effective_date)
SELECT 'reservation_expiry_days', '3', CURDATE()
WHERE NOT EXISTS (
	SELECT 1 FROM library_policies WHERE name = 'reservation_expiry_days' AND deleted_date IS NULL
);

UPDATE library_policies
SET policy_value = 14
WHERE name = 'loan_period_days' AND (policy_value IS NULL OR policy_value = 0);

UPDATE library_policies
SET policy_value = 3
WHERE name = 'reservation_expiry_days' AND (policy_value IS NULL OR policy_value = 0);

-- Page list entries for new pages/actions
INSERT IGNORE INTO page_list (page_name, page_path, is_active)
VALUES
	('User Dashboard', 'user_dashboard.php', 1),
	('Request Loan', 'actions/request_loan.php', 1),
	('Request Reservation', 'actions/request_reservation.php', 1),
	('Request Return', 'actions/request_return.php', 1),
	('Process Loan', 'actions/admin_process_loan.php', 1),
	('Process Reservation', 'actions/admin_process_reservation.php', 1),
	('Process Return', 'actions/admin_process_return.php', 1);

-- Permissions for Admin and Librarian
INSERT IGNORE INTO permissions (role_id, page_id, can_read, can_write, deny)
SELECT r.role_id, pl.page_id, 1, 1, 0
FROM roles r
JOIN page_list pl ON pl.page_path IN (
	'user_dashboard.php',
	'actions/request_loan.php',
	'actions/request_reservation.php',
	'actions/request_return.php',
	'actions/admin_process_loan.php',
	'actions/admin_process_reservation.php',
	'actions/admin_process_return.php'
)
WHERE r.role_name IN ('Admin', 'Librarian');

-- Permissions for Users (request + user dashboard)
INSERT IGNORE INTO permissions (role_id, page_id, can_read, can_write, deny)
SELECT r.role_id, pl.page_id,
	CASE
		WHEN pl.page_path = 'user_dashboard.php' THEN 1
		ELSE 0
	END AS can_read,
	CASE
		WHEN pl.page_path LIKE 'actions/request_%' THEN 1
		ELSE 0
	END AS can_write,
	0 AS deny
FROM roles r
JOIN page_list pl ON pl.page_path IN (
	'user_dashboard.php',
	'actions/request_loan.php',
	'actions/request_reservation.php',
	'actions/request_return.php'
)
WHERE r.role_name = 'User';
