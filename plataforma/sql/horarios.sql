CREATE DATABASE  IF NOT EXISTS `horarios_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `horarios_db`;
-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: horarios_db
-- ------------------------------------------------------
-- Server version	8.0.41

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
-- Table structure for table `carreras`
--

DROP TABLE IF EXISTS `carreras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carreras` (
  `id_carrera` int NOT NULL AUTO_INCREMENT,
  `nombre_carrera` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_carrera`),
  UNIQUE KEY `nombre_carrera` (`nombre_carrera`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carreras`
--

LOCK TABLES `carreras` WRITE;
/*!40000 ALTER TABLE `carreras` DISABLE KEYS */;
INSERT INTO `carreras` VALUES (12,'Gastronomía'),(11,'Ingeniería en Animación Digital y Efectos Visuales'),(10,'Ingeniería en Gestión Empresarial'),(1,'Ingeniería en Sistemas Computacionales'),(2,'Ingeniería Industrial'),(13,'Ingeniería Mecatrónica');
/*!40000 ALTER TABLE `carreras` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `horarios`
--

DROP TABLE IF EXISTS `horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horarios` (
  `id_horario` int NOT NULL AUTO_INCREMENT,
  `id_profesor` int NOT NULL,
  `id_materia` int NOT NULL,
  `id_carrera` int NOT NULL,
  `semestre` int NOT NULL,
  `imagen_horario` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_horario`),
  KEY `id_profesor` (`id_profesor`),
  KEY `id_materia` (`id_materia`),
  KEY `id_carrera` (`id_carrera`),
  CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id_profesor`),
  CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`),
  CONSTRAINT `horarios_ibfk_3` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES (1,1,1,1,5,'/buscarMaterias-main/horarios/Captura de pantalla 2025-04-29 015956.png'),(2,2,3,2,3,'/buscarMaterias-main/horarios/OIP.jpeg'),(20,20,19,13,0,'/buscarMaterias-main/horarios/Captura de pantalla 2025-05-07 235125.png'),(22,22,2,2,0,'/buscarMaterias-main/horarios/Captura de pantalla 2025-05-12 102901.png'),(23,23,18,11,7,'/buscarMaterias-main/horarios/rosa.png'),(24,24,17,10,5,'/buscarMaterias-main/horarios/Diagrama sin título.png');
/*!40000 ALTER TABLE `horarios` ENABLE KEYS */;
UNLOCK TABLES;



--
-- Table structure for table `materias`
--

DROP TABLE IF EXISTS `materias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materias` (
  `id_materia` int NOT NULL AUTO_INCREMENT,
  `nombre_materia` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `clave_materia` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_carrera` int DEFAULT NULL,
  PRIMARY KEY (`id_materia`),
  UNIQUE KEY `clave_materia` (`clave_materia`),
  KEY `id_carrera` (`id_carrera`),
  CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materias`
--

LOCK TABLES `materias` WRITE;
/*!40000 ALTER TABLE `materias` DISABLE KEYS */;
INSERT INTO `materias` VALUES (1,'Programación Web','PW101',1),(2,'Matemáticas Avanzadas','MA202',2),(3,'Contabilidad Básica','CB303',NULL),(4,'Base de Datos','BD101',1),(5,'Inteligencia Artificial','IA101',1),(6,'Logística','LOG101',2),(7,'Producción','PROD101',2),(8,'Administración de Empresas','AE101',10),(9,'Marketing','MKT101',10),(10,'Finanzas','FIN101',10),(11,'Diseño Gráfico','DG101',11),(12,'Animación 3D','A3D101',11),(13,'Efectos Especiales','EE101',11),(14,'Cocina Internacional','CI101',12),(15,'Panadería','PAN101',12),(16,'Nutrición','NUT101',12),(17,'Robótica','ROB101',13),(18,'Automatización','AUT101',13),(19,'Electrónica','ELE101',13);
/*!40000 ALTER TABLE `materias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profesores`
--

DROP TABLE IF EXISTS `profesores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profesores` (
  `id_profesor` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_profesor`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesores`
--

LOCK TABLES `profesores` WRITE;
/*!40000 ALTER TABLE `profesores` DISABLE KEYS */;
INSERT INTO `profesores` VALUES (1,'Juan Luis','Pérez Domimges'),(2,'María','González'),(19,'Alberto','Romero'),(20,'Alberto','Romero'),(22,'Jose Chema','Rivera'),(23,'juan','pepino'),(24,'Miguel','Miguelito');
/*!40000 ALTER TABLE `profesores` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-22 12:55:17
