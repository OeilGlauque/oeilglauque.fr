-- MariaDB dump 10.17  Distrib 10.5.5-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: fogdb
-- ------------------------------------------------------
-- Server version	10.5.5-MariaDB

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
-- Table structure for table `app_users`
--

DROP TABLE IF EXISTS `app_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `roles` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C250282486CC499D` (`pseudo`),
  UNIQUE KEY `UNIQ_C2502824E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_users`
--

LOCK TABLES `app_users` WRITE;
/*!40000 ALTER TABLE `app_users` DISABLE KEYS */;
INSERT INTO `app_users` VALUES (1,'root','iam','groot','$2y$13$lLcoql3xcjCWAs0xaTRieOThIQccCoWQ9hPnTQGZQ.vFmmES.AYvm',NULL,'testfog@yopmail.com',NULL,'2020-08-16 17:12:21',1,'ROLE_USER;ROLE_ADMIN'),(2,'TheBoss','Cave','Jhonson','$2y$13$WvG15/itFHsjjtEhNoNCteS6Gpqf3c70zXDcEJBSKgcF9PlonLy/K','dSjzHqa6C7xA-t_ZaVtLnD-opr2YLt4Xa9i--1V0Z-Q','testfog2@yopmail.com',NULL,'2020-08-16 17:18:34',1,'ROLE_USER;ROLE_SHOP'),(3,'Ours de markarth','Ulfric','Sombrage','$2y$13$f7efcjY4AHqhg4yIDOdsrenKApL96gRU7Ep7hWgwBZq8/76ifToiS',NULL,'testfog3@yopmail.com',NULL,'2020-08-16 17:19:15',1,'ROLE_USER');
/*!40000 ALTER TABLE `app_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `board_game`
--

DROP TABLE IF EXISTS `board_game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `board_game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `missing` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `excess` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `board_game`
--

LOCK TABLES `board_game` WRITE;
/*!40000 ALTER TABLE `board_game` DISABLE KEYS */;
INSERT INTO `board_game` VALUES (1,'Perudo',2015,15,NULL,NULL,'1 dé bleu'),(2,'Les aventuriers du rail',2017,30,NULL,NULL,NULL),(3,'Shadow hunter',2010,25,'1 cube orange',NULL,NULL);
/*!40000 ALTER TABLE `board_game` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `board_game_reservation`
--

DROP TABLE IF EXISTS `board_game_reservation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `board_game_reservation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `validated` tinyint(1) NOT NULL,
  `note` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_beg` date NOT NULL,
  `date_end` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BD5DD2F7F675F31B` (`author_id`),
  CONSTRAINT `FK_BD5DD2F7F675F31B` FOREIGN KEY (`author_id`) REFERENCES `app_users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `board_game_reservation`
--

LOCK TABLES `board_game_reservation` WRITE;
/*!40000 ALTER TABLE `board_game_reservation` DISABLE KEYS */;
/*!40000 ALTER TABLE `board_game_reservation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `board_game_reservation_board_game`
--

DROP TABLE IF EXISTS `board_game_reservation_board_game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `board_game_reservation_board_game` (
  `board_game_reservation_id` int(11) NOT NULL,
  `board_game_id` int(11) NOT NULL,
  PRIMARY KEY (`board_game_reservation_id`,`board_game_id`),
  KEY `IDX_AF04D01B701E2A0C` (`board_game_reservation_id`),
  KEY `IDX_AF04D01BAC91F10A` (`board_game_id`),
  CONSTRAINT `FK_AF04D01B701E2A0C` FOREIGN KEY (`board_game_reservation_id`) REFERENCES `board_game_reservation` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_AF04D01BAC91F10A` FOREIGN KEY (`board_game_id`) REFERENCES `board_game` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `board_game_reservation_board_game`
--

LOCK TABLES `board_game_reservation_board_game` WRITE;
/*!40000 ALTER TABLE `board_game_reservation_board_game` DISABLE KEYS */;
/*!40000 ALTER TABLE `board_game_reservation_board_game` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `edition`
--

DROP TABLE IF EXISTS `edition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `edition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `annee` int(11) NOT NULL,
  `dates` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `edition`
--

LOCK TABLES `edition` WRITE;
/*!40000 ALTER TABLE `edition` DISABLE KEYS */;
INSERT INTO `edition` VALUES (1,2021,'Du 18 au 20 octobre','Vive les jeux !'),(2,2020,'Du 20 au 22 octobrecb','Jeux de rôle et jeux de plateau vous attendent à la Xème édition du Festival de lŒil Glauque !\r\nPendant 48h vous pourrez profiter de séances de jeux de rôle, découvrir notre ludothèque de plus de 150 jeux de plateau ou assister à un concert de métal par le groupe XXXXX.\r\nAu cours du week-end n’hésitez pas à prendre part à une murder-party, un escape game, ainsi quà des tournois de Magic ou Warhammer. \r\nVous pourrez aussi tout simplement venir profiter de lambiance et de nos sandwichs, pizzas, burgers et autres friandises et boissons, notamment nos crêpes et nos fameux 3D6 !\r\n                '),(3,2022,'Du 20 au 22 octobre','Jeux de rôle et jeux de plateau vous attendent à la Xème édition du Festival de lŒil Glauque !\r\nPendant 48h vous pourrez profiter de séances de jeux de rôle, découvrir notre ludothèque de plus de 150 jeux de plateau ou assister à un concert de métal par le groupe XXXXX.\r\nAu cours du week-end n’hésitez pas à prendre part à une murder-party, un escape game, ainsi quà des tournois de Magic ou Warhammer. \r\nVous pourrez aussi tout simplement venir profiter de lambiance et de nos sandwichs, pizzas, burgers et autres friandises et boissons, notamment nos crêpes et nos fameux 3D6 !\r\n                ');
/*!40000 ALTER TABLE `edition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feature`
--

DROP TABLE IF EXISTS `feature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feature` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feature`
--

LOCK TABLES `feature` WRITE;
/*!40000 ALTER TABLE `feature` DISABLE KEYS */;
INSERT INTO `feature` VALUES (1,'Shop de noël',0),(2,'Réservations du local',1),(3,'Réservations de jeux',1),(4,'Mode FOG',1),(5,'Système de partie',1),(6,'Système de news',0);
/*!40000 ALTER TABLE `feature` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game`
--

DROP TABLE IF EXISTS `game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `game_slot_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seats` int(11) NOT NULL,
  `force_online_seats` tinyint(1) NOT NULL,
  `validated` tinyint(1) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_232B318CF675F31B` (`author_id`),
  KEY `IDX_232B318CCC276EB3` (`game_slot_id`),
  CONSTRAINT `FK_232B318CCC276EB3` FOREIGN KEY (`game_slot_id`) REFERENCES `game_slot` (`id`),
  CONSTRAINT `FK_232B318CF675F31B` FOREIGN KEY (`author_id`) REFERENCES `app_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game`
--

LOCK TABLES `game` WRITE;
/*!40000 ALTER TABLE `game` DISABLE KEYS */;
INSERT INTO `game` VALUES (14,1,1,'fgdsfgsdf','Vous venez de découvrir votre ascendance divine. [sqdfqsf](https://sdfqsdqf)\r\nVous êtes __des__ demi-dieux ! Vous avez des capacités surhumaines et des supers pouvoirs.\r\nPlus tard on vous donnera des missions extraordinaires, de \r\nMais là ! Cest le Festival de lOeil Glaude à Rennes et vous partez de Brest. Tante Mé a accepté de vous prêtez sa voiture si vous la ramener intact !\r\n*Une vraie 205 rouge des années 85, je suis allé voir la destruction du mur de berlin avec ! Cest dire si elle est costaude !*\r\n735mille km au conteur, suspension dun autre temps et pas de direction assistée, le coffre est un peu enfoncé... Lauteur de laccident na jamais été revu...\r\n\r\nOh ! Vous ai-je dit quêtre un demi-dieu attire autant les emmerdes, quune voiture rouge... les taureaux énervés ?\r\nBon courage !',NULL,5,0,1,0),(15,1,4,'fgsdfgdf','hfghfghdgfh',NULL,5,0,0,0),(16,2,5,'fgdfsg','dfgsdfgfsdgpez',NULL,5,0,1,0),(17,1,6,'dfsqqsdf','Arsia Mons est un volcan bouclier situé sur la planète Mars par 8,4° S et 238,9° E&nbsp;[archive] dans le quadrangle de Phoenicis Lacus. Large de 460&nbsp;km, il  possède une caldeira',NULL,4,0,1,0);
/*!40000 ALTER TABLE `game` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game_slot`
--

DROP TABLE IF EXISTS `game_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_slot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edition_id` int(11) NOT NULL,
  `text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_games` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_471B4C4B74281A5E` (`edition_id`),
  CONSTRAINT `FK_471B4C4B74281A5E` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_slot`
--

LOCK TABLES `game_slot` WRITE;
/*!40000 ALTER TABLE `game_slot` DISABLE KEYS */;
INSERT INTO `game_slot` VALUES (1,1,'Vendredi 20h',1),(2,1,'Vendredi minuit',9),(3,1,'Samedi midi',9),(4,1,'Samedi 16h',9),(5,1,'Samedi 20h',9),(6,1,'Samedi minuit',9),(7,1,'Dimanche midi',9),(8,1,'Dimanche 16h',9),(9,1,'Dimanche 20h',9);
/*!40000 ALTER TABLE `game_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game_user`
--

DROP TABLE IF EXISTS `game_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_user` (
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`game_id`,`user_id`),
  KEY `IDX_6686BA65E48FD905` (`game_id`),
  KEY `IDX_6686BA65A76ED395` (`user_id`),
  CONSTRAINT `FK_6686BA65A76ED395` FOREIGN KEY (`user_id`) REFERENCES `app_users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_6686BA65E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_user`
--

LOCK TABLES `game_user` WRITE;
/*!40000 ALTER TABLE `game_user` DISABLE KEYS */;
INSERT INTO `game_user` VALUES (14,2);
/*!40000 ALTER TABLE `game_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_shop`
--

DROP TABLE IF EXISTS `item_shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edition_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_645A085974281A5E` (`edition_id`),
  KEY `IDX_645A0859C54C8C93` (`type_id`),
  CONSTRAINT `FK_645A085974281A5E` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`),
  CONSTRAINT `FK_645A0859C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `item_shop_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_shop`
--

LOCK TABLES `item_shop` WRITE;
/*!40000 ALTER TABLE `item_shop` DISABLE KEYS */;
INSERT INTO `item_shop` VALUES (7,1,9,'Jambon beurre',3.5,'Du jambon et du beurre'),(8,1,9,'Rosette',4,'Cest chouette rosette'),(9,1,8,'Napolitaine',9,'Je taime à litalienne, à la sauce Napolitaine'),(10,1,8,'4 fromage',9,'Classique 20/20');
/*!40000 ALTER TABLE `item_shop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_shop_order`
--

DROP TABLE IF EXISTS `item_shop_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_shop_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `slot_id` int(11) DEFAULT NULL,
  `pseudo` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` datetime NOT NULL,
  `collected` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8F8EE1C0126F525E` (`item_id`),
  KEY `IDX_8F8EE1C059E5119C` (`slot_id`),
  CONSTRAINT `FK_8F8EE1C0126F525E` FOREIGN KEY (`item_id`) REFERENCES `item_shop` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_8F8EE1C059E5119C` FOREIGN KEY (`slot_id`) REFERENCES `item_shop_slot` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_shop_order`
--

LOCK TABLES `item_shop_order` WRITE;
/*!40000 ALTER TABLE `item_shop_order` DISABLE KEYS */;
INSERT INTO `item_shop_order` VALUES (17,9,10,'Zerator','2021-10-10 23:50:10',0),(18,10,10,'JdG','2021-10-10 17:50:10',0),(19,9,10,'Antoine Daniel','2021-10-10 23:50:28',0),(21,8,11,'Seb la frite','2021-10-10 23:55:40',1);
/*!40000 ALTER TABLE `item_shop_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_shop_slot`
--

DROP TABLE IF EXISTS `item_shop_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_shop_slot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edition_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `delivery_time` datetime NOT NULL,
  `order_time` datetime NOT NULL,
  `pre_order_time` datetime DEFAULT NULL,
  `max_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6126D0EC74281A5E` (`edition_id`),
  KEY `IDX_6126D0ECC54C8C93` (`type_id`),
  CONSTRAINT `FK_6126D0EC74281A5E` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`),
  CONSTRAINT `FK_6126D0ECC54C8C93` FOREIGN KEY (`type_id`) REFERENCES `item_shop_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_shop_slot`
--

LOCK TABLES `item_shop_slot` WRITE;
/*!40000 ALTER TABLE `item_shop_slot` DISABLE KEYS */;
INSERT INTO `item_shop_slot` VALUES (10,1,8,'2021-10-10 20:00:00','2021-10-10 19:00:00','2021-10-10 18:30:00',5),(11,1,9,'2021-10-10 12:00:00','2021-10-10 11:30:00',NULL,NULL),(12,1,9,'2021-10-10 14:00:00','2021-10-10 13:30:00',NULL,NULL);
/*!40000 ALTER TABLE `item_shop_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_shop_type`
--

DROP TABLE IF EXISTS `item_shop_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_shop_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_shop_type`
--

LOCK TABLES `item_shop_type` WRITE;
/*!40000 ALTER TABLE `item_shop_type` DISABLE KEYS */;
INSERT INTO `item_shop_type` VALUES (8,'Pizza'),(9,'Sandwich');
/*!40000 ALTER TABLE `item_shop_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `local_reservation`
--

DROP TABLE IF EXISTS `local_reservation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `local_reservation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `validated` tinyint(1) NOT NULL,
  `motif` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_29C13BFBF675F31B` (`author_id`),
  CONSTRAINT `FK_29C13BFBF675F31B` FOREIGN KEY (`author_id`) REFERENCES `app_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `local_reservation`
--

LOCK TABLES `local_reservation` WRITE;
/*!40000 ALTER TABLE `local_reservation` DISABLE KEYS */;
INSERT INTO `local_reservation` VALUES (1,1,0,'Faire la fête','2020-08-29 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `local_reservation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` date NOT NULL DEFAULT curdate(),
  PRIMARY KEY (`id`),
  KEY `IDX_1DD39950F675F31B` (`author_id`),
  CONSTRAINT `FK_1DD39950F675F31B` FOREIGN KEY (`author_id`) REFERENCES `app_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (1,1,'Le saucisson cest bon','oui','saucisson','2020-08-16'),(2,1,'Tu continues à danser...','sur des hits sales','taxi','2020-08-16');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-10-11  0:13:45
