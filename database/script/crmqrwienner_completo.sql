-- ============================================
-- BASE DE DATOS COMPLETA: CRMQRWIENNER
-- Incluye estructura base + actualizaciones
-- ============================================

-- MySQL dump 10.13  Distrib 8.0.28, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: crmqrwienner
-- ------------------------------------------------------
-- Server version	8.0.28

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

-- ============================================
-- CREACIÓN DE BASE DE DATOS
-- ============================================
-- DROP DATABASE IF EXISTS crmqrwienner;
-- CREATE DATABASE crmqrwienner CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE crmqrwienner;

-- ============================================
-- TABLA: cuentas
-- ============================================
DROP TABLE IF EXISTS `cuentas`;
CREATE TABLE `cuentas` (
  `idcuenta` int NOT NULL AUTO_INCREMENT,
  `empresa` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `razonsocial` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contrato` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechainicio` timestamp NULL DEFAULT NULL,
  `mensual` decimal(7,2) NOT NULL DEFAULT '0.00',
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcuenta`),
  UNIQUE KEY `ak_razonsocial_cuentas` (`razonsocial`,`estado`),
  UNIQUE KEY `ak_ruc_cuentas` (`ruc`,`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cuentas` VALUES
(1,'PADINSOLUTIONS','20600781236','PADINSOLUTIONS E.I.R.L',NULL,'2020-06-20 23:46:46',0.00,'A','sistemas@padinsolutions.com','2020-06-20 23:46:46',NULL,NULL),
(2,'DESCUBRE TU VOCACIÓN','10859011052','UNI MULTIFERIAS SAC',NULL,'2020-09-09 01:37:26',0.00,'A','padinsolutions@gmail.com','2020-09-08 23:37:26',NULL,'2024-07-08 20:47:29');

-- ============================================
-- TABLA: campanias
-- ============================================
DROP TABLE IF EXISTS `campanias`;
CREATE TABLE `campanias` (
  `idcampania` int NOT NULL AUTO_INCREMENT,
  `nombrecampania` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechainicio` timestamp NULL DEFAULT NULL,
  `fechafin` timestamp NULL DEFAULT NULL,
  `idcuenta` int NOT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcampania`),
  UNIQUE KEY `ak_nombrecampania_campanias` (`nombrecampania`,`estado`),
  KEY `fk_idcuenta_campanias` (`idcuenta`),
  CONSTRAINT `fk_idcuenta_campanias` FOREIGN KEY (`idcuenta`) REFERENCES `cuentas` (`idcuenta`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `campanias` VALUES
(1,'Evento Feria QR 2024','2024-06-06 17:00:00','2025-06-06 17:00:00',2,'A','padinsolutions@gmail.com','2020-09-08 23:40:55','sistemas@padinsolutions.com','2024-07-08 20:49:31'),
(13,'tes campña','2024-07-09 21:49:08','2024-07-13 06:00:00',2,'A','sistemas@padinsolutions.com','2024-07-09 21:49:20',NULL,NULL);

-- ============================================
-- TABLA: eventos
-- ============================================
DROP TABLE IF EXISTS `eventos`;
CREATE TABLE `eventos` (
  `idevento` int NOT NULL AUTO_INCREMENT,
  `nombreevento` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechainicio` timestamp NULL DEFAULT NULL,
  `fechafin` timestamp NULL DEFAULT NULL,
  `idcampania` int NOT NULL,
  `mensaje_exito` TEXT NULL,
  `mensaje_error` TEXT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idevento`),
  UNIQUE KEY `ak_nombreevento_eventos` (`nombreevento`,`estado`),
  KEY `fk_idcampania_eventos` (`idcampania`),
  CONSTRAINT `fk_idcampania_eventos` FOREIGN KEY (`idcampania`) REFERENCES `campanias` (`idcampania`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `eventos` VALUES
(1,'Stand Nutrición','2023-04-04 12:49:16','2023-04-06 01:00:00',1,NULL,NULL,'A','sistemas@padinsolutions.com','2023-01-10 21:49:49','sistemas@padinsolutions.com','2024-07-08 20:49:19'),
(2,'Stand Contabilidad','2024-05-27 17:58:46','2024-05-31 05:00:00',1,NULL,NULL,'A','sistemas@padinsolutions.com','2024-05-27 17:59:40',NULL,'2024-07-08 20:49:19'),
(3,'stand administración','2024-07-10 17:17:49','2024-07-11 05:00:00',13,NULL,NULL,'A','sistemas@padinsolutions.com','2024-07-10 17:18:05',NULL,NULL);

-- ============================================
-- TABLA: clientes
-- ============================================
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `idcliente` int NOT NULL AUTO_INCREMENT,
  `nombres` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidopaterno` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidomaterno` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idcampania` int NOT NULL,
  `fecharegistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `coddistrito` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `utm` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_content` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_medium` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_name` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_source` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_term` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `procedencia` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Orgánico',
  `grado` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrera` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evento` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `colegio` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uuidimportacion` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idtipoatencion` int NOT NULL DEFAULT '1',
  `asistencia` int NOT NULL DEFAULT '0',
  `fechaasistencia` timestamp NULL DEFAULT NULL,
  `moderador` tinyint DEFAULT '0',
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `tipodocumento` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` char(24) DEFAULT NULL,
  `anioegreso` char(10) DEFAULT NULL,
  `ismatricula` int NOT NULL DEFAULT '0',
  `uuidmatriculaimportacion` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idcliente`),
  UNIQUE KEY `ak_email_idcampania_clientes` (`email`,`idcampania`,`estado`),
  KEY `fk_idcampania_clientes` (`idcampania`),
  CONSTRAINT `fk_idcampania_clientes` FOREIGN KEY (`idcampania`) REFERENCES `campanias` (`idcampania`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clientes` VALUES
(144,'Jober','Riveras','Aguilar','prueba@gmail.com','987987987',1,'2024-09-06 20:32:38',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Orgánico',NULL,'Arquitectura y Urbanismo Ambiental',NULL,'Campus Aramburu',NULL,1,0,NULL,0,'A','sistemas@padinsolutions.com','2024-09-06 20:32:38',NULL,NULL,'DNI',NULL,NULL,0,NULL),
(145,'Jober','Riveras','Aguilar','sdsdsd@gmail.com','987987987',1,'2024-09-06 21:28:35',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Orgánico',NULL,'Arquitectura y Urbanismo Ambiental',NULL,'Campus Aramburu',NULL,1,1,'2024-09-06 21:35:24',0,'A','sistemas@padinsolutions.com','2024-09-06 21:28:35',NULL,'2024-09-06 21:35:24','DNI',NULL,NULL,0,NULL);

-- ============================================
-- TABLA: evento_cliente
-- ============================================
DROP TABLE IF EXISTS `evento_cliente`;
CREATE TABLE `evento_cliente` (
  `ideventocliente` int NOT NULL AUTO_INCREMENT,
  `idevento` int NOT NULL,
  `idcliente` int NOT NULL,
  `nombres` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidopaterno` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidomaterno` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecharegistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `coddistrito` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `utm` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_content` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_medium` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_name` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_source` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_term` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `procedencia` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grado` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrera` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `colegio` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uuidimportacion` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asistencia` int NOT NULL DEFAULT '0',
  `fechaasistencia` timestamp NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `tipodocumento` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` char(24) DEFAULT NULL,
  `anioegreso` char(10) DEFAULT NULL,
  `ismatricula` int NOT NULL DEFAULT '0',
  `uuidmatriculaimportacion` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ideventocliente`,`idevento`,`idcliente`),
  UNIQUE KEY `unique_evento_cliente` (`idevento`,`idcliente`),
  KEY `fk_idevento_evento_cliente` (`idevento`),
  KEY `fk_idcliente_evento_cliente` (`idcliente`),
  CONSTRAINT `fk_idevento_evento_cliente` FOREIGN KEY (`idevento`) REFERENCES `eventos` (`idevento`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `evento_cliente` VALUES
(17,1,145,'Jober','Riveras','Aguilar','sdsdsd@gmail.com','987987987','2024-09-06 21:28:35',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Orgánico',NULL,'Arquitectura y Urbanismo Ambiental','Campus Aramburu',NULL,1,'2024-09-06 21:36:05','A','sistemas@padinsolutions.com','2024-09-06 21:36:05',NULL,NULL,'DNI',NULL,NULL,0,NULL),
(29,2,145,'Jober','Riveras','Aguilar','sdsdsd@gmail.com','987987987','2024-09-06 21:28:35',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Orgánico',NULL,'Arquitectura y Urbanismo Ambiental','Campus Aramburu',NULL,1,'2024-09-06 21:57:56','A','sistemas@padinsolutions.com','2024-09-06 21:57:56',NULL,NULL,'DNI',NULL,NULL,0,NULL);

-- ============================================
-- TABLA: roles
-- ============================================
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `idrol` int NOT NULL AUTO_INCREMENT,
  `rol` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idrol`),
  UNIQUE KEY `ak_rol_roles` (`rol`,`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` VALUES
(1,'Administrador Sistema','A','sistemas@padinsolutions.com','2020-06-20 23:53:34',NULL,NULL),
(2,'Administrador','A','sistemas@padinsolutions.com','2020-06-20 23:53:34',NULL,NULL),
(3,'Estandar ','A','sistemas@padinsolutions.com','2020-06-20 23:53:34',NULL,NULL),
(4,'Editor','A','sistemas@padinsolutions.com','2020-06-20 23:53:34',NULL,NULL),
(5,'Asesor Comercial','A','sistemas@padinsolutions.com','2022-05-16 19:21:55',NULL,NULL),
(6,'Personal Asistencia QR','A','sistemas@padinsolutions.com','2024-06-24 23:20:37','sistemas@padinsolutions.com','2024-07-08 20:52:01'),
(7,'Personal Estand QR','A','sistemas@padinsolutions.com','2024-06-24 23:21:44','sistemas@padinsolutions.com','2024-07-08 20:52:01');

-- ============================================
-- TABLA: users
-- ============================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idrol` int DEFAULT NULL,
  `idcuenta` int NOT NULL,
  `idcampania` int NULL,
  `idevento` int DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_token` (`api_token`),
  UNIQUE KEY `ak_email_users` (`email`,`estado`),
  KEY `fk_idrol_users` (`idrol`),
  KEY `fk_idcuenta_users` (`idcuenta`),
  CONSTRAINT `fk_idcuenta_users` FOREIGN KEY (`idcuenta`) REFERENCES `cuentas` (`idcuenta`),
  CONSTRAINT `fk_idrol_users` FOREIGN KEY (`idrol`) REFERENCES `roles` (`idrol`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` VALUES
(1,'Admin Sistema CRM','sistemas@padinsolutions.com',NULL,'$2y$10$NYs4lacRXKKunsvj5sFAeuYqTV3FJf8m9o6.GPu.x9CoOZAlPExee','$2y$10$sZvMQ5V6YH/11n1QTfAVv.Vhxq5EJwKkK9yf83byqZrN2KUc/bVCO',1,1,NULL,NULL,'A','3lq7D9RSCTddEhvPEotPywCW52frFBjW7nMcAlmNsj8dMxkXTGgZ7Yr2oZxS','2020-06-20 23:54:22','2024-09-06 22:15:46'),
(42,'Personal asistencia','principal@gmail.com',NULL,'$2y$10$NEV1Cnxh3EF5ehP0pkHgDOcMvG70FuxymwpwsDbWyNR2/GS5D1E7.','$2y$10$qJgAYWAm/.x.JTfsNWkFLu2K50DAitFjhMcjuD0TStmynfKryKWA2',6,2,NULL,NULL,'A',NULL,'2024-07-15 20:15:54','2024-07-15 20:15:54'),
(43,'Estand Nutricion','segundario@gmail.com',NULL,'$2y$10$bPwV3FvuDxryW3uJfJEQJO94LrwjnD6FV7CJJI/P3HIGfR9lox.by','$2y$10$pvjw8XJXzKT3n7wNlWTIreFEWudCROKRY.MfpQZo1zYnpySD9qD66',7,2,NULL,1,'A',NULL,'2024-07-15 20:16:27','2024-07-15 20:16:27'),
(44,'admin','admin@gmail.com',NULL,'$2y$10$uPooSXltX9ojXqiRIDIU1.QIcUq5.MJE7DLtd80m7DqrejXOgHe6i','$2y$10$vuI9Hk8jVWR7g7/GUGNwaeM2Y0P6ZfNIUD3jKZZf2sYswN0X9ZL8.',2,2,NULL,NULL,'A',NULL,'2024-07-15 21:58:05','2024-07-15 21:58:05'),
(45,'STAND CONTABILIDAD','contabilidad@gmail.com',NULL,'$2y$10$xH2Zbg5RAr80jBwIbgXzEe4DNLeyaSS2yhen5v073btVkeWM8Wmfa','$2y$10$PUfRR7UlE.yI2CRkVb.DhOJheIcbkGqfPEZzdXviVhZY9VDdYv5Ne',7,2,NULL,2,'A',NULL,'2024-09-06 21:39:46','2024-09-06 21:39:46');

-- ============================================
-- TABLA: tipoatenciones
-- ============================================
DROP TABLE IF EXISTS `tipoatenciones`;
CREATE TABLE `tipoatenciones` (
  `idtipoatencion` int NOT NULL AUTO_INCREMENT,
  `tipoatencion` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `backgroundColor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '#d3d3d3',
  `textColor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '#000',
  `estado` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idtipoatencion`),
  UNIQUE KEY `ak_tipoatencion_tipoatenciones` (`tipoatencion`,`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: atenciones
-- ============================================
DROP TABLE IF EXISTS `atenciones`;
CREATE TABLE `atenciones` (
  `idatencion` int NOT NULL AUTO_INCREMENT,
  `iduser` int NOT NULL,
  `idcliente` int NOT NULL,
  `fechaatencion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comentario` varchar(700) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idtipoatencion` int NOT NULL,
  `estado` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idatencion`),
  KEY `fk_iduser_atenciones` (`iduser`),
  KEY `fk_idtipoatencion_atenciones` (`idtipoatencion`),
  KEY `fk_idcliente_atenciones` (`idcliente`),
  CONSTRAINT `fk_idcliente_atenciones` FOREIGN KEY (`idcliente`) REFERENCES `clientes` (`idcliente`),
  CONSTRAINT `fk_idtipoatencion_atenciones` FOREIGN KEY (`idtipoatencion`) REFERENCES `tipoatenciones` (`idtipoatencion`),
  CONSTRAINT `fk_iduser_atenciones` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: buttonmessages
-- ============================================
DROP TABLE IF EXISTS `buttonmessages`;
CREATE TABLE `buttonmessages` (
  `idbutton` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `backgroundColor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#d3d3d3',
  `textColor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#000',
  `descriptionwhatsapp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idbutton`),
  UNIQUE KEY `ak_name_buttonmessages` (`name`,`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: importaciones
-- ============================================
DROP TABLE IF EXISTS `importaciones`;
CREATE TABLE `importaciones` (
  `idimportacion` int NOT NULL AUTO_INCREMENT,
  `nombrearchivo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecharegistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cantregistros` int NOT NULL DEFAULT '0',
  `idcampania` int DEFAULT NULL,
  `iduser` int NOT NULL,
  `uuidimportacion` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idimportacion`),
  KEY `fk_iduser_importaciones` (`iduser`),
  CONSTRAINT `fk_iduser_importaciones` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: logerrors
-- ============================================
DROP TABLE IF EXISTS `logerrors`;
CREATE TABLE `logerrors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `classname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `methodname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `errormessage` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dateoccurred` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: password_resets
-- ============================================
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(350) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(350) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: ubigeos
-- ============================================
DROP TABLE IF EXISTS `ubigeos`;
CREATE TABLE `ubigeos` (
  `pkubigeo` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fkubigeo` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombreubigeo` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nivel` int DEFAULT '0',
  `nombrecompleto` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`pkubigeo`),
  KEY `fk_fkubigeo_ubigeos` (`fkubigeo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- STORED PROCEDURES
-- ============================================

DROP PROCEDURE IF EXISTS `SP_REGSITROSFECHAS_CLIENTES`;
DELIMITER ;;
CREATE PROCEDURE `SP_REGSITROSFECHAS_CLIENTES`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
	IN p_asistencia INT,
	IN p_import INT
)
BEGIN
    IF p_idevento > 0 THEN
        SELECT
            cl.colegio,
            DATE_FORMAT(ec.fecharegistro, "%Y-%m-%d %H:00:00") AS fechaGeneral,
            COUNT(ec.idcliente) AS total
        FROM
            clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
        WHERE
            cl.estado = 'A'
            AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
            AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
        GROUP BY
            cl.colegio, DATE_FORMAT(ec.fecharegistro, "%Y-%m-%d %H:00:00")
        ORDER BY
            fechaGeneral ASC;
    ELSE
        SELECT
            cl.colegio,
            DATE_FORMAT(cl.fecharegistro, "%Y-%m-%d %H:00:00") AS fechaGeneral,
            COUNT(cl.idcliente) AS total
        FROM
            clientes cl
        WHERE
            cl.estado = 'A'
            AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
            AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
        GROUP BY
            cl.colegio, DATE_FORMAT(cl.fecharegistro, "%Y-%m-%d %H:00:00")
        ORDER BY
            fechaGeneral ASC;
    END IF;
END ;;
DELIMITER ;

DROP PROCEDURE IF EXISTS `SP_REGSITROSFECHAS_CLIENTES_ASISTIDO`;
DELIMITER ;;
CREATE PROCEDURE `SP_REGSITROSFECHAS_CLIENTES_ASISTIDO`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
	IN p_asistencia INT,
	IN p_import INT
)
BEGIN
    IF p_idevento > 0 THEN
        SELECT
            cl.colegio,
            DATE_FORMAT(ec.fechaasistencia, "%Y-%m-%d %H:00:00") AS fechaGeneral,
            COUNT(ec.idcliente) AS total
        FROM
            clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
        WHERE
            cl.estado = 'A'
            AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
            AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
        GROUP BY
            cl.colegio, DATE_FORMAT(ec.fechaasistencia, "%Y-%m-%d %H:00:00")
        ORDER BY
            fechaGeneral ASC;
    ELSE
        SELECT
            cl.colegio,
            DATE_FORMAT(cl.fechaasistencia, "%Y-%m-%d %H:00:00") AS fechaGeneral,
            COUNT(cl.idcliente) AS total
        FROM
            clientes cl
        WHERE
            cl.estado = 'A'
            AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
            AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
        GROUP BY
            cl.colegio, DATE_FORMAT(cl.fechaasistencia, "%Y-%m-%d %H:00:00")
        ORDER BY
            fechaGeneral ASC;
    END IF;
END ;;
DELIMITER ;

DROP PROCEDURE IF EXISTS `SP_RP_ASISTENCIA_SEDE`;
DELIMITER ;;
CREATE PROCEDURE `SP_RP_ASISTENCIA_SEDE`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100)
)
BEGIN
    IF p_idevento > 0 THEN
        SELECT
			cl.colegio, COUNT(CASE WHEN ec.asistencia = '1' THEN 1 END) AS cantidad_asistencias
		FROM clientes cl INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
		WHERE cl.estado = 'A'
            AND cl.idcampania = p_idcampania
            AND ec.idevento = p_idevento
            AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY colegio;
    ELSE
		SELECT
			colegio, COUNT(CASE WHEN asistencia = '1' THEN 1 END) AS cantidad_asistencias
		FROM clientes
		WHERE 	estado = 'A'
            AND idcampania = p_idcampania
            AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY colegio;
    END IF;
END ;;
DELIMITER ;

DROP PROCEDURE IF EXISTS `SP_RP_REGISTROS_CARRERAS`;
DELIMITER ;;
CREATE PROCEDURE `SP_RP_REGISTROS_CARRERAS`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
    IN p_asistencia INT,
	IN p_import INT
)
BEGIN
	IF p_idevento > 0 THEN
        SELECT
			cl.carrera, COUNT(ec.idcliente) AS total
			FROM clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
			WHERE 	cl.estado = 'A'
			AND cl.idcampania = p_idcampania
			AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
			AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY cl.carrera;
    ELSE
		SELECT
			carrera, COUNT(idcliente) AS total
			FROM clientes
			WHERE 	estado = 'A'
			AND idcampania = p_idcampania
			AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
			AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY carrera;
	END IF;
