CREATE TABLE users (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(255),
  password_hash VARCHAR(255)
);

CREATE TABLE user_profiles (
  profile_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  dob DATE,
  address TEXT,
  phone VARCHAR(20),
  photo VARCHAR(255),
  institution_name VARCHAR(255),
  designation VARCHAR(255)
);

CREATE TABLE roles (
  role_id INT PRIMARY KEY AUTO_INCREMENT,
  role_name VARCHAR(100)
);

CREATE TABLE user_roles (
  user_role_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  role_id INT
);

CREATE TABLE books (
  book_id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255),
  author VARCHAR(255),
  isbn VARCHAR(50),
  publisher VARCHAR(255),
  publication_year INT
);

CREATE TABLE book_editions (
  edition_id INT PRIMARY KEY AUTO_INCREMENT,
  book_id INT,
  edition_number INT,
  publication_year INT,
  pages INT
);

CREATE TABLE book_copies (
  copy_id INT PRIMARY KEY AUTO_INCREMENT,
  edition_id INT,
  barcode VARCHAR(100),
  status VARCHAR(50),
  location VARCHAR(255)
);

CREATE TABLE categories (
  category_id INT PRIMARY KEY AUTO_INCREMENT,
  category_name VARCHAR(100)
);

CREATE TABLE book_categories (
  book_cat_id INT PRIMARY KEY AUTO_INCREMENT,
  book_id INT,
  category_id INT
);

CREATE TABLE loans (
  loan_id INT PRIMARY KEY AUTO_INCREMENT,
  copy_id INT,
  user_id INT,
  issue_date DATE,
  due_date DATE,
  return_date DATE
);

CREATE TABLE reservations (
  reservation_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  copy_id INT,
  reservation_date DATE,
  expiry_date DATE
);

CREATE TABLE returns (
  return_id INT PRIMARY KEY AUTO_INCREMENT,
  loan_id INT,
  return_date DATE
);

CREATE TABLE fines (
  fine_id INT PRIMARY KEY AUTO_INCREMENT,
  loan_id INT,
  amount DECIMAL(10,2),
  fine_date DATE
);

CREATE TABLE payments (
  payment_id INT PRIMARY KEY AUTO_INCREMENT,
  fine_id INT,
  payment_date DATE,
  amount DECIMAL(10,2),
  payment_method VARCHAR(50)
);

CREATE TABLE fine_waivers (
  waiver_id INT PRIMARY KEY AUTO_INCREMENT,
  fine_id INT,
  approved_by VARCHAR(255),
  waiver_date DATE
);

CREATE TABLE digital_resources (
  resource_id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255),
  description TEXT,
  type VARCHAR(50)
);

CREATE TABLE digital_files (
  file_id INT PRIMARY KEY AUTO_INCREMENT,
  resource_id INT,
  file_path VARCHAR(255),
  file_size INT,
  download_count INT
);

CREATE TABLE library_policies (
  policy_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255),
  description TEXT,
  effective_date DATE
);

CREATE TABLE policy_changes (
  change_id INT PRIMARY KEY AUTO_INCREMENT,
  policy_id INT,
  proposed_by VARCHAR(255),
  proposal_date DATE,
  status VARCHAR(50)
);

CREATE TABLE holidays (
  holiday_id INT PRIMARY KEY AUTO_INCREMENT,
  date DATE,
  description VARCHAR(255)
);

CREATE TABLE notifications (
  notification_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  title VARCHAR(255),
  message TEXT,
  created_at DATETIME,
  read_status TINYINT(1)
);

CREATE TABLE announcements (
  announcement_id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255),
  message TEXT,
  created_at DATETIME
);

CREATE TABLE audit_logs (
  log_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  action VARCHAR(255),
  target_table VARCHAR(255),
  target_id INT,
  timestamp DATETIME
);

CREATE TABLE system_settings (
  setting_id INT PRIMARY KEY AUTO_INCREMENT
);

CREATE TABLE backups (
  backup_id INT PRIMARY KEY AUTO_INCREMENT,
  backup_date DATETIME,
  file_path VARCHAR(255),
  status VARCHAR(50)
);
