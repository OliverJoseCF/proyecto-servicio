-- ============================================================
-- Esquema de base de datos — Módulo de Convenios TecSJ
-- Motor: MySQL 5.7+ / MariaDB 10.3+
-- Charset: utf8mb4 (soporte completo Unicode + emojis)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `convenios_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `convenios_db`;

-- ── Tabla principal ───────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `convenios` (
    `id`          INT(11)      NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(200) NOT NULL                 COMMENT 'Nombre de la empresa/institución',
    `convenio`    ENUM('Servicio Social','Prácticas','Ambos') NOT NULL COMMENT 'Tipo de convenio',
    `logo`        VARCHAR(100) DEFAULT NULL             COMMENT 'Nombre del archivo (solo basename, sin ruta)',
    `contacto`    VARCHAR(200) NOT NULL                 COMMENT 'Nombre de la persona de contacto',
    `telefono`    VARCHAR(25)  NOT NULL,
    `correo`      VARCHAR(254) NOT NULL,
    `vencimiento` DATE         NOT NULL,
    `web`         VARCHAR(500) DEFAULT NULL,
    `facebook`    VARCHAR(500) DEFAULT NULL,
    `youtube`     VARCHAR(500) DEFAULT NULL,
    `twitter`     VARCHAR(500) DEFAULT NULL,
    `carrera`     ENUM('IADEV','IM','ISC','II','LG','IGE') NOT NULL COMMENT 'Carrera asociada al convenio',
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_carrera`    (`carrera`),
    KEY `idx_vencimiento`(`vencimiento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Registro de auditoría (acciones admin) ────────────────────────────────────
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id`        INT(11)      NOT NULL AUTO_INCREMENT,
    `ts`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `usuario`   VARCHAR(100) NOT NULL DEFAULT 'superuser',
    `accion`    VARCHAR(50)  NOT NULL COMMENT 'CREAR | EDITAR | ELIMINAR',
    `entity_id` INT(11)      DEFAULT NULL,
    `detalle`   TEXT         DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_ts`(`ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Migración: si ya existe la tabla sin columna carrera ──────────────────────
-- Ejecutar SOLO si la tabla existe y no tiene la columna carrera:
--
-- ALTER TABLE `convenios`
--   ADD COLUMN `carrera` ENUM('IADEV','IM','ISC','II','LG','IGE')
--     NOT NULL DEFAULT 'ISC'
--     COMMENT 'Carrera asociada al convenio'
--     AFTER `twitter`,
--   ADD KEY `idx_carrera` (`carrera`);
--
-- Luego actualizar registros existentes con la carrera correcta:
-- UPDATE `convenios` SET `carrera` = 'ISC' WHERE `carrera` IS NULL;

-- ── Migración: cambiar columna logo de VARCHAR(255) a VARCHAR(100) ─────────────
-- (si ya existe con el formato antiguo '../src/pages/upload/...' se puede
--  normalizar con):
-- UPDATE `convenios` SET `logo` = SUBSTRING_INDEX(`logo`, '/', -1)
--   WHERE `logo` LIKE '%/%';
