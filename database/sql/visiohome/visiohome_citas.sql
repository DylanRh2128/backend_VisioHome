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
-- Table structure for table `citas`
--

DROP TABLE IF EXISTS `citas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `citas` (
  `idCita` bigint NOT NULL AUTO_INCREMENT,
  `idPropiedad` bigint NOT NULL,
  `docUsuario` varchar(36) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `docAgente` varchar(36) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `canal` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `notas` text COLLATE utf8mb4_general_ci,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  `idDisponibilidad` bigint DEFAULT NULL,
  `precio` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`idCita`),
  KEY `fk_cita_propiedad` (`idPropiedad`),
  KEY `fk_cita_usuario` (`docUsuario`),
  KEY `fk_cita_agente` (`docAgente`),
  CONSTRAINT `fk_cita_propiedad` FOREIGN KEY (`idPropiedad`) REFERENCES `propiedades` (`idPropiedad`),
  CONSTRAINT `fk_cita_usuario` FOREIGN KEY (`docUsuario`) REFERENCES `usuarios` (`docUsuario`),
  CONSTRAINT `citas_chk_1` CHECK ((`estado` in (_utf8mb4'pendiente',_utf8mb4'confirmada',_utf8mb4'realizada',_utf8mb4'cancelada',_utf8mb4'no_asistio'))),
  CONSTRAINT `citas_chk_2` CHECK ((`canal` in (_utf8mb4'presencial',_utf8mb4'virtual')))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citas`
--

LOCK TABLES `citas` WRITE;
/*!40000 ALTER TABLE `citas` DISABLE KEYS */;
INSERT INTO `citas` VALUES (2,11,'bdaed2a0-7827-4ccf-b22e-d4af818e0ecd','fc04e9c7-3046-11f1-ba6d-8c8caad63877','2026-04-06 08:00:00','cancelada','presencial',NULL,'2026-04-04 12:12:21',2,30000.00),(3,1,'bdaed2a0-7827-4ccf-b22e-d4af818e0ecd','fc04e9c7-3046-11f1-ba6d-8c8caad63877','2026-04-07 08:00:00','confirmada','presencial',NULL,'2026-04-05 19:37:35',3,50000.00);
/*!40000 ALTER TABLE `citas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-05 21:00:18
