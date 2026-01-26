-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: lms_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `lms_db`
--

/*!40000 DROP DATABASE IF EXISTS `lms_db`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `lms_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `lms_db`;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`announcement_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `announcements_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `announcements_bi` BEFORE INSERT ON `announcements` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `announcements_bu` BEFORE UPDATE ON `announcements` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `audit_logs_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `audit_logs_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `audit_logs_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `audit_logs_bi` BEFORE INSERT ON `audit_logs` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `audit_logs_bu` BEFORE UPDATE ON `audit_logs` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `backups`
--

DROP TABLE IF EXISTS `backups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backups` (
  `backup_id` int(11) NOT NULL AUTO_INCREMENT,
  `backup_date` datetime DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`backup_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `backups_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `backups_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `backups_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backups`
--

LOCK TABLES `backups` WRITE;
/*!40000 ALTER TABLE `backups` DISABLE KEYS */;
/*!40000 ALTER TABLE `backups` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `backups_bi` BEFORE INSERT ON `backups` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `backups_bu` BEFORE UPDATE ON `backups` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `book_categories`
--

DROP TABLE IF EXISTS `book_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_categories` (
  `book_cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`book_cat_id`),
  KEY `book_id` (`book_id`),
  KEY `category_id` (`category_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `book_categories_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  CONSTRAINT `book_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  CONSTRAINT `book_categories_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `book_categories_ibfk_4` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `book_categories_ibfk_5` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_categories`
--

LOCK TABLES `book_categories` WRITE;
/*!40000 ALTER TABLE `book_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `book_categories` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `book_categories_bi` BEFORE INSERT ON `book_categories` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `book_categories_bu` BEFORE UPDATE ON `book_categories` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `book_copies`
--

DROP TABLE IF EXISTS `book_copies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_copies` (
  `copy_id` int(11) NOT NULL AUTO_INCREMENT,
  `edition_id` int(11) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`copy_id`),
  KEY `edition_id` (`edition_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `book_copies_ibfk_1` FOREIGN KEY (`edition_id`) REFERENCES `book_editions` (`edition_id`),
  CONSTRAINT `book_copies_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `book_copies_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `book_copies_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_copies`
--

LOCK TABLES `book_copies` WRITE;
/*!40000 ALTER TABLE `book_copies` DISABLE KEYS */;
/*!40000 ALTER TABLE `book_copies` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `book_copies_bi` BEFORE INSERT ON `book_copies` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `book_copies_bu` BEFORE UPDATE ON `book_copies` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `book_editions`
--

DROP TABLE IF EXISTS `book_editions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_editions` (
  `edition_id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) DEFAULT NULL,
  `edition_number` int(11) DEFAULT NULL,
  `publication_year` int(11) DEFAULT NULL,
  `pages` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`edition_id`),
  KEY `book_id` (`book_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `book_editions_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  CONSTRAINT `book_editions_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `book_editions_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `book_editions_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_editions`
--

LOCK TABLES `book_editions` WRITE;
/*!40000 ALTER TABLE `book_editions` DISABLE KEYS */;
/*!40000 ALTER TABLE `book_editions` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `book_editions_bi` BEFORE INSERT ON `book_editions` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `book_editions_bu` BEFORE UPDATE ON `book_editions` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `books` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`book_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  KEY `books_ibfk_4` (`category_id`),
  CONSTRAINT `books_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `books_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `books_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `books_ibfk_4` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` VALUES (1,'Digital Fortress','Introduces Susan Fletcher, the NSA\'s head cryptographer, whose romantic getaway plans are shattered by a sudden, urgent work call.','Dan Brown','0-312-18087-X (US)','St. Martin\'s Press',1998,1,'uploads/book_cover/1769333690_Dan-Brown_Digital-Fortress_book-cover-2025.jpg',NULL,'2026-01-25 15:34:50',NULL,'2026-01-25 15:34:50',NULL,NULL);
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `books_bi` BEFORE INSERT ON `books` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `books_bu` BEFORE UPDATE ON `books` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `categories_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `categories_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Thriller',NULL,'2026-01-25 15:28:37',NULL,'2026-01-25 15:28:37',NULL,NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `categories_bi` BEFORE INSERT ON `categories` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `categories_bu` BEFORE UPDATE ON `categories` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `digital_files`
--

DROP TABLE IF EXISTS `digital_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `digital_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`file_id`),
  KEY `resource_id` (`resource_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `digital_files_ibfk_1` FOREIGN KEY (`resource_id`) REFERENCES `digital_resources` (`resource_id`),
  CONSTRAINT `digital_files_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `digital_files_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `digital_files_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `digital_files`
--

LOCK TABLES `digital_files` WRITE;
/*!40000 ALTER TABLE `digital_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `digital_files` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `digital_files_bi` BEFORE INSERT ON `digital_files` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `digital_files_bu` BEFORE UPDATE ON `digital_files` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `digital_resources`
--

DROP TABLE IF EXISTS `digital_resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `digital_resources` (
  `resource_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`resource_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `digital_resources_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `digital_resources_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `digital_resources_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `digital_resources`
--

LOCK TABLES `digital_resources` WRITE;
/*!40000 ALTER TABLE `digital_resources` DISABLE KEYS */;
/*!40000 ALTER TABLE `digital_resources` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `digital_resources_bi` BEFORE INSERT ON `digital_resources` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `digital_resources_bu` BEFORE UPDATE ON `digital_resources` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `fine_waivers`
--

DROP TABLE IF EXISTS `fine_waivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fine_waivers` (
  `waiver_id` int(11) NOT NULL AUTO_INCREMENT,
  `fine_id` int(11) DEFAULT NULL,
  `approved_by` varchar(255) DEFAULT NULL,
  `waiver_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`waiver_id`),
  KEY `fine_id` (`fine_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `fine_waivers_ibfk_1` FOREIGN KEY (`fine_id`) REFERENCES `fines` (`fine_id`),
  CONSTRAINT `fine_waivers_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `fine_waivers_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `fine_waivers_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fine_waivers`
--

LOCK TABLES `fine_waivers` WRITE;
/*!40000 ALTER TABLE `fine_waivers` DISABLE KEYS */;
/*!40000 ALTER TABLE `fine_waivers` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `fine_waivers_bi` BEFORE INSERT ON `fine_waivers` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `fine_waivers_bu` BEFORE UPDATE ON `fine_waivers` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `fines`
--

DROP TABLE IF EXISTS `fines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fines` (
  `fine_id` int(11) NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `fine_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`fine_id`),
  KEY `loan_id` (`loan_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `fines_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`loan_id`),
  CONSTRAINT `fines_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `fines_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `fines_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fines`
--

LOCK TABLES `fines` WRITE;
/*!40000 ALTER TABLE `fines` DISABLE KEYS */;
/*!40000 ALTER TABLE `fines` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `fines_bi` BEFORE INSERT ON `fines` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `fines_bu` BEFORE UPDATE ON `fines` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `holidays`
--

DROP TABLE IF EXISTS `holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holidays` (
  `holiday_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`holiday_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `holidays_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `holidays_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `holidays_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `holidays`
--

LOCK TABLES `holidays` WRITE;
/*!40000 ALTER TABLE `holidays` DISABLE KEYS */;
/*!40000 ALTER TABLE `holidays` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `holidays_bi` BEFORE INSERT ON `holidays` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `holidays_bu` BEFORE UPDATE ON `holidays` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `library_policies`
--

DROP TABLE IF EXISTS `library_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `library_policies` (
  `policy_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`policy_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `library_policies_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `library_policies_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `library_policies_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_policies`
--

LOCK TABLES `library_policies` WRITE;
/*!40000 ALTER TABLE `library_policies` DISABLE KEYS */;
/*!40000 ALTER TABLE `library_policies` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `library_policies_bi` BEFORE INSERT ON `library_policies` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `library_policies_bu` BEFORE UPDATE ON `library_policies` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `loans`
--

DROP TABLE IF EXISTS `loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loans` (
  `loan_id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`loan_id`),
  KEY `copy_id` (`copy_id`),
  KEY `user_id` (`user_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`copy_id`) REFERENCES `book_copies` (`copy_id`),
  CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `loans_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `loans_ibfk_4` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `loans_ibfk_5` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loans`
--

LOCK TABLES `loans` WRITE;
/*!40000 ALTER TABLE `loans` DISABLE KEYS */;
/*!40000 ALTER TABLE `loans` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `loans_bi` BEFORE INSERT ON `loans` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `loans_bu` BEFORE UPDATE ON `loans` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menus` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`menu_id`),
  KEY `page_id` (`page_id`),
  CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `page_list` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `notifications_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `notifications_bi` BEFORE INSERT ON `notifications` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `notifications_bu` BEFORE UPDATE ON `notifications` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `page_list`
--

DROP TABLE IF EXISTS `page_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_list` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(255) DEFAULT NULL,
  `page_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_list`
--

LOCK TABLES `page_list` WRITE;
/*!40000 ALTER TABLE `page_list` DISABLE KEYS */;
INSERT INTO `page_list` VALUES (52,'Announcement List','announcement_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(53,'Audit Log List','audit_log_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(54,'Backup List','backup_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(55,'Book Details','book-details.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(56,'Book Category List','book_category_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(57,'Book Copy List','book_copy_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(58,'Book Edition List','book_edition_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(59,'Book List','book_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(60,'Bookloader','bookloader.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(61,'Category List','category_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(62,'Crud Check','crud_check.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(63,'Dashboard','dashboard.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(64,'Data View','data_view.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(65,'Designation','designation.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(66,'Digital File List','digital_file_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(67,'Digital Resource List','digital_resource_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(68,'Edit Profile','edit_profile.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(69,'Erd','erd.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(70,'Fine List','fine_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(71,'Fine Waiver List','fine_waiver_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(72,'Holiday List','holiday_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(73,'Home','home.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(74,'Index','index.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(75,'Library Policy List','library_policy_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(76,'Library Rbac Matrix','library_rbac_matrix.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(77,'Loan List','loan_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(78,'Login','login.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(79,'Logout','logout.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(80,'Manage User Role','manage_user_role.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(81,'Notification List','notification_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(82,'Payment List','payment_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(83,'Permission Managemnt','permission_managemnt.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(84,'Policy Change List','policy_change_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(85,'Register','register.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(86,'Reservation List','reservation_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(87,'Return List','return_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(88,'Role List','role_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(89,'Sidebar','sidebar.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(90,'System Setting List','system_setting_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(91,'Home','system_settings/home.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(92,'Index','system_settings/index.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(93,'Sidebar','system_settings/sidebar.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(94,'Blank Page','templates/blank_page.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(95,'Create Or Add Page','templates/create_or_add_page.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(96,'Delete Page','templates/delete_page.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(97,'Edit Or Update Page','templates/edit_or_update_page.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(98,'Read Or View Page','templates/read_or_view_page.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(99,'User List','user_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(100,'User Profile List','user_profile_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(101,'User Role List','user_role_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(102,'View Profile','view_profile.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL);
/*!40000 ALTER TABLE `page_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `fine_id` int(11) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `fine_id` (`fine_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`fine_id`) REFERENCES `fines` (`fine_id`),
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `payments_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `payments_bi` BEFORE INSERT ON `payments` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `payments_bu` BEFORE UPDATE ON `payments` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `can_read` tinyint(1) NOT NULL DEFAULT 0,
  `can_write` tinyint(1) NOT NULL DEFAULT 0,
  `deny` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `uniq_role_page` (`role_id`,`page_id`),
  KEY `idx_role` (`role_id`),
  KEY `idx_page` (`page_id`),
  CONSTRAINT `permissions_page_fk` FOREIGN KEY (`page_id`) REFERENCES `page_list` (`page_id`),
  CONSTRAINT `permissions_role_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=236 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,1,52,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(2,1,53,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(3,1,54,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(4,1,94,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(5,1,56,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(6,1,57,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(7,1,55,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(8,1,58,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(9,1,59,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(10,1,60,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(11,1,61,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(12,1,95,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(13,1,62,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(14,1,63,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(15,1,64,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(16,1,96,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(17,1,65,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(18,1,66,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(19,1,67,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(20,1,97,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(21,1,68,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(22,1,69,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(23,1,70,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(24,1,71,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(25,1,72,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(26,1,73,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(27,1,91,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(28,1,92,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(29,1,75,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(30,1,76,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(31,1,77,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(32,1,80,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(33,1,81,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(34,1,82,1,1,0,NULL,'2026-01-26 16:17:37',NULL,'2026-01-26 16:17:37'),(35,1,83,1,1,0,NULL,'2026-01-26 16:17:38',NULL,'2026-01-26 16:17:38'),(36,1,84,1,1,0,NULL,'2026-01-26 16:17:38',NULL,'2026-01-26 16:17:38'),(37,1,98,1,1,0,NULL,'2026-01-26 16:17:38',NULL,'2026-01-26 16:17:38'),(38,1,86,1,1,0,NULL,'2026-01-26 16:17:38',NULL,'2026-01-26 16:17:38'),(39,1,87,1,1,0,NULL,'2026-01-26 16:17:38',NULL,'2026-01-26 16:17:38'),(40,1,88,1,1,0,NULL,'2026-01-26 16:17:38',NULL,'2026-01-26 16:17:38'),(41,1,89,1,1,0,NULL,'2026-01-26 16:17:38',NULL,'2026-01-26 16:17:38'),(42,1,93,1,1,0,NULL,'2026-01-26 16:17:38',NULL,'2026-01-26 16:17:38'),(43,1,90,1,1,0,NULL,'2026-01-26 16:17:38',NULL,'2026-01-26 16:17:38'),(44,1,99,1,1,0,NULL,'2026-01-26 16:17:38',NULL,'2026-01-26 16:17:38'),(45,1,100,1,1,0,NULL,'2026-01-26 16:17:38',NULL,'2026-01-26 16:17:38'),(46,1,101,1,1,0,NULL,'2026-01-26 16:17:38',NULL,'2026-01-26 16:17:38'),(47,1,102,1,1,0,NULL,'2026-01-26 16:17:38',NULL,'2026-01-26 16:17:38'),(95,3,52,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(96,3,53,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(97,3,54,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(98,3,94,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(99,3,56,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(100,3,57,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(101,3,55,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(102,3,58,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(103,3,59,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(104,3,60,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(105,3,61,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(106,3,95,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(107,3,62,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(108,3,63,1,0,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(109,3,64,1,0,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(110,3,96,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(111,3,65,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(112,3,66,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(113,3,67,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(114,3,97,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(115,3,68,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(116,3,69,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(117,3,70,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(118,3,71,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(119,3,72,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(120,3,73,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(121,3,91,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(122,3,92,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(123,3,75,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(124,3,76,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(125,3,77,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(126,3,80,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(127,3,81,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(128,3,82,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(129,3,83,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(130,3,84,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(131,3,98,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(132,3,86,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(133,3,87,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(134,3,88,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(135,3,89,1,0,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(136,3,93,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(137,3,90,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(138,3,99,1,0,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(139,3,100,1,0,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(140,3,101,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(141,3,102,1,0,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(189,2,52,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(190,2,53,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(191,2,54,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(192,2,94,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(193,2,56,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(194,2,57,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(195,2,55,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(196,2,58,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(197,2,59,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(198,2,60,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(199,2,61,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(200,2,95,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(201,2,62,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(202,2,63,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(203,2,64,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(204,2,96,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(205,2,65,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(206,2,66,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(207,2,67,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(208,2,97,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(209,2,68,1,1,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(210,2,69,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(211,2,70,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(212,2,71,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(213,2,72,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(214,2,73,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(215,2,91,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(216,2,92,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(217,2,75,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(218,2,76,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(219,2,77,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(220,2,80,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(221,2,81,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(222,2,82,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(223,2,83,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(224,2,84,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(225,2,98,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(226,2,86,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(227,2,87,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(228,2,88,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(229,2,89,1,0,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(230,2,93,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(231,2,90,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(232,2,99,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(233,2,100,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(234,2,101,0,0,1,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42'),(235,2,102,1,1,0,NULL,'2026-01-26 16:27:42',NULL,'2026-01-26 16:27:42');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `policy_changes`
--

DROP TABLE IF EXISTS `policy_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `policy_changes` (
  `change_id` int(11) NOT NULL AUTO_INCREMENT,
  `policy_id` int(11) DEFAULT NULL,
  `proposed_by` varchar(255) DEFAULT NULL,
  `proposal_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`change_id`),
  KEY `policy_id` (`policy_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `policy_changes_ibfk_1` FOREIGN KEY (`policy_id`) REFERENCES `library_policies` (`policy_id`),
  CONSTRAINT `policy_changes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `policy_changes_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `policy_changes_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policy_changes`
--

LOCK TABLES `policy_changes` WRITE;
/*!40000 ALTER TABLE `policy_changes` DISABLE KEYS */;
/*!40000 ALTER TABLE `policy_changes` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `policy_changes_bi` BEFORE INSERT ON `policy_changes` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `policy_changes_bu` BEFORE UPDATE ON `policy_changes` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `copy_id` int(11) DEFAULT NULL,
  `reservation_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`reservation_id`),
  KEY `user_id` (`user_id`),
  KEY `copy_id` (`copy_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`copy_id`) REFERENCES `book_copies` (`copy_id`),
  CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `reservations_ibfk_4` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `reservations_ibfk_5` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `reservations_bi` BEFORE INSERT ON `reservations` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `reservations_bu` BEFORE UPDATE ON `reservations` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `returns`
--

DROP TABLE IF EXISTS `returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `returns` (
  `return_id` int(11) NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`return_id`),
  KEY `loan_id` (`loan_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `returns_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`loan_id`),
  CONSTRAINT `returns_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `returns_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `returns_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `returns`
--

LOCK TABLES `returns` WRITE;
/*!40000 ALTER TABLE `returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `returns` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `returns_bi` BEFORE INSERT ON `returns` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `returns_bu` BEFORE UPDATE ON `returns` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `roles_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `roles_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `roles_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin',NULL,'2026-01-26 12:33:40',NULL,'2026-01-26 12:33:40',NULL,NULL),(2,'User',NULL,'2026-01-26 12:44:16',NULL,'2026-01-26 12:44:16',NULL,NULL),(3,'Librarian',NULL,'2026-01-26 12:44:21',NULL,'2026-01-26 12:44:21',NULL,NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `roles_bi` BEFORE INSERT ON `roles` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `roles_bu` BEFORE UPDATE ON `roles` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`setting_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `system_settings_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `system_settings_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `system_settings_bi` BEFORE INSERT ON `system_settings` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `system_settings_bu` BEFORE UPDATE ON `system_settings` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`profile_id`),
  KEY `user_id` (`user_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `user_profiles_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `user_profiles_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `user_profiles_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profiles`
--

LOCK TABLES `user_profiles` WRITE;
/*!40000 ALTER TABLE `user_profiles` DISABLE KEYS */;
INSERT INTO `user_profiles` VALUES (1,1,'LMS','Admin','2024-01-01',';saijf;adsjf;asj;ajd;aj;','1234567890','lms inc.','Admin','uploads/profile_picture/1769403805_avatar.png',NULL,'2026-01-26 11:03:25',NULL,'2026-01-26 12:04:04',NULL,NULL),(2,3,'Tahmid','Shaheer','2026-01-26',NULL,'1234567890','lms inc.','Librarian','uploads/profile_picture/1769424080_avatar.png',NULL,'2026-01-26 16:41:20',NULL,'2026-01-26 16:41:20',NULL,NULL),(3,2,'Abul','kasem','2026-01-26',NULL,'1234567890','lms inc.','user','uploads/profile_picture/1769424167_avatar.png',NULL,'2026-01-26 16:42:47',NULL,'2026-01-26 16:42:47',NULL,NULL);
/*!40000 ALTER TABLE `user_profiles` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `user_profiles_bi` BEFORE INSERT ON `user_profiles` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `user_profiles_bu` BEFORE UPDATE ON `user_profiles` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_roles` (
  `user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(150) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `role_name` varchar(150) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`user_role_id`),
  UNIQUE KEY `uniq_user_id` (`user_id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `user_roles_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `user_roles_ibfk_4` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `user_roles_ibfk_5` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (1,3,'tahmid',3,'Librarian',NULL,'2026-01-26 14:27:25',NULL,'2026-01-26 14:27:33',NULL,NULL),(2,2,'kasem',2,'User',NULL,'2026-01-26 14:27:40',NULL,'2026-01-26 14:27:40',NULL,NULL),(3,1,'lms_admin',1,'Admin',NULL,'2026-01-26 16:13:09',NULL,'2026-01-26 16:13:09',NULL,NULL);
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `user_roles_bi` BEFORE INSERT ON `user_roles` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `user_roles_bu` BEFORE UPDATE ON `user_roles` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'lms_admin','lms_admin@karigori.site','$2y$10$Qcs6B9zb2/wO3kNz8rK1oeYP0D7at6dCdsBh.0kDUS/J5K56rES1i',NULL,'2026-01-26 09:31:29',NULL,'2026-01-26 09:31:29',NULL,NULL),(2,'kasem','kasem@karigori.site','$2y$10$K0I/E4BgJxRnIyXZDTbLIujo/y2SETklrsCjvlt63DFmkAb9T0VK6',NULL,'2026-01-26 12:29:04',NULL,'2026-01-26 12:29:04',NULL,NULL),(3,'tahmid','tahmid@karigori.site','$2y$10$gnvL2hZoxmqE17G/v4yYPOf3yPU4dEiIxGwrhN6IVBF429CcyMvnK',NULL,'2026-01-26 12:29:45',NULL,'2026-01-26 12:29:45',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `users_bi` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
  SET NEW.created_by  = IFNULL(NEW.created_by, @current_user_id);
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `users_bu` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
  SET NEW.modified_by = IFNULL(NEW.modified_by, @current_user_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-26 17:01:05
