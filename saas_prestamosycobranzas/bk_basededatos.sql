-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para saas_prestamos_cobranzas
CREATE DATABASE IF NOT EXISTS `saas_prestamos_cobranzas` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `saas_prestamos_cobranzas`;

-- Volcando estructura para tabla saas_prestamos_cobranzas.auditorias
CREATE TABLE IF NOT EXISTS `auditorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `usuario_nombre` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modulo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auditorias_user_id_foreign` (`user_id`),
  KEY `auditorias_modulo_accion_index` (`modulo`,`accion`),
  CONSTRAINT `auditorias_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.auditorias: ~11 rows (aproximadamente)
DELETE FROM `auditorias`;
INSERT INTO `auditorias` (`id`, `user_id`, `usuario_nombre`, `accion`, `modulo`, `referencia`, `descripcion`, `ip`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Administrador', 'cierre sesion', 'Autenticacion', NULL, 'El usuario cerro sesion.', '127.0.0.1', '2026-06-19 10:26:11', '2026-06-19 10:26:11'),
	(2, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-19 10:26:15', '2026-06-19 10:26:15'),
	(3, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-19 13:47:57', '2026-06-19 13:47:57'),
	(4, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-19 19:26:42', '2026-06-19 19:26:42'),
	(5, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-20 14:12:28', '2026-06-20 14:12:28'),
	(6, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-21 12:55:20', '2026-06-21 12:55:20'),
	(7, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-22 01:31:34', '2026-06-22 01:31:34'),
	(8, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-22 12:05:09', '2026-06-22 12:05:09'),
	(9, 1, 'Administrador', 'cierre sesion', 'Autenticacion', NULL, 'El usuario cerro sesion.', '127.0.0.1', '2026-06-22 12:40:40', '2026-06-22 12:40:40'),
	(10, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-22 12:40:56', '2026-06-22 12:40:56'),
	(11, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-23 13:21:30', '2026-06-23 13:21:30'),
	(12, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-23 20:37:41', '2026-06-23 20:37:41'),
	(13, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-24 04:25:13', '2026-06-24 04:25:13'),
	(14, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-24 12:24:35', '2026-06-24 12:24:35'),
	(15, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-25 12:20:25', '2026-06-25 12:20:25'),
	(16, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-26 02:17:40', '2026-06-26 02:17:40'),
	(17, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-26 12:15:26', '2026-06-26 12:15:26'),
	(18, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-27 01:51:51', '2026-06-27 01:51:51'),
	(19, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-27 11:40:41', '2026-06-27 11:40:41'),
	(20, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-28 04:25:19', '2026-06-28 04:25:19'),
	(21, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-28 11:53:19', '2026-06-28 11:53:19'),
	(22, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-29 01:00:45', '2026-06-29 01:00:45'),
	(23, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-29 13:23:38', '2026-06-29 13:23:38'),
	(24, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-30 02:41:59', '2026-06-30 02:41:59'),
	(25, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-30 12:36:27', '2026-06-30 12:36:27'),
	(26, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-30 15:56:35', '2026-06-30 15:56:35'),
	(27, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-06-30 20:45:15', '2026-06-30 20:45:15'),
	(28, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-01 12:54:22', '2026-07-01 12:54:22'),
	(29, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-02 11:43:45', '2026-07-02 11:43:45'),
	(30, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-03 12:47:29', '2026-07-03 12:47:29'),
	(31, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-04 12:24:28', '2026-07-04 12:24:28'),
	(32, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-05 04:21:49', '2026-07-05 04:21:49'),
	(33, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-05 11:56:32', '2026-07-05 11:56:32'),
	(34, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-05 20:20:39', '2026-07-05 20:20:39'),
	(35, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-06 12:59:52', '2026-07-06 12:59:52'),
	(36, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-06 17:42:47', '2026-07-06 17:42:47'),
	(37, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-07 15:54:19', '2026-07-07 15:54:19'),
	(38, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-08 12:13:12', '2026-07-08 12:13:12'),
	(39, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-09 12:29:21', '2026-07-09 12:29:21'),
	(40, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-10 12:40:25', '2026-07-10 12:40:25'),
	(41, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-11 16:29:53', '2026-07-11 16:29:53'),
	(42, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-12 12:35:20', '2026-07-12 12:35:20'),
	(43, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-13 04:04:44', '2026-07-13 04:04:44'),
	(44, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-13 13:27:53', '2026-07-13 13:27:53'),
	(45, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-13 20:29:12', '2026-07-13 20:29:12'),
	(46, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-14 01:52:49', '2026-07-14 01:52:49'),
	(47, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-14 12:56:09', '2026-07-14 12:56:09'),
	(48, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-15 02:32:47', '2026-07-15 02:32:47'),
	(49, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-24 13:30:43', '2026-07-24 13:30:43'),
	(50, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-25 04:49:26', '2026-07-25 04:49:26'),
	(51, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-25 13:01:17', '2026-07-25 13:01:17'),
	(52, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-26 04:09:58', '2026-07-26 04:09:58'),
	(53, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-26 13:43:29', '2026-07-26 13:43:29'),
	(54, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-27 03:06:31', '2026-07-27 03:06:31'),
	(55, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-27 12:57:48', '2026-07-27 12:57:48'),
	(56, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-28 02:00:01', '2026-07-28 02:00:01'),
	(57, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-28 14:02:59', '2026-07-28 14:02:59'),
	(58, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-29 03:04:23', '2026-07-29 03:04:23'),
	(59, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-29 13:06:32', '2026-07-29 13:06:32'),
	(60, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-30 13:28:24', '2026-07-30 13:28:24'),
	(61, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-31 01:50:47', '2026-07-31 01:50:47'),
	(62, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-07-31 14:29:40', '2026-07-31 14:29:40'),
	(63, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-01 12:49:34', '2026-08-01 12:49:34'),
	(64, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-01 14:53:26', '2026-08-01 14:53:26'),
	(65, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-02 16:12:16', '2026-08-02 16:12:16'),
	(66, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-03 13:59:33', '2026-08-03 13:59:33'),
	(67, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-03 18:42:47', '2026-08-03 18:42:47'),
	(68, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-04 03:06:10', '2026-08-04 03:06:10'),
	(69, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-04 05:33:21', '2026-08-04 05:33:21'),
	(70, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-04 13:31:50', '2026-08-04 13:31:50'),
	(71, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-04 22:58:13', '2026-08-04 22:58:13'),
	(72, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-05 16:13:07', '2026-08-05 16:13:07'),
	(73, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-05 18:30:28', '2026-08-05 18:30:28'),
	(74, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-06 18:54:14', '2026-08-06 18:54:14'),
	(75, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-07 18:29:04', '2026-08-07 18:29:04'),
	(76, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-08 14:55:20', '2026-08-08 14:55:20'),
	(77, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-08 19:27:55', '2026-08-08 19:27:55'),
	(78, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-09 18:31:37', '2026-08-09 18:31:37'),
	(79, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-10 04:02:48', '2026-08-10 04:02:48'),
	(80, 1, 'Administrador', 'inicio sesion', 'Autenticacion', NULL, 'El usuario inicio sesion.', '127.0.0.1', '2026-08-10 06:11:12', '2026-08-10 06:11:12');

-- Volcando estructura para tabla saas_prestamos_cobranzas.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.cache: ~0 rows (aproximadamente)
DELETE FROM `cache`;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('sistema_de_prestamos_pro_cache_config_all', 'a:0:{}', 2097223671);

-- Volcando estructura para tabla saas_prestamos_cobranzas.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.cache_locks: ~0 rows (aproximadamente)
DELETE FROM `cache_locks`;

-- Volcando estructura para tabla saas_prestamos_cobranzas.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombres` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_documento` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DNI',
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ocupacion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ingreso_mensual` decimal(12,2) NOT NULL DEFAULT '0.00',
  `estado` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientes_codigo_unique` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.clientes: ~11 rows (aproximadamente)
DELETE FROM `clientes`;
INSERT INTO `clientes` (`id`, `codigo`, `nombres`, `apellidos`, `documento`, `tipo_documento`, `telefono`, `email`, `direccion`, `ocupacion`, `ingreso_mensual`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 'CLI-0001', 'Maria', 'Lopez Quispe', '40665269', 'DNI', '960752952', 'maria@correo.test', 'Av. Principal 701', 'Comerciante', 1107.00, 'activo', NULL, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(2, 'CLI-0002', 'Juan', 'Perez Rojas', '40643203', 'DNI', '934955127', 'juan@correo.test', 'Av. Principal 414', 'Empleado', 3978.00, 'activo', NULL, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(3, 'CLI-0003', 'Ana', 'Torres Diaz', '40552356', 'DNI', '964082339', 'ana@correo.test', 'Av. Principal 423', 'Comerciante', 4310.00, 'activo', NULL, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(4, 'CLI-0004', 'Luis', 'Garcia Mamani', '40382942', 'DNI', '991185531', 'luis@correo.test', 'Av. Principal 279', 'Agricultor', 4217.00, 'activo', NULL, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(5, 'CLI-0005', 'Rosa', 'Flores Vega', '40451894', 'DNI', '929500396', 'rosa@correo.test', 'Av. Principal 347', 'Comerciante', 3205.00, 'activo', NULL, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(6, 'CLI-0006', 'Pedro', 'Ramos Soto', '40451117', 'DNI', '990464510', 'pedro@correo.test', 'Av. Principal 289', 'Independiente', 2268.00, 'activo', NULL, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(7, 'CLI-0007', 'Carmen', 'Castro Nina', '40690025', 'DNI', '988511737', 'carmen@correo.test', 'Av. Principal 256', 'Agricultor', 4496.00, 'activo', NULL, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(8, 'CLI-0008', 'Jorge', 'Vargas Leon', '40369199', 'DNI', '933142222', 'jorge@correo.test', 'Av. Principal 577', 'Independiente', 2371.00, 'activo', NULL, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(9, 'CLI-0009', 'Lucia', 'Mendoza Cruz', '40535656', 'DNI', '950487588', 'lucia@correo.test', 'Av. Principal 246', 'Comerciante', 4839.00, 'activo', NULL, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(10, 'CLI-0010', 'Miguel', 'Chavez Pino', '40143702', 'DNI', '930509007', 'miguel@correo.test', 'Av. Principal 369', 'Empleado', 3705.00, 'activo', NULL, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(11, 'CLI-0011', 'Elena', 'Salas Ruiz', '40674351', 'DNI', '993757907', 'elena@correo.test', 'Av. Principal 348', 'Independiente', 2352.00, 'activo', NULL, '2026-06-19 09:04:13', '2026-06-19 09:04:13');

-- Volcando estructura para tabla saas_prestamos_cobranzas.comprobantes
CREATE TABLE IF NOT EXISTS `comprobantes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '03',
  `serie` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correlativo` int unsigned NOT NULL,
  `prestamo_id` bigint unsigned DEFAULT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `cliente_tipo_doc` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `cliente_num_doc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliente_nombre` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moneda` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEN',
  `afectacion` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '10',
  `gravado` decimal(12,2) NOT NULL DEFAULT '0.00',
  `exonerado` decimal(12,2) NOT NULL DEFAULT '0.00',
  `inafecto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `igv` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `concepto` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `sunat_codigo` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci,
  `hash` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `xml_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comprobantes_tipo_serie_correlativo_unique` (`tipo`,`serie`,`correlativo`),
  KEY `comprobantes_prestamo_id_foreign` (`prestamo_id`),
  KEY `comprobantes_cliente_id_foreign` (`cliente_id`),
  KEY `comprobantes_user_id_foreign` (`user_id`),
  CONSTRAINT `comprobantes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `comprobantes_prestamo_id_foreign` FOREIGN KEY (`prestamo_id`) REFERENCES `prestamos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `comprobantes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.comprobantes: ~0 rows (aproximadamente)