END ;;
DELIMITER ;

DROP PROCEDURE IF EXISTS `SP_RP_REGISTROS_SEDES`;
DELIMITER ;;
CREATE PROCEDURE `SP_RP_REGISTROS_SEDES`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
    IN p_asistencia INT,
    IN p_import INT
)
BEGIN
	IF p_idevento > 0 THEN
		SELECT
			cl.colegio, COUNT(ec.idcliente) AS total
			FROM clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
			WHERE 	cl.estado = 'A'
			AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
			AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY cl.colegio;
    ELSE
		SELECT
			colegio, COUNT(idcliente) AS total
			FROM clientes
			WHERE 	estado = 'A'
			AND idcampania = p_idcampania
            AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
			AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY colegio;
	END IF;
END ;;
DELIMITER ;

DROP PROCEDURE IF EXISTS `SP_RP_REGISTROS_PROCEDENCIAS`;
DELIMITER ;;
CREATE PROCEDURE `SP_RP_REGISTROS_PROCEDENCIAS`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
    IN p_asistencia INT,
	IN p_import INT
)
BEGIN
	IF p_idevento > 0 THEN
        SELECT
			cl.procedencia, COUNT(ec.idcliente) AS total
			FROM clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
			WHERE cl.estado = 'A'
			AND cl.idcampania = p_idcampania
			AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
			AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY cl.procedencia;
    ELSE
		SELECT
			procedencia, COUNT(idcliente) AS total
			FROM clientes
			WHERE estado = 'A'
			AND idcampania = p_idcampania
			AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
			AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY procedencia;
	END IF;
