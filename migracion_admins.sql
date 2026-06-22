-- ================================================================
-- MIGRACIÓN: Administradores multi-usuario
-- ================================================================
-- Ejecuta este script UNA SOLA VEZ sobre la base de datos kiosko_tsj
-- existente para habilitar el sistema de múltiples cuentas de admin.
--
-- Cómo ejecutarlo:
--   • phpMyAdmin → selecciona la BD kiosko_tsj → pestaña "SQL" → pega esto → Continuar
--   • o por consola:  mysql -u root kiosko_tsj < migracion_admins.sql
--
-- Es seguro ejecutarlo aunque algo ya exista (usa IF NOT EXISTS).
-- ================================================================

USE `kiosko_tsj`;

-- 1) Tabla de cuentas de administrador
CREATE TABLE IF NOT EXISTS `admins` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`        VARCHAR(150) NOT NULL,
  `email`         VARCHAR(254) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `activo`        TINYINT(1)   NOT NULL DEFAULT 1,
  `ultimo_acceso` TIMESTAMP    NULL DEFAULT NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admins_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Columnas de identidad en la bitácora (quién hizo cada acción)
--    IF NOT EXISTS es compatible con MariaDB (XAMPP). Si tu MySQL no lo
--    soporta, elimina "IF NOT EXISTS" y ejecuta cada ALTER por separado.
ALTER TABLE `admin_log`
  ADD COLUMN IF NOT EXISTS `admin_id`     INT UNSIGNED NULL AFTER `detalle`,
  ADD COLUMN IF NOT EXISTS `admin_nombre` VARCHAR(150) NULL AFTER `admin_id`;