DELETE FROM `comprobantes`;

-- Volcando estructura para tabla saas_prestamos_cobranzas.configuraciones
CREATE TABLE IF NOT EXISTS `configuraciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci,
  `grupo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configuraciones_clave_unique` (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.configuraciones: ~0 rows (aproximadamente)
DELETE FROM `configuraciones`;

-- Volcando estructura para tabla saas_prestamos_cobranzas.cortes_caja
CREATE TABLE IF NOT EXISTS `cortes_caja` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `total_cobros` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_ingresos` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_egresos` decimal(12,2) NOT NULL DEFAULT '0.00',
  `saldo_calculado` decimal(12,2) NOT NULL DEFAULT '0.00',
  `monto_contado` decimal(12,2) NOT NULL DEFAULT '0.00',
  `diferencia` decimal(12,2) NOT NULL DEFAULT '0.00',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cortes_caja_user_id_foreign` (`user_id`),
  CONSTRAINT `cortes_caja_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.cortes_caja: ~0 rows (aproximadamente)
DELETE FROM `cortes_caja`;

-- Volcando estructura para tabla saas_prestamos_cobranzas.cuotas
CREATE TABLE IF NOT EXISTS `cuotas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `prestamo_id` bigint unsigned NOT NULL,
  `numero` int NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `capital` decimal(12,2) NOT NULL DEFAULT '0.00',
  `interes` decimal(12,2) NOT NULL DEFAULT '0.00',
  `mora` decimal(12,2) NOT NULL DEFAULT '0.00',
  `monto_pagado` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fecha_pago` date DEFAULT NULL,
  `estado` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cuotas_prestamo_id_foreign` (`prestamo_id`),
  CONSTRAINT `cuotas_prestamo_id_foreign` FOREIGN KEY (`prestamo_id`) REFERENCES `prestamos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.cuotas: ~64 rows (aproximadamente)
DELETE FROM `cuotas`;
INSERT INTO `cuotas` (`id`, `prestamo_id`, `numero`, `fecha_vencimiento`, `monto`, `capital`, `interes`, `mora`, `monto_pagado`, `fecha_pago`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, '2026-06-06', 82.50, 75.00, 7.50, 0.00, 82.50, '2026-06-06', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(2, 1, 2, '2026-07-06', 82.50, 75.00, 7.50, 0.00, 82.50, '2026-07-06', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(3, 1, 3, '2026-08-06', 82.50, 75.00, 7.50, 0.00, 82.50, '2026-08-01', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(4, 1, 4, '2026-09-06', 82.50, 75.00, 7.50, 0.00, 82.50, '2026-09-03', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(5, 1, 5, '2026-10-06', 82.50, 75.00, 7.50, 0.00, 82.50, '2026-10-04', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(6, 1, 6, '2026-11-06', 82.50, 75.00, 7.50, 0.00, 82.50, '2026-11-04', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(7, 1, 7, '2026-12-06', 82.50, 75.00, 7.50, 0.00, 82.50, '2026-12-02', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(8, 1, 8, '2027-01-06', 82.50, 75.00, 7.50, 0.00, 82.50, '2027-01-06', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(9, 2, 1, '2026-07-09', 513.33, 466.67, 46.67, 0.00, 513.33, '2026-07-09', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(10, 2, 2, '2026-08-09', 513.33, 466.67, 46.67, 0.00, 513.33, '2026-08-06', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(11, 2, 3, '2026-09-09', 513.33, 466.67, 46.67, 0.00, 513.33, '2026-09-04', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(12, 2, 4, '2026-10-09', 513.33, 466.67, 46.67, 0.00, 513.33, '2026-10-08', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(13, 2, 5, '2026-11-09', 513.33, 466.67, 46.67, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(14, 2, 6, '2026-12-09', 513.33, 466.67, 46.67, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(15, 3, 1, '2026-06-04', 280.00, 250.00, 30.00, 0.00, 280.00, '2026-05-31', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(16, 3, 2, '2026-07-04', 280.00, 250.00, 30.00, 0.00, 280.00, '2026-07-01', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(17, 3, 3, '2026-08-04', 280.00, 250.00, 30.00, 0.00, 280.00, '2026-07-30', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(18, 3, 4, '2026-09-04', 280.00, 250.00, 30.00, 0.00, 280.00, '2026-08-31', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(19, 4, 1, '2026-03-06', 366.67, 333.33, 33.33, 0.00, 366.67, '2026-03-06', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(20, 4, 2, '2026-04-06', 366.67, 333.33, 33.33, 0.00, 366.67, '2026-04-04', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(21, 4, 3, '2026-05-06', 366.67, 333.33, 33.33, 0.00, 0.00, NULL, 'vencido', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(22, 4, 4, '2026-06-06', 366.67, 333.33, 33.33, 0.00, 0.00, NULL, 'vencido', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(23, 4, 5, '2026-07-06', 366.67, 333.33, 33.33, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(24, 4, 6, '2026-08-06', 366.67, 333.33, 33.33, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(25, 5, 1, '2026-04-05', 268.33, 233.33, 35.00, 0.00, 268.33, '2026-04-03', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(26, 5, 2, '2026-05-05', 268.33, 233.33, 35.00, 0.00, 268.33, '2026-04-30', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(27, 5, 3, '2026-06-05', 268.33, 233.33, 35.00, 0.00, 268.33, '2026-06-01', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(28, 5, 4, '2026-07-05', 268.33, 233.33, 35.00, 0.00, 268.33, '2026-07-01', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(29, 5, 5, '2026-08-05', 268.33, 233.33, 35.00, 0.00, 268.33, '2026-08-02', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(30, 5, 6, '2026-09-05', 268.33, 233.33, 35.00, 0.00, 268.33, '2026-09-03', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(31, 5, 7, '2026-10-05', 268.33, 233.33, 35.00, 0.00, 268.33, '2026-10-03', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(32, 5, 8, '2026-11-05', 268.33, 233.33, 35.00, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(33, 5, 9, '2026-12-05', 268.33, 233.33, 35.00, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(34, 5, 10, '2027-01-05', 268.33, 233.33, 35.00, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(35, 5, 11, '2027-02-05', 268.33, 233.33, 35.00, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(36, 5, 12, '2027-03-05', 268.33, 233.33, 35.00, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(37, 6, 1, '2026-05-03', 261.33, 233.33, 28.00, 0.00, 261.33, '2026-05-02', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(38, 6, 2, '2026-06-03', 261.33, 233.33, 28.00, 0.00, 261.33, '2026-06-02', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(39, 6, 3, '2026-07-03', 261.33, 233.33, 28.00, 0.00, 261.33, '2026-06-29', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(40, 6, 4, '2026-08-03', 261.33, 233.33, 28.00, 0.00, 261.33, '2026-08-03', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(41, 6, 5, '2026-09-03', 261.33, 233.33, 28.00, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(42, 6, 6, '2026-10-03', 261.33, 233.33, 28.00, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(43, 7, 1, '2026-04-15', 149.33, 133.33, 16.00, 0.00, 149.33, '2026-04-11', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(44, 7, 2, '2026-05-15', 149.33, 133.33, 16.00, 0.00, 149.33, '2026-05-14', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(45, 7, 3, '2026-06-15', 149.33, 133.33, 16.00, 0.00, 149.33, '2026-06-12', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(46, 7, 4, '2026-07-15', 149.33, 133.33, 16.00, 0.00, 149.33, '2026-07-15', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(47, 7, 5, '2026-08-15', 149.33, 133.33, 16.00, 0.00, 149.33, '2026-08-12', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(48, 7, 6, '2026-09-15', 149.33, 133.33, 16.00, 0.00, 149.33, '2026-09-13', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(49, 7, 7, '2026-10-15', 149.33, 133.33, 16.00, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(50, 7, 8, '2026-11-15', 149.33, 133.33, 16.00, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(51, 7, 9, '2026-12-15', 149.33, 133.33, 16.00, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(52, 7, 10, '2027-01-15', 149.33, 133.33, 16.00, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(53, 7, 11, '2027-02-15', 149.33, 133.33, 16.00, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(54, 7, 12, '2027-03-15', 149.33, 133.33, 16.00, 0.00, 0.00, NULL, 'pendiente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(55, 8, 1, '2026-03-18', 687.50, 625.00, 62.50, 0.00, 687.50, '2026-03-16', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(56, 8, 2, '2026-04-18', 687.50, 625.00, 62.50, 0.00, 687.50, '2026-04-16', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(57, 8, 3, '2026-05-18', 687.50, 625.00, 62.50, 0.00, 687.50, '2026-05-17', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(58, 8, 4, '2026-06-18', 687.50, 625.00, 62.50, 0.00, 687.50, '2026-06-18', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(59, 9, 1, '2026-07-02', 153.33, 133.33, 20.00, 0.00, 153.33, '2026-07-01', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(60, 9, 2, '2026-08-02', 153.33, 133.33, 20.00, 0.00, 153.33, '2026-07-28', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(61, 9, 3, '2026-09-02', 153.33, 133.33, 20.00, 0.00, 153.33, '2026-08-29', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(62, 9, 4, '2026-10-02', 153.33, 133.33, 20.00, 0.00, 153.33, '2026-09-29', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(63, 9, 5, '2026-11-02', 153.33, 133.33, 20.00, 0.00, 153.33, '2026-10-29', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(64, 9, 6, '2026-12-02', 153.33, 133.33, 20.00, 0.00, 153.33, '2026-12-01', 'pagado', '2026-06-19 09:04:13', '2026-06-19 09:04:13');

-- Volcando estructura para tabla saas_prestamos_cobranzas.empenos
CREATE TABLE IF NOT EXISTS `empenos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `articulo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `valor_tasacion` decimal(12,2) NOT NULL,
  `monto_prestado` decimal(12,2) NOT NULL,
  `tasa_interes` decimal(5,2) NOT NULL DEFAULT '0.00',
  `fecha_inicio` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vigente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empenos_codigo_unique` (`codigo`),
  KEY `empenos_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `empenos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.empenos: ~4 rows (aproximadamente)
