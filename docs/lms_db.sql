-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 27, 2026 at 05:19 AM
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
  `time_stamp` datetime DEFAULT NULL,
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
  `book_excerpt` text NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `publication_year` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `book_cover_path` varchar(255) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `book_excerpt`, `author`, `isbn`, `publisher`, `publication_year`, `category_id`, `book_cover_path`, `created_by`, `created_date`, `modified_by`, `modified_date`, `deleted_by`, `deleted_date`) VALUES
(1, 'Digital Fortress', 'Introduces Susan Fletcher, the NSA\'s head cryptographer, whose romantic getaway plans are shattered by a sudden, urgent work call.', 'Dan Brown', '0-312-18087-X (US)', 'St. Martin\'s Press', 1998, 1, 'uploads/book_cover/1769333690_Dan-Brown_Digital-Fortress_book-cover-2025.jpg', NULL, '2026-01-25 15:34:50', NULL, '2026-01-25 15:34:50', NULL, NULL);

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
-- Dumping data for table `book_copies`
--

INSERT INTO `book_copies` (`copy_id`, `edition_id`, `barcode`, `status`, `location`, `created_by`, `created_date`, `modified_by`, `modified_date`, `deleted_by`, `deleted_date`) VALUES
(1, 1, 'B1-E1-20260126185952-1', 'hold_loan', NULL, NULL, '2026-01-26 23:59:52', 1, '2026-01-27 00:00:28', NULL, NULL),
(2, 1, 'B1-E1-20260126185952-2', 'available', NULL, NULL, '2026-01-26 23:59:52', NULL, '2026-01-26 23:59:52', NULL, NULL),
(3, 1, 'B1-E1-20260126185952-3', 'available', NULL, NULL, '2026-01-26 23:59:52', NULL, '2026-01-26 23:59:52', NULL, NULL),
(4, 1, 'B1-E1-20260126185952-4', 'available', NULL, NULL, '2026-01-26 23:59:52', NULL, '2026-01-26 23:59:52', NULL, NULL),
(5, 1, 'B1-E1-20260126185952-5', 'available', NULL, NULL, '2026-01-26 23:59:52', NULL, '2026-01-26 23:59:52', NULL, NULL),
(6, 1, 'B1-E1-20260126185952-6', 'available', NULL, NULL, '2026-01-26 23:59:52', NULL, '2026-01-26 23:59:52', NULL, NULL),
(7, 1, 'B1-E1-20260126185952-7', 'available', NULL, NULL, '2026-01-26 23:59:52', NULL, '2026-01-26 23:59:52', NULL, NULL),
(8, 1, 'B1-E1-20260126185952-8', 'available', NULL, NULL, '2026-01-26 23:59:52', NULL, '2026-01-26 23:59:52', NULL, NULL),
(9, 1, 'B1-E1-20260126185952-9', 'available', NULL, NULL, '2026-01-26 23:59:52', NULL, '2026-01-26 23:59:52', NULL, NULL);

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
-- Dumping data for table `book_editions`
--

INSERT INTO `book_editions` (`edition_id`, `book_id`, `edition_number`, `publication_year`, `pages`, `created_by`, `created_date`, `modified_by`, `modified_date`, `deleted_by`, `deleted_date`) VALUES
(1, 1, 1, 1998, NULL, NULL, '2026-01-26 23:59:52', NULL, '2026-01-26 23:59:52', NULL, NULL);

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
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `created_by`, `created_date`, `modified_by`, `modified_date`, `deleted_by`, `deleted_date`) VALUES
(1, 'Thriller', NULL, '2026-01-25 15:28:37', NULL, '2026-01-25 15:28:37', NULL, NULL);

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
  `policy_value` int(11) DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `library_policies`
--