END ;;
DELIMITER ;

DROP PROCEDURE IF EXISTS `SP_RP_REGISTROS_ENVIONOTIFACION`;
DELIMITER //
CREATE PROCEDURE `SP_RP_REGISTROS_ENVIONOTIFACION`(
    IN p_idcampania INT
)
BEGIN
    SELECT
        ta.tipoatencion,
        DATE_FORMAT(a.fechaatencion, '%Y-%m-%d') AS fecha_atencion,
        COUNT(a.idatencion) AS total_atenciones
    FROM
        atenciones a
    JOIN
        clientes c ON a.idcliente = c.idcliente
    JOIN
        tipoatenciones ta ON a.idtipoatencion = ta.idtipoatencion
    WHERE
        a.idtipoatencion = 4
        AND c.idcampania = p_idcampania
        AND c.estado = 'A'
    GROUP BY
        fecha_atencion, ta.tipoatencion
    ORDER BY
        fecha_atencion ASC, ta.tipoatencion ASC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `SP_RP_REGISTROS_GRADO`;
DELIMITER //
CREATE PROCEDURE `SP_RP_REGISTROS_GRADO`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
    IN p_asistencia INT,
    IN p_import INT
)
BEGIN
	IF p_idevento > 0 THEN
		SELECT
			cl.grado, COUNT(ec.idcliente) AS total
			FROM clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
			WHERE cl.estado = 'A'
            AND cl.grado IS NOT NULL
			AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
			AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY cl.grado;
    ELSE
		SELECT
			grado, COUNT(idcliente) AS total
			FROM clientes
			WHERE estado = 'A'
            AND grado IS NOT NULL
			AND idcampania = p_idcampania
            AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
			AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY grado;
	END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `SP_RP_REGISTROS_ANIOEGRESO`;
