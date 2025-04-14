-- MariaDB dump 10.19  Distrib 10.5.19-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: fogdb
-- ------------------------------------------------------
-- Server version	10.5.19-MariaDB-1:10.5.19+maria~ubu2004

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
  `pseudo` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `first_name` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(254) NOT NULL,
  `avatar` varchar(512) DEFAULT NULL,
  `date_created` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `is_active` tinyint(1) NOT NULL,
  `roles` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C250282486CC499D` (`pseudo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_users`
--

LOCK TABLES `app_users` WRITE;
/*!40000 ALTER TABLE `app_users` DISABLE KEYS */;
INSERT INTO `app_users` VALUES (1,'root','iam','groot','$2y$13$lLcoql3xcjCWAs0xaTRieOThIQccCoWQ9hPnTQGZQ.vFmmES.AYvm',NULL,'testfog@yopmail.com',NULL,'2020-08-16 17:12:21',1,'ROLE_USER;ROLE_ADMIN'),(2,'TheBoss','Cave','Jhonson','$2y$13$WvG15/itFHsjjtEhNoNCteS6Gpqf3c70zXDcEJBSKgcF9PlonLy/K','dSjzHqa6C7xA-t_ZaVtLnD-opr2YLt4Xa9i--1V0Z-Q','testfog2@yopmail.com',NULL,'2020-08-16 17:18:34',1,'ROLE_USER;ROLE_SHOP'),(3,'Ours de markarth','Ulfric','Sombrage','$2y$13$f7efcjY4AHqhg4yIDOdsrenKApL96gRU7Ep7hWgwBZq8/76ifToiS',NULL,'testfog3@yopmail.com',NULL,'2020-08-16 17:19:15',1,'ROLE_USER'),(4,'Istar','bh','fog','$2y$13$0RI4QlFtVLHncjNgSJKqU.Y/1/oe/W4NUYqhKMvdAlUnnnkj6FwAO',NULL,'arthur.branchuharel@gmail.com',NULL,'2023-07-07 16:09:25',1,'ROLE_USER');
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
  `name` varchar(190) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `missing` longtext DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `excess` varchar(255) DEFAULT NULL,
  `editor` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `players` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `board_game`
--

LOCK TABLES `board_game` WRITE;
/*!40000 ALTER TABLE `board_game` DISABLE KEYS */;
INSERT INTO `board_game` VALUES (1,'Perudo',2015,15,NULL,NULL,'1 dé bleu', NULL, 'Bon', '1h/2h', '2+'),(2,'Les aventuriers du rail',2017,30,NULL,NULL,NULL, NULL, 'Mauvais'),(3,'Shadow hunter',2010,25,'1 cube orange',NULL,NULL, NULL, 'Excellent');
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
  `validated` tinyint(1) DEFAULT NULL,
  `note` longtext DEFAULT NULL,
  `date_beg` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BD5DD2F7F675F31B` (`author_id`),
  CONSTRAINT `FK_BD5DD2F7F675F31B` FOREIGN KEY (`author_id`) REFERENCES `app_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `board_game_reservation`
--

LOCK TABLES `board_game_reservation` WRITE;
/*!40000 ALTER TABLE `board_game_reservation` DISABLE KEYS */;
INSERT INTO `board_game_reservation` VALUES (2,1,1,'lmkdjg','2023-07-19 00:00:00','2023-07-20 00:00:00'),(3,1,1,'aefazfe','2023-09-22 00:00:00','2023-09-23 00:00:00'),(4,4,0,'zefazef','2023-09-27 00:00:00','2023-09-29 00:00:00'),(5,4,0,'zefazef','2023-09-27 00:00:00','2023-09-29 00:00:00'),(6,1,NULL,'','2023-10-01 00:00:00','2023-10-03 00:00:00'),(7,1,NULL,'','2023-11-04 00:00:00','2023-11-05 00:00:00'),(8,1,NULL,'','2023-11-14 00:00:00','2023-11-16 00:00:00'),(9,4,0,'Test de ping de réservation','2023-09-11 00:00:00','2023-09-13 00:00:00'),(10,4,1,'','2023-09-19 00:00:00','2023-09-20 00:00:00'),(11,4,0,'','2023-09-22 00:00:00','2023-09-30 00:00:00'),(12,4,NULL,'','2023-09-23 00:00:00','2023-09-24 00:00:00'),(13,4,NULL,'lmkazehmlnqsfg','2023-09-25 00:00:00','2023-09-26 00:00:00'),(14,1,NULL,'','2023-11-01 00:00:00','2023-11-01 00:00:00'),(15,1,NULL,'','2023-12-05 00:00:00','2023-12-06 00:00:00'),(16,1,NULL,'','2023-12-27 00:00:00','2023-12-28 00:00:00'),(17,1,NULL,'','2023-12-27 00:00:00','2023-12-28 00:00:00'),(18,4,NULL,'','2023-12-28 00:00:00','2023-12-30 00:00:00');
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
INSERT INTO `board_game_reservation_board_game` VALUES (2,1),(3,1),(4,1),(5,3),(6,1),(6,3),(7,1),(7,3),(8,1),(8,3),(9,1),(9,2),(9,3),(10,1),(10,2),(10,3),(11,2),(12,3),(13,1),(14,1),(15,1),(16,1),(17,3),(18,2);
/*!40000 ALTER TABLE `board_game_reservation_board_game` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
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
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20230709160041','2023-07-09 16:02:29',288),('DoctrineMigrations\\Version20230709160916','2023-07-09 16:09:23',102),('DoctrineMigrations\\Version20230911111758','2023-09-11 11:18:09',56),('DoctrineMigrations\\Version20230922203339','2023-09-22 20:34:36',19);
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
  `dates` varchar(255) NOT NULL,
  `home_text` longtext NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `edition`
--

LOCK TABLES `edition` WRITE;
/*!40000 ALTER TABLE `edition` DISABLE KEYS */;
INSERT INTO `edition` VALUES (1,2021,'Du 18 au 20 octobre','Vive les jeux !','FOG'),(2,2020,'Du 20 au 22 octobre','Jeux de rôle et jeux de plateau vous attendent à la Xème édition du Festival de lŒil Glauque !\r\nPendant 48h vous pourrez profiter de séances de jeux de rôle, découvrir notre ludothèque de plus de 150 jeux de plateau ou assister à un concert de métal par le groupe XXXXX.\r\nAu cours du week-end n\'hésitez pas à prendre part à une murder-party, un escape game, ainsi quà des tournois de Magic ou Warhammer. \r\n Vous pourrez aussi tout simplement venir profiter de lambiance et de nos sandwichs, pizzas, burgers et autres friandises et boissons, notamment nos crêpes et nos fameux 3D6 !\r\n                ','FOG'),(3,2022,'Du 20 au 22 octobre','Jeux de rôle et jeux de plateau vous attendent à la Xème édition du Festival de lŒil Glauque !\r\nPendant 48h vous pourrez profiter de séances de jeux de rôle, découvrir notre ludothèque de plus de 150 jeux de plateau ou assister à un concert de métal par le groupe XXXXX.\r\nAu cours du week-end n\'hésitez pas à prendre part à une murder-party, un escape game, ainsi quà des tournois de Magic ou Warhammer. \r\nVous pourrez aussi tout simplement venir profiter de lambiance et de nos sandwichs, pizzas, burgers et autres friandises et boissons, notamment nos crêpes et nos fameux 3D6 ! <a href=\"https://www.instagram.com/festival_oeil_glauque/\"> TEst </a>\r\n                ','FOG'),(4,2023,'11 Octobre 2023 au 13 Octobre 2023','Jeux de rôle et jeux de plateau vous attendent à la Xème édition du Festival de l\'Œil Glauque !\r\nPendant 48h vous pourrez profiter de séances de jeux de rôle, découvrir notre ludothèque de plus de 150 jeux de plateau ou assister à un concert de métal par le groupe XXXXX.\r\nAu cours du week-end n’hésitez pas à prendre part à une murder-party, un escape game, ainsi qu\'à des tournois de Magic ou Warhammer. \r\nVous pourrez aussi tout simplement venir profiter de l\'ambiance et de nos sandwichs, pizzas, burgers et autres friandises et boissons, notamment nos crêpes et nos fameux 3D6 !\r\n                ','FOG'),(5,2024,'12 Octobre au 14 Octobre','azeflmhzelhajfqsf','FOG'),(7,2025,'ljkzahfel','esmfkajzepofi','FOG');
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
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feature`
--

