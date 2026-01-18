-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 18, 2026 at 09:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `announcements`
--
DELIMITER $$
CREATE TRIGGER `announcements_bi` BEFORE INSERT ON `announcements` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `announcements_bu` BEFORE UPDATE ON `announcements` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `target_table` varchar(255) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `audit_logs`
--
DELIMITER $$
CREATE TRIGGER `audit_logs_bi` BEFORE INSERT ON `audit_logs` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `audit_logs_bu` BEFORE UPDATE ON `audit_logs` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `backups`
--

CREATE TABLE `backups` (
  `backup_id` int(11) NOT NULL,
  `backup_date` datetime DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `backups`
--
DELIMITER $$
CREATE TRIGGER `backups_bi` BEFORE INSERT ON `backups` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `backups_bu` BEFORE UPDATE ON `backups` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `publication_year` int(11) DEFAULT NULL,
  `book_cover_path` varchar(255) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `books`
--
DELIMITER $$
CREATE TRIGGER `books_bi` BEFORE INSERT ON `books` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `books_bu` BEFORE UPDATE ON `books` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `book_categories`
--

CREATE TABLE `book_categories` (
  `book_cat_id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `book_categories`
--
DELIMITER $$
CREATE TRIGGER `book_categories_bi` BEFORE INSERT ON `book_categories` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `book_categories_bu` BEFORE UPDATE ON `book_categories` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `book_copies`
--

CREATE TABLE `book_copies` (
  `copy_id` int(11) NOT NULL,
  `edition_id` int(11) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `book_copies`
--
DELIMITER $$
CREATE TRIGGER `book_copies_bi` BEFORE INSERT ON `book_copies` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `book_copies_bu` BEFORE UPDATE ON `book_copies` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `book_editions`
--

CREATE TABLE `book_editions` (
  `edition_id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `edition_number` int(11) DEFAULT NULL,
  `publication_year` int(11) DEFAULT NULL,
  `pages` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `book_editions`
--
DELIMITER $$
CREATE TRIGGER `book_editions_bi` BEFORE INSERT ON `book_editions` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `book_editions_bu` BEFORE UPDATE ON `book_editions` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `categories`
--
DELIMITER $$
CREATE TRIGGER `categories_bi` BEFORE INSERT ON `categories` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `categories_bu` BEFORE UPDATE ON `categories` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `digital_files`
--

CREATE TABLE `digital_files` (
  `file_id` int(11) NOT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `digital_files`
--
DELIMITER $$
CREATE TRIGGER `digital_files_bi` BEFORE INSERT ON `digital_files` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `digital_files_bu` BEFORE UPDATE ON `digital_files` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `digital_resources`
--

CREATE TABLE `digital_resources` (
  `resource_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `digital_resources`
--
DELIMITER $$
CREATE TRIGGER `digital_resources_bi` BEFORE INSERT ON `digital_resources` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `digital_resources_bu` BEFORE UPDATE ON `digital_resources` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

CREATE TABLE `fines` (
  `fine_id` int(11) NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `fine_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `fines`
--
DELIMITER $$
CREATE TRIGGER `fines_bi` BEFORE INSERT ON `fines` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `fines_bu` BEFORE UPDATE ON `fines` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `fine_waivers`
--

CREATE TABLE `fine_waivers` (
  `waiver_id` int(11) NOT NULL,
  `fine_id` int(11) DEFAULT NULL,
  `approved_by` varchar(255) DEFAULT NULL,
  `waiver_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `fine_waivers`
--
DELIMITER $$
CREATE TRIGGER `fine_waivers_bi` BEFORE INSERT ON `fine_waivers` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `fine_waivers_bu` BEFORE UPDATE ON `fine_waivers` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `holiday_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `holidays`
--
DELIMITER $$
CREATE TRIGGER `holidays_bi` BEFORE INSERT ON `holidays` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `holidays_bu` BEFORE UPDATE ON `holidays` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `library_policies`
--

CREATE TABLE `library_policies` (
  `policy_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `library_policies`
--
DELIMITER $$
CREATE TRIGGER `library_policies_bi` BEFORE INSERT ON `library_policies` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `library_policies_bu` BEFORE UPDATE ON `library_policies` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `loan_id` int(11) NOT NULL,
  `copy_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `loans`
--
DELIMITER $$
CREATE TRIGGER `loans_bi` BEFORE INSERT ON `loans` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `loans_bu` BEFORE UPDATE ON `loans` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `read_status` tinyint(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `notifications`
--
DELIMITER $$
CREATE TRIGGER `notifications_bi` BEFORE INSERT ON `notifications` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `notifications_bu` BEFORE UPDATE ON `notifications` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `fine_id` int(11) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `payments`
--
DELIMITER $$
CREATE TRIGGER `payments_bi` BEFORE INSERT ON `payments` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `payments_bu` BEFORE UPDATE ON `payments` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `policy_changes`
--

CREATE TABLE `policy_changes` (
  `change_id` int(11) NOT NULL,
  `policy_id` int(11) DEFAULT NULL,
  `proposed_by` varchar(255) DEFAULT NULL,
  `proposal_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `policy_changes`
--
DELIMITER $$
CREATE TRIGGER `policy_changes_bi` BEFORE INSERT ON `policy_changes` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `policy_changes_bu` BEFORE UPDATE ON `policy_changes` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `copy_id` int(11) DEFAULT NULL,
  `reservation_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `reservations`
--
DELIMITER $$
CREATE TRIGGER `reservations_bi` BEFORE INSERT ON `reservations` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `reservations_bu` BEFORE UPDATE ON `reservations` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `return_id` int(11) NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `returns`
--
DELIMITER $$
CREATE TRIGGER `returns_bi` BEFORE INSERT ON `returns` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `returns_bu` BEFORE UPDATE ON `returns` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `roles`
--
DELIMITER $$
CREATE TRIGGER `roles_bi` BEFORE INSERT ON `roles` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `roles_bu` BEFORE UPDATE ON `roles` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `system_settings`
--
DELIMITER $$
CREATE TRIGGER `system_settings_bi` BEFORE INSERT ON `system_settings` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `system_settings_bu` BEFORE UPDATE ON `system_settings` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `users_bi` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `users_bu` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `institution_name` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `user_profiles`
--
DELIMITER $$
CREATE TRIGGER `user_profiles_bi` BEFORE INSERT ON `user_profiles` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `user_profiles_bu` BEFORE UPDATE ON `user_profiles` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_role_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `user_roles`
--
DELIMITER $$
CREATE TRIGGER `user_roles_bi` BEFORE INSERT ON `user_roles` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `user_roles_bu` BEFORE UPDATE ON `user_roles` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `backups`
--
ALTER TABLE `backups`
  ADD PRIMARY KEY (`backup_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `book_categories`
--
ALTER TABLE `book_categories`
  ADD PRIMARY KEY (`book_cat_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `book_copies`
--
ALTER TABLE `book_copies`
  ADD PRIMARY KEY (`copy_id`),
  ADD KEY `edition_id` (`edition_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `book_editions`
--
ALTER TABLE `book_editions`
  ADD PRIMARY KEY (`edition_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `digital_files`
--
ALTER TABLE `digital_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `resource_id` (`resource_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `digital_resources`
--
ALTER TABLE `digital_resources`
  ADD PRIMARY KEY (`resource_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `fines`
--
ALTER TABLE `fines`
  ADD PRIMARY KEY (`fine_id`),
  ADD KEY `loan_id` (`loan_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `fine_waivers`
--
ALTER TABLE `fine_waivers`
  ADD PRIMARY KEY (`waiver_id`),
  ADD KEY `fine_id` (`fine_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`holiday_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `library_policies`
--
ALTER TABLE `library_policies`
  ADD PRIMARY KEY (`policy_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `copy_id` (`copy_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fine_id` (`fine_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `policy_changes`
--
ALTER TABLE `policy_changes`
  ADD PRIMARY KEY (`change_id`),
  ADD KEY `policy_id` (`policy_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `copy_id` (`copy_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `loan_id` (`loan_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_role_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `backups`
--
ALTER TABLE `backups`
  MODIFY `backup_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `book_categories`
--
ALTER TABLE `book_categories`
  MODIFY `book_cat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book_copies`
--
ALTER TABLE `book_copies`
  MODIFY `copy_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book_editions`
--
ALTER TABLE `book_editions`
  MODIFY `edition_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `digital_files`
--
ALTER TABLE `digital_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `digital_resources`
--
ALTER TABLE `digital_resources`
  MODIFY `resource_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fines`
--
ALTER TABLE `fines`
  MODIFY `fine_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fine_waivers`
--
ALTER TABLE `fine_waivers`
  MODIFY `waiver_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `holiday_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `library_policies`
--
ALTER TABLE `library_policies`
  MODIFY `policy_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `loan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `policy_changes`
--
ALTER TABLE `policy_changes`
  MODIFY `change_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `return_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `user_role_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `announcements_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `audit_logs_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `audit_logs_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `audit_logs_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `backups`
--
ALTER TABLE `backups`
  ADD CONSTRAINT `backups_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `backups_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `backups_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `books_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `book_categories`
--
ALTER TABLE `book_categories`
  ADD CONSTRAINT `book_categories_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `book_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `book_categories_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `book_categories_ibfk_4` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `book_categories_ibfk_5` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `book_copies`
--
ALTER TABLE `book_copies`
  ADD CONSTRAINT `book_copies_ibfk_1` FOREIGN KEY (`edition_id`) REFERENCES `book_editions` (`edition_id`),
  ADD CONSTRAINT `book_copies_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `book_copies_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `book_copies_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `book_editions`
--
ALTER TABLE `book_editions`
  ADD CONSTRAINT `book_editions_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `book_editions_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `book_editions_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `book_editions_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `categories_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `categories_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `digital_files`
--
ALTER TABLE `digital_files`
  ADD CONSTRAINT `digital_files_ibfk_1` FOREIGN KEY (`resource_id`) REFERENCES `digital_resources` (`resource_id`),
  ADD CONSTRAINT `digital_files_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `digital_files_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `digital_files_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `digital_resources`
--
ALTER TABLE `digital_resources`
  ADD CONSTRAINT `digital_resources_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `digital_resources_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `digital_resources_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `fines`
--
ALTER TABLE `fines`
  ADD CONSTRAINT `fines_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`loan_id`),
  ADD CONSTRAINT `fines_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fines_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fines_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `fine_waivers`
--
ALTER TABLE `fine_waivers`
  ADD CONSTRAINT `fine_waivers_ibfk_1` FOREIGN KEY (`fine_id`) REFERENCES `fines` (`fine_id`),
  ADD CONSTRAINT `fine_waivers_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fine_waivers_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fine_waivers_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `holidays`
--
ALTER TABLE `holidays`
  ADD CONSTRAINT `holidays_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `holidays_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `holidays_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `library_policies`
--
ALTER TABLE `library_policies`
  ADD CONSTRAINT `library_policies_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `library_policies_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `library_policies_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`copy_id`) REFERENCES `book_copies` (`copy_id`),
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `loans_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `loans_ibfk_4` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `loans_ibfk_5` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `notifications_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`fine_id`) REFERENCES `fines` (`fine_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `payments_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `policy_changes`
--
ALTER TABLE `policy_changes`
  ADD CONSTRAINT `policy_changes_ibfk_1` FOREIGN KEY (`policy_id`) REFERENCES `library_policies` (`policy_id`),
  ADD CONSTRAINT `policy_changes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `policy_changes_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `policy_changes_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`copy_id`) REFERENCES `book_copies` (`copy_id`),
  ADD CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reservations_ibfk_4` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reservations_ibfk_5` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `returns`
--
ALTER TABLE `returns`
  ADD CONSTRAINT `returns_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`loan_id`),
  ADD CONSTRAINT `returns_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `returns_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `returns_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `roles_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `roles_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `system_settings_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `system_settings_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_profiles_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_profiles_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_profiles_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `user_roles_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_roles_ibfk_4` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_roles_ibfk_5` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