INSERT INTO `library_policies` (`policy_id`, `name`, `description`, `policy_value`, `effective_date`, `created_by`, `created_date`, `modified_by`, `modified_date`, `deleted_by`, `deleted_date`) VALUES
(1, 'loan_period_days', '14', 14, '2026-01-27', NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04', NULL, NULL),
(2, 'reservation_expiry_days', '3', 3, '2026-01-27', NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04', NULL, NULL);

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
  `deleted_date` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `remarks` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loan_id`, `copy_id`, `user_id`, `issue_date`, `due_date`, `return_date`, `created_by`, `created_date`, `modified_by`, `modified_date`, `deleted_by`, `deleted_date`, `status`, `remarks`) VALUES
(1, 1, 1, NULL, NULL, NULL, 1, '2026-01-27 00:00:28', 1, '2026-01-27 00:00:28', NULL, NULL, 'pending', '');

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
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `menu_id` int(11) NOT NULL,
  `menu_title` varchar(255) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `menu_order` int(11) DEFAULT 0,
  `icon` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `read_status` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `title`, `message`, `created_at`, `read_status`, `created_by`, `created_date`, `modified_by`, `modified_date`, `deleted_by`, `deleted_date`) VALUES
(1, 1, 'Loan request submitted', 'Your loan request for \"Digital Fortress\" has been submitted.', '2026-01-27 00:00:28', 0, 1, '2026-01-27 00:00:28', 1, '2026-01-27 00:00:28', NULL, NULL),
(2, 1, 'New loan request', 'A new loan request was submitted for \"Digital Fortress\".', '2026-01-27 00:00:28', 0, 1, '2026-01-27 00:00:28', 1, '2026-01-27 00:00:28', NULL, NULL),
(3, 3, 'New loan request', 'A new loan request was submitted for \"Digital Fortress\".', '2026-01-27 00:00:28', 0, 1, '2026-01-27 00:00:28', 1, '2026-01-27 00:00:28', NULL, NULL);

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
-- Table structure for table `page_list`
--

CREATE TABLE `page_list` (
  `page_id` int(11) NOT NULL,
  `page_name` varchar(255) DEFAULT NULL,
  `page_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_list`
--

INSERT INTO `page_list` (`page_id`, `page_name`, `page_path`, `is_active`, `created_by`, `created_date`, `modified_by`, `modified_date`, `deleted_by`, `deleted_date`) VALUES
(52, 'Announcement List', 'announcement_list.php', 1, NULL, '2026-01-26 16:14:56', NULL, '2026-01-26 16:14:56', NULL, NULL),
(53, 'Audit Log List', 'audit_log_list.php', 1, NULL, '2026-01-26 16:14:56', NULL, '2026-01-26 16:14:56', NULL, NULL),
(54, 'Backup List', 'backup_list.php', 1, NULL, '2026-01-26 16:14:56', NULL, '2026-01-26 16:14:56', NULL, NULL),
(55, 'Book Details', 'book-details.php', 1, NULL, '2026-01-26 16:14:56', NULL, '2026-01-26 16:14:56', NULL, NULL),
(56, 'Book Category List', 'book_category_list.php', 1, NULL, '2026-01-26 16:14:56', NULL, '2026-01-26 16:14:56', NULL, NULL),
(57, 'Book Copy List', 'book_copy_list.php', 1, NULL, '2026-01-26 16:14:56', NULL, '2026-01-26 16:14:56', NULL, NULL),
(58, 'Book Edition List', 'book_edition_list.php', 1, NULL, '2026-01-26 16:14:56', NULL, '2026-01-26 16:14:56', NULL, NULL),
(59, 'Book List', 'book_list.php', 1, NULL, '2026-01-26 16:14:56', NULL, '2026-01-26 16:14:56', NULL, NULL),
(60, 'Bookloader', 'bookloader.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(61, 'Category List', 'category_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(62, 'Crud Check', 'crud_check.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(63, 'Dashboard', 'dashboard.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(64, 'Data View', 'data_view.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(65, 'Designation', 'designation.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(66, 'Digital File List', 'digital_file_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(67, 'Digital Resource List', 'digital_resource_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(68, 'Edit Profile', 'edit_profile.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(69, 'Erd', 'erd.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(70, 'Fine List', 'fine_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(71, 'Fine Waiver List', 'fine_waiver_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(72, 'Holiday List', 'holiday_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(73, 'Home', 'home.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(74, 'Index', 'index.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(75, 'Library Policy List', 'library_policy_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(76, 'Library Rbac Matrix', 'library_rbac_matrix.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(77, 'Loan List', 'loan_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(78, 'Login', 'login.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(79, 'Logout', 'logout.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(80, 'Manage User Role', 'manage_user_role.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(81, 'Notification List', 'notification_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(82, 'Payment List', 'payment_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(83, 'Permission Managemnt', 'permission_managemnt.php', 0, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 17:09:31', NULL, NULL),
(84, 'Policy Change List', 'policy_change_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(85, 'Register', 'register.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(86, 'Reservation List', 'reservation_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(87, 'Return List', 'return_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(88, 'Role List', 'role_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(89, 'Sidebar', 'sidebar.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(90, 'System Setting List', 'system_setting_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(91, 'Home', 'system_settings/home.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(92, 'Index', 'system_settings/index.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(93, 'Sidebar', 'system_settings/sidebar.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(94, 'Blank Page', 'templates/blank_page.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(95, 'Create Or Add Page', 'templates/create_or_add_page.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(96, 'Delete Page', 'templates/delete_page.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(97, 'Edit Or Update Page', 'templates/edit_or_update_page.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(98, 'Read Or View Page', 'templates/read_or_view_page.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(99, 'User List', 'user_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(100, 'User Profile List', 'user_profile_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(101, 'User Role List', 'user_role_list.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(102, 'View Profile', 'view_profile.php', 1, NULL, '2026-01-26 16:14:57', NULL, '2026-01-26 16:14:57', NULL, NULL),
(103, 'Permission Management', 'permission_management.php', 1, NULL, '2026-01-26 17:09:31', NULL, '2026-01-26 17:09:31', NULL, NULL),
(104, 'User Dashboard', 'user_dashboard.php', 1, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04', NULL, NULL),
(105, 'Request Loan', 'actions/request_loan.php', 1, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04', NULL, NULL),
(106, 'Request Reservation', 'actions/request_reservation.php', 1, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04', NULL, NULL),
(107, 'Request Return', 'actions/request_return.php', 1, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04', NULL, NULL),
(108, 'Process Loan', 'actions/admin_process_loan.php', 1, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04', NULL, NULL),
(109, 'Process Reservation', 'actions/admin_process_reservation.php', 1, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04', NULL, NULL),
(110, 'Process Return', 'actions/admin_process_return.php', 1, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04', NULL, NULL);

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
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `can_read` tinyint(1) NOT NULL DEFAULT 0,
  `can_write` tinyint(1) NOT NULL DEFAULT 0,
  `deny` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `role_id`, `page_id`, `can_read`, `can_write`, `deny`, `created_by`, `created_date`, `modified_by`, `modified_date`) VALUES
(1, 1, 52, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(2, 1, 53, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(3, 1, 54, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(4, 1, 94, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(5, 1, 56, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(6, 1, 57, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(7, 1, 55, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(8, 1, 58, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(9, 1, 59, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(10, 1, 60, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(11, 1, 61, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(12, 1, 95, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(13, 1, 62, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(14, 1, 63, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(15, 1, 64, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(16, 1, 96, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(17, 1, 65, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(18, 1, 66, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(19, 1, 67, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(20, 1, 97, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(21, 1, 68, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(22, 1, 69, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(23, 1, 70, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(24, 1, 71, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(25, 1, 72, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(26, 1, 73, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(27, 1, 91, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(28, 1, 92, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(29, 1, 75, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(30, 1, 76, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(31, 1, 77, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(32, 1, 80, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(33, 1, 81, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(34, 1, 82, 1, 1, 0, NULL, '2026-01-26 16:17:37', NULL, '2026-01-26 16:17:37'),
(35, 1, 83, 1, 1, 0, NULL, '2026-01-26 16:17:38', NULL, '2026-01-26 16:17:38'),
(36, 1, 84, 1, 1, 0, NULL, '2026-01-26 16:17:38', NULL, '2026-01-26 16:17:38'),
(37, 1, 98, 1, 1, 0, NULL, '2026-01-26 16:17:38', NULL, '2026-01-26 16:17:38'),
(38, 1, 86, 1, 1, 0, NULL, '2026-01-26 16:17:38', NULL, '2026-01-26 16:17:38'),
(39, 1, 87, 1, 1, 0, NULL, '2026-01-26 16:17:38', NULL, '2026-01-26 16:17:38'),
(40, 1, 88, 1, 1, 0, NULL, '2026-01-26 16:17:38', NULL, '2026-01-26 16:17:38'),
(41, 1, 89, 1, 1, 0, NULL, '2026-01-26 16:17:38', NULL, '2026-01-26 16:17:38'),
(42, 1, 93, 1, 1, 0, NULL, '2026-01-26 16:17:38', NULL, '2026-01-26 16:17:38'),
(43, 1, 90, 1, 1, 0, NULL, '2026-01-26 16:17:38', NULL, '2026-01-26 16:17:38'),
(44, 1, 99, 1, 1, 0, NULL, '2026-01-26 16:17:38', NULL, '2026-01-26 16:17:38'),
(45, 1, 100, 1, 1, 0, NULL, '2026-01-26 16:17:38', NULL, '2026-01-26 16:17:38'),
(46, 1, 101, 1, 1, 0, NULL, '2026-01-26 16:17:38', NULL, '2026-01-26 16:17:38'),
(47, 1, 102, 1, 1, 0, NULL, '2026-01-26 16:17:38', NULL, '2026-01-26 16:17:38'),
(95, 3, 52, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(96, 3, 53, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(97, 3, 54, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(98, 3, 94, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(99, 3, 56, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(100, 3, 57, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(101, 3, 55, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(102, 3, 58, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(103, 3, 59, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(104, 3, 60, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(105, 3, 61, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(106, 3, 95, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(107, 3, 62, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(108, 3, 63, 1, 0, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(109, 3, 64, 1, 0, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(110, 3, 96, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(111, 3, 65, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(112, 3, 66, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(113, 3, 67, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(114, 3, 97, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(115, 3, 68, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(116, 3, 69, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(117, 3, 70, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(118, 3, 71, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(119, 3, 72, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(120, 3, 73, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(121, 3, 91, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(122, 3, 92, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(123, 3, 75, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(124, 3, 76, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(125, 3, 77, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(126, 3, 80, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(127, 3, 81, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(128, 3, 82, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(129, 3, 83, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(130, 3, 84, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(131, 3, 98, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(132, 3, 86, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(133, 3, 87, 1, 1, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(134, 3, 88, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(135, 3, 89, 1, 0, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(136, 3, 93, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(137, 3, 90, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(138, 3, 99, 1, 0, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(139, 3, 100, 1, 0, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(140, 3, 101, 0, 0, 1, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(141, 3, 102, 1, 0, 0, NULL, '2026-01-26 16:23:57', NULL, '2026-01-26 16:23:57'),
(189, 2, 52, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(190, 2, 53, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(191, 2, 54, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(192, 2, 94, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(193, 2, 56, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(194, 2, 57, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(195, 2, 55, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(196, 2, 58, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(197, 2, 59, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(198, 2, 60, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(199, 2, 61, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(200, 2, 95, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(201, 2, 62, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(202, 2, 63, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(203, 2, 64, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(204, 2, 96, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(205, 2, 65, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(206, 2, 66, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(207, 2, 67, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(208, 2, 97, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(209, 2, 68, 1, 1, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(210, 2, 69, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(211, 2, 70, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(212, 2, 71, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(213, 2, 72, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(214, 2, 73, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(215, 2, 91, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(216, 2, 92, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(217, 2, 75, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(218, 2, 76, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(219, 2, 77, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(220, 2, 80, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(221, 2, 81, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(222, 2, 82, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(223, 2, 83, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(224, 2, 84, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(225, 2, 98, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(226, 2, 86, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(227, 2, 87, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(228, 2, 88, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(229, 2, 89, 1, 0, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(230, 2, 93, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(231, 2, 90, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(232, 2, 99, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(233, 2, 100, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(234, 2, 101, 0, 0, 1, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(235, 2, 102, 1, 1, 0, NULL, '2026-01-26 16:27:42', NULL, '2026-01-26 16:27:42'),
(236, 1, 104, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(237, 3, 104, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(238, 1, 105, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(239, 3, 105, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(240, 1, 106, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(241, 3, 106, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(242, 1, 107, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(243, 3, 107, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(244, 1, 108, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(245, 3, 108, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(246, 1, 109, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(247, 3, 109, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(248, 1, 110, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(249, 3, 110, 1, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(251, 2, 104, 1, 0, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(252, 2, 105, 0, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(253, 2, 106, 0, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04'),
(254, 2, 107, 0, 1, 0, NULL, '2026-01-27 00:38:04', NULL, '2026-01-27 00:38:04');

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
  `book_id` int(11) DEFAULT NULL,
  `reservation_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL
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
  `deleted_date` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL
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
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `created_by`, `created_date`, `modified_by`, `modified_date`, `deleted_by`, `deleted_date`) VALUES
(1, 'Admin', NULL, '2026-01-26 12:33:40', NULL, '2026-01-26 12:33:40', NULL, NULL),
(2, 'User', NULL, '2026-01-26 12:44:16', NULL, '2026-01-26 12:44:16', NULL, NULL),
(3, 'Librarian', NULL, '2026-01-26 12:44:21', NULL, '2026-01-26 12:44:21', NULL, NULL);

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
  `username` varchar(100) NOT NULL,
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `created_by`, `created_date`, `modified_by`, `modified_date`, `deleted_by`, `deleted_date`) VALUES
(1, 'lms_admin', 'lms_admin@karigori.site', '$2y$10$Qcs6B9zb2/wO3kNz8rK1oeYP0D7at6dCdsBh.0kDUS/J5K56rES1i', NULL, '2026-01-26 09:31:29', NULL, '2026-01-26 09:31:29', NULL, NULL),
(2, 'kasem', 'kasem@karigori.site', '$2y$10$K0I/E4BgJxRnIyXZDTbLIujo/y2SETklrsCjvlt63DFmkAb9T0VK6', NULL, '2026-01-26 12:29:04', NULL, '2026-01-26 12:29:04', NULL, NULL),
(3, 'tahmid', 'tahmid@karigori.site', '$2y$10$gnvL2hZoxmqE17G/v4yYPOf3yPU4dEiIxGwrhN6IVBF429CcyMvnK', NULL, '2026-01-26 12:29:45', NULL, '2026-01-26 12:29:45', NULL, NULL);

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
  `institution_name` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`profile_id`, `user_id`, `first_name`, `last_name`, `dob`, `address`, `phone`, `institution_name`, `designation`, `profile_picture`, `created_by`, `created_date`, `modified_by`, `modified_date`, `deleted_by`, `deleted_date`) VALUES
