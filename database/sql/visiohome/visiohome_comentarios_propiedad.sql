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
-- Table structure for table `comentarios_propiedad`
--

DROP TABLE IF EXISTS `comentarios_propiedad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comentarios_propiedad` (
  `idComentario` bigint NOT NULL AUTO_INCREMENT,
  `idPropiedad` bigint NOT NULL,
  `docUsuario` varchar(36) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comentario` text COLLATE utf8mb4_general_ci NOT NULL,
  `puntuacion` tinyint NOT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idComentario`),
  KEY `fk_coment_propiedad` (`idPropiedad`),
  KEY `fk_coment_usuario` (`docUsuario`),
  CONSTRAINT `fk_coment_propiedad` FOREIGN KEY (`idPropiedad`) REFERENCES `propiedades` (`idPropiedad`) ON DELETE CASCADE,
  CONSTRAINT `fk_coment_usuario` FOREIGN KEY (`docUsuario`) REFERENCES `usuarios` (`docUsuario`),
  CONSTRAINT `comentarios_propiedad_chk_1` CHECK ((`puntuacion` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentarios_propiedad`
--

LOCK TABLES `comentarios_propiedad` WRITE;
/*!40000 ALTER TABLE `comentarios_propiedad` DISABLE KEYS */;
INSERT INTO `comentarios_propiedad` VALUES (1,11,'bdaed2a0-7827-4ccf-b22e-d4af818e0ecd','esta muy linda',5,'2026-04-03 06:52:20');
/*!40000 ALTER TABLE `comentarios_propiedad` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-05 21:00:13
