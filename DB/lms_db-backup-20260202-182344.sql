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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'download_ebook','books',6,'2026-01-30 18:30:54',NULL,'2026-01-30 18:30:54',NULL,'2026-01-30 18:30:54',NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_copies`
--

LOCK TABLES `book_copies` WRITE;
/*!40000 ALTER TABLE `book_copies` DISABLE KEYS */;
INSERT INTO `book_copies` VALUES (1,1,'B1-E1-20260126185952-1','hold_loan',NULL,NULL,'2026-01-26 23:59:52',1,'2026-01-27 00:00:28',NULL,NULL),(2,1,'B1-E1-20260126185952-2','available',NULL,NULL,'2026-01-26 23:59:52',1,'2026-01-28 00:18:50',NULL,NULL),(3,1,'B1-E1-20260126185952-3','available',NULL,NULL,'2026-01-26 23:59:52',NULL,'2026-01-26 23:59:52',NULL,NULL),(4,1,'B1-E1-20260126185952-4','available',NULL,NULL,'2026-01-26 23:59:52',NULL,'2026-01-26 23:59:52',NULL,NULL),(5,1,'B1-E1-20260126185952-5','available',NULL,NULL,'2026-01-26 23:59:52',NULL,'2026-01-26 23:59:52',NULL,NULL),(6,1,'B1-E1-20260126185952-6','available',NULL,NULL,'2026-01-26 23:59:52',NULL,'2026-01-26 23:59:52',NULL,NULL),(7,1,'B1-E1-20260126185952-7','available',NULL,NULL,'2026-01-26 23:59:52',NULL,'2026-01-26 23:59:52',NULL,NULL),(8,1,'B1-E1-20260126185952-8','available',NULL,NULL,'2026-01-26 23:59:52',NULL,'2026-01-26 23:59:52',NULL,NULL),(9,1,'B1-E1-20260126185952-9','available',NULL,NULL,'2026-01-26 23:59:52',NULL,'2026-01-26 23:59:52',NULL,NULL),(10,2,'B2-E2-20260127110340-1','available',NULL,NULL,'2026-01-27 16:03:40',1,'2026-01-28 00:18:43',NULL,NULL),(11,2,'B2-E2-20260127110340-2','available',NULL,NULL,'2026-01-27 16:03:40',NULL,'2026-01-27 16:03:40',NULL,NULL),(12,2,'B2-E2-20260127110340-3','available',NULL,NULL,'2026-01-27 16:03:40',NULL,'2026-01-27 16:03:40',NULL,NULL),(13,2,'B2-E2-20260127110340-4','available',NULL,NULL,'2026-01-27 16:03:40',NULL,'2026-01-27 16:03:40',NULL,NULL),(14,2,'B2-E2-20260127110340-5','available',NULL,NULL,'2026-01-27 16:03:40',NULL,'2026-01-27 16:03:40',NULL,NULL),(15,2,'B2-E2-20260127110340-6','available',NULL,NULL,'2026-01-27 16:03:40',NULL,'2026-01-27 16:03:40',NULL,NULL),(16,2,'B2-E2-20260127110340-7','available',NULL,NULL,'2026-01-27 16:03:40',NULL,'2026-01-27 16:03:40',NULL,NULL),(17,2,'B2-E2-20260127110340-8','available',NULL,NULL,'2026-01-27 16:03:40',NULL,'2026-01-27 16:03:40',NULL,NULL),(18,2,'B2-E2-20260127110340-9','available',NULL,NULL,'2026-01-27 16:03:40',NULL,'2026-01-27 16:03:40',NULL,NULL),(19,2,'B2-E2-20260127110340-10','available',NULL,NULL,'2026-01-27 16:03:40',NULL,'2026-01-27 16:03:40',NULL,NULL),(20,3,'B4-E3-20260127142315-1','available',NULL,NULL,'2026-01-27 19:23:15',1,'2026-01-29 12:41:26',NULL,NULL),(21,3,'B4-E3-20260127142315-2','hold_loan',NULL,NULL,'2026-01-27 19:23:15',2,'2026-01-28 17:38:02',NULL,NULL),(22,3,'B4-E3-20260127142315-3','hold_loan',NULL,NULL,'2026-01-27 19:23:15',2,'2026-01-28 17:40:14',NULL,NULL),(23,3,'B4-E3-20260127142315-4','hold_loan',NULL,NULL,'2026-01-27 19:23:15',2,'2026-01-28 17:40:20',NULL,NULL),(24,3,'B4-E3-20260127142315-5','available',NULL,NULL,'2026-01-27 19:23:15',NULL,'2026-01-27 19:23:15',NULL,NULL),(25,3,'B4-E3-20260127142315-6','available',NULL,NULL,'2026-01-27 19:23:15',NULL,'2026-01-27 19:23:15',NULL,NULL),(26,3,'B4-E3-20260127142315-7','available',NULL,NULL,'2026-01-27 19:23:15',NULL,'2026-01-27 19:23:15',NULL,NULL),(27,3,'B4-E3-20260127142315-8','available',NULL,NULL,'2026-01-27 19:23:15',NULL,'2026-01-27 19:23:15',NULL,NULL),(28,3,'B4-E3-20260127142315-9','available',NULL,NULL,'2026-01-27 19:23:15',NULL,'2026-01-27 19:23:15',NULL,NULL),(29,3,'B4-E3-20260127142315-10','available',NULL,NULL,'2026-01-27 19:23:15',NULL,'2026-01-27 19:23:15',NULL,NULL),(30,4,'B5-E4-20260129073634-1','available',NULL,NULL,'2026-01-29 12:36:34',1,'2026-01-29 13:26:37',NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_editions`
--

