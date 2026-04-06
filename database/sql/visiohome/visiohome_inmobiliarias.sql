-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: visiohome
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `inmobiliarias`
--

DROP TABLE IF EXISTS `inmobiliarias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inmobiliarias` (
  `nitInmobiliaria` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(180) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direccion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `objetivo` text COLLATE utf8mb4_general_ci,
  `logo_light` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `logo_dark` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`nitInmobiliaria`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inmobiliarias`
--

LOCK TABLES `inmobiliarias` WRITE;
/*!40000 ALTER TABLE `inmobiliarias` DISABLE KEYS */;
INSERT INTO `inmobiliarias` VALUES ('900123456-1','VisioHome Inmobiliaria','contacto@visiohome.com','+57 601 234 5678','Calle 100 #15-20, Bogotá','Líder en soluciones inmobiliarias con tecnología de realidad aumentada','storage/logos/logo_light.png','storage/logos/logo_dark.png'),('900987654-2','Propiedades Premium','info@premiumprop.com','+57 601 987 6543','Carrera 7 #80-45, Bogotá','Propiedades de lujo en las mejores zonas de la ciudad',NULL,NULL);
/*!40000 ALTER TABLE `inmobiliarias` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-05 21:00:16