DELIMITER //
CREATE PROCEDURE `SP_RP_REGISTROS_ANIOEGRESO`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
    IN p_asistencia INT,
    IN p_import INT
)
BEGIN
	IF p_idevento > 0 THEN
		SELECT
			cl.anioegreso, COUNT(ec.idcliente) AS total
			FROM clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
			WHERE 	cl.estado = 'A'
            AND cl.anioegreso IS NOT NULL
			AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
			AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY cl.anioegreso;
    ELSE
		SELECT
			anioegreso, COUNT(idcliente) AS total
			FROM clientes
			WHERE 	estado = 'A'
            AND anioegreso IS NOT NULL
			AND idcampania = p_idcampania
            AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
			AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY anioegreso;
	END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `SP_RP_REGISTROSMATRICULADOS`;
DELIMITER //
CREATE PROCEDURE `SP_RP_REGISTROSMATRICULADOS`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
    IN p_asistencia INT,
    IN p_import INT
)
BEGIN
    IF p_idevento > 0 THEN
        SELECT
            'Matriculados' AS colegio,
            COUNT(CASE WHEN cl.ismatricula = 1 THEN 1 END) AS total
        FROM clientes cl
        INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
        WHERE cl.estado = 'A'
        AND cl.idcampania = p_idcampania
        AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
        AND ec.idevento = p_idevento
        AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci);
    ELSE
        SELECT
            'Matriculados' AS colegio,
            COUNT(CASE WHEN ismatricula = 1 THEN 1 END) AS total
        FROM clientes
        WHERE estado = 'A'
        AND idcampania = p_idcampania
        AND (p_asistencia = 0 OR asistencia = p_asistencia)
        AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
        AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci);
    END IF;