LOCK TABLES `book_editions` WRITE;
/*!40000 ALTER TABLE `book_editions` DISABLE KEYS */;
INSERT INTO `book_editions` VALUES (1,1,1,1998,NULL,NULL,'2026-01-26 23:59:52',NULL,'2026-01-26 23:59:52',NULL,NULL),(2,2,1,1997,NULL,NULL,'2026-01-27 16:03:40',NULL,'2026-01-27 16:03:40',NULL,NULL),(3,4,1,2009,NULL,NULL,'2026-01-27 19:23:15',NULL,'2026-01-27 19:23:15',NULL,NULL),(4,5,1,2003,NULL,NULL,'2026-01-29 12:36:34',NULL,'2026-01-29 12:36:34',NULL,NULL);
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
  `book_type` varchar(20) NOT NULL DEFAULT 'physical',
  `ebook_format` varchar(20) DEFAULT NULL,
  `ebook_file_path` varchar(255) DEFAULT NULL,
  `ebook_file_size` int(11) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` VALUES (1,'Digital Fortress','Introduces Susan Fletcher, the NSA\'s head cryptographer, whose romantic getaway plans are shattered by a sudden, urgent work call.','Dan Brown','0-312-18087-X (US)','St. Martin\'s Press',1998,1,'physical',NULL,NULL,NULL,'uploads/book_cover/1769333690_Dan-Brown_Digital-Fortress_book-cover-2025.jpg',NULL,'2026-01-25 15:34:50',NULL,'2026-01-25 15:34:50',NULL,NULL),(2,'Harry Potter and the Philosopher\'s Stone','Nearly ten years had passed since the Dursleys had woken up to find their nephew on the front step, but Privet Drive had hardly changed at all. The su','J. K. Rowling','978-0-7475-3269-9','Bloomsbury',1997,2,'physical',NULL,NULL,NULL,'uploads/book_cover/1769508220_Harry-Potter-and-the-sorcorers-stone.jpg',NULL,'2026-01-27 16:03:40',NULL,'2026-01-27 16:03:40',NULL,NULL),(3,'Lord of the Mysteries','The story features a meticulously crafted world with hidden, dangerous, and often occult, societies. It follows the protagonist, Klein, who becomes...','Cuttlefish That Loves Diving','234-038545792874','Cuttlefish That Loves Diving',2018,3,'ebook','pdf',NULL,NULL,'uploads/book_cover/1769508610_Lord_of_Mysteries.png',NULL,'2026-01-27 16:10:10',NULL,'2026-01-27 16:10:10',NULL,NULL),(4,'The Lost Symbol','The Lost Symbol is a 2009 novel written by American writer Dan Brown. It is a thriller set in Washington, D.C., after the events of The Da Vinci Code.','Dan Brown','978-0385504225 (US)','St. Martin\'s Press (US)',2009,1,'physical',NULL,NULL,NULL,'uploads/book_cover/1769520195_Dan-Brown_The_Lost_Symbol_book-cover.jpg',NULL,'2026-01-27 19:23:15',NULL,'2026-01-27 19:23:15',NULL,NULL),(5,'The Da Vinci Code','The Da Vinci Code is a 2003 mystery thriller novel by Dan Brown. It is one of the best-selling American novels of all time.','Dan Brown','0-385-50420-9 (US)','St. Martin\'s Press (US)',2003,1,'physical',NULL,NULL,NULL,'uploads/book_cover/1769668594_Dan-Brown_The_Vinci_Code_book-cover.jpg',NULL,'2026-01-29 12:36:34',NULL,'2026-01-29 12:36:34',NULL,NULL),(6,'Online Plagiarism Checker','This study focuses primarily on plagiarism, which is prevalent in schools and colleges. Many students have been found to have copied assignments...','Aryan Patil','ISSN (O) 2278-1021 p','IJARCCE',2025,4,'ebook','pdf','uploads/ebooks/1769776235_Online-Plagirism-Checker.pdf',166889,'uploads/book_cover/1770013915_best_plagcheckers-3.png',NULL,'2026-01-30 18:30:35',NULL,'2026-02-02 12:31:55',NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Thriller',1,'2026-01-25 15:28:37',NULL,'2026-01-30 21:04:26',NULL,NULL),(2,'Fantasy',1,'2026-01-27 15:54:25',NULL,'2026-01-30 21:04:32',NULL,NULL),(3,'Mystery',1,'2026-01-27 15:54:35',NULL,'2026-01-30 21:04:35',NULL,NULL),(4,'Research Journal',1,'2026-01-30 18:14:17',NULL,'2026-01-30 21:04:39',NULL,NULL),(5,'Science Fiction',1,'2026-01-30 21:04:07',NULL,'2026-01-30 21:04:07',NULL,NULL),(6,'Biography',1,'2026-01-30 21:04:07',NULL,'2026-01-30 21:04:07',NULL,NULL),(7,'Self-Help',1,'2026-01-30 21:04:07',NULL,'2026-01-30 21:04:07',NULL,NULL),(8,'Memoir',1,'2026-01-30 21:04:07',NULL,'2026-01-30 21:04:07',NULL,NULL),(9,'Historical',1,'2026-01-30 21:04:07',NULL,'2026-01-30 21:04:07',NULL,NULL);
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
  `policy_value` int(11) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_policies`
--

