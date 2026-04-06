-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 10, 2026 at 03:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `visiohome`
--

-- --------------------------------------------------------

--
-- Table structure for table `agentes`
--

CREATE TABLE `agentes` (
  `docAgente` varchar(20) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `correo` varchar(180) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `nitInmobiliaria` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agentes`
--

INSERT INTO `agentes` (`docAgente`, `nombre`, `direccion`, `correo`, `telefono`, `nitInmobiliaria`, `activo`) VALUES
('1001234567', 'Ana Martínez', 'Calle 85 #12-34, Bogotá', 'ana.martinez@visiohome.com', '+57 300 123 4567', '900123456-1', 1),
('1002345678', 'Roberto Silva', 'Carrera 15 #90-12, Bogotá', 'roberto.silva@visiohome.com', '+57 301 234 5678', '900123456-1', 1),
('1003456789', 'Laura Gómez', 'Avenida 19 #120-45, Bogotá', 'laura.gomez@visiohome.com', '+57 302 345 6789', '900987654-2', 0);

-- --------------------------------------------------------

--
-- Table structure for table `citas`
--

CREATE TABLE `citas` (
  `idCita` bigint(20) NOT NULL,
  `idPropiedad` bigint(20) NOT NULL,
  `docUsuario` varchar(20) NOT NULL,
  `docAgente` varchar(20) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `estado` varchar(20) NOT NULL CHECK (`estado` in ('pendiente','confirmada','realizada','cancelada','no_asistio')),
  `canal` varchar(20) NOT NULL CHECK (`canal` in ('presencial','virtual')),
  `notas` text DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comentarios_propiedad`
--

CREATE TABLE `comentarios_propiedad` (
  `idComentario` bigint(20) NOT NULL,
  `idPropiedad` bigint(20) NOT NULL,
  `docUsuario` varchar(20) NOT NULL,
  `comentario` text NOT NULL,
  `puntuacion` tinyint(4) NOT NULL CHECK (`puntuacion` between 1 and 5),
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inmobiliarias`
--

CREATE TABLE `inmobiliarias` (
  `nitInmobiliaria` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `correo` varchar(180) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `objetivo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inmobiliarias`
--

INSERT INTO `inmobiliarias` (`nitInmobiliaria`, `nombre`, `correo`, `telefono`, `direccion`, `objetivo`) VALUES
('900123456-1', 'VisioHome Inmobiliaria', 'contacto@visiohome.com', '+57 601 234 5678', 'Calle 100 #15-20, Bogotá', 'Líder en soluciones inmobiliarias con tecnología de realidad aumentada'),
('900987654-2', 'Propiedades Premium', 'info@premiumprop.com', '+57 601 987 6543', 'Carrera 7 #80-45, Bogotá', 'Propiedades de lujo en las mejores zonas de la ciudad');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_02_09_212022_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pagos`
--

CREATE TABLE `pagos` (
  `idPago` bigint(20) NOT NULL,
  `docUsuario` varchar(20) NOT NULL,
  `idPropiedad` bigint(20) NOT NULL,
  `idCita` bigint(20) DEFAULT NULL,
  `monto` decimal(15,2) NOT NULL,
  `metodoPago` varchar(20) NOT NULL CHECK (`metodoPago` in ('tarjeta','transferencia','efectivo','paypal','otro')),
  `estado` varchar(20) NOT NULL CHECK (`estado` in ('pendiente','aprobado','rechazado','reembolsado')),
  `referencia` varchar(80) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pagos`
--

INSERT INTO `pagos` (`idPago`, `docUsuario`, `idPropiedad`, `idCita`, `monto`, `metodoPago`, `estado`, `referencia`, `fecha`) VALUES
(7, '1033183120', 11, NULL, 1500000.00, 'transferencia', 'aprobado', 'FAC-001-2025', '2026-01-26 09:45:42'),
(8, '1001111111', 12, NULL, 2300000.00, 'tarjeta', 'pendiente', 'FAC-002-2025', '2026-02-05 09:45:42'),
(9, '1033183120', 13, NULL, 890000.00, 'efectivo', 'rechazado', 'FAC-003-2025', '2026-02-08 09:45:42');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Usuario', 1033183120, 'visiohome-api', '8a454e68c10deed33195430b32b83d590976e51891a0b5f92e30c68bc407c190', '[\"*\"]', NULL, NULL, '2026-02-10 19:46:46', '2026-02-10 19:46:46');

-- --------------------------------------------------------

--
-- Table structure for table `propiedades`
--

CREATE TABLE `propiedades` (
  `idPropiedad` bigint(20) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(255) NOT NULL,
  `tamano_m2` decimal(10,2) DEFAULT NULL,
  `precio` decimal(15,2) NOT NULL,
  `estado` varchar(20) NOT NULL CHECK (`estado` in ('disponible','reservada','vendida','arrendada')),
  `tipo` varchar(20) NOT NULL CHECK (`tipo` in ('casa','apartamento','lote','oficina','local','bodega','finca','otro')),
  `nitInmobiliaria` varchar(20) NOT NULL,
  `creado_en` datetime DEFAULT current_timestamp(),
  `actualizado_en` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `propiedades`
--

INSERT INTO `propiedades` (`idPropiedad`, `titulo`, `descripcion`, `ubicacion`, `tamano_m2`, `precio`, `estado`, `tipo`, `nitInmobiliaria`, `creado_en`, `actualizado_en`) VALUES
(11, 'Apartamento Moderno en Chapinero', 'Hermoso apartamento de 3 habitaciones con acabados de lujo.', 'Chapinero, Bogotá', 85.00, 450000000.00, 'disponible', 'apartamento', '900123456-1', '2026-02-10 09:45:42', '2026-02-10 09:45:42'),
(12, 'Casa Campestre en La Calera', 'Casa campestre con 4 habitaciones y zona BBQ.', 'La Calera, Cundinamarca', 220.00, 850000000.00, 'vendida', 'casa', '900123456-1', '2026-02-10 09:45:42', '2026-02-10 09:45:42'),
(13, 'Oficina en Zona Rosa', 'Oficina moderna en el corazón de la Zona Rosa.', 'Zona Rosa, Bogotá', 65.00, 320000000.00, 'arrendada', 'oficina', '900987654-2', '2026-02-10 09:45:42', '2026-02-10 09:45:42'),
(14, 'Apartamento Familiar en Cedritos', 'Apartamento amplio ideal para familia.', 'Cedritos, Bogotá', 95.00, 380000000.00, 'disponible', 'apartamento', '900123456-1', '2026-02-10 09:45:42', '2026-02-10 09:45:42'),
(15, 'Local Comercial en Suba', 'Local comercial en zona de alto tráfico.', 'Suba, Bogotá', 120.00, 280000000.00, 'reservada', 'local', '900987654-2', '2026-02-10 09:45:42', '2026-02-10 09:45:42');

-- --------------------------------------------------------

--
-- Table structure for table `propiedad_imagenes`
--

CREATE TABLE `propiedad_imagenes` (
  `idImagen` bigint(20) NOT NULL,
  `idPropiedad` bigint(20) NOT NULL,
  `urlImagen` varchar(500) NOT NULL,
  `orden` int(11) DEFAULT 1,
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `idRol` tinyint(4) NOT NULL,
  `nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`idRol`, `nombre`) VALUES
(1, 'admin'),
(3, 'agente'),
(2, 'cliente');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `docUsuario` varchar(20) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `correo` varchar(180) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `idRol` tinyint(4) NOT NULL,
  `creado_en` datetime DEFAULT current_timestamp(),
  `actualizado_en` datetime DEFAULT NULL,
  `intentosFallidos` int(11) NOT NULL DEFAULT 0,
  `bloqueadoHasta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`docUsuario`, `nombre`, `correo`, `telefono`, `direccion`, `password`, `idRol`, `creado_en`, `actualizado_en`, `intentosFallidos`, `bloqueadoHasta`) VALUES
('1001111111', 'Juan Pérez', 'juan.perez@example.com', '3001234567', 'Calle 50 #10-20', '$2y$10$NKTfPixVnLefzXaBXtvJ/.bp5k/DWX3QY7BsUbNSePhRCA/rwCrFa', 2, '2026-02-10 09:42:13', NULL, 0, NULL),
('1002222222', 'María García', 'maria.garcia@example.com', '3009876543', 'Avenida 68 #45-30', '$2y$10$NKTfPixVnLefzXaBXtvJ/.bp5k/DWX3QY7BsUbNSePhRCA/rwCrFa', 3, '2026-02-10 09:42:13', NULL, 0, NULL),
('1003333333', 'Carlos López', 'carlos.lopez@example.com', '3015551234', 'Carrera 15 #80-10', '$2y$10$NKTfPixVnLefzXaBXtvJ/.bp5k/DWX3QY7BsUbNSePhRCA/rwCrFa', 2, '2026-02-10 09:42:13', NULL, 0, NULL),
('1033183120', 'Dylan Rios', 'dylanrios211@gmail.com', '3135057694', 'Carrera 31 #75c-44', '$2y$10$N9yjvq82BP6rX8h.7b2Quea6z8OS1a1TSHyfIx0LY9M3lGrjvafma', 1, '2026-02-10 09:42:13', NULL, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agentes`
--
ALTER TABLE `agentes`
  ADD PRIMARY KEY (`docAgente`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `fk_agente_inmobiliaria` (`nitInmobiliaria`);

--
-- Indexes for table `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`idCita`),
  ADD KEY `fk_cita_propiedad` (`idPropiedad`),
  ADD KEY `fk_cita_usuario` (`docUsuario`),
  ADD KEY `fk_cita_agente` (`docAgente`);

--
-- Indexes for table `comentarios_propiedad`
--
ALTER TABLE `comentarios_propiedad`
  ADD PRIMARY KEY (`idComentario`),
  ADD KEY `fk_coment_propiedad` (`idPropiedad`),
  ADD KEY `fk_coment_usuario` (`docUsuario`);

--
-- Indexes for table `inmobiliarias`
--
ALTER TABLE `inmobiliarias`
  ADD PRIMARY KEY (`nitInmobiliaria`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`idPago`),
  ADD UNIQUE KEY `referencia` (`referencia`),
  ADD KEY `fk_pago_usuario` (`docUsuario`),
  ADD KEY `fk_pago_propiedad` (`idPropiedad`),
  ADD KEY `fk_pago_cita` (`idCita`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `propiedades`
--
ALTER TABLE `propiedades`
  ADD PRIMARY KEY (`idPropiedad`),
  ADD KEY `fk_prop_inmobiliaria` (`nitInmobiliaria`);

--
-- Indexes for table `propiedad_imagenes`
--
ALTER TABLE `propiedad_imagenes`
  ADD PRIMARY KEY (`idImagen`),
  ADD KEY `fk_img_propiedad` (`idPropiedad`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`idRol`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`docUsuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `fk_usuarios_roles` (`idRol`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `citas`
--
ALTER TABLE `citas`
  MODIFY `idCita` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comentarios_propiedad`
--
ALTER TABLE `comentarios_propiedad`
  MODIFY `idComentario` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pagos`
--
ALTER TABLE `pagos`
  MODIFY `idPago` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `propiedades`
--
ALTER TABLE `propiedades`
  MODIFY `idPropiedad` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `propiedad_imagenes`
--
ALTER TABLE `propiedad_imagenes`
  MODIFY `idImagen` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `idRol` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agentes`
--
ALTER TABLE `agentes`
  ADD CONSTRAINT `fk_agente_inmobiliaria` FOREIGN KEY (`nitInmobiliaria`) REFERENCES `inmobiliarias` (`nitInmobiliaria`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `fk_cita_agente` FOREIGN KEY (`docAgente`) REFERENCES `agentes` (`docAgente`),
  ADD CONSTRAINT `fk_cita_propiedad` FOREIGN KEY (`idPropiedad`) REFERENCES `propiedades` (`idPropiedad`),
  ADD CONSTRAINT `fk_cita_usuario` FOREIGN KEY (`docUsuario`) REFERENCES `usuarios` (`docUsuario`);

--
-- Constraints for table `comentarios_propiedad`
--
ALTER TABLE `comentarios_propiedad`
  ADD CONSTRAINT `fk_coment_propiedad` FOREIGN KEY (`idPropiedad`) REFERENCES `propiedades` (`idPropiedad`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_coment_usuario` FOREIGN KEY (`docUsuario`) REFERENCES `usuarios` (`docUsuario`);

--
-- Constraints for table `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `fk_pago_cita` FOREIGN KEY (`idCita`) REFERENCES `citas` (`idCita`),
  ADD CONSTRAINT `fk_pago_propiedad` FOREIGN KEY (`idPropiedad`) REFERENCES `propiedades` (`idPropiedad`),
  ADD CONSTRAINT `fk_pago_usuario` FOREIGN KEY (`docUsuario`) REFERENCES `usuarios` (`docUsuario`);

--
-- Constraints for table `propiedades`
--
ALTER TABLE `propiedades`
  ADD CONSTRAINT `fk_prop_inmobiliaria` FOREIGN KEY (`nitInmobiliaria`) REFERENCES `inmobiliarias` (`nitInmobiliaria`);

--
-- Constraints for table `propiedad_imagenes`
--
ALTER TABLE `propiedad_imagenes`
  ADD CONSTRAINT `fk_img_propiedad` FOREIGN KEY (`idPropiedad`) REFERENCES `propiedades` (`idPropiedad`) ON DELETE CASCADE;

--
-- Constraints for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`idRol`) REFERENCES `roles` (`idRol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
