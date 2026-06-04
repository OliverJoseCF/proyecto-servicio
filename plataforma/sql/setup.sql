-- ============================================================
-- setup.sql  —  Plataforma TSJ Chapala
-- Ejecutar como root UNA SOLA VEZ, después de importar kiosko_tsj.sql
-- ============================================================

-- 1. Crear usuario de aplicación (mínimos privilegios)
--    EDITA la contraseña antes de ejecutar.
CREATE USER IF NOT EXISTS 'tsjplat'@'127.0.0.1' IDENTIFIED BY 'TU_CLAVE_SEGURA';

GRANT SELECT, INSERT, UPDATE, DELETE ON `kiosko_tsj`.* TO 'tsjplat'@'127.0.0.1';

FLUSH PRIVILEGES;

-- ============================================================
-- Tras ejecutar este script:
-- 1. Copia  plataforma/shared/config.local.example.php
--        → plataforma/shared/config.local.php
-- 2. En config.local.php pon:
--      define('DB_USER', 'tsjplat');
--      define('DB_PASS', 'TU_CLAVE_SEGURA');  // misma que arriba
-- 3. Genera el hash de la contraseña admin:
--      php -r "echo password_hash('tu_contraseña', PASSWORD_BCRYPT, ['cost'=>12]);"
--    y pégalo en config.local.php → GLOBAL_ADMIN_HASH
-- ============================================================
