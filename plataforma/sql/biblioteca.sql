CREATE DATABASE  IF NOT EXISTS `biblioteca_escolar` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016  */;
USE `biblioteca_escolar`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: biblioteca_escolar
-- ------------------------------------------------------
-- Server version	8.0.43

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
-- Table structure for table `libros`
--

DROP TABLE IF EXISTS `libros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `libros` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `editorial` varchar(255) DEFAULT NULL,
  `clasificacion` varchar(100) DEFAULT NULL,
  `autor` varchar(255) DEFAULT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libros`
--

LOCK TABLES `libros` WRITE;
/*!40000 ALTER TABLE `libros` DISABLE KEYS */;
INSERT INTO `libros` VALUES (1,'El Principito','Salamandra','Infantil','Antoine de Saint-Exupéry','LIB-001'),(2,'Don Quijote','Castalia','Clásico','Miguel de Cervantes','LIB-002'),(3,'Cien Años de Soledad','Sudamericana','Novela','Gabriel García Márquez','LIB-003');
/*!40000 ALTER TABLE `libros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitud_controles`
--

DROP TABLE IF EXISTS `solicitud_controles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitud_controles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `nombre_docente` varchar(255) DEFAULT NULL,
  `aula` varchar(100) DEFAULT NULL,
  `recibo` varchar(100) DEFAULT NULL,
  `hora_prestamo` time DEFAULT NULL,
  `hora_entrega` time DEFAULT NULL,
  `estado` enum('Pendiente','Aceptado','Rechazado') DEFAULT 'Pendiente',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitud_controles`
--

LOCK TABLES `solicitud_controles` WRITE;
/*!40000 ALTER TABLE `solicitud_controles` DISABLE KEYS */;
INSERT INTO `solicitud_controles` VALUES (1,'2026-04-14','Wicho','c3','55656565','11:27:00','00:25:00','Rechazado'),(2,'2026-04-14','Toño','c3','11212121','01:32:00','03:29:00','Aceptado'),(3,'2026-04-14','lola ','a2','233223','00:43:00','13:31:00','Aceptado');
/*!40000 ALTER TABLE `solicitud_controles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitud_libros`
--

DROP TABLE IF EXISTS `solicitud_libros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitud_libros` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha_solicitud` date DEFAULT NULL,
  `nombre_usuario` varchar(255) DEFAULT NULL,
  `libro_id` int DEFAULT NULL,
  `nombre_libro` varchar(255) DEFAULT NULL,
  `codigo_libro` varchar(100) DEFAULT NULL,
  `estado` enum('Pendiente','Aceptado','Rechazado') DEFAULT 'Pendiente',
  `entregado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_devolucion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitud_libros`
--

LOCK TABLES `solicitud_libros` WRITE;
/*!40000 ALTER TABLE `solicitud_libros` DISABLE KEYS */;
INSERT INTO `solicitud_libros` VALUES (1,'2026-04-14','IthaN',NULL,'EL QUIJOTE','LIB-001','Rechazado',0,NULL),(2,'2026-04-14','Juan pablo',NULL,'El Principito','LIB-001','Rechazado',0,NULL),(3,'2026-04-14','pedro',NULL,'El Principito','LIB-001','Aceptado',1,NULL),(4,'2026-04-14','Toño',NULL,'Don Quijote','LIB-002','Aceptado',1,NULL);
/*!40000 ALTER TABLE `solicitud_libros` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-14 14:00:35