LOCK TABLES `feature` WRITE;
/*!40000 ALTER TABLE `feature` DISABLE KEYS */;
INSERT INTO `feature` VALUES (1,'Shop de noël',0),(2,'Réservations du local',1),(3,'Réservations de jeux',1),(4,'Mode FOG',1),(5,'Système de partie',1),(6,'Système de news',0),(7,'Planning',1),(8,'Menu',1),(9, 'Proposition de parties', 0);
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
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `image` varchar(1024) DEFAULT NULL,
  `seats` int(11) NOT NULL,
  `force_online_seats` tinyint(1) NOT NULL,
  `validated` tinyint(1) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  `tags` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_232B318CF675F31B` (`author_id`),
  KEY `IDX_232B318CCC276EB3` (`game_slot_id`),
  CONSTRAINT `FK_232B318CCC276EB3` FOREIGN KEY (`game_slot_id`) REFERENCES `game_slot` (`id`),
  CONSTRAINT `FK_232B318CF675F31B` FOREIGN KEY (`author_id`) REFERENCES `app_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game`
--

LOCK TABLES `game` WRITE;
/*!40000 ALTER TABLE `game` DISABLE KEYS */;
INSERT INTO `game` VALUES (18,1,11,'Test','# Test\r\nazlhflqksdoqmkjefm',NULL,6,0,1,0,'Sci-fi'),(19,1,12,'Lorem Ispum','Qu\'est-ce que le Lorem Ipsum?\r\n\r\nLe Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un imprimeur anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique, sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.',NULL,6,0,1,0,'Latin'),(20,1,13,'Test img','# Test image','',6,0,1,0,'médiéval'),(21,1,14,'Test img2','lazjeknsdlfnazehrlk jlkjfsldkqjflkazelkfslkqflkzaefl,sdfl;nlkazneklfnlksndfllzelfknlk lkznefklnlsnfklzae klnlksndfklnzakenflksdlkfn lknzelkflkqsndfza kzekf azefjrj  jzlef jzef a jzeflqlkjsdfkljzaejfsdflknzaeflsdnf l,dfjzaejflksdnfk  l,sdlkfkanzekflsd,fk zajefjieurnnqslmkdfksdf  kjfj    ozfklsldfj zke j fdsf jzjf sf opds fozpeofjsldjf sfd qsdjfopzjefjs dùmsld fzekùfjsmdjf sdjfozjeofj sjfdhfhjghiuzgfjdfuzeyrhgsjkfhuze  oidshfuizhf  uezhf uihfqsdh fezhf iz fuhduf zif zhf oif iief iozhfoislqhdfiohefslidfhsqiuf ui  sdhfqheofhsd izefi sdfhzioehfshdfihz iohf iofh qsdf heif sqdhf zehi sdfh hqsdfh iehfi qshdfoi zeifqsdi fds hifs dd hifizehfhsdfhulaefhsdhflqs iozei isdf shdfi zh isd hfisdf hsdf zefhsqdlhifh lifhds i is dhfh sdchdvxncvnomefj mshdfoaz ih fzehf hd izqd fhdqfozeifq.','uploads/games/2ik32m-650764fd60590.jpg',5,0,1,0,NULL),(22,1,10,'kmazheflkqsdf','Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un imprimeur anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique, sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un imprimeur anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique, sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.',NULL,6,0,1,0,NULL),(23,1,15,'azefqsd','azegsqdgq',NULL,1,0,1,0,NULL);
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
  `text` varchar(255) NOT NULL,
  `max_games` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_471B4C4B74281A5E` (`edition_id`),
  CONSTRAINT `FK_471B4C4B74281A5E` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_slot`
--

LOCK TABLES `game_slot` WRITE;
/*!40000 ALTER TABLE `game_slot` DISABLE KEYS */;
INSERT INTO `game_slot` VALUES (1,1,'Vendredi 20h',1),(2,1,'Vendredi minuit',9),(3,1,'Samedi midi',9),(4,1,'Samedi 16h',9),(5,1,'Samedi 20h',9),(6,1,'Samedi minuit',9),(7,1,'Dimanche midi',9),(8,1,'Dimanche 16h',9),(9,1,'Dimanche 20h',9),(10,4,'Vendredi 19h',9),(11,3,'Vendredi 20h',9),(12,3,'Vendredi 21h',9),(13,3,'Samedi 12h',9),(14,3,'Samedi 13h',9),(15,4,'samedi 0h',9),(16,4,'samedi 12h',9);
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
INSERT INTO `game_user` VALUES (22,4);
/*!40000 ALTER TABLE `game_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `google_auth_token`
--

