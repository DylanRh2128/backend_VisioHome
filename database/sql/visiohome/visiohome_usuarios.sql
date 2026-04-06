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
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `docUsuario` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(180) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direccion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `idRol` tinyint NOT NULL,
  `rol` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'cliente',
  `especialidad` varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `biografia` text COLLATE utf8mb4_general_ci,
  `carrera` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `experiencia_anos` int NOT NULL DEFAULT '0',
  `nitInmobiliaria` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime DEFAULT NULL,
  `intentosFallidos` int NOT NULL DEFAULT '0',
  `bloqueadoHasta` datetime DEFAULT NULL,
  `google_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token_expire` datetime DEFAULT NULL,
  `genero` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'prefiero_no_decirlo',
  `departamento` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ciudad` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `login_count` int NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `cv_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`docUsuario`),
  UNIQUE KEY `correo` (`correo`),
  KEY `fk_usuarios_roles` (`idRol`),
  CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`idRol`) REFERENCES `roles` (`idRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES ('1001111111','Juan Pérez','juan.perez@example.com','3001234567','Calle 50 #10-20','$2y$10$NKTfPixVnLefzXaBXtvJ/.bp5k/DWX3QY7BsUbNSePhRCA/rwCrFa',2,'cliente',NULL,NULL,NULL,0,NULL,'2026-02-10 09:42:13',NULL,0,NULL,NULL,NULL,NULL,NULL,'prefiero_no_decirlo',NULL,NULL,1,0,NULL,NULL),('1003333333','Carlos López','carlos.lopez@example.com','3015551234','Carrera 15 #80-10','$2y$10$NKTfPixVnLefzXaBXtvJ/.bp5k/DWX3QY7BsUbNSePhRCA/rwCrFa',2,'cliente',NULL,NULL,NULL,0,NULL,'2026-02-10 09:42:13',NULL,0,NULL,NULL,NULL,NULL,NULL,'prefiero_no_decirlo',NULL,NULL,1,0,NULL,NULL),('1017185377','Senaida','senarios@gmail.com','3136205648',NULL,'$2y$12$2zzsvUQwYdAuGLbo2c.YAeHpBbCUILZF37GxzxHRqryrPaSzYdb6S',2,'cliente',NULL,NULL,NULL,0,NULL,'2026-04-06 01:46:45','2026-04-06 01:46:45',0,NULL,NULL,NULL,NULL,NULL,'Femenino','Antioquia','Medellín',1,0,NULL,NULL),('1033183120','Dylan Rios','dylanrios211@gmail.com','3135057694','Carrera 31 #75c-44','$2y$12$Sqd9Kp5hYsk..wybVKKJROeOOuf8XBwutPW8ITeegajjfVMU8Ttrm',1,'admin',NULL,NULL,NULL,0,NULL,'2026-02-10 09:42:13','2026-04-06 01:09:37',0,NULL,NULL,'avatars/MtvQ8aYsBoFVCHp7Sj2SvSTvR6OEr3XpXymUx49l.jpg',NULL,NULL,'prefiero_no_decirlo',NULL,NULL,1,0,NULL,NULL),('b706c840-07b1-4c30-bf8a-1365838e94a2','VisioHome','visiohome33@gmail.com',NULL,NULL,'$2y$12$l4FAy5ChIouKltBxfCBxk.IIFeK04Y9PcFCXwQfdMhiPpT0mIaZK6',2,'cliente',NULL,NULL,NULL,0,NULL,'2026-04-03 22:45:39','2026-04-03 22:45:39',0,NULL,'109631544141666808270','https://lh3.googleusercontent.com/a/ACg8ocK4z_ZpmSYc0rzQGUPw2itYAMwb2oVBBix19wXVu3U2WOWc-g=s96-c',NULL,NULL,'prefiero_no_decirlo','Sin asignar',NULL,1,0,'2026-04-04 03:45:38',NULL),('bdaed2a0-7827-4ccf-b22e-d4af818e0ecd','Dylan Rios','dylanhenao211@gmail.com',NULL,NULL,'$2y$12$XkV0SSKN7iMjEdn2DxzQfOWy8Oac0b2jCPQXmtR/Sybyt9pcaKfAu',2,'cliente',NULL,NULL,NULL,0,NULL,'2026-04-01 21:21:14','2026-04-06 00:06:15',0,NULL,'109864573026230044160','avatars/IIDAK6NKYCN7aFDeJmmSiiq7fEdDpEp4gzIbjwxY.jpg',NULL,NULL,'prefiero_no_decirlo','Sin asignar',NULL,1,0,'2026-04-02 02:21:14',NULL),('fc04e9c7-3046-11f1-ba6d-8c8caad63877','Carlos Ramirez','carlos@visiohome.com',NULL,NULL,'$2y$12$jCbf.xsBrRJCcJCnXlnVr.V937jv5LX7woCMDrxhxcfjwKGnN.BAS',3,'agente',NULL,NULL,NULL,0,NULL,'2026-04-04 11:54:45','2026-04-06 00:49:16',0,NULL,NULL,'avatars/sXNqSFByvFNY6I4BkjPaq1Lb06HmEMR4B4Sppc3o.jpg',NULL,NULL,'prefiero_no_decirlo',NULL,NULL,1,0,NULL,'agentes_cv/SmGfVoM4G83fZvy4GtuHg2N5x8wYMD27VqNm10NX.pdf'),('fc052fea-3046-11f1-ba6d-8c8caad63877','Andres','andres@visiohome.com',NULL,NULL,'$2y$12$22rl5OR7SdEYp6S9ipvGrOveWgBUFCDSOXozOvvNGHcXO/UMWGEUq',3,'agente',NULL,NULL,NULL,0,NULL,'2026-04-04 11:54:45','2026-04-04 11:54:45',0,NULL,NULL,NULL,NULL,NULL,'prefiero_no_decirlo',NULL,NULL,1,0,NULL,NULL),('fc055a18-3046-11f1-ba6d-8c8caad63877','Maria','maria@visiohome.com',NULL,NULL,'$2y$12$Ov1y2PIWDR7C0Iv0ndhlsOAttOb/JwzN/v/rvOwUk34D7XGdI0YCG',3,'agente',NULL,NULL,NULL,0,NULL,'2026-04-04 11:54:45','2026-04-04 11:54:45',0,NULL,NULL,NULL,NULL,NULL,'prefiero_no_decirlo',NULL,NULL,1,0,NULL,NULL),('fc056c0d-3046-11f1-ba6d-8c8caad63877','Laura','laura@visiohome.com',NULL,NULL,'$2y$12$ZuzpQx6cNOHI.cg635nUVeT1R9Etlsn7nO.OW9hBaG6IRjP6Zmzlq',3,'agente',NULL,NULL,NULL,0,NULL,'2026-04-04 11:54:45','2026-04-04 11:54:45',0,NULL,NULL,NULL,NULL,NULL,'prefiero_no_decirlo',NULL,NULL,1,0,NULL,NULL),('fc0576bc-3046-11f1-ba6d-8c8caad63877','Pedro','pedro@visiohome.com',NULL,NULL,'$2y$12$7AubeV04c8WhXMoNFddSoezgoxF/HG1.ezJIY4mNkFR7cL9/jsJDy',3,'agente',NULL,NULL,NULL,0,NULL,'2026-04-04 11:54:45','2026-04-04 11:54:45',0,NULL,NULL,NULL,NULL,NULL,'prefiero_no_decirlo',NULL,NULL,1,0,NULL,NULL),('fc058aa7-3046-11f1-ba6d-8c8caad63877','Juan','juan@visiohome.com',NULL,NULL,'$2y$12$WyWmpNGv3UrGuVsNj9Okde6LTOVvgUfMONRnRXWpMU6UC1DdigVre',3,'agente',NULL,NULL,NULL,0,NULL,'2026-04-04 11:54:45','2026-04-04 11:54:45',0,NULL,NULL,NULL,NULL,NULL,'prefiero_no_decirlo',NULL,NULL,1,0,NULL,NULL),('fc059f7e-3046-11f1-ba6d-8c8caad63877','Sofia','sofia@visiohome.com',NULL,NULL,'$2y$12$fCh4U6KN8uzQv6HP2ffxtOOGV3RTreRUYyI4PVfkGPy9bmLf0Kcei',3,'agente',NULL,NULL,NULL,0,NULL,'2026-04-04 11:54:45','2026-04-04 11:54:45',0,NULL,NULL,NULL,NULL,NULL,'prefiero_no_decirlo',NULL,NULL,1,0,NULL,NULL),('fc05af31-3046-11f1-ba6d-8c8caad63877','Daniel','daniel@visiohome.com',NULL,NULL,'$2y$12$fPrf2pB0npf3vzdbM7c2ieQG5Ah2GnESjHIDpsos85xnhqFh5K6b6',3,'agente',NULL,NULL,NULL,0,NULL,'2026-04-04 11:54:45','2026-04-04 11:54:45',0,NULL,NULL,NULL,NULL,NULL,'prefiero_no_decirlo',NULL,NULL,1,0,NULL,NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
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
