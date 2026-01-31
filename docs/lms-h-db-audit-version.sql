CREATE TABLE users (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(255),
  password_hash VARCHAR(255),

  created_by INT NULL,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT NULL,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT NULL,
  deleted_date DATETIME NULL
);


CREATE TABLE roles (
  role_id INT PRIMARY KEY AUTO_INCREMENT,
  role_name VARCHAR(100),

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
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
  designation VARCHAR(255),

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE user_roles (
  user_role_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  role_id INT,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (role_id) REFERENCES roles(role_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE books (
  book_id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255),
  author VARCHAR(255),
  isbn VARCHAR(50),
  publisher VARCHAR(255),
  publication_year INT,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE book_editions (
  edition_id INT PRIMARY KEY AUTO_INCREMENT,
  book_id INT,
  edition_number INT,
  publication_year INT,
  pages INT,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (book_id) REFERENCES books(book_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);

CREATE TABLE book_copies (
  copy_id INT PRIMARY KEY AUTO_INCREMENT,
  edition_id INT,
  barcode VARCHAR(100),
  status VARCHAR(50),
  location VARCHAR(255),

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (edition_id) REFERENCES book_editions(edition_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE categories (
  category_id INT PRIMARY KEY AUTO_INCREMENT,
  category_name VARCHAR(100),

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE book_categories (
  book_cat_id INT PRIMARY KEY AUTO_INCREMENT,
  book_id INT,
  category_id INT,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (book_id) REFERENCES books(book_id),
  FOREIGN KEY (category_id) REFERENCES categories(category_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE loans (
  loan_id INT PRIMARY KEY AUTO_INCREMENT,
  copy_id INT,
  user_id INT,
  issue_date DATE,
  due_date DATE,
  return_date DATE,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (copy_id) REFERENCES book_copies(copy_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE reservations (
  reservation_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  copy_id INT,
  reservation_date DATE,
  expiry_date DATE,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (copy_id) REFERENCES book_copies(copy_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE returns (
  return_id INT PRIMARY KEY AUTO_INCREMENT,
  loan_id INT,
  return_date DATE,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (loan_id) REFERENCES loans(loan_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE fines (
  fine_id INT PRIMARY KEY AUTO_INCREMENT,
  loan_id INT,
  amount DECIMAL(10,2),
  fine_date DATE,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (loan_id) REFERENCES loans(loan_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE payments (
  payment_id INT PRIMARY KEY AUTO_INCREMENT,
  fine_id INT,
  payment_date DATE,
  amount DECIMAL(10,2),
  payment_method VARCHAR(50),

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (fine_id) REFERENCES fines(fine_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE fine_waivers (
  waiver_id INT PRIMARY KEY AUTO_INCREMENT,
  fine_id INT,
  approved_by VARCHAR(255),
  waiver_date DATE,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (fine_id) REFERENCES fines(fine_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE digital_resources (
  resource_id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255),
  description TEXT,
  type VARCHAR(50),

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE digital_files (
  file_id INT PRIMARY KEY AUTO_INCREMENT,
  resource_id INT,
  file_path VARCHAR(255),
  file_size INT,
  download_count INT,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (resource_id) REFERENCES digital_resources(resource_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE library_policies (
  policy_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255),
  description TEXT,
  effective_date DATE,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE policy_changes (
  change_id INT PRIMARY KEY AUTO_INCREMENT,
  policy_id INT,
  proposed_by VARCHAR(255),
  proposal_date DATE,
  status VARCHAR(50),

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (policy_id) REFERENCES library_policies(policy_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE holidays (
  holiday_id INT PRIMARY KEY AUTO_INCREMENT,
  date DATE,
  description VARCHAR(255),

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE notifications (
  notification_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  title VARCHAR(255),
  message TEXT,
  created_at DATETIME,
  read_status TINYINT(1),

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE announcements (
  announcement_id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255),
  message TEXT,
  created_at DATETIME,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE audit_logs (
  log_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  action VARCHAR(255),
  target_table VARCHAR(255),
  target_id INT,
  timestamp DATETIME,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE system_settings (
  setting_id INT PRIMARY KEY AUTO_INCREMENT,

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);


CREATE TABLE backups (
  backup_id INT PRIMARY KEY AUTO_INCREMENT,
  backup_date DATETIME,
  file_path VARCHAR(255),
  status VARCHAR(50),

  created_by INT,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified_by INT,
  modified_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_by INT,
  deleted_date DATETIME,

  FOREIGN KEY (created_by) REFERENCES users(user_id),
  FOREIGN KEY (modified_by) REFERENCES users(user_id),
  FOREIGN KEY (deleted_by) REFERENCES users(user_id)
);