DROP TABLE IF EXISTS `google_auth_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_auth_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_token` varchar(255) NOT NULL,
  `refresh_token` varchar(255) NOT NULL,
  `created` int(11) NOT NULL,
  `expires_in` int(11) NOT NULL,
  `id_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `google_auth_token`
--

LOCK TABLES `google_auth_token` WRITE;
/*!40000 ALTER TABLE `google_auth_token` DISABLE KEYS */;
INSERT INTO `google_auth_token` VALUES (1,'ya29.a0AfB_byDVvTZ-QQOrIYijsli9Nw_10lw-Pkmdt_2AkUW1Akcbd81XU_Qo-EVaqbtLH5qs5YqpYMj60Lw_JTODof2JHJ0E2Hb3Irl9PG4fwCU2l6T3dTOg5gM3iNBIOnGuSL5EtAZG-vV2PRCQVc7LMkNuuLOLsNi0DP2PqOUaCgYKAfgSARMSFQGOcNnCFcUdbgaGyf7NqAZYaML17g0174','1//09EJg1E8Oh_QTCgYIARAAGAkSNwF-L9Irn1mVHQ8MGl2P1s-GlbTXBt4cwlNoUcig3YcjZdJk1cAn_HVJJvMt2-AOYAeRWOVRI-Y',1697638478,3599,NULL);
/*!40000 ALTER TABLE `google_auth_token` ENABLE KEYS */;
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
  `name` longtext NOT NULL,
  `price` double NOT NULL,
  `description` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_645A085974281A5E` (`edition_id`),
  KEY `IDX_645A0859C54C8C93` (`type_id`),
  CONSTRAINT `FK_645A085974281A5E` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`),
  CONSTRAINT `FK_645A0859C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `item_shop_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_shop`
