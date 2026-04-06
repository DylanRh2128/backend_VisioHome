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
-- Table structure for table `agentes`
--

DROP TABLE IF EXISTS `agentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agentes` (
  `docAgente` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `carrera` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `especialidad` varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `experiencia_anos` int NOT NULL DEFAULT '0',
  `direccion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ciudad` varchar(80) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `correo` varchar(180) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nitInmobiliaria` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `promedio_valoracion` decimal(3,2) NOT NULL DEFAULT '0.00',
  `cv_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`docAgente`),
  UNIQUE KEY `correo` (`correo`),
  KEY `fk_agente_inmobiliaria` (`nitInmobiliaria`),
  CONSTRAINT `fk_agente_inmobiliaria` FOREIGN KEY (`nitInmobiliaria`) REFERENCES `inmobiliarias` (`nitInmobiliaria`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agentes`
--

LOCK TABLES `agentes` WRITE;
/*!40000 ALTER TABLE `agentes` DISABLE KEYS */;
INSERT INTO `agentes` VALUES ('1001234567','Ana Martínez',NULL,NULL,0,'Calle 85 #12-34, Bogotá',NULL,'ana.martinez@visiohome.com','+57 300 123 4567','900123456-1',1,0.00,NULL),('1002345678','Roberto Silva',NULL,NULL,0,'Carrera 15 #90-12, Bogotá',NULL,'roberto.silva@visiohome.com','+57 301 234 5678','900123456-1',1,0.00,NULL),('1003456789','Laura Gómez',NULL,NULL,0,'Avenida 19 #120-45, Bogotá',NULL,'laura.gomez@visiohome.com','+57 302 345 6789','900987654-2',0,0.00,NULL);
/*!40000 ALTER TABLE `agentes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-05 21:00:19
