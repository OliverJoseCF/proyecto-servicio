-- ============================================================
-- Setup inicial de la plataforma TSJ Chapala
-- Ejecutar como root una sola vez tras importar los dumps.
-- ============================================================

-- 1. Crear usuario de aplicación (mínimos privilegios)
--
--    INSTRUCCIÓN: Sustituye TU_CLAVE_SEGURA por una contraseña real (≥16 chars)
--    antes de ejecutar. NUNCA dejes el placeholder.
--
--    Ejemplo (en terminal):
--      mysql -u root -e "CREATE USER IF NOT EXISTS 'tsjplat'@'127.0.0.1' IDENTIFIED BY 'MiClaveSegura123!';"
--
-- Si ejecutas este archivo directamente, edita la línea siguiente primero:
CREATE USER IF NOT EXISTS 'tsjplat'@'127.0.0.1' IDENTIFIED BY 'TU_CLAVE_SEGURA';

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
-- 1. Pon en shared/config.local.php:
--      define('DB_USER', 'tsjplat');
--      define('DB_PASS', 'TU_CLAVE_SEGURA');
-- 2. Genera los hashes de admin con los scripts tools/setup_password.php
--    de biblioteca y horarios, y añádelos también a config.local.php.
-- 3. Revoca el acceso de root a la aplicación (solo para CLI/herramientas).
-- ============================================================
