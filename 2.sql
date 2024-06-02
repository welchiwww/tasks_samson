-- MySQL dump 10.13  Distrib 5.6.51, for Win64 (x86_64)
--
-- Host: localhost    Database: test_samson
-- ------------------------------------------------------
-- Server version	5.6.51-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `a_category`
--

DROP TABLE IF EXISTS `a_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `a_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `a_category_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `a_category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_category`
--

LOCK TABLES `a_category` WRITE;
/*!40000 ALTER TABLE `a_category` DISABLE KEYS */;
INSERT INTO `a_category` VALUES (1,'10','╨С╤Г╨╝╨░╨│╨░',NULL),(2,'20','╨Я╤А╨╕╨╜╤В╨╡╤А╤Л',NULL),(3,'21','╨Ь╨д╨г',2),(4,'22','╨Ь╨д╨г',2);
/*!40000 ALTER TABLE `a_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_price`
--

DROP TABLE IF EXISTS `a_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `a_price` (
  `product_id` int(11) NOT NULL,
  `price_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`product_id`,`price_type`),
  CONSTRAINT `a_price_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `a_product` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_price`
--

LOCK TABLES `a_price` WRITE;
/*!40000 ALTER TABLE `a_price` DISABLE KEYS */;
INSERT INTO `a_price` VALUES (1,'╨С╨░╨╖╨╛╨▓╨░╤П',11.50),(1,'╨Ь╨╛╤Б╨║╨▓╨░',12.50),(2,'╨С╨░╨╖╨╛╨▓╨░╤П',18.50),(2,'╨Ь╨╛╤Б╨║╨▓╨░',22.50),(3,'╨С╨░╨╖╨╛╨▓╨░╤П',3010.00),(3,'╨Ь╨╛╤Б╨║╨▓╨░',3500.00),(4,'╨С╨░╨╖╨╛╨▓╨░╤П',3310.00),(4,'╨Ь╨╛╤Б╨║╨▓╨░',2999.00);
/*!40000 ALTER TABLE `a_price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_product`
--

DROP TABLE IF EXISTS `a_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `a_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_product`
--

LOCK TABLES `a_product` WRITE;
/*!40000 ALTER TABLE `a_product` DISABLE KEYS */;
INSERT INTO `a_product` VALUES (1,'201','╨С╤Г╨╝╨░╨│╨░ ╨Р4'),(2,'202','╨С╤Г╨╝╨░╨│╨░ ╨Р3'),(3,'302','╨Я╤А╨╕╨╜╤В╨╡╤А Canon'),(4,'305','╨Я╤А╨╕╨╜╤В╨╡╤А HP');
/*!40000 ALTER TABLE `a_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_product_category`
--

DROP TABLE IF EXISTS `a_product_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `a_product_category` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `a_product_category_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `a_product` (`id`),
  CONSTRAINT `a_product_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `a_category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_product_category`
--

LOCK TABLES `a_product_category` WRITE;
/*!40000 ALTER TABLE `a_product_category` DISABLE KEYS */;
INSERT INTO `a_product_category` VALUES (1,1),(2,1),(3,2),(4,2),(3,3),(4,4);
/*!40000 ALTER TABLE `a_product_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_property`
--

DROP TABLE IF EXISTS `a_property`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `a_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `property_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_value` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`,`property_name`,`property_value`),
  CONSTRAINT `a_property_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `a_product` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_property`
--

LOCK TABLES `a_property` WRITE;
/*!40000 ALTER TABLE `a_property` DISABLE KEYS */;
INSERT INTO `a_property` VALUES (1,1,'╨Я╨╗╨╛╤В╨╜╨╛╤Б╤В╤М','100',NULL),(2,1,'╨С╨╡╨╗╨╕╨╖╨╜╨░','150',NULL),(3,2,'╨Я╨╗╨╛╤В╨╜╨╛╤Б╤В╤М','90',NULL),(4,2,'╨С╨╡╨╗╨╕╨╖╨╜╨░','100',NULL),(5,3,'╨д╨╛╤А╨╝╨░╤В','A4',NULL),(6,3,'╨д╨╛╤А╨╝╨░╤В','A3',NULL),(7,3,'╨в╨╕╨┐','╨Ы╨░╨╖╨╡╤А╨╜╤Л╨╣',NULL),(8,4,'╨д╨╛╤А╨╝╨░╤В','A3',NULL),(9,4,'╨в╨╕╨┐','╨Ы╨░╨╖╨╡╤А╨╜╤Л╨╣',NULL);
/*!40000 ALTER TABLE `a_property` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-06-02 19:38:57