END //
DELIMITER ;

-- ============================================
-- RESTORE DEFAULTS
-- ============================================
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Script completado exitosamente


-- ===================================
-- TABLA PARA PÁGINAS DINÁMICAS
-- Sistema BuscaTuQR
-- ===================================

DROP TABLE IF EXISTS `paginas_dinamicas`;

CREATE TABLE `paginas_dinamicas` (
  `idpagina` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` VARCHAR(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` TEXT COLLATE utf8mb4_unicode_ci NULL,
  `idcampania` INT UNSIGNED NULL,
  `estado_general` CHAR(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `estado` CHAR(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `userinsert` VARCHAR(50) COLLATE utf8mb4_unicode_ci NULL DEFAULT 'sistema',
  `dateinsert` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` VARCHAR(50) COLLATE utf8mb4_unicode_ci NULL,
  `dateupdate` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idpagina`),
  UNIQUE KEY `uk_slug_paginas_dinamicas` (`slug`),
  KEY `idx_slug_estado` (`slug`, `estado_general`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla para control de páginas dinámicas públicas';

-- Insertar registro inicial para BuscaTuQR
INSERT INTO `paginas_dinamicas`
(`slug`, `titulo`, `descripcion`, `estado_general`, `estado`, `userinsert`, `dateinsert`)
VALUES
('buscatuqr', 'Consulta de Códigos QR', 'Página para buscar y validar códigos QR activos.', 'A', 'A', 'sistema', NOW());

-- Verificar inserción
SELECT * FROM paginas_dinamicas WHERE slug = 'buscatuqr';

---------------------------------------------
-- ============================================
-- MODIFICACIONES A LA TABLA CLIENTES
-- ============================================
-- fecha : 12-12-2025
-- Agregar campos a la tabla clientes
-- Agregar el campo isbeca18 a la tabla clientes
ALTER TABLE clientes ADD COLUMN isbeca18 INT NOT NULL DEFAULT 0 AFTER fechaasistencia;

-- Agregar el campo isbeca18 a la tabla evento_cliente
ALTER TABLE evento_cliente ADD COLUMN isbeca18 INT NOT NULL DEFAULT 0 AFTER fechaasistencia;

-- ============================================
-- TABLA: configuracion_qr_colores
-- Configuración administrable de colores para QR
-- ============================================

CREATE TABLE `configuracion_qr_colores` (
  `idconfig` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `anio_egreso` VARCHAR(10) NULL COMMENT 'Año de egreso, NULL para default',
  `es_beca18` TINYINT NOT NULL DEFAULT 0 COMMENT '0=No aplica, 1=Sí es Beca18',
  `color` VARCHAR(50) NOT NULL COMMENT 'Color en hex (#ff0000) o nombre (orange)',
  `descripcion` VARCHAR(200) NOT NULL COMMENT 'Texto descriptivo que se mostrará',
  `orden` INT NOT NULL DEFAULT 999 COMMENT 'Orden de prioridad (menor es más prioritario)',
  `estado` CHAR(1) NOT NULL DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `userinsert` VARCHAR(60) DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` VARCHAR(60) NULL,
  `dateupdate` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idconfig`),
  KEY `idx_anio_beca` (`anio_egreso`, `es_beca18`),
  KEY `idx_estado_orden` (`estado`, `orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Configuración administrable de colores para códigos QR';

-- Insertar configuraciones por defecto
INSERT INTO `configuracion_qr_colores`
(`anio_egreso`, `es_beca18`, `color`, `descripcion`, `orden`, `estado`, `dateinsert`)
VALUES
('2026', 0, 'orange', 'Cuarto año de secundaria', 1, 'A', NOW()),
('2025', 0, 'blue', 'Quinto año de secundaria', 2, 'A', NOW()),
(NULL, 1, '#00bfff', 'Egresado / Beca 18', 3, 'A', NOW()),
(NULL, 0, 'white', 'Egresado', 999, 'A', NOW());

-- Verificar los registros insertados
SELECT * FROM configuracion_qr_colores ORDER BY orden;



-- Actualizar la página con el ID de la campaña activa
UPDATE paginas_dinamicas
SET idcampania = 16
WHERE slug = 'buscatuqr';

ALTER TABLE `paginas_dinamicas`
ADD COLUMN `api_token` VARCHAR(100) NULL;

-- Insertar registro para página de registro
INSERT INTO `paginas_dinamicas`
(`slug`, `titulo`, `descripcion`, `idcampania`, `api_token`, `estado_general`, `estado`, `userinsert`, `dateinsert`)
VALUES
('registro', 'Registro de Participantes', 'Formulario de registro público para eventos.', 16, 'wiener_registro_2026_secret_token', 'A', 'A', 'sistema', NOW());

-- Renombrar la columna anio_egreso a grado en la tabla configuracion_qr_colores
ALTER TABLE configuracion_qr_colores CHANGE anio_egreso grado VARCHAR(50) NULL COMMENT 'Grado, NULL para default';

-- Eliminar las columnas relacionadas con isbeca18
ALTER TABLE clientes DROP COLUMN isbeca18;
ALTER TABLE evento_cliente DROP COLUMN isbeca18;
ALTER TABLE configuracion_qr_colores DROP COLUMN es_beca18;

-- DROP INDEX idx_grado_beca ON configuracion_qr_colores;
CREATE INDEX idx_grado_beca ON configuracion_qr_colores (grado);

-- Agregar el campo autorizo_uso_imagen a la tabla clientes
ALTER TABLE clientes ADD autorizo_uso_imagen TINYINT(1) NOT NULL DEFAULT 0;

-- fecha: 2025-12-20
-- Agregar tabla para tipo de recordatorios
CREATE TABLE `tiporecordatorios` (
  `idtiporecordatorio` int NOT NULL AUTO_INCREMENT,
  `tiporecordatorio` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `backgroundColor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '#d3d3d3',
  `textColor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '#000',
  `estado` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idtiporecordatorio`),
  UNIQUE KEY `ak_tiporecordatorio_tiporecordatorio` (`tiporecordatorio`,`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO tiporecordatorios (tiporecordatorio, backgroundColor, textColor, estado, userinsert, dateinsert)
VALUES
('MENSAJE DE REGISTRO ENVIADO', '#405ff7', '#ffffff', 'A', 'sistemas@padinsolutions.com', '2026-02-23 14:00:34'),
('Recordatorio a 2 días', '#a824e5', '#ffffff', 'A', 'sistemas@padinsolutions.com', '2026-02-23 14:00:50'),
('ULTIMO RECORDATORIO ES HOY', '#00c292', '#ffffff', 'A', 'sistemas@padinsolutions.com', '2026-02-23 14:01:05');

ALTER TABLE clientes
ADD COLUMN idtiporecordatorio INT NOT NULL DEFAULT 0 ,
ADD COLUMN fecha_ultimo_envio  TIMESTAMP NULL DEFAULT NULL  AFTER idtiporecordatorio;

ALTER TABLE clientes
ADD COLUMN wamid VARCHAR(150) NULL;

-- fecha: 2026-04-23
-- Agregar campo sede_destino a clientes
ALTER TABLE clientes
ADD COLUMN sede_destino VARCHAR(150) NULL AFTER colegio;

ALTER TABLE evento_cliente
ADD COLUMN sede_destino VARCHAR(150) NULL AFTER colegio;