DELETE FROM `empenos`;
INSERT INTO `empenos` (`id`, `codigo`, `cliente_id`, `articulo`, `descripcion`, `valor_tasacion`, `monto_prestado`, `tasa_interes`, `fecha_inicio`, `fecha_vencimiento`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 'EMP-0001', 2, 'Laptop HP', 'Articulo en garantia', 800.00, 480.00, 15.00, '2026-04-26', '2026-07-09', 'vigente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(2, 'EMP-0002', 6, 'Television Samsung 50"', 'Articulo en garantia', 2400.00, 1440.00, 15.00, '2026-05-18', '2026-08-15', 'vigente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(3, 'EMP-0003', 8, 'Anillo de oro 18k', 'Articulo en garantia', 2400.00, 1440.00, 15.00, '2026-04-29', '2026-07-26', 'vigente', '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(4, 'EMP-0004', 6, 'Moto Honda', 'Articulo en garantia', 3200.00, 1920.00, 15.00, '2026-05-16', '2026-08-03', 'vigente', '2026-06-19 09:04:13', '2026-06-19 09:04:13');

-- Volcando estructura para tabla saas_prestamos_cobranzas.facturacion_configs
CREATE TABLE IF NOT EXISTS `facturacion_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `habilitado` tinyint(1) NOT NULL DEFAULT '0',
  `emitir_automatico` tinyint(1) NOT NULL DEFAULT '1',
  `driver` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ninguno',
  `entorno` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'beta',
  `ruc` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razon_social` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_comercial` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ubigeo` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departamento` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provincia` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distrito` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serie_factura` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'F001',
  `serie_boleta` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'B001',
  `sol_usuario` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sol_clave` text COLLATE utf8mb4_unicode_ci,
  `certificado_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificado_clave` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.facturacion_configs: ~0 rows (aproximadamente)
DELETE FROM `facturacion_configs`;
INSERT INTO `facturacion_configs` (`id`, `habilitado`, `emitir_automatico`, `driver`, `entorno`, `ruc`, `razon_social`, `nombre_comercial`, `direccion`, `ubigeo`, `departamento`, `provincia`, `distrito`, `serie_factura`, `serie_boleta`, `sol_usuario`, `sol_clave`, `certificado_path`, `certificado_clave`, `created_at`, `updated_at`) VALUES
	(1, 0, 1, 'ninguno', 'beta', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'F001', 'B001', NULL, NULL, NULL, NULL, '2026-08-10 03:44:30', '2026-08-10 03:44:30');

-- Volcando estructura para tabla saas_prestamos_cobranzas.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.failed_jobs: ~0 rows (aproximadamente)
DELETE FROM `failed_jobs`;

-- Volcando estructura para tabla saas_prestamos_cobranzas.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.jobs: ~0 rows (aproximadamente)
DELETE FROM `jobs`;

-- Volcando estructura para tabla saas_prestamos_cobranzas.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.job_batches: ~0 rows (aproximadamente)
DELETE FROM `job_batches`;

-- Volcando estructura para tabla saas_prestamos_cobranzas.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.migrations: ~12 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_01_01_000010_create_clientes_table', 1),
	(5, '2026_01_01_000020_create_prestamos_table', 1),
	(6, '2026_01_01_000030_create_cuotas_table', 1),
	(7, '2026_01_01_000040_create_pagos_table', 1),
	(8, '2026_01_01_000050_create_empenos_table', 1),
	(9, '2026_01_01_000060_create_configuraciones_table', 2),
	(10, '2026_01_01_000070_create_caja_tables', 3),
	(11, '2026_01_01_000080_create_auditorias_table', 4),
	(12, '2026_01_01_000090_add_avatar_to_users_table', 5),
	(13, '2026_08_09_000100_create_facturacion_configs_table', 6),
	(14, '2026_08_09_000200_create_comprobantes_table', 7);

-- Volcando estructura para tabla saas_prestamos_cobranzas.movimientos_caja
CREATE TABLE IF NOT EXISTS `movimientos_caja` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `tipo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `concepto` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `metodo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `movimientos_caja_codigo_unique` (`codigo`),
  KEY `movimientos_caja_user_id_foreign` (`user_id`),
  CONSTRAINT `movimientos_caja_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.movimientos_caja: ~0 rows (aproximadamente)
DELETE FROM `movimientos_caja`;

-- Volcando estructura para tabla saas_prestamos_cobranzas.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prestamo_id` bigint unsigned NOT NULL,
  `cuota_id` bigint unsigned DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `metodo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `referencia` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pagos_codigo_unique` (`codigo`),
  KEY `pagos_prestamo_id_foreign` (`prestamo_id`),
  KEY `pagos_cuota_id_foreign` (`cuota_id`),
  KEY `pagos_user_id_foreign` (`user_id`),
  CONSTRAINT `pagos_cuota_id_foreign` FOREIGN KEY (`cuota_id`) REFERENCES `cuotas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pagos_prestamo_id_foreign` FOREIGN KEY (`prestamo_id`) REFERENCES `prestamos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pagos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.pagos: ~45 rows (aproximadamente)
DELETE FROM `pagos`;
INSERT INTO `pagos` (`id`, `codigo`, `prestamo_id`, `cuota_id`, `monto`, `fecha_pago`, `metodo`, `referencia`, `user_id`, `created_at`, `updated_at`) VALUES
	(1, 'PAG-1-1', 1, 1, 82.50, '2026-06-06', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(2, 'PAG-1-2', 1, 2, 82.50, '2026-07-06', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(3, 'PAG-1-3', 1, 3, 82.50, '2026-08-01', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(4, 'PAG-1-4', 1, 4, 82.50, '2026-09-03', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(5, 'PAG-1-5', 1, 5, 82.50, '2026-10-04', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(6, 'PAG-1-6', 1, 6, 82.50, '2026-11-04', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(7, 'PAG-1-7', 1, 7, 82.50, '2026-12-02', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(8, 'PAG-1-8', 1, 8, 82.50, '2027-01-06', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(9, 'PAG-2-1', 2, 9, 513.33, '2026-07-09', 'transferencia', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(10, 'PAG-2-2', 2, 10, 513.33, '2026-08-06', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(11, 'PAG-2-3', 2, 11, 513.33, '2026-09-04', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(12, 'PAG-2-4', 2, 12, 513.33, '2026-10-08', 'transferencia', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(13, 'PAG-3-1', 3, 15, 280.00, '2026-05-31', 'transferencia', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(14, 'PAG-3-2', 3, 16, 280.00, '2026-07-01', 'transferencia', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(15, 'PAG-3-3', 3, 17, 280.00, '2026-07-30', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(16, 'PAG-3-4', 3, 18, 280.00, '2026-08-31', 'transferencia', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(17, 'PAG-4-1', 4, 19, 366.67, '2026-03-06', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(18, 'PAG-4-2', 4, 20, 366.67, '2026-04-04', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(19, 'PAG-5-1', 5, 25, 268.33, '2026-04-03', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(20, 'PAG-5-2', 5, 26, 268.33, '2026-04-30', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(21, 'PAG-5-3', 5, 27, 268.33, '2026-06-01', 'transferencia', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(22, 'PAG-5-4', 5, 28, 268.33, '2026-07-01', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(23, 'PAG-5-5', 5, 29, 268.33, '2026-08-02', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(24, 'PAG-5-6', 5, 30, 268.33, '2026-09-03', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(25, 'PAG-5-7', 5, 31, 268.33, '2026-10-03', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(26, 'PAG-6-1', 6, 37, 261.33, '2026-05-02', 'transferencia', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(27, 'PAG-6-2', 6, 38, 261.33, '2026-06-02', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(28, 'PAG-6-3', 6, 39, 261.33, '2026-06-29', 'transferencia', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(29, 'PAG-6-4', 6, 40, 261.33, '2026-08-03', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(30, 'PAG-7-1', 7, 43, 149.33, '2026-04-11', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(31, 'PAG-7-2', 7, 44, 149.33, '2026-05-14', 'transferencia', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(32, 'PAG-7-3', 7, 45, 149.33, '2026-06-12', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(33, 'PAG-7-4', 7, 46, 149.33, '2026-07-15', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(34, 'PAG-7-5', 7, 47, 149.33, '2026-08-12', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(35, 'PAG-7-6', 7, 48, 149.33, '2026-09-13', 'transferencia', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(36, 'PAG-8-1', 8, 55, 687.50, '2026-03-16', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(37, 'PAG-8-2', 8, 56, 687.50, '2026-04-16', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(38, 'PAG-8-3', 8, 57, 687.50, '2026-05-17', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(39, 'PAG-8-4', 8, 58, 687.50, '2026-06-18', 'transferencia', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(40, 'PAG-9-1', 9, 59, 153.33, '2026-07-01', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(41, 'PAG-9-2', 9, 60, 153.33, '2026-07-28', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(42, 'PAG-9-3', 9, 61, 153.33, '2026-08-29', 'efectivo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(43, 'PAG-9-4', 9, 62, 153.33, '2026-09-29', 'transferencia', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(44, 'PAG-9-5', 9, 63, 153.33, '2026-10-29', 'yape', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(45, 'PAG-9-6', 9, 64, 153.33, '2026-12-01', 'transferencia', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13');

-- Volcando estructura para tabla saas_prestamos_cobranzas.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.password_reset_tokens: ~0 rows (aproximadamente)
DELETE FROM `password_reset_tokens`;

-- Volcando estructura para tabla saas_prestamos_cobranzas.prestamos
CREATE TABLE IF NOT EXISTS `prestamos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `tasa_interes` decimal(5,2) NOT NULL,
  `numero_cuotas` int NOT NULL,
  `frecuencia` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mensual',
  `monto_cuota` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_pagar` decimal(12,2) NOT NULL DEFAULT '0.00',
  `interes_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `saldo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fecha_inicio` date NOT NULL,
  `estado` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prestamos_codigo_unique` (`codigo`),
  KEY `prestamos_cliente_id_foreign` (`cliente_id`),
  KEY `prestamos_user_id_foreign` (`user_id`),
  CONSTRAINT `prestamos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `prestamos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.prestamos: ~9 rows (aproximadamente)
DELETE FROM `prestamos`;
INSERT INTO `prestamos` (`id`, `codigo`, `cliente_id`, `monto`, `tasa_interes`, `numero_cuotas`, `frecuencia`, `monto_cuota`, `total_pagar`, `interes_total`, `saldo`, `fecha_inicio`, `estado`, `observaciones`, `user_id`, `created_at`, `updated_at`) VALUES
	(1, 'PRE-0001', 1, 600.00, 10.00, 8, 'mensual', 82.50, 660.00, 60.00, 0.00, '2026-05-06', 'pagado', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(2, 'PRE-0002', 2, 2800.00, 10.00, 6, 'mensual', 513.33, 3080.00, 280.00, 1026.68, '2026-06-09', 'activo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(3, 'PRE-0003', 3, 1000.00, 12.00, 4, 'mensual', 280.00, 1120.00, 120.00, 0.00, '2026-05-04', 'pagado', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(4, 'PRE-0005', 5, 2000.00, 10.00, 6, 'mensual', 366.67, 2200.00, 200.00, 1466.66, '2026-02-06', 'mora', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(5, 'PRE-0006', 6, 2800.00, 15.00, 12, 'mensual', 268.33, 3220.00, 420.00, 1341.69, '2026-03-05', 'activo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(6, 'PRE-0007', 7, 1400.00, 12.00, 6, 'mensual', 261.33, 1568.00, 168.00, 522.68, '2026-04-03', 'activo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(7, 'PRE-0009', 9, 1600.00, 12.00, 12, 'mensual', 149.33, 1792.00, 192.00, 896.02, '2026-03-15', 'activo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(8, 'PRE-0010', 10, 2500.00, 10.00, 4, 'mensual', 687.50, 2750.00, 250.00, 0.00, '2026-02-18', 'pagado', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13'),
	(9, 'PRE-0011', 11, 800.00, 15.00, 6, 'mensual', 153.33, 920.00, 120.00, 0.02, '2026-06-02', 'activo', NULL, 1, '2026-06-19 09:04:13', '2026-06-19 09:04:13');

-- Volcando estructura para tabla saas_prestamos_cobranzas.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.sessions: ~14 rows (aproximadamente)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('0O93CTaaN2vbGEMQ5WtMhRen9oK3Ey3ydIFYkSG0', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiN1Nmb3ltM1Y1MXVxMThhNGE2ODFyOTc1Nk81U0xqb3RXTVpLazFEaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1786300299),
	('1fRSKagDirXuunxl8DLEGcyMHg1w7cwdXmw6ugdL', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibXdnaFVNWTZ4OFhtdkd5TzhKRG5SZzJNeGtvalNJTHBXTjdjQndrSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1785812770),
	('BzJhLAgActxca0JV63sE6IieeSDKcP1wcjL70jXR', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRVRmODhnWWV3SXVYY1ZnUlI0VG1TakkzMFRHMURVOEl1VmoyVGhtUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1785855746),
	('cdqMrt8okcmZzUXNY6bi9sGIQe6KOeMCpPr0NK1R', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZUxmbnpRQmtzczVQVU5jdElJeVh3ZUkxNGNHc1dCenk5Z1JuemdRVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1785954629),
	('H4e6VJm23W33rsu2y6tmwbvoayaHc7SLmvPlcYMJ', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRHY3WVhHM0hQMDhmSVE0RXcwa3pZbXJESk5GcDNMenVzdjRtVTllZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1786127345),
	('Ivx989jbkim8xwaOkZjQ6KbKie3e4hUSCKGusm77', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieUZEZUQ1UE5yQ1NkVVpVaEdXWTJCZGhsRWhLS1BKbHd2UTE4QzRkTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1786042456),
	('lGTcmc0Q0YdPobgB0bNQKS0aMHl5mnuIjQnJ2fhT', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQ2hOYWo4VXNMVlpIcFNIc2RMNHlSNXZsWjhqREE4NmVQVmlUS2RsRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1785884294),
	('mfClHGqxWt6VW0YgEQgoYv1fPDOIUOgGI64KXiAw', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMEExMGRrQ295YmF5YURGVUhVY2Y1blU0VUx1SU1XTk1kSENQSkRrOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1785946388),
	('Q2n1AEIgjJymrwcMM3r3qcOsYQLdKjQA4134pEjH', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMWh3b01FR0s0QW5BRHY5YUtnT3o5MlJRa3Z4MFlLR1c4YllobTN5MyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1786342314),
	('tCuZ8rIl8EobNlpuoq2EZcQnmQjEg69T9gNw9Aob', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoic0FRTEJxOE5oQ0RLdDBvbU5HU1RhNGlWZEZ6dmF5RVZqZU96VDRIUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1786334577),
	('VG8b9yIzLAjyiN7B61lByb8Amdtg4OUKdFM0wuU0', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTzQ2REJNSm9uMldFd2tMSllZcE9UZktPQkdLR1duRGdTOXBCVXA4MCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1786217276),
	('VYtnG3u1VFjtmQTqQY0gt2Tb1yZPuqMZpoy4QTqy', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib3lyT1FPNjVEMHVZRjFnbHVFaldBdkVkQUZIbDVJTnk5d0dVbVR6ayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1785821602),
	('zIl61m6RcP0NJwS1dy9D0MeMRksZwNHMnX5JXkqZ', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZjJJTlV0M0RCTGpyTDZiM2lJVmtzV1Z4WDQ5ajN0MHRONnZQVldBciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1785782568),
	('ztUXrpuZwSHLtlmeXkT6TO60nnz0dMCCAiAF7Rc3', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVVltQmVXRnkwMXc3YXRwV2tuOXZuN3o4OXBLZnVsc3FFazVqNTNUbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA5MS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1786207794);

-- Volcando estructura para tabla saas_prestamos_cobranzas.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operador',
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_prestamos_cobranzas.users: ~3 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `rol`, `telefono`, `avatar`, `activo`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador', 'admin@prestamospro.test', NULL, '$2y$12$eUKWjJMQqaKPYY8ICE6j3eUPJRnUj2OuRWhP0iegnhjziCCG2qhUW', 'admin', '999111222', NULL, 1, NULL, '2026-06-19 09:04:13', '2026-06-22 15:31:44'),
	(2, 'Carlos Cobrador', 'cobrador@prestamospro.test', NULL, '$2y$12$MOweWp9dhQUk8vU4y1lLL.N.VHfURChKdhSFNLPpKtHQbZu2t2Ieu', 'cobrador', NULL, NULL, 1, NULL, '2026-06-19 09:04:13', '2026-06-22 15:31:44'),
	(3, 'Super Administrador', 'superadmin@prestamospro.test', NULL, '$2y$12$b0vOC6Dm9WCVUsyxmzVBPeJXhuhczG3x7HTwcByzHLqYL.1KcrqBO', 'superadmin', '999000111', NULL, 1, NULL, '2026-06-22 12:40:27', '2026-06-22 15:31:44');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
