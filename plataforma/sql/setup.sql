-- ============================================================
-- Setup inicial de la plataforma TSJ Chapala
-- Ejecutar como root una sola vez tras importar los dumps.
-- ============================================================

-- 1. Crear usuario de aplicación (mínimos privilegios)
--    Cambia 'CambiaEstaClaveSegura2024!' por una contraseña real antes de ejecutar.
CREATE USER IF NOT EXISTS 'tsjplat'@'127.0.0.1' IDENTIFIED BY 'CambiaEstaClaveSegura2024!';

GRANT SELECT, INSERT, UPDATE, DELETE ON `biblioteca_escolar`.* TO 'tsjplat'@'127.0.0.1';
GRANT SELECT, INSERT, UPDATE, DELETE ON `convenios_db`.*       TO 'tsjplat'@'127.0.0.1';
GRANT SELECT, INSERT, UPDATE, DELETE ON `horarios_db`.*        TO 'tsjplat'@'127.0.0.1';

FLUSH PRIVILEGES;

-- 2. Migrar rutas antiguas de archivos en Horarios
--    (El dump tiene rutas '/buscarMaterias-main/horarios/...' que ya no aplican)
USE `horarios_db`;
UPDATE `horarios`
SET `imagen_horario` = REPLACE(`imagen_horario`, '/buscarMaterias-main/horarios/', 'horarios/')
WHERE `imagen_horario` LIKE '/buscarMaterias-main/horarios/%';

-- Verificar resultado:
-- SELECT id_horario, imagen_horario FROM horarios;

-- ============================================================
-- Tras ejecutar este script:
-- 1. Actualiza shared/config.php: DB_USER='tsjplat', DB_PASS='<tu_clave>'
-- 2. Revoca el acceso de root desde la aplicación (solo para CLI/herramientas)
-- ============================================================
