-- ============================================================
--  horarios_minimo.sql — Esquema MÍNIMO inferido de Horarios
--  AVISO: Este esquema fue construido a partir de las consultas
--  SQL presentes en index.php, VistaAdmin.php y AgregarMaestro.php.
--  NO es un dump oficial. Puede faltar columnas, índices o datos.
--  Si el equipo de Horarios entrega su dump real, úsalo en lugar
--  de este archivo y renombra la BD a "horarios_db".
-- ============================================================

CREATE DATABASE IF NOT EXISTS `horarios_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `horarios_db`;

-- ------------------------------------------------------------
-- Carreras
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Carreras` (
  `id_carrera`     INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `nombre_carrera` VARCHAR(150)    NOT NULL,
  PRIMARY KEY (`id_carrera`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos semilla (adaptar según la institución)
INSERT IGNORE INTO `Carreras` (`id_carrera`, `nombre_carrera`) VALUES
  (1, 'Ingeniería en Sistemas Computacionales'),
  (2, 'Ingeniería en Animación Digital y Efectos Visuales'),
  (3, 'Ingeniería Mecatrónica'),
  (4, 'Ingeniería Industrial'),
  (5, 'Ingeniería en Gestión Empresarial'),
  (6, 'Gastronomía');

-- ------------------------------------------------------------
-- Materias  (referenciada en AgregarMaestro.php)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Materias` (
  `id_materia`     INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `nombre_materia` VARCHAR(200)    NOT NULL,
  `id_carrera`     INT UNSIGNED    DEFAULT NULL,
  PRIMARY KEY (`id_materia`),
  KEY `fk_materia_carrera` (`id_carrera`),
  CONSTRAINT `fk_materia_carrera`
    FOREIGN KEY (`id_carrera`) REFERENCES `Carreras` (`id_carrera`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Profesores
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Profesores` (
  `id_profesor` INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `nombre`      VARCHAR(100)    NOT NULL,
  `apellido`    VARCHAR(100)    NOT NULL,
  PRIMARY KEY (`id_profesor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Horarios
-- (imagen_horario: ruta relativa al archivo PDF/imagen subido)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Horarios` (
  `id_horario`      INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `id_profesor`     INT UNSIGNED    NOT NULL,
  `id_carrera`      INT UNSIGNED    NOT NULL,
  `id_materia`      INT UNSIGNED    DEFAULT NULL,
  `semestre`        TINYINT UNSIGNED DEFAULT NULL,
  `imagen_horario`  VARCHAR(255)    DEFAULT NULL,
  PRIMARY KEY (`id_horario`),
  KEY `fk_horario_profesor` (`id_profesor`),
  KEY `fk_horario_carrera`  (`id_carrera`),
  KEY `fk_horario_materia`  (`id_materia`),
  CONSTRAINT `fk_horario_profesor`
    FOREIGN KEY (`id_profesor`) REFERENCES `Profesores` (`id_profesor`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_horario_carrera`
    FOREIGN KEY (`id_carrera`)  REFERENCES `Carreras`   (`id_carrera`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_horario_materia`
    FOREIGN KEY (`id_materia`)  REFERENCES `Materias`   (`id_materia`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
