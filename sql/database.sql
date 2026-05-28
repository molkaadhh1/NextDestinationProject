-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: next_destination
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
-- Table structure for table `agency_settings`
--

DROP TABLE IF EXISTS `agency_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agency_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agency_name` varchar(100) NOT NULL,
  `contact_email` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agency_settings`
--

LOCK TABLES `agency_settings` WRITE;
/*!40000 ALTER TABLE `agency_settings` DISABLE KEYS */;
INSERT INTO `agency_settings` VALUES (1,'Next Destination','contact@nextdestination.tn','+216 71 000 000','Ariana, Tunis, Tunisie','2026-05-18 09:06:43');
/*!40000 ALTER TABLE `agency_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
INSERT INTO `contact_messages` VALUES (1,'molka','mail@gmail.com','99999999','ddddddd','mmmmmmmmmmm',1,'2026-05-16 13:56:11');
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `home_sliders`
--

DROP TABLE IF EXISTS `home_sliders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `home_sliders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_path` varchar(255) NOT NULL,
  `media_type` enum('image','video') NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `home_sliders`
--

LOCK TABLES `home_sliders` WRITE;
/*!40000 ALTER TABLE `home_sliders` DISABLE KEYS */;
/*!40000 ALTER TABLE `home_sliders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotel_reservations`
--

DROP TABLE IF EXISTS `hotel_reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotel_reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `checkin_date` date NOT NULL,
  `checkout_date` date NOT NULL,
  `rooms` int(11) NOT NULL,
  `adults` int(11) NOT NULL,
  `room_type` varchar(100) DEFAULT NULL,
  `meal_type` varchar(100) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `hotel_id` (`hotel_id`),
  CONSTRAINT `hotel_reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hotel_reservations_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotel_reservations`
--

