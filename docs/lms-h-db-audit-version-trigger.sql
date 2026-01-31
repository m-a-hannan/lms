DELIMITER $$


CREATE TRIGGER users_bi
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER users_bu
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER roles_bi
BEFORE INSERT ON roles
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER roles_bu
BEFORE UPDATE ON roles
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER user_profiles_bi
BEFORE INSERT ON user_profiles
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER user_profiles_bu
BEFORE UPDATE ON user_profiles
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER user_roles_bi
BEFORE INSERT ON user_roles
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER user_roles_bu
BEFORE UPDATE ON user_roles
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER books_bi
BEFORE INSERT ON books
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER books_bu
BEFORE UPDATE ON books
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER book_editions_bi
BEFORE INSERT ON book_editions
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER book_editions_bu
BEFORE UPDATE ON book_editions
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER book_copies_bi
BEFORE INSERT ON book_copies
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER book_copies_bu
BEFORE UPDATE ON book_copies
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER categories_bi
BEFORE INSERT ON categories
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER categories_bu
BEFORE UPDATE ON categories
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER book_categories_bi
BEFORE INSERT ON book_categories
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER book_categories_bu
BEFORE UPDATE ON book_categories
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER loans_bi
BEFORE INSERT ON loans
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER loans_bu
BEFORE UPDATE ON loans
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER reservations_bi
BEFORE INSERT ON reservations
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER reservations_bu
BEFORE UPDATE ON reservations
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER returns_bi
BEFORE INSERT ON returns
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER returns_bu
BEFORE UPDATE ON returns
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER fines_bi
BEFORE INSERT ON fines
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER fines_bu
BEFORE UPDATE ON fines
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER payments_bi
BEFORE INSERT ON payments
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER payments_bu
BEFORE UPDATE ON payments
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER fine_waivers_bi
BEFORE INSERT ON fine_waivers
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER fine_waivers_bu
BEFORE UPDATE ON fine_waivers
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER digital_resources_bi
BEFORE INSERT ON digital_resources
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER digital_resources_bu
BEFORE UPDATE ON digital_resources
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER digital_files_bi
BEFORE INSERT ON digital_files
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER digital_files_bu
BEFORE UPDATE ON digital_files
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER library_policies_bi
BEFORE INSERT ON library_policies
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER library_policies_bu
BEFORE UPDATE ON library_policies
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER policy_changes_bi
BEFORE INSERT ON policy_changes
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER policy_changes_bu
BEFORE UPDATE ON policy_changes
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER holidays_bi
BEFORE INSERT ON holidays
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER holidays_bu
BEFORE UPDATE ON holidays
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER notifications_bi
BEFORE INSERT ON notifications
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER notifications_bu
BEFORE UPDATE ON notifications
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER announcements_bi
BEFORE INSERT ON announcements
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER announcements_bu
BEFORE UPDATE ON announcements
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER audit_logs_bi
BEFORE INSERT ON audit_logs
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER audit_logs_bu
BEFORE UPDATE ON audit_logs
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER system_settings_bi
BEFORE INSERT ON system_settings
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER system_settings_bu
BEFORE UPDATE ON system_settings
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$


CREATE TRIGGER backups_bi
BEFORE INSERT ON backups
FOR EACH ROW
BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

CREATE TRIGGER backups_bu
BEFORE UPDATE ON backups
FOR EACH ROW
BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END$$

DELIMITER ;