--

LOCK TABLES `item_shop` WRITE;
/*!40000 ALTER TABLE `item_shop` DISABLE KEYS */;
INSERT INTO `item_shop` VALUES (7,1,9,'Jambon beurre',3.5,'Du jambon et du beurre'),(8,1,9,'Rosette',4,'Cest chouette rosette'),(9,1,8,'Napolitaine',9,'Je taime à litalienne, à la sauce Napolitaine'),(10,1,8,'4 fromage',9,'Classique 20/20'),(11,3,8,'Rennes',9,'Jambon, fromage, champignon'),(12,4,8,'Rennes',9,'Jambon, Tomates, fromage'),(13,4,8,'akjlzefhjksqndfljkaljkz',10,'oazehfsndqlmkfnzemfnklmsqdf');
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
  `pseudo` longtext NOT NULL,
  `time` datetime NOT NULL,
  `collected` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8F8EE1C0126F525E` (`item_id`),
  KEY `IDX_8F8EE1C059E5119C` (`slot_id`),
  CONSTRAINT `FK_8F8EE1C0126F525E` FOREIGN KEY (`item_id`) REFERENCES `item_shop` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_8F8EE1C059E5119C` FOREIGN KEY (`slot_id`) REFERENCES `item_shop_slot` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_shop_order`
--

LOCK TABLES `item_shop_order` WRITE;
/*!40000 ALTER TABLE `item_shop_order` DISABLE KEYS */;
INSERT INTO `item_shop_order` VALUES (17,9,10,'Zerator','2021-10-10 23:50:10',0),(18,10,10,'JdG','2021-10-10 17:50:10',0),(19,9,10,'Antoine Daniel','2021-10-10 23:50:28',0),(21,8,11,'Seb la frite','2021-10-10 23:55:40',1),(23,11,13,'Istar','2023-03-03 23:00:58',1),(25,11,13,'teazt','2023-07-09 18:41:51',0),(27,12,14,'Istar','2023-10-01 20:30:42',1),(28,12,16,'Test','2023-10-01 22:53:29',1),(29,13,16,'lqkjef','2023-10-02 00:39:15',0);
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_shop_slot`
--

