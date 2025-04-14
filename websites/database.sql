-- MariaDB dump 10.19  Distrib 10.5.19-MariaDB, for Linux (x86_64)
--
-- Host: mysql    Database: assignment1
-- ------------------------------------------------------
-- Server version	11.6.2-MariaDB-ubu2404

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
-- Current Database: `assignment1`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `assignment1` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci */;

USE `assignment1`;

--
-- Table structure for table `auction`
--

DROP TABLE IF EXISTS `auction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `categoryId` int(11) NOT NULL,
  `endDate` datetime NOT NULL,
  `userId` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoryId` (`categoryId`),
  KEY `idx_auction_endDate` (`endDate`),
  KEY `idx_auction_userId` (`userId`),
  CONSTRAINT `auction_ibfk_1` FOREIGN KEY (`categoryId`) REFERENCES `category` (`id`),
  CONSTRAINT `auction_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auction`
--

LOCK TABLES `auction` WRITE;
/*!40000 ALTER TABLE `auction` DISABLE KEYS */;
INSERT INTO `auction` VALUES (1,'Toyota Camry 2020','This is a well-maintained Toyota Camry 2020 with only 30,000 miles on the odometer. It features a sleek black exterior, leather seats, a premium sound system, and a sunroof. The car has been regularly serviced and is in excellent condition, perfect for daily commuting or long drives.',4,'2025-04-04 13:36:00',1,NULL),(2,'Toyota Camry 2020','This is a well-maintained Toyota Camry 2020 with only 30,000 miles on the odometer. It features a sleek black exterior, leather seats, a premium sound system, and a sunroof. The car has been regularly serviced and is in excellent condition, perfect for daily commuting or long drives.',4,'2025-04-04 03:39:00',1,NULL),(3,'Toyota Camry 2020','This is a well-maintained Toyota Camry 2020 with only 30,000 miles on the odometer. It features a sleek black exterior, leather seats, a premium sound system, and a sunroof. The car has been regularly serviced and is in excellent condition, perfect for daily commuting or long drives.',4,'2025-04-04 03:39:00',1,NULL),(4,'Toyota Camry 2020','This is a well-maintained Toyota Camry 2020 with only 30,000 miles on the odometer. It features a sleek black exterior, leather seats, a premium sound system, and a sunroof. The car has been regularly serviced and is in excellent condition, perfect for daily commuting or long drives.',4,'2025-04-04 03:39:00',1,NULL),(5,'Toyota Camry 2020','This is a well-maintained Toyota Camry 2020 with only 30,000 miles on the odometer. It features a sleek black exterior, leather seats, a premium sound system, and a sunroof. The car has been regularly serviced and is in excellent condition, perfect for daily commuting or long drives.',4,'2025-04-04 03:39:00',1,NULL),(6,'Toyota Camry 2020','This is a well-maintained Toyota Camry 2020 with only 30,000 miles on the odometer. It features a sleek black exterior, leather seats, a premium sound system, and a sunroof. The car has been regularly serviced and is in excellent condition, perfect for daily commuting or long drives.',4,'2025-04-04 05:00:00',1,NULL),(7,'Toyota Camry','This is a well-maintained Toyota Camry 2020 with only 30,000 miles on the odometer. It features a sleek black exterior, leather seats, a premium sound system, and a sunroof. The car has been regularly serviced and is in excellent condition, perfect for daily commuting or long drives.',4,'2025-04-30 19:09:00',1,NULL),(8,'Porsche 911 Carrera','A stunning 2023 Porsche 911 Carrera in Guards Red. This sports car features a 3.0L twin-turbo flat-six engine, delivering 379 horsepower. It has a sleek design, premium leather interior, and advanced tech like a 10.9-inch touchscreen',6,'2025-04-09 07:33:00',1,NULL),(9,'Porsche 911 Carrera','A stunning 2023 Porsche 911 Carrera in Guards Red. This sports car features a 3.0L twin-turbo flat-six engine, delivering 379 horsepower',6,'2025-04-10 07:35:00',1,NULL),(10,'Tesla','This electric sedan offers a range of up to 358 miles on a single charge, with dual electric motors for all-wheel drive and a 0-60 mph in 4.2 seconds',2,'2025-04-10 11:34:00',2,NULL),(11,'Land Rover Defender','This 4x4 is equipped with a 3.0L inline-six engine producing 395 horsepower, perfect for off-road adventures.',5,'2025-04-16 22:42:00',2,NULL),(12,'Tesla Model','This electric sedan offers a range of up to 358 miles on a single charge, with dual electric motors for all-wheel drive and a 0-60 mph in 4.2 seconds',2,'2025-04-17 22:44:00',2,NULL),(13,'Porsche 911 Carrera 2023','A stunning 2023 Porsche 911 Carrera in Guards Red. This sports car features a 3.0L twin-turbo flat-six engine, delivering 379 horsepower. It has a sleek design, premium leather interior, and advanced tech like a 10.9-inch touchscreen.',6,'2025-04-26 22:45:00',2,NULL);
/*!40000 ALTER TABLE `auction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bid`
--

DROP TABLE IF EXISTS `bid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL,
  `auctionId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `idx_bid_auctionId` (`auctionId`),
  CONSTRAINT `bid_ibfk_1` FOREIGN KEY (`auctionId`) REFERENCES `auction` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bid_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bid`
--

LOCK TABLES `bid` WRITE;
/*!40000 ALTER TABLE `bid` DISABLE KEYS */;
INSERT INTO `bid` VALUES (1,90000.00,7,1,'2025-04-12 13:40:53'),(2,790000.00,13,2,'2025-04-12 17:03:18'),(3,1000000.00,11,2,'2025-04-13 15:40:37');
/*!40000 ALTER TABLE `bid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Estate'),(2,'Electric'),(3,'Coupe'),(4,'Saloon'),(5,'4x4'),(6,'Sports'),(7,'Hybrid');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reviewText` text NOT NULL,
  `userId` int(11) NOT NULL,
  `reviewerId` int(11) NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reviewerId` (`reviewerId`),
  KEY `idx_review_userId` (`userId`),
  CONSTRAINT `review_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `review_ibfk_2` FOREIGN KEY (`reviewerId`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review`
--

LOCK TABLES `review` WRITE;
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
INSERT INTO `review` VALUES (1,'Best\r\n',2,2,'2025-04-13 15:23:43');
/*!40000 ALTER TABLE `review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'jesinabastola40@gmail.com','$2y$12$xpbVKpANYc9yxWC01SC2OuZ9JvhmbqGTSAP9uiOkwpdNR0XQHzSsS','JESINA',0),(2,'userlogin@gmail.com','$2y$12$3i6SDRVnCLbh/J2xk4faqOKM4TqD1Rl2.d76OZ87wCUepnCbyKBJO','ss',1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'assignment1'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-14  2:06:38