LOCK TABLES `library_policies` WRITE;
/*!40000 ALTER TABLE `library_policies` DISABLE KEYS */;
INSERT INTO `library_policies` VALUES (1,'loan_period_days','14',14,'2026-01-27',NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 00:38:04',NULL,NULL),(2,'reservation_expiry_days','3',3,'2026-01-27',NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 00:38:04',NULL,NULL);
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
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`loan_id`),
  KEY `copy_id` (`copy_id`),
  KEY `user_id` (`user_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  KEY `idx_loans_user_status` (`user_id`,`status`),
  CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`copy_id`) REFERENCES `book_copies` (`copy_id`),
  CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `loans_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `loans_ibfk_4` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `loans_ibfk_5` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loans`
--

LOCK TABLES `loans` WRITE;
/*!40000 ALTER TABLE `loans` DISABLE KEYS */;
INSERT INTO `loans` VALUES (1,1,1,'2026-01-27','2026-02-10',NULL,1,'2026-01-27 00:00:28',1,'2026-01-27 10:50:02',NULL,NULL,'approved',''),(2,2,2,'2026-01-27','2026-02-10','2026-01-27',2,'2026-01-27 10:53:41',1,'2026-01-27 10:54:58',NULL,NULL,'returned',NULL),(3,10,2,'2026-01-27','2026-02-10','2026-01-27',2,'2026-01-27 18:58:44',1,'2026-01-27 19:02:08',NULL,NULL,'returned',NULL),(4,20,2,'2026-01-27','2026-02-10','2026-01-29',2,'2026-01-27 19:23:47',1,'2026-01-29 12:32:28',NULL,NULL,'returned',NULL),(5,2,2,NULL,NULL,NULL,2,'2026-01-27 19:26:30',1,'2026-01-28 00:18:50',NULL,NULL,'rejected',NULL),(6,10,2,NULL,NULL,NULL,2,'2026-01-27 20:08:32',1,'2026-01-28 00:18:43',NULL,NULL,'rejected',NULL),(7,21,2,NULL,NULL,NULL,2,'2026-01-28 17:38:02',1,'2026-01-29 12:32:21',NULL,NULL,'rejected',NULL),(8,22,2,NULL,NULL,NULL,2,'2026-01-28 17:40:14',1,'2026-01-29 12:32:22',NULL,NULL,'rejected',NULL),(9,23,2,NULL,NULL,NULL,2,'2026-01-28 17:40:20',1,'2026-01-29 12:32:23',NULL,NULL,'rejected',NULL),(10,20,2,'2026-01-29','2026-02-12','2026-01-29',2,'2026-01-29 12:33:10',1,'2026-01-29 12:41:26',NULL,NULL,'returned',NULL),(11,20,2,NULL,NULL,NULL,2,'2026-01-29 12:33:23',1,'2026-01-29 12:34:02',NULL,NULL,'rejected',NULL),(12,20,2,NULL,NULL,NULL,2,'2026-01-29 12:33:29',1,'2026-01-29 12:34:03',NULL,NULL,'rejected',NULL),(13,20,2,NULL,NULL,NULL,2,'2026-01-29 12:33:34',1,'2026-01-29 12:34:03',NULL,NULL,'rejected',NULL),(14,20,2,NULL,NULL,NULL,2,'2026-01-29 12:33:37',1,'2026-01-29 12:34:04',NULL,NULL,'rejected',NULL),(15,30,2,'2026-01-29','2026-02-12','2026-01-29',2,'2026-01-29 12:36:57',1,'2026-01-29 12:41:31',NULL,NULL,'returned',NULL),(16,30,2,'2026-01-29','2026-02-12','2026-01-29',2,'2026-01-29 13:25:24',1,'2026-01-29 13:26:37',NULL,NULL,'returned',NULL);
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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `read_status` tinyint(1) NOT NULL DEFAULT 0,
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
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (4,2,'Loan request submitted','Your loan request for \"Digital Fortress\" has been submitted.','2026-01-27 10:53:41',0,2,'2026-01-27 10:53:41',2,'2026-01-27 19:00:16',2,'2026-01-27 19:00:16'),(5,1,'New loan request','A new loan request was submitted for \"Digital Fortress\".','2026-01-27 10:53:41',0,2,'2026-01-27 10:53:41',2,'2026-01-27 19:28:20',1,'2026-01-27 19:28:20'),(7,2,'Loan request approved','Your loan request for \"Digital Fortress\" has been approved.','2026-01-27 10:54:13',0,1,'2026-01-27 10:54:13',1,'2026-01-27 18:54:57',2,'2026-01-27 18:54:57'),(8,2,'Return request submitted','Your return request for \"Digital Fortress\" has been submitted.','2026-01-27 10:54:38',0,2,'2026-01-27 10:54:38',2,'2026-01-27 18:49:50',2,'2026-01-27 18:49:50'),(9,1,'New return request','A return request was submitted for loan #2 (\"Digital Fortress\").','2026-01-27 10:54:38',0,2,'2026-01-27 10:54:38',2,'2026-01-27 19:28:20',1,'2026-01-27 19:28:20'),(11,2,'Return approved','Your return for \"Digital Fortress\" has been approved.','2026-01-27 10:54:58',0,1,'2026-01-27 10:54:58',1,'2026-01-27 12:42:17',2,'2026-01-27 12:42:17'),(12,2,'Loan request submitted','Your loan request for \"Harry Potter and the Philosopher\'s Stone\" has been submitted.','2026-01-27 18:58:44',0,2,'2026-01-27 18:58:44',2,'2026-01-27 19:00:12',2,'2026-01-27 19:00:12'),(13,1,'New loan request','A new loan request was submitted for \"Harry Potter and the Philosopher\'s Stone\".','2026-01-27 18:58:44',0,2,'2026-01-27 18:58:44',2,'2026-01-27 19:28:20',1,'2026-01-27 19:28:20'),(15,2,'Loan request approved','Your loan request for \"Harry Potter and the Philosopher\'s Stone\" has been approved.','2026-01-27 19:01:08',0,1,'2026-01-27 19:01:08',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(16,2,'Return request submitted','Your return request for \"Harry Potter and the Philosopher\'s Stone\" has been submitted.','2026-01-27 19:01:49',0,2,'2026-01-27 19:01:49',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(17,1,'New return request','A return request was submitted for loan #3 (\"Harry Potter and the Philosopher\'s Stone\").','2026-01-27 19:01:49',0,2,'2026-01-27 19:01:49',2,'2026-01-27 19:28:20',1,'2026-01-27 19:28:20'),(19,2,'Return approved','Your return for \"Harry Potter and the Philosopher\'s Stone\" has been approved.','2026-01-27 19:02:08',0,1,'2026-01-27 19:02:08',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(20,2,'Loan request submitted','Your loan request for \"The Lost Symbol\" has been submitted.','2026-01-27 19:23:47',0,2,'2026-01-27 19:23:47',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(21,1,'New loan request','A new loan request was submitted for \"The Lost Symbol\".','2026-01-27 19:23:47',0,2,'2026-01-27 19:23:47',2,'2026-01-27 19:28:20',1,'2026-01-27 19:28:20'),(23,2,'Loan request submitted','Your loan request for \"Digital Fortress\" has been submitted.','2026-01-27 19:26:30',0,2,'2026-01-27 19:26:30',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(24,1,'New loan request','A new loan request was submitted for \"Digital Fortress\".','2026-01-27 19:26:30',0,2,'2026-01-27 19:26:30',2,'2026-01-27 19:28:20',1,'2026-01-27 19:28:20'),(26,2,'Loan request approved','Your loan request for \"The Lost Symbol\" has been approved.','2026-01-27 19:28:06',0,1,'2026-01-27 19:28:06',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(27,2,'Loan request submitted','Your loan request for \"Harry Potter and the Philosopher\'s Stone\" has been submitted.','2026-01-27 20:08:32',0,2,'2026-01-27 20:08:32',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(29,1,'New loan request','A new loan request was submitted for \"Harry Potter and the Philosopher\'s Stone\".','2026-01-27 20:08:32',0,2,'2026-01-27 20:08:32',2,'2026-01-28 12:02:53',1,'2026-01-28 12:02:53'),(30,2,'Loan request rejected','Your loan request for \"Harry Potter and the Philosopher\'s Stone\" has been rejected.','2026-01-28 00:18:43',0,1,'2026-01-28 00:18:43',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(31,2,'Loan request rejected','Your loan request for \"Digital Fortress\" has been rejected.','2026-01-28 00:18:50',0,1,'2026-01-28 00:18:50',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(33,1,'Password change request','kasem requested a password reset. Please set a temporary password from the Users list.','2026-01-28 12:33:19',0,2,'2026-01-28 12:33:19',NULL,'2026-01-28 12:53:35',1,'2026-01-28 12:53:35'),(34,2,'Password reset requested','Your request has been sent. An admin will set a temporary password for you.','2026-01-28 12:33:19',0,2,'2026-01-28 12:33:19',NULL,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(35,2,'Temporary password set','An admin has set a temporary password for you. Please log in and change it immediately.','2026-01-28 12:34:14',0,1,'2026-01-28 12:34:14',NULL,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(37,1,'Password change request','kasem requested a password reset. Please set a temporary password from the Users list.','2026-01-28 12:40:07',0,2,'2026-01-28 12:40:07',NULL,'2026-01-28 12:53:35',1,'2026-01-28 12:53:35'),(38,2,'Password reset requested','Your request has been sent. An admin will set a temporary password for you.','2026-01-28 12:40:07',0,2,'2026-01-28 12:40:07',NULL,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(39,2,'Temporary password set','An admin has set a temporary password for you. Please log in and change it immediately.','2026-01-28 12:41:12',0,1,'2026-01-28 12:41:12',NULL,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(41,1,'Password change request','kasem requested a password reset. Please set a temporary password from the Users list.','2026-01-28 12:45:55',0,2,'2026-01-28 12:45:55',NULL,'2026-01-28 12:53:35',1,'2026-01-28 12:53:35'),(42,2,'Password reset requested','Your request has been sent. An admin will set a temporary password for you.','2026-01-28 12:45:55',0,2,'2026-01-28 12:45:55',NULL,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(44,1,'Password change request','kasem requested a password reset. Please set a temporary password from the Users list.','2026-01-28 12:53:22',0,2,'2026-01-28 12:53:22',NULL,'2026-01-28 12:53:35',1,'2026-01-28 12:53:35'),(45,2,'Password reset requested','Your request has been sent. An admin will set a temporary password for you.','2026-01-28 12:53:22',0,2,'2026-01-28 12:53:22',NULL,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(46,2,'Temporary password set','An admin has set a temporary password for you. Please log in and change it immediately.','2026-01-28 12:54:00',0,1,'2026-01-28 12:54:00',NULL,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(48,1,'Password change request','kasem requested a password reset. Please set a temporary password from the Users list.','2026-01-28 13:01:31',0,2,'2026-01-28 13:01:31',NULL,'2026-01-28 13:01:53',1,'2026-01-28 13:01:53'),(49,2,'Password reset requested','Your request has been sent. An admin will set a temporary password for you.','2026-01-28 13:01:31',0,2,'2026-01-28 13:01:31',NULL,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(50,2,'Temporary password set','An admin has set a temporary password for you. Please log in and change it immediately.','2026-01-28 13:02:22',0,1,'2026-01-28 13:02:22',NULL,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(51,2,'Return request submitted','Your return request for \"The Lost Symbol\" has been submitted.','2026-01-28 17:37:48',0,2,'2026-01-28 17:37:48',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(53,1,'New return request','A return request was submitted for loan #4 (\"The Lost Symbol\").','2026-01-28 17:37:48',0,2,'2026-01-28 17:37:48',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(54,2,'Loan request submitted','Your loan request for \"The Lost Symbol\" has been submitted.','2026-01-28 17:38:02',0,2,'2026-01-28 17:38:02',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(56,1,'New loan request','A new loan request was submitted for \"The Lost Symbol\".','2026-01-28 17:38:02',0,2,'2026-01-28 17:38:02',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(57,2,'Loan request submitted','Your loan request for \"The Lost Symbol\" has been submitted.','2026-01-28 17:40:14',0,2,'2026-01-28 17:40:14',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(59,1,'New loan request','A new loan request was submitted for \"The Lost Symbol\".','2026-01-28 17:40:14',0,2,'2026-01-28 17:40:14',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(60,2,'Loan request submitted','Your loan request for \"The Lost Symbol\" has been submitted.','2026-01-28 17:40:20',0,2,'2026-01-28 17:40:20',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(62,1,'New loan request','A new loan request was submitted for \"The Lost Symbol\".','2026-01-28 17:40:20',0,2,'2026-01-28 17:40:20',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(63,2,'Loan request rejected','Your loan request for \"The Lost Symbol\" has been rejected.','2026-01-29 12:32:21',0,1,'2026-01-29 12:32:21',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(64,2,'Loan request rejected','Your loan request for \"The Lost Symbol\" has been rejected.','2026-01-29 12:32:22',0,1,'2026-01-29 12:32:22',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(65,2,'Loan request rejected','Your loan request for \"The Lost Symbol\" has been rejected.','2026-01-29 12:32:23',0,1,'2026-01-29 12:32:23',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(66,2,'Return approved','Your return for \"The Lost Symbol\" has been approved.','2026-01-29 12:32:28',0,1,'2026-01-29 12:32:28',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(67,2,'Loan request submitted','Your loan request for \"The Lost Symbol\" has been submitted.','2026-01-29 12:33:10',0,2,'2026-01-29 12:33:10',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(69,1,'New loan request','A new loan request was submitted for \"The Lost Symbol\".','2026-01-29 12:33:10',0,2,'2026-01-29 12:33:10',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(70,2,'Loan request submitted','Your loan request for \"The Lost Symbol\" has been submitted.','2026-01-29 12:33:23',0,2,'2026-01-29 12:33:23',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(72,1,'New loan request','A new loan request was submitted for \"The Lost Symbol\".','2026-01-29 12:33:23',0,2,'2026-01-29 12:33:23',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(73,2,'Loan request submitted','Your loan request for \"The Lost Symbol\" has been submitted.','2026-01-29 12:33:29',0,2,'2026-01-29 12:33:29',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(75,1,'New loan request','A new loan request was submitted for \"The Lost Symbol\".','2026-01-29 12:33:29',0,2,'2026-01-29 12:33:29',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(76,2,'Loan request submitted','Your loan request for \"The Lost Symbol\" has been submitted.','2026-01-29 12:33:34',0,2,'2026-01-29 12:33:34',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(78,1,'New loan request','A new loan request was submitted for \"The Lost Symbol\".','2026-01-29 12:33:34',0,2,'2026-01-29 12:33:34',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(79,2,'Loan request submitted','Your loan request for \"The Lost Symbol\" has been submitted.','2026-01-29 12:33:37',0,2,'2026-01-29 12:33:37',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(81,1,'New loan request','A new loan request was submitted for \"The Lost Symbol\".','2026-01-29 12:33:37',0,2,'2026-01-29 12:33:37',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(82,2,'Loan request approved','Your loan request for \"The Lost Symbol\" has been approved.','2026-01-29 12:33:51',0,1,'2026-01-29 12:33:51',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(83,2,'Loan request rejected','Your loan request for \"The Lost Symbol\" has been rejected.','2026-01-29 12:34:02',0,1,'2026-01-29 12:34:02',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(84,2,'Loan request rejected','Your loan request for \"The Lost Symbol\" has been rejected.','2026-01-29 12:34:03',0,1,'2026-01-29 12:34:03',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(85,2,'Loan request rejected','Your loan request for \"The Lost Symbol\" has been rejected.','2026-01-29 12:34:03',0,1,'2026-01-29 12:34:03',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(86,2,'Loan request rejected','Your loan request for \"The Lost Symbol\" has been rejected.','2026-01-29 12:34:04',0,1,'2026-01-29 12:34:04',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(87,2,'Loan request submitted','Your loan request for \"The Da Vinci Code\" has been submitted.','2026-01-29 12:36:57',0,2,'2026-01-29 12:36:57',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(89,1,'New loan request','A new loan request was submitted for \"The Da Vinci Code\".','2026-01-29 12:36:57',0,2,'2026-01-29 12:36:57',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(90,2,'Loan request approved','Your loan request for \"The Da Vinci Code\" has been approved.','2026-01-29 12:37:16',0,1,'2026-01-29 12:37:16',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(91,2,'Reservation request submitted','Your reservation request for \"The Da Vinci Code\" has been submitted.','2026-01-29 12:37:36',0,2,'2026-01-29 12:37:36',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(93,1,'New reservation request','A new reservation request was submitted for \"The Da Vinci Code\".','2026-01-29 12:37:36',0,2,'2026-01-29 12:37:36',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(94,2,'Return request submitted','Your return request for \"The Lost Symbol\" has been submitted.','2026-01-29 12:40:57',0,2,'2026-01-29 12:40:57',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(96,1,'New return request','A return request was submitted for loan #10 (\"The Lost Symbol\").','2026-01-29 12:40:57',0,2,'2026-01-29 12:40:57',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(97,2,'Return request submitted','Your return request for \"The Da Vinci Code\" has been submitted.','2026-01-29 12:41:05',0,2,'2026-01-29 12:41:05',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(99,1,'New return request','A return request was submitted for loan #15 (\"The Da Vinci Code\").','2026-01-29 12:41:05',0,2,'2026-01-29 12:41:05',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(100,2,'Return approved','Your return for \"The Lost Symbol\" has been approved.','2026-01-29 12:41:26',0,1,'2026-01-29 12:41:26',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(101,2,'Return approved','Your return for \"The Da Vinci Code\" has been approved.','2026-01-29 12:41:31',0,1,'2026-01-29 12:41:31',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(102,2,'Reservation approved','Your reservation for \"The Da Vinci Code\" is now available.','2026-01-29 12:41:31',0,1,'2026-01-29 12:41:31',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(104,1,'Password change request','hannan requested a password reset. Please set a temporary password from the Users list.','2026-01-29 12:44:45',0,NULL,'2026-01-29 12:44:45',NULL,'2026-02-01 11:44:34',1,'2026-01-29 18:19:43'),(107,1,'Password change request','hannan requested a password reset. Please set a temporary password from the Users list.','2026-01-29 12:45:38',0,NULL,'2026-01-29 12:45:38',NULL,'2026-02-01 11:44:34',1,'2026-01-29 18:19:43'),(110,2,'Loan request submitted','Your loan request for \"The Da Vinci Code\" has been submitted.','2026-01-29 13:25:24',0,2,'2026-01-29 13:25:24',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(112,1,'New loan request','A new loan request was submitted for \"The Da Vinci Code\".','2026-01-29 13:25:24',0,2,'2026-01-29 13:25:24',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(113,2,'Loan request approved','Your loan request for \"The Da Vinci Code\" has been approved.','2026-01-29 13:25:31',0,1,'2026-01-29 13:25:31',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(114,2,'Return request submitted','Your return request for \"The Da Vinci Code\" has been submitted.','2026-01-29 13:26:28',0,2,'2026-01-29 13:26:28',2,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30'),(116,1,'New return request','A return request was submitted for loan #16 (\"The Da Vinci Code\").','2026-01-29 13:26:28',0,2,'2026-01-29 13:26:28',2,'2026-01-29 18:19:43',1,'2026-01-29 18:19:43'),(117,2,'Return approved','Your return for \"The Da Vinci Code\" has been approved.','2026-01-29 13:26:37',0,1,'2026-01-29 13:26:37',1,'2026-01-31 13:59:30',2,'2026-01-31 13:59:30');
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
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_list`
--

LOCK TABLES `page_list` WRITE;
/*!40000 ALTER TABLE `page_list` DISABLE KEYS */;
INSERT INTO `page_list` VALUES (52,'Announcement List','announcement_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(53,'Audit Log List','audit_log_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(54,'Backup List','backup_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(55,'Book Details','book-details.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(56,'Book Category List','book_category_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(57,'Book Copy List','book_copy_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(58,'Book Edition List','book_edition_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(59,'Book List','book_list.php',1,NULL,'2026-01-26 16:14:56',NULL,'2026-01-26 16:14:56',NULL,NULL),(60,'Bookloader','bookloader.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(61,'Category List','category_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(62,'Crud Check','crud_check.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(63,'Dashboard','dashboard.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(64,'Data View','data_view.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(65,'Designation','designation.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(66,'Digital File List','digital_file_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(67,'Digital Resource List','digital_resource_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(68,'Edit Profile','edit_profile.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(69,'Erd','erd.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(70,'Fine List','fine_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(71,'Fine Waiver List','fine_waiver_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(72,'Holiday List','holiday_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(73,'Home','home.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(74,'Index','index.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(75,'Library Policy List','library_policy_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(76,'Library Rbac Matrix','library_rbac_matrix.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(77,'Loan List','loan_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(78,'Login','login.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(79,'Logout','logout.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(80,'Manage User Role','manage_user_role.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(81,'Notification List','notification_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(82,'Payment List','payment_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(83,'Permission Managemnt','permission_managemnt.php',0,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 17:09:31',NULL,NULL),(84,'Policy Change List','policy_change_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(85,'Register','register.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(86,'Reservation List','reservation_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(87,'Return List','return_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(88,'Role List','role_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(89,'Sidebar','sidebar.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(90,'System Setting List','system_setting_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(91,'Home','system_settings/home.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(92,'Index','system_settings/index.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(93,'Sidebar','system_settings/sidebar.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(94,'Blank Page','templates/blank_page.php',0,NULL,'2026-01-26 16:14:57',NULL,'2026-01-30 20:52:54',NULL,NULL),(95,'Create Or Add Page','templates/create_or_add_page.php',0,NULL,'2026-01-26 16:14:57',NULL,'2026-01-30 20:52:54',NULL,NULL),(96,'Delete Page','templates/delete_page.php',0,NULL,'2026-01-26 16:14:57',NULL,'2026-01-30 20:52:54',NULL,NULL),(97,'Edit Or Update Page','templates/edit_or_update_page.php',0,NULL,'2026-01-26 16:14:57',NULL,'2026-01-30 20:52:54',NULL,NULL),(98,'Read Or View Page','templates/read_or_view_page.php',0,NULL,'2026-01-26 16:14:57',NULL,'2026-01-30 20:52:54',NULL,NULL),(99,'User List','user_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(100,'User Profile List','user_profile_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(101,'User Role List','user_role_list.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(102,'View Profile','view_profile.php',1,NULL,'2026-01-26 16:14:57',NULL,'2026-01-26 16:14:57',NULL,NULL),(103,'Permission Management','permission_management.php',1,NULL,'2026-01-26 17:09:31',NULL,'2026-01-26 17:09:31',NULL,NULL),(104,'User Dashboard','user_dashboard.php',1,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 00:38:04',NULL,NULL),(105,'Request Loan','actions/request_loan.php',1,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 00:38:04',NULL,NULL),(106,'Request Reservation','actions/request_reservation.php',1,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 00:38:04',NULL,NULL),(107,'Request Return','actions/request_return.php',1,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 00:38:04',NULL,NULL),(108,'Admin Process Loan','actions/admin_process_loan.php',1,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 12:38:19',NULL,NULL),(109,'Admin Process Reservation','actions/admin_process_reservation.php',1,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 12:38:19',NULL,NULL),(110,'Admin Process Return','actions/admin_process_return.php',1,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 12:38:19',NULL,NULL),(111,'Remove Notification','actions/remove_notification.php',1,NULL,'2026-01-27 12:38:19',NULL,'2026-01-27 12:38:19',NULL,NULL),(112,'Change Password','change_password.php',1,NULL,'2026-01-27 13:30:03',NULL,'2026-01-27 13:30:03',NULL,NULL),(113,'Admin Process User','actions/admin_process_user.php',1,NULL,'2026-01-28 11:08:54',NULL,'2026-01-28 11:08:54',NULL,NULL),(114,'Clear Notifications','actions/clear_notifications.php',1,NULL,'2026-01-28 11:08:54',NULL,'2026-01-28 11:08:54',NULL,NULL),(115,'Download Ebook','actions/download_ebook.php',1,NULL,'2026-01-28 11:08:54',NULL,'2026-01-28 11:08:54',NULL,NULL),(116,'Search Suggest','actions/search_suggest.php',1,NULL,'2026-01-28 11:08:54',NULL,'2026-01-28 11:08:54',NULL,NULL),(117,'Category View','category_view.php',1,NULL,'2026-01-28 11:08:54',NULL,'2026-01-28 11:08:54',NULL,NULL),(118,'Reports','reports.php',1,NULL,'2026-01-28 11:08:54',NULL,'2026-01-28 11:08:54',NULL,NULL),(119,'Search Results','search_results.php',1,NULL,'2026-01-28 11:08:54',NULL,'2026-01-28 11:08:54',NULL,NULL),(120,'Download Sample Books Csv','actions/download_sample_books_csv.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(121,'Import Books Bulk','actions/import_books_bulk.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(122,'Process Reset Password','actions/process_reset_password.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(123,'Request Password Reset','actions/request_password_reset.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(124,'Book Bulk Import','book_bulk_import.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(125,'Library Stock Summary','library_stock_summary.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(126,'Reset Password','reset_password.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(127,'Autoload','vendor/autoload.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(128,'ClassLoader','vendor/composer/ClassLoader.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(129,'InstalledVersions','vendor/composer/InstalledVersions.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(130,'Autoload Classmap','vendor/composer/autoload_classmap.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(131,'Autoload Namespaces','vendor/composer/autoload_namespaces.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(132,'Autoload Psr4','vendor/composer/autoload_psr4.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(133,'Autoload Real','vendor/composer/autoload_real.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(134,'Autoload Static','vendor/composer/autoload_static.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(135,'Installed','vendor/composer/installed.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(136,'Platform Check','vendor/composer/platform_check.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(137,'Get Oauth Token','vendor/phpmailer/phpmailer/get_oauth_token.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(138,'Phpmailer.lang Af','vendor/phpmailer/phpmailer/language/phpmailer.lang-af.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(139,'Phpmailer.lang Ar','vendor/phpmailer/phpmailer/language/phpmailer.lang-ar.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(140,'Phpmailer.lang As','vendor/phpmailer/phpmailer/language/phpmailer.lang-as.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(141,'Phpmailer.lang Az','vendor/phpmailer/phpmailer/language/phpmailer.lang-az.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(142,'Phpmailer.lang Ba','vendor/phpmailer/phpmailer/language/phpmailer.lang-ba.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(143,'Phpmailer.lang Be','vendor/phpmailer/phpmailer/language/phpmailer.lang-be.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(144,'Phpmailer.lang Bg','vendor/phpmailer/phpmailer/language/phpmailer.lang-bg.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(145,'Phpmailer.lang Bn','vendor/phpmailer/phpmailer/language/phpmailer.lang-bn.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(146,'Phpmailer.lang Ca','vendor/phpmailer/phpmailer/language/phpmailer.lang-ca.php',1,NULL,'2026-01-30 20:52:53',NULL,'2026-01-30 20:52:53',NULL,NULL),(147,'Phpmailer.lang Cs','vendor/phpmailer/phpmailer/language/phpmailer.lang-cs.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(148,'Phpmailer.lang Da','vendor/phpmailer/phpmailer/language/phpmailer.lang-da.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(149,'Phpmailer.lang De','vendor/phpmailer/phpmailer/language/phpmailer.lang-de.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(150,'Phpmailer.lang El','vendor/phpmailer/phpmailer/language/phpmailer.lang-el.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(151,'Phpmailer.lang Eo','vendor/phpmailer/phpmailer/language/phpmailer.lang-eo.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(152,'Phpmailer.lang Es','vendor/phpmailer/phpmailer/language/phpmailer.lang-es.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(153,'Phpmailer.lang Et','vendor/phpmailer/phpmailer/language/phpmailer.lang-et.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(154,'Phpmailer.lang Fa','vendor/phpmailer/phpmailer/language/phpmailer.lang-fa.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(155,'Phpmailer.lang Fi','vendor/phpmailer/phpmailer/language/phpmailer.lang-fi.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(156,'Phpmailer.lang Fo','vendor/phpmailer/phpmailer/language/phpmailer.lang-fo.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(157,'Phpmailer.lang Fr','vendor/phpmailer/phpmailer/language/phpmailer.lang-fr.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(158,'Phpmailer.lang Gl','vendor/phpmailer/phpmailer/language/phpmailer.lang-gl.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(159,'Phpmailer.lang He','vendor/phpmailer/phpmailer/language/phpmailer.lang-he.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(160,'Phpmailer.lang Hi','vendor/phpmailer/phpmailer/language/phpmailer.lang-hi.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(161,'Phpmailer.lang Hr','vendor/phpmailer/phpmailer/language/phpmailer.lang-hr.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(162,'Phpmailer.lang Hu','vendor/phpmailer/phpmailer/language/phpmailer.lang-hu.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(163,'Phpmailer.lang Hy','vendor/phpmailer/phpmailer/language/phpmailer.lang-hy.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(164,'Phpmailer.lang Id','vendor/phpmailer/phpmailer/language/phpmailer.lang-id.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(165,'Phpmailer.lang It','vendor/phpmailer/phpmailer/language/phpmailer.lang-it.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(166,'Phpmailer.lang Ja','vendor/phpmailer/phpmailer/language/phpmailer.lang-ja.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(167,'Phpmailer.lang Ka','vendor/phpmailer/phpmailer/language/phpmailer.lang-ka.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(168,'Phpmailer.lang Ko','vendor/phpmailer/phpmailer/language/phpmailer.lang-ko.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(169,'Phpmailer.lang Ku','vendor/phpmailer/phpmailer/language/phpmailer.lang-ku.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(170,'Phpmailer.lang Lt','vendor/phpmailer/phpmailer/language/phpmailer.lang-lt.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(171,'Phpmailer.lang Lv','vendor/phpmailer/phpmailer/language/phpmailer.lang-lv.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(172,'Phpmailer.lang Mg','vendor/phpmailer/phpmailer/language/phpmailer.lang-mg.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(173,'Phpmailer.lang Mn','vendor/phpmailer/phpmailer/language/phpmailer.lang-mn.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(174,'Phpmailer.lang Ms','vendor/phpmailer/phpmailer/language/phpmailer.lang-ms.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(175,'Phpmailer.lang Nb','vendor/phpmailer/phpmailer/language/phpmailer.lang-nb.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(176,'Phpmailer.lang Nl','vendor/phpmailer/phpmailer/language/phpmailer.lang-nl.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(177,'Phpmailer.lang Pl','vendor/phpmailer/phpmailer/language/phpmailer.lang-pl.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(178,'Phpmailer.lang Pt','vendor/phpmailer/phpmailer/language/phpmailer.lang-pt.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(179,'Phpmailer.lang Pt Br','vendor/phpmailer/phpmailer/language/phpmailer.lang-pt_br.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(180,'Phpmailer.lang Ro','vendor/phpmailer/phpmailer/language/phpmailer.lang-ro.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(181,'Phpmailer.lang Ru','vendor/phpmailer/phpmailer/language/phpmailer.lang-ru.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(182,'Phpmailer.lang Si','vendor/phpmailer/phpmailer/language/phpmailer.lang-si.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(183,'Phpmailer.lang Sk','vendor/phpmailer/phpmailer/language/phpmailer.lang-sk.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(184,'Phpmailer.lang Sl','vendor/phpmailer/phpmailer/language/phpmailer.lang-sl.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(185,'Phpmailer.lang Sr','vendor/phpmailer/phpmailer/language/phpmailer.lang-sr.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(186,'Phpmailer.lang Sr Latn','vendor/phpmailer/phpmailer/language/phpmailer.lang-sr_latn.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(187,'Phpmailer.lang Sv','vendor/phpmailer/phpmailer/language/phpmailer.lang-sv.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(188,'Phpmailer.lang Tl','vendor/phpmailer/phpmailer/language/phpmailer.lang-tl.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(189,'Phpmailer.lang Tr','vendor/phpmailer/phpmailer/language/phpmailer.lang-tr.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(190,'Phpmailer.lang Uk','vendor/phpmailer/phpmailer/language/phpmailer.lang-uk.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(191,'Phpmailer.lang Ur','vendor/phpmailer/phpmailer/language/phpmailer.lang-ur.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(192,'Phpmailer.lang Vi','vendor/phpmailer/phpmailer/language/phpmailer.lang-vi.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(193,'Phpmailer.lang Zh','vendor/phpmailer/phpmailer/language/phpmailer.lang-zh.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(194,'Phpmailer.lang Zh Cn','vendor/phpmailer/phpmailer/language/phpmailer.lang-zh_cn.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(195,'DSNConfigurator','vendor/phpmailer/phpmailer/src/DSNConfigurator.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(196,'Exception','vendor/phpmailer/phpmailer/src/Exception.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(197,'OAuth','vendor/phpmailer/phpmailer/src/OAuth.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(198,'OAuthTokenProvider','vendor/phpmailer/phpmailer/src/OAuthTokenProvider.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(199,'PHPMailer','vendor/phpmailer/phpmailer/src/PHPMailer.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(200,'POP3','vendor/phpmailer/phpmailer/src/POP3.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL),(201,'SMTP','vendor/phpmailer/phpmailer/src/SMTP.php',1,NULL,'2026-01-30 20:52:54',NULL,'2026-01-30 20:52:54',NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=706 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (95,3,52,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(96,3,53,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(97,3,54,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(98,3,94,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(99,3,56,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(100,3,57,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(101,3,55,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(102,3,58,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(103,3,59,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(104,3,60,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(105,3,61,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(106,3,95,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(107,3,62,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(108,3,63,1,0,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(109,3,64,1,0,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(110,3,96,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(111,3,65,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(112,3,66,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(113,3,67,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(114,3,97,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(115,3,68,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(116,3,69,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(117,3,70,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(118,3,71,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(119,3,72,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(120,3,73,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(121,3,91,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(122,3,92,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(123,3,75,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(124,3,76,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(125,3,77,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(126,3,80,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(127,3,81,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(128,3,82,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(129,3,83,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(130,3,84,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(131,3,98,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(132,3,86,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(133,3,87,1,1,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(134,3,88,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(135,3,89,1,0,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(136,3,93,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(137,3,90,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(138,3,99,1,0,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(139,3,100,1,0,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(140,3,101,0,0,1,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(141,3,102,1,0,0,NULL,'2026-01-26 16:23:57',NULL,'2026-01-26 16:23:57'),(237,3,104,1,1,0,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 00:38:04'),(239,3,105,1,1,0,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 00:38:04'),(241,3,106,1,1,0,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 00:38:04'),(243,3,107,1,1,0,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 00:38:04'),(245,3,108,1,1,0,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 00:38:04'),(247,3,109,1,1,0,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 00:38:04'),(249,3,110,1,1,0,NULL,'2026-01-27 00:38:04',NULL,'2026-01-27 00:38:04'),(603,1,108,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(604,1,109,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(605,1,110,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(606,1,52,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(607,1,53,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(608,1,54,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(609,1,94,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(610,1,56,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(611,1,57,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(612,1,55,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(613,1,58,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(614,1,59,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(615,1,60,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(616,1,61,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(617,1,112,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(618,1,95,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(619,1,62,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(620,1,63,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(621,1,64,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(622,1,96,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(623,1,65,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(624,1,66,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(625,1,67,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(626,1,97,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(627,1,68,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(628,1,69,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(629,1,70,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(630,1,71,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(631,1,72,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(632,1,73,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(633,1,91,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(634,1,92,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(635,1,75,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(636,1,76,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(637,1,77,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(638,1,80,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(639,1,81,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(640,1,82,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(641,1,103,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(642,1,84,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(643,1,98,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(644,1,111,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(645,1,105,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(646,1,106,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(647,1,107,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(648,1,86,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(649,1,87,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(650,1,88,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(651,1,89,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(652,1,93,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(653,1,90,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(654,1,104,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(655,1,99,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(656,1,100,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(657,1,101,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(658,1,102,1,1,0,NULL,'2026-01-27 13:40:29',NULL,'2026-01-27 13:40:29'),(659,2,52,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(660,2,53,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(661,2,54,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(662,2,56,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(663,2,57,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(664,2,55,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(665,2,58,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(666,2,60,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(667,2,61,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(668,2,112,1,1,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(669,2,114,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(670,2,62,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(671,2,64,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(672,2,65,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(673,2,68,1,1,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(674,2,69,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(675,2,70,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(676,2,71,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(677,2,72,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(678,2,73,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(679,2,91,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(680,2,121,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(681,2,92,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(682,2,75,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(683,2,76,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(684,2,77,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(685,2,80,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(686,2,81,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(687,2,82,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(688,2,84,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(689,2,111,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(690,2,105,0,1,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(691,2,106,0,1,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(692,2,107,0,1,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(693,2,86,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(694,2,87,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(695,2,88,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(696,2,119,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(697,2,116,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(698,2,89,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(699,2,93,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(700,2,90,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(701,2,104,1,0,0,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(702,2,99,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(703,2,100,0,0,1,NULL,'2026-01-31 13:58:57',NULL,'2026-01-31 13:58:57'),(704,2,101,0,0,1,NULL,'2026-01-31 13:58:58',NULL,'2026-01-31 13:58:58'),(705,2,102,1,1,0,NULL,'2026-01-31 13:58:58',NULL,'2026-01-31 13:58:58');
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
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`reservation_id`),
  KEY `user_id` (`user_id`),
  KEY `copy_id` (`copy_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  KEY `idx_reservations_user_status` (`user_id`,`status`),
  KEY `idx_reservations_book_status` (`book_id`,`status`,`created_date`),
  CONSTRAINT `reservations_book_fk` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`copy_id`) REFERENCES `book_copies` (`copy_id`),
  CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `reservations_ibfk_4` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `reservations_ibfk_5` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,2,30,5,'2026-01-29','2026-02-01',2,'2026-01-29 12:37:36',1,'2026-01-29 13:25:31',NULL,NULL,'fulfilled',NULL);
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
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`return_id`),
  KEY `loan_id` (`loan_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  KEY `idx_returns_status` (`status`),
  CONSTRAINT `returns_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`loan_id`),
  CONSTRAINT `returns_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `returns_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `returns_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `returns`
--

LOCK TABLES `returns` WRITE;
/*!40000 ALTER TABLE `returns` DISABLE KEYS */;
INSERT INTO `returns` VALUES (1,2,'2026-01-27',2,'2026-01-27 10:54:38',1,'2026-01-27 10:54:58',NULL,NULL,'approved',NULL),(2,3,'2026-01-27',2,'2026-01-27 19:01:49',1,'2026-01-27 19:02:08',NULL,NULL,'approved',NULL),(3,4,'2026-01-29',2,'2026-01-28 17:37:48',1,'2026-01-29 12:32:28',NULL,NULL,'approved',NULL),(4,10,'2026-01-29',2,'2026-01-29 12:40:57',1,'2026-01-29 12:41:26',NULL,NULL,'approved',NULL),(5,15,'2026-01-29',2,'2026-01-29 12:41:05',1,'2026-01-29 12:41:31',NULL,NULL,'approved',NULL),(6,16,'2026-01-29',2,'2026-01-29 13:26:28',1,'2026-01-29 13:26:37',NULL,NULL,'approved',NULL);
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
-- Table structure for table `search_logs`
--

DROP TABLE IF EXISTS `search_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `search_logs` (
  `search_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `query_text` varchar(255) NOT NULL,
  `results_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`search_log_id`),
  KEY `idx_search_logs_user` (`user_id`),
  KEY `idx_search_logs_query` (`query_text`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `search_logs`
--

LOCK TABLES `search_logs` WRITE;
/*!40000 ALTER TABLE `search_logs` DISABLE KEYS */;
INSERT INTO `search_logs` VALUES (1,1,'Digital Fortress',1,'2026-01-27 14:27:26'),(2,1,'Digital Fortress',1,'2026-01-27 14:29:43'),(3,1,'Digital Fortress',1,'2026-01-27 14:32:05'),(4,1,'Lord of the Mysteries',1,'2026-01-27 18:03:22'),(5,1,'lor',1,'2026-01-27 19:05:19'),(6,1,'Online Plagiarism Checker',1,'2026-01-31 13:38:53');
/*!40000 ALTER TABLE `search_logs` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profiles`
--

LOCK TABLES `user_profiles` WRITE;
/*!40000 ALTER TABLE `user_profiles` DISABLE KEYS */;
INSERT INTO `user_profiles` VALUES (1,1,'LMS','Admin','2024-01-01',';saijf;adsjf;asj;ajd;aj;','1234567890','lms inc.','Admin','uploads/profile_picture/1770014149_avatar.png',NULL,'2026-01-26 11:03:25',NULL,'2026-02-02 12:35:49',NULL,NULL),(3,2,'Abul','kasem','2026-01-26',NULL,'1234567890','lms inc.','user','uploads/profile_picture/1769424167_avatar.png',NULL,'2026-01-26 16:42:47',NULL,'2026-01-26 16:42:47',NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (2,2,'kasem',2,'User',NULL,'2026-01-26 14:27:40',1,'2026-01-28 13:03:36',NULL,NULL),(3,1,'lms_admin',1,'Admin',NULL,'2026-01-26 16:13:09',NULL,'2026-01-26 16:13:09',NULL,NULL);
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
  `account_status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `reset_token_hash` (`reset_token_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'lms_admin','lms_admin@karigori.site','$2y$10$4TjZaY2iUx2D7Rppf/fXPOtxZIKXknnRBCXnrYkHexNf64IeZf8rG','approved',NULL,'2026-01-26 09:31:29',1,'2026-01-30 18:12:01',NULL,NULL,NULL,NULL),(2,'kasem','kasem@karigori.site','$2y$10$oin764E5Mk.S56PEGrXFHuPnMGOqWcmKjRpe7cCyeLfkbf.pVWFx6','approved',NULL,'2026-01-26 12:29:04',1,'2026-01-28 13:04:02',NULL,NULL,NULL,NULL);
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

-- Dump completed on 2026-02-02 18:23:44