LOCK TABLES `item_shop_slot` WRITE;
/*!40000 ALTER TABLE `item_shop_slot` DISABLE KEYS */;
INSERT INTO `item_shop_slot` VALUES (10,1,8,'2021-10-10 20:00:00','2021-10-10 19:00:00','2021-10-10 18:30:00',5),(11,1,9,'2021-10-10 12:00:00','2021-10-10 11:30:00',NULL,NULL),(12,1,9,'2021-10-10 14:00:00','2021-10-10 13:30:00',NULL,NULL),(13,3,8,'2023-03-04 19:00:00','2023-03-04 18:00:00','2023-03-04 18:00:00',NULL),(14,4,8,'2023-10-11 19:00:00','2023-10-11 18:00:00','2023-10-11 18:00:00',50),(15,4,8,'2023-10-11 22:00:00','2023-10-11 21:00:00','2023-10-11 21:00:00',NULL),(16,4,8,'2023-10-11 20:00:00','2023-10-11 19:00:00',NULL,NULL);
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
  `type` longtext NOT NULL,
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
  `motif` longtext NOT NULL,
  `date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_29C13BFBF675F31B` (`author_id`),
  CONSTRAINT `FK_29C13BFBF675F31B` FOREIGN KEY (`author_id`) REFERENCES `app_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `local_reservation`
--

LOCK TABLES `local_reservation` WRITE;
/*!40000 ALTER TABLE `local_reservation` DISABLE KEYS */;
INSERT INTO `local_reservation` VALUES (5,1,0,'jlkgsfdmlkg','2023-03-21 19:22:00','2023-03-21 19:52:00'),(8,4,0,'a','2023-07-17 14:30:00','2023-07-17 15:30:00'),(9,4,0,'a','2023-07-17 16:30:00','2023-07-17 17:30:00'),(10,4,0,'a','2023-07-17 18:30:00','2023-07-17 19:30:00'),(12,4,0,'b','2023-07-19 10:20:00','2023-07-19 10:20:00'),(13,4,0,'d','2023-07-20 06:40:00','2023-07-20 07:40:00'),(14,4,1,'q','2023-07-26 04:06:00','2023-07-26 05:06:00'),(15,4,0,'qkjzefb','2023-09-29 18:00:00','2023-09-29 19:00:00');
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
  `title` varchar(255) NOT NULL,
  `text` longtext NOT NULL,
  `slug` varchar(128) NOT NULL,
  `date_created` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
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
INSERT INTO `news` VALUES (1,1,'Le saucisson cest bon','oui','saucisson','2020-08-16 00:00:00'),(2,1,'Tu continues à danser...','sur des hits sales','taxi','2020-08-16 00:00:00');
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

-- Dump completed on 2023-10-24 18:25:54