LOCK TABLES `hotel_reservations` WRITE;
/*!40000 ALTER TABLE `hotel_reservations` DISABLE KEYS */;
INSERT INTO `hotel_reservations` VALUES (3,3,8,'2026-05-20','2026-05-23',2,3,'Chambre Sup├⌐rieure','Petit-d├⌐jeuner',8239.94,'cancelled','2026-05-18 08:54:52'),(4,4,13,'2026-05-18','2026-05-21',2,2,'Suite Prestige','All Inclusive',3708.00,'pending','2026-05-18 13:12:08');
/*!40000 ALTER TABLE `hotel_reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotel_rooms`
--

DROP TABLE IF EXISTS `hotel_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotel_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hotel_id` int(11) NOT NULL,
  `room_type` varchar(100) NOT NULL,
  `total_rooms` int(11) NOT NULL,
  `capacity_adults` int(11) NOT NULL DEFAULT 2,
  `capacity_children` int(11) NOT NULL DEFAULT 0,
  `price_modifier` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `hotel_id` (`hotel_id`),
  CONSTRAINT `hotel_rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotel_rooms`
--

LOCK TABLES `hotel_rooms` WRITE;
/*!40000 ALTER TABLE `hotel_rooms` DISABLE KEYS */;
/*!40000 ALTER TABLE `hotel_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotel_services`
--

DROP TABLE IF EXISTS `hotel_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotel_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hotel_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-check',
  PRIMARY KEY (`id`),
  KEY `hotel_id` (`hotel_id`),
  CONSTRAINT `hotel_services_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotel_services`
--

LOCK TABLES `hotel_services` WRITE;
/*!40000 ALTER TABLE `hotel_services` DISABLE KEYS */;
INSERT INTO `hotel_services` VALUES (9,8,'Swimming Pools','fas fa-swimming-pool'),(11,8,'Private Beach','fas fa-umbrella-beach'),(12,8,'Gym','fas fa-dumbbell'),(13,8,'Free Parking','fas fa-parking'),(15,8,'Elevator','fas fa-caret-square-up'),(16,8,'Free Wifi','fas fa-wifi'),(17,9,'Pets Allowed','fas fa-paw'),(18,9,'Air Conditioning','fas fa-wind'),(19,9,'Free Parking','fas fa-parking'),(20,9,'Bars / Lounge','fas fa-glass-martini-alt'),(21,9,'Restaurants','fas fa-utensils'),(22,10,'Airport Shuttle','fas fa-shuttle-van'),(23,10,'Private Beach','fas fa-umbrella-beach'),(24,10,'Restaurants','fas fa-utensils'),(25,10,'Swimming Pools','fas fa-swimming-pool'),(26,11,'Airport Shuttle','fas fa-shuttle-van'),(27,11,'Kids Club','fas fa-child'),(28,11,'Free Parking','fas fa-parking'),(29,11,'Bars / Lounge','fas fa-glass-martini-alt'),(30,11,'Restaurants','fas fa-utensils'),(31,12,'Air Conditioning','fas fa-wind'),(32,12,'Bars / Lounge','fas fa-glass-martini-alt'),(33,12,'Free Parking','fas fa-parking'),(34,14,'Private Beach','fas fa-umbrella-beach'),(35,14,'Air Conditioning','fas fa-wind'),(36,14,'Air Conditioning','fas fa-wind');
/*!40000 ALTER TABLE `hotel_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotels`
--

DROP TABLE IF EXISTS `hotels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `location` varchar(100) NOT NULL,
  `stars` tinyint(4) NOT NULL DEFAULT 5,
  `price` decimal(10,2) NOT NULL,
  `promo` tinyint(4) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `image` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotels`
--

LOCK TABLES `hotels` WRITE;
/*!40000 ALTER TABLE `hotels` DISABLE KEYS */;
INSERT INTO `hotels` VALUES (8,'The Mirage Resort & Spa','Hammamet, Tunisie Station touristique',5,622.33,30,'Situ├⌐ au c┼ôur de Hammamet, cet h├┤tel b├⌐n├⌐ficie dΓÇÖun acc├¿s direct ├á une magnifique plage de sable fin. ├Ç proximit├⌐ de la m├⌐dina historique et de la marina de Yasmine Hammamet, il permet de profiter pleinement des attractions locales tout en s├⌐journant dans un cadre paisible et verdoyant.\r\n\r\nH├⌐bergement de Hotel The Mirage & Spa\r\nLΓÇÖ├⌐tablissement dispose de 271 chambres et bungalows spacieux, parfaitement adapt├⌐s ├á tous les types de voyageurs. Que vous choisissiez une chambre avec vue sur jardin ou piscine, un bungalow familial ou une suite, chaque logement est ├⌐quip├⌐ de commodit├⌐s modernes pour garantir un s├⌐jour agr├⌐able dans un h├┤tel pas cher ├á Hammamet sans compromis sur la qualit├⌐.','images/hotels/hotel_6a0ad3047d50e1.22718025.jpg','2026-05-18 08:51:16'),(9,'H├┤tel Laico Hammamet 5','Hammamet, Tunisie Station touristique',5,379.50,25,'Parmi les h├┤tels Laico Hammamet, sΓÇÖimpose comme une r├⌐f├⌐rence ├á Yasmine Hammamet. Son architecture unique en forme de paquebot face ├á la M├⌐diterran├⌐e offre une exp├⌐rience immersive d├¿s lΓÇÖarriv├⌐e : ├⌐l├⌐gance, ambiance marine et confort haut de gamme.\r\n\r\nSitu├⌐ ├á seulement 500 m├¿tres de la marina, lΓÇÖh├┤tel b├⌐n├⌐ficie dΓÇÖun emplacement strat├⌐gique, id├⌐al pour profiter des plages, des galeries commer├ºantes et de lΓÇÖanimation de la station baln├⌐aire. En 35 minutes depuis lΓÇÖa├⌐roport dΓÇÖEnfidha, vos vacances commencent presque imm├⌐diatement.','images/hotels/hotel_6a0ad44bc6a726.46452552.jpg','2026-05-18 08:56:43'),(10,'Hilton Skanes Monastir Beach Resort','Monastir, Tunisie',5,704.72,23,'Il y a des endroits o├╣ lΓÇÖon se sent instantan├⌐ment en vacances, sans effortΓÇª et le Hilton Skanes Monastir Beach Resort fait clairement partie de ces adresses-l├á. D├¿s quΓÇÖon franchit le seuil, on est accueilli par une atmosph├¿re douce, moderne, presque apaisante. Le bruit des vagues, les jardins soign├⌐s, les piscines qui brillent au soleilΓÇª Tout semble fait pour vous rappeler que vous ├¬tes l├á pour souffler, vous reposer, et surtout profiter.\r\n\r\nCe complexe en bord de mer, situ├⌐ entre Monastir et Sousse, r├⌐ussit ├á m├⌐langer le confort dΓÇÖun grand resort international avec la g├⌐n├⌐rosit├⌐ et la chaleur de lΓÇÖhospitalit├⌐ tunisienne. Il nΓÇÖy a pas de chichi, juste ce quΓÇÖil faut de raffinement : des chambres lumineuses, de belles piscines, un spa bien ├⌐quip├⌐ et une cuisine vari├⌐e qui fait plaisir autant aux gourmands quΓÇÖaux amateurs de plats simples.\r\n\r\nLe Hilton Skanes, cΓÇÖest un peu le compromis parfait : assez grand pour proposer mille activit├⌐s, mais suffisamment calme pour permettre ├á chacun de trouver son coin de tranquillit├⌐. Que vous soyez en couple, en famille ou en solo, vous avez la sensation de pouvoir modeler vos journ├⌐es comme vous le souhaitez, entre d├⌐tente, animations et moments ├á la plage.','images/hotels/hotel_6a0ad4cde1e5e0.32736145.jpg','2026-05-18 08:58:53'),(11,'Golden Tulip President Hammamet','Hammamet, Tunisie Station touristique',5,354.00,20,'Golden Tulip Hammamet, un h├┤tel 4 ├⌐toiles ├á Hammamet pens├⌐ pour les s├⌐jours baln├⌐aires, les familles et les vacances tout inclus en Tunisie. Profitez dΓÇÖun cadre moderne, dΓÇÖune plage priv├⌐e et dΓÇÖune large gamme de services haut de gamme.','images/hotels/hotel_6a0ad537f21384.80111473.jpg','2026-05-18 09:00:39'),(12,'Royal Garden Palace','Djerba, Tunisie Station touristique',5,327.00,0,'Rayonnement oriental Si vous ├¬tes ├á la recherche de vacances exotiques dans un d├⌐cor oriental superbe, ne cherchez plus. D├¿s votre entr├⌐e au Royal Garden Palace vous baignez dans lΓÇÖatmosph├¿re orientale. LΓÇÖentr├⌐e a ├⌐t├⌐ magnifiquement d├⌐cor├⌐e avec des ├⌐l├⌐ments locaux typiques, que vous retrouvez dans tout lΓÇÖh├┤tel : du restaurant, jusquΓÇÖaux bars et m├¬me au centre de baln├⌐oth├⌐rapie. Relaxez-vous, profitez et laissez-vous dorloter ├á lΓÇÖorientale ! Entour├⌐ dΓÇÖun magnifique jardin plant├⌐ de nombreux palmiers, vous y trouverez un v├⌐ritable havre de paix.','images/hotels/hotel_6a0ad5b012c141.21680981.jpg','2026-05-18 09:02:40'),(13,'Khayam Garden Beach & Spa','Nabeul, Tunisie Station touristique',5,139.00,0,'lΓÇÖh├┤tel Khayam Garden Beach & Spa est un h├┤tel de cat├⌐gorie 4 ├⌐toiles ├⌐mergeant dans la merveilleuse ville de Nabeul. Cette ville historique et culturelle est tr├¿s riche sur tous les plans. Les clients de l\'H├┤tel Khayam appr├⌐cient particuli├¿rement son emplacement qui leur permet de mieux d├⌐couvrir Nabeul et les villes ├á proximit├⌐ et donc de vivre une aventure tunisienne par excellence.  Le khayam Garden b├⌐n├⌐ficie d\'une vue sur la mer et d\'un acc├¿s directe ├á la plage.','images/hotels/hotel_6a0ad63192c846.93998895.jpg','2026-05-18 09:04:49'),(14,'Khayam Garden Beach & Spa','Hammamet, Tunisie Station touristique',5,100.00,20,'test test','images/hotels/hotel_6a0b2817492140.83688203.png','2026-05-18 14:54:15');
/*!40000 ALTER TABLE `hotels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `package_reservations`
--

DROP TABLE IF EXISTS `package_reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `departure_date` date NOT NULL,
  `adults` int(11) NOT NULL,
  `children` int(11) NOT NULL DEFAULT 0,
  `room_type` varchar(100) DEFAULT NULL,
  `options` text DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `package_id` (`package_id`),
  CONSTRAINT `package_reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_reservations_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `package_reservations`
--

LOCK TABLES `package_reservations` WRITE;
/*!40000 ALTER TABLE `package_reservations` DISABLE KEYS */;
INSERT INTO `package_reservations` VALUES (2,4,16,'2026-05-27',4,2,'Chambre Sup├⌐rieure','[\"Safari Quad\"]',21449.00,'confirmed','2026-05-18 13:15:23'),(3,5,16,'2026-05-18',3,1,'Chambre Sup├⌐rieure','[\"Transfert priv├⌐\"]',14388.00,'pending','2026-05-18 14:53:11');
/*!40000 ALTER TABLE `package_reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `location` varchar(100) NOT NULL,
  `stars` tinyint(4) NOT NULL DEFAULT 5,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `group_size` varchar(50) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `difficulty` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `included` text DEFAULT NULL,
  `excluded` text DEFAULT NULL,
  `inclus` text DEFAULT NULL,
  `exclus` text DEFAULT NULL,
  `programme` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packages`
--

LOCK TABLES `packages` WRITE;
/*!40000 ALTER TABLE `packages` DISABLE KEYS */;
INSERT INTO `packages` VALUES (14,'S├⌐jour 5j 4n ├Ç L\'h├┤tel Genova Hotel','Istanbul, Turquie',5,1999.00,NULL,'05 Jours / 04 Nuits','20','francais/arabe','facie','M├⌐tropole fascinante o├╣ l\'Orient rencontre l\'Occident, Istanbul vous s├⌐duira par son quartier historique de Sultanahmet, ses palais ottomans au bord du Bosphore, l\'effervescence du Grand Bazar et sa gastronomie g├⌐n├⌐reuse.','[\"images\\/6a0ad8fd6ebb1_Hotel-11288-20260313-014045.jpg\"]','2026-05-18 09:16:45',NULL,NULL,'Billet d\'avion Tunis Istanbul Tunis avec Turkish Airlines\r\n\r\nTransferts a├⌐roport / h├┤tel / a├⌐roport\r\n\r\n4 nuits ├á l\'H├┤tel Genova 3* Laleli (ou similaire) en logement petit-d├⌐jeuner','Excursions (en extra : 535 DT)\r\n\r\nRepas hors petit-d├⌐jeuner\r\n\r\nD├⌐penses personnelles\r\n\r\nTimbre de voyage : 60 DT\r\n\r\nAssurance voyage','[{\"title\":\"Tunis / Istanbul\",\"desc\":\"Arriv├⌐e, accueil et transfert ├á l\'h├┤tel.\",\"tags\":[\"hebergement\",\"petitdej\",\"vol\"]},{\"title\":\"C├┤t├⌐ asiatique & Bosphore (en extra)\",\"desc\":\"Croisi├¿re priv├⌐e sur le Bosphore depuis le pont de Galata, visite des jardins du Palais de Beylerbeyi, Palais de K├╝c├╝ksu, panorama depuis la colline de ├çaml?ca et shopping ├á l\'Optimum Outlet.\",\"tags\":[\"petitdej\",\"dejeuner\",\"transport\"]},{\"title\":\"Sapanca, Masukiye & Aslanpark\",\"desc\":\"Escapade nature : visite d\'Aslanpark (zoo des f├⌐lins), promenade ├á Masukiye et d├⌐tente au bord du lac de Sapanca.\",\"tags\":[\"petitdej\"]},{\"title\":\"C┼ôur historique d\'Istanbul\",\"desc\":\"Parc d\'Emirgan, Hippodrome, Sainte-Sophie, Palais de Topkapi, quartiers de Balat & Ey├╝p, et vue sur la Corne d\'Or depuis la colline de Pierre Loti.\",\"tags\":[\"transport\"]},{\"title\":\"Istanbul / Tunis\",\"desc\":\"transfert ├á l\'a├⌐roport selon l\'horaire du vol.\",\"tags\":[\"petitdej\",\"transport\"]}]'),(15,'D├⌐part en Groupe Kuala Lumpur & Bali','Kuala Lumpur & Bali',5,3000.00,NULL,'05 Jours / 04 Nuits','10','Anglais/francais','','Offrez-vous un voyage inoubliable entre modernit├⌐ asiatique\r\net paradis tropical. Ce circuit en groupe vous emm├¿ne ├á la d├⌐couverte de Kuala\r\nLumpur, capitale dynamique de la Malaisie, puis vers Bali, ├«le mythique aux\r\npaysages ├⌐poustouflants et ├á la culture fascinante.\r\nEntre gratte-ciels iconiques,\r\ntemples sacr├⌐s, rizi├¿res verdoyantes et plages paradisiaques, ce s├⌐jour est une\r\nimmersion compl├¿te alliant d├⌐couverte, d├⌐tente et exp├⌐riences authentiques.','[\"images\\/6a0ada62da835_{02981E59-B904-4059-ABEF-6F9773C0D386}.png\"]','2026-05-18 09:22:42',NULL,NULL,'Vols internationaux Tunis -\r\nKuala Lumpur / Bali - Tunis avec Emirates (du 24 juillet au 04 ao├╗t)\r\nVol interne Kuala Lumpur - Bali\r\nTransferts a├⌐roport aller/retour\r\nTransport priv├⌐ climatis├⌐ durant tout le circuit\r\nH├⌐bergement en h├┤tels 4 et 5* (ou similaires) :\r\n3 N ├á lΓÇÖh├┤tel Royal Chulan Kuala Lumpur 5* ou similaire\r\n4 N ├á lΓÇÖh├┤tel Sacred Valley by Genuinehost 4* ou similaire\r\n3 N ├á lΓÇÖh├┤tel Harris Seminyak Hotel 4* ou similaire\r\nPetits-d├⌐jeuners quotidiens\r\nGuide francophone en Malaisie et ├á Bali\r\nToutes les visites et excursions mentionn├⌐es\r\nFrais dΓÇÖentr├⌐e aux sites touristiques\r\nEau min├⌐rale et boissons soft durant les excursions','D├⌐penses personnelles\r\nRepas non mentionn├⌐s dans le programme\r\nVisa dΓÇÖentr├⌐e en Indon├⌐sie\r\nTaxe touristique ├á payer avant lΓÇÖarriv├⌐e ├á Bali\r\nPourboires pour les guides et chauffeurs\r\nLe timbre de voyage : 60 DT\r\nLΓÇÖassurance voyage (disponible en agence)\r\nInformations Importantes :\r\nDocuments de voyage :\r\nPasseport obligatoire avec une validit├⌐ dΓÇÖau moins 6 mois\r\nMoins de 35 ans : autorisation parentale l├⌐galis├⌐e\r\nobligatoire\r\nMoins de 25 ans : autorisation + pr├⌐sence des parents ├á lΓÇÖa├⌐roport','[{\"title\":\"D├⌐part de Tunis vers Kuala Lumpur vers Emirates Airlines\",\"desc\":\"D├⌐part de Tunis vers Kuala Lumpur vers Emirates Airlines\",\"tags\":[\"petitdej\",\"transport\"]},{\"title\":\"Arriv├⌐e ├á Kuala Lumpur\",\"desc\":\"Accueil ├á lΓÇÖa├⌐roport, transfert ├á lΓÇÖh├┤tel et installation. Temps libre.\",\"tags\":[\"guide\"]},{\"title\":\"Visite de Kuala Lumpur\",\"desc\":\"Tour de ville : Monument National, Mosqu├⌐e Nationale, Place Merdeka, Chinatown,\\nChocolate Kingdom et les c├⌐l├¿bres Tours Petronas (ext├⌐rieur).\",\"tags\":[\"hebergement\",\"diner\"]},{\"title\":\"Batu Caves & Genting Highlands\",\"desc\":\"Visite des impressionnantes Batu Caves puis excursion ├á Genting Highlands avec\\nt├⌐l├⌐ph├⌐rique, temple Chin Swee et shopping.\",\"tags\":[\"petitdej\",\"transport\",\"guide\"]},{\"title\":\"Kuala Lumpur - Bali (Ubud)\",\"desc\":\"Transfert ├á lΓÇÖa├⌐roport et envol vers Bali via un vol domestique ├á travers une\\ncompagnie locale. Accueil et installation ├á Ubud.\",\"tags\":[\"hebergement\"]}]'),(16,'D├⌐part en Groupe ├á Barcelone','Barcelone',5,3976.00,NULL,'','30','Anglais/francais','facie','Partez ├á la\r\nd├⌐couverte de Barcelone, une destination vibrante o├╣ culture, d├⌐tente et art de\r\nvivre se rencontrent harmonieusement. Commencez par explorer le c┼ôur historique\r\nde la ville en fl├ónant sur La Rambla et dans le Quartier gothique de Barcelone,\r\nv├⌐ritables symboles de lΓÇÖ├óme catalane.\r\n\r\nAccordez-vous\r\nensuite une parenth├¿se de d├⌐tente sur la magnifique Costa Brava, entre plages\r\naux eaux cristallines, criques sauvages et paysages m├⌐diterran├⌐ens ├á couper le\r\nsouffle.\r\n\r\nUn s├⌐jour complet et\r\n├⌐quilibr├⌐, id├⌐al pour d├⌐couvrir Barcelone autrement, entre visites\r\nincontournables, d├⌐tente et libert├⌐','[\"images\\/6a0adba967b0f_Product-11309-20260428-035336.png\"]','2026-05-18 09:28:09',NULL,NULL,'- Billet dΓÇÖavion aller-retour\r\n\r\n- Transferts A├⌐roport - H├┤tel - A├⌐roport\r\n\r\n- 5 nuits ├á lΓÇÖh├┤tel Grupotel Gravina 3* en LPD\r\n\r\n- Excursion incluse : Journ├⌐e ├á la Costa Brava\r\n\r\n- Accompagnement tout au long du s├⌐jour et des excursions en\r\noption','- Les pourboires et les d├⌐penses ├á caract├¿re personnel\r\n\r\n- Les entr├⌐es aux sites touristiques\r\n\r\n- Les transports en commun\r\n\r\n- Les activit├⌐s optionnelles\r\n\r\n- City tax : 20Γé¼ par personne (├á payer sur place\r\n\r\n- Le timbre de voyage : 60 DT\r\n\r\n- LΓÇÖassurance voyage (disponible en agence)','[{\"title\":\"Tunis - Barcelone\",\"desc\":\"Vol ├á destination de Barcelone, accueil et transfert ├á lΓÇÖh├┤tel.\\nInstallation dans les chambres.\\nPremi├¿re d├⌐couverte ├á pied avec votre accompagnateur : La Rambla et le Quartier\\ngothique de Barcelone.\\nNuit ├á lΓÇÖh├┤tel.\",\"tags\":[\"hebergement\",\"transport\"]},{\"title\":\"Sagrada Fam├¡lia\",\"desc\":\"Petit d├⌐jeuner ├á lΓÇÖh├┤tel.\\nProposition de visite de la c├⌐l├¿bre basilique con├ºue par Antoni Gaud├¡ (entr├⌐e\\net transport en option).\\nApr├¿s-midi libre avec assistance de votre accompagnateur.\\nNuit ├á lΓÇÖh├┤tel.\",\"tags\":[\"petitdej\",\"transport\"]},{\"title\":\"Costa Brava (incluse)\",\"desc\":\"Petit d├⌐jeuner ├á lΓÇÖh├┤tel.\\nD├⌐part pour une journ├⌐e baignade ├á la Costa Brava.\\nProfitez des plages, criques et paysages exceptionnels.\\nD├⌐jeuner libre.\\nRetour ├á Barcelone en fin de journ├⌐e.\\nNuit ├á lΓÇÖh├┤tel.\",\"tags\":[\"vol\",\"guide\"]},{\"title\":\"La Roca Village\",\"desc\":\"Petit d├⌐jeuner ├á lΓÇÖh├┤tel.\\nJourn├⌐e d├⌐di├⌐e au shopping dans un outlet de luxe avec plus de 150 boutiques et\\ndes r├⌐ductions jusquΓÇÖ├á -60%.\\n(Transport non inclus).\\nNuit ├á lΓÇÖh├┤tel.\",\"tags\":[\"petitdej\",\"transport\"]},{\"title\":\"Parc G├╝ell\",\"desc\":\"Petit d├⌐jeuner ├á lΓÇÖh├┤tel.\\nVisite du c├⌐l├¿bre parc sign├⌐ Gaud├¡ (transport et entr├⌐e en option).\\nApr├¿s-midi libre.\\nNuit ├á lΓÇÖh├┤tel.\",\"tags\":[\"petitdej\"]},{\"title\":\"Barcelone - Tunis\",\"desc\":\"Petit d├⌐jeuner ├á lΓÇÖh├┤tel.\\nTransfert ├á lΓÇÖa├⌐roport et vol retour vers Tunis.\",\"tags\":[\"petitdej\",\"dejeuner\",\"diner\"]}]');
/*!40000 ALTER TABLE `packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hotel_id` int(11) DEFAULT NULL,
  `client_name` varchar(100) NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `package_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hotel_id` (`hotel_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (3,NULL,'molka1',3,'ddddddddddd','rejected','2026-05-17 01:35:05',11),(4,NULL,'molka1',4,'ccffffffffffffffffff','rejected','2026-05-17 01:41:13',11),(5,NULL,'molka1',4,'kkkkkkkkkkkkkk','rejected','2026-05-17 01:51:06',11),(6,8,'molka1',4,'good experience !!!! im so satisfied','pending','2026-05-18 08:53:53',NULL),(7,NULL,'molka',5,'its good','pending','2026-05-18 14:52:51',16);
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@nextdestination.com','$2y$10$GyFsW9xh.neVUglrbKz6OuhcN9CdFRXJk1.8on05dwk71ZZ5PUeES','admin','2026-05-14 20:45:01'),(2,'molka','molka@gmil.com','$2y$10$3vgbUT9dr4BHEnAp/T7sX.XLBMWi3iX5sYoh4ltenJr8VG4lTDZ3O','user','2026-05-14 22:02:26'),(3,'molka1','mail@gmail.com','$2y$10$wNpwrgLbmZ6Jb9s7P498i.vMGW7u4OcnXZVtK/NWT57IWjsRTuTMy','user','2026-05-15 19:51:33'),(4,'mouhaned','m@gmail.com','$2y$10$ysD50lpiolgfi/u/8MNRPuuV.tOwRN4YZ71MrBWu4n./vY4gO1XTa','user','2026-05-18 12:59:02'),(5,'molka','molka@gmail.com','$2y$10$yy/VcSGxv1t/Fn3RyX0NBOMWr1QhuWKcOGMdH.SdP02Ly76JedUJq','user','2026-05-18 14:50:52');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-28 13:18:09