(1, 1, 'LMS', 'Admin', '2024-01-01', ';saijf;adsjf;asj;ajd;aj;', '1234567890', 'lms inc.', 'Admin', 'uploads/profile_picture/1769403805_avatar.png', NULL, '2026-01-26 11:03:25', NULL, '2026-01-26 12:04:04', NULL, NULL),
(2, 3, 'Tahmid', 'Shaheer', '2026-01-26', NULL, '1234567890', 'lms inc.', 'Librarian', 'uploads/profile_picture/1769424080_avatar.png', NULL, '2026-01-26 16:41:20', NULL, '2026-01-26 16:41:20', NULL, NULL),
(3, 2, 'Abul', 'kasem', '2026-01-26', NULL, '1234567890', 'lms inc.', 'user', 'uploads/profile_picture/1769424167_avatar.png', NULL, '2026-01-26 16:42:47', NULL, '2026-01-26 16:42:47', NULL, NULL);

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
  `username` varchar(150) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `role_name` varchar(150) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_role_id`, `user_id`, `username`, `role_id`, `role_name`, `created_by`, `created_date`, `modified_by`, `modified_date`, `deleted_by`, `deleted_date`) VALUES
(1, 3, 'tahmid', 3, 'Librarian', NULL, '2026-01-26 14:27:25', NULL, '2026-01-26 14:27:33', NULL, NULL),
(2, 2, 'kasem', 2, 'User', NULL, '2026-01-26 14:27:40', NULL, '2026-01-26 14:27:40', NULL, NULL),
(3, 1, 'lms_admin', 1, 'Admin', NULL, '2026-01-26 16:13:09', NULL, '2026-01-26 16:13:09', NULL, NULL);

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
  ADD KEY `deleted_by` (`deleted_by`),
  ADD KEY `books_ibfk_4` (`category_id`);

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
  ADD KEY `deleted_by` (`deleted_by`),
  ADD KEY `idx_loans_user_status` (`user_id`,`status`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`menu_id`),
  ADD KEY `page_id` (`page_id`);

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
-- Indexes for table `page_list`
--
ALTER TABLE `page_list`
  ADD PRIMARY KEY (`page_id`);

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
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `uniq_role_page` (`role_id`,`page_id`),
  ADD KEY `idx_role` (`role_id`),
  ADD KEY `idx_page` (`page_id`);

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
  ADD KEY `deleted_by` (`deleted_by`),
  ADD KEY `idx_reservations_user_status` (`user_id`,`status`),
  ADD KEY `idx_reservations_book_status` (`book_id`,`status`,`created_date`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `loan_id` (`loan_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `deleted_by` (`deleted_by`),
  ADD KEY `idx_returns_status` (`status`);

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
  ADD UNIQUE KEY `uniq_user_id` (`user_id`),
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
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `book_categories`
--
ALTER TABLE `book_categories`
  MODIFY `book_cat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book_copies`
--
ALTER TABLE `book_copies`
  MODIFY `copy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `book_editions`
--
ALTER TABLE `book_editions`
  MODIFY `edition_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `policy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `loan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `page_list`
--
ALTER TABLE `page_list`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=258;

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
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `user_role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  ADD CONSTRAINT `books_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `books_ibfk_4` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

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
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `page_list` (`page_id`);

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
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_page_fk` FOREIGN KEY (`page_id`) REFERENCES `page_list` (`page_id`),
  ADD CONSTRAINT `permissions_role_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

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
  ADD CONSTRAINT `reservations_book_fk` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
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
