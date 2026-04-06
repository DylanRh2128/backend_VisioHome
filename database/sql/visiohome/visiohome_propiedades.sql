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
-- Table structure for table `propiedades`
--

DROP TABLE IF EXISTS `propiedades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `propiedades` (
  `idPropiedad` bigint NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `ubicacion` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ciudad` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tamano_m2` decimal(10,2) DEFAULT NULL,
  `precio` decimal(15,2) NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nitInmobiliaria` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime DEFAULT NULL,
  `modelo_3d_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `categoria_ciudad` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`idPropiedad`),
  KEY `fk_prop_inmobiliaria` (`nitInmobiliaria`),
  CONSTRAINT `fk_prop_inmobiliaria` FOREIGN KEY (`nitInmobiliaria`) REFERENCES `inmobiliarias` (`nitInmobiliaria`),
  CONSTRAINT `propiedades_chk_1` CHECK ((`estado` in (_utf8mb4'disponible',_utf8mb4'reservada',_utf8mb4'vendida',_utf8mb4'arrendada'))),
  CONSTRAINT `propiedades_chk_2` CHECK ((`tipo` in (_utf8mb4'casa',_utf8mb4'apartamento',_utf8mb4'lote',_utf8mb4'oficina',_utf8mb4'local',_utf8mb4'bodega',_utf8mb4'finca',_utf8mb4'otro')))
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `propiedades`
--

LOCK TABLES `propiedades` WRITE;
/*!40000 ALTER TABLE `propiedades` DISABLE KEYS */;
INSERT INTO `propiedades` VALUES (1,'Apartamento Moderno en Chapinero','Hermoso apartamento de 3 habitaciones con acabados de lujo, cocina integral, balcón con vista panorámica y parqueadero.','Chapinero, Bogotá','Bogotá',85.00,450000000.00,'disponible','apartamento','900123456-1','2026-04-05 20:40:19','2026-04-05 20:40:19','propiedades/modelos/casa_demo_1/scene.gltf','properties/propiedad_1/img1.jpg','Urbano'),(2,'Casa Campestre en La Calera','Espectacular casa campestre con 4 habitaciones, chimenea, jardín amplio, zona BBQ y vista a las montañas.','La Calera, Cundinamarca','La Calera',220.00,850000000.00,'disponible','casa','900123456-1','2026-04-05 20:40:19','2026-04-05 20:40:19','propiedades/modelos/casa_demo_2/scene.gltf','properties/propiedad_2/img1.jpg','Campestre'),(3,'Penthouse El Chicó Premium','Penthouse de lujo con ventanales de piso a techo, terraza privada de 40m2 y sistema domótico integrado.','El Chicó, Bogotá','Bogotá',180.00,1200000000.00,'disponible','apartamento','900123456-1','2026-04-05 20:40:19','2026-04-05 20:40:19','propiedades/modelos/casa_demo_3/scene.gltf','properties/propiedad_3/img1.jpg','Exclusivo'),(4,'Loft Industrial en Usaquén','Diseño vanguardista con techos de doble altura, paredes de ladrillo a la vista y excelente iluminación natural.','Usaquén, Bogotá','Bogotá',75.00,310000000.00,'disponible','apartamento','900987654-2','2026-04-05 20:40:19','2026-04-05 20:40:19',NULL,'properties/propiedad_4/img1.jpg','Urbano'),(5,'Dúplex en Santa Bárbara','Dúplex remodelado con acabados premium, cocina tipo americana y dos balcones.','Santa Bárbara, Bogotá','Bogotá',110.00,520000000.00,'disponible','apartamento','900987654-2','2026-04-05 20:40:19','2026-04-05 20:40:19',NULL,'properties/propiedad_5/img1.jpg','Urbano'),(6,'Apartamento en Cedritos','Apartamento de 3 habitaciones, 2 baños, sala-comedor amplia, cocina integral y cuarto útil.','Cedritos, Bogotá','Bogotá',95.00,380000000.00,'disponible','apartamento','900123456-1','2026-04-05 20:40:19','2026-04-05 20:40:19',NULL,'properties/propiedad_6/img1.jpg','Familiar'),(7,'Oficina en Zona Rosa (Vendida)','Oficina moderna en el corazón de la Zona Rosa, ideal para empresas de tecnología.','Zona Rosa, Bogotá','Bogotá',65.00,320000000.00,'vendida','oficina','900987654-2','2026-04-05 20:40:19','2026-04-05 20:40:19',NULL,'properties/propiedad_7/img1.jpg','Corporativo'),(8,'Local Comercial en Suba (Vendido)','Local comercial en zona de alto tráfico, ideal para restaurante o tienda.','Suba, Bogotá','Bogotá',120.00,280000000.00,'vendida','local','900987654-2','2026-04-05 20:40:19','2026-04-05 20:40:19',NULL,'properties/propiedad_8/img1.jpg','Comercial'),(9,'Casa Unifamiliar Modelia (Vendida)','Casa de dos pisos con garaje cubierto y amplio patio trasero.','Modelia, Bogotá','Bogotá',160.00,600000000.00,'vendida','casa','900123456-1','2026-04-05 20:40:19','2026-04-05 20:40:19',NULL,'propiedades/thumb_casa_default.jpg','Familiar');
/*!40000 ALTER TABLE `propiedades` ENABLE KEYS */;
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
