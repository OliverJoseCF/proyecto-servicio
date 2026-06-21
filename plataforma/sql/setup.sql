-- ============================================================
-- setup.sql  —  Plataforma TSJ Chapala
-- Solo necesario en PRODUCCIÓN. En desarrollo local con XAMPP
-- (root sin contraseña) no hace falta: config.php ya tiene ese fallback.
--
-- Ejecutar como root UNA SOLA VEZ, DESPUÉS de importar kiosko_tsj.sql:
--   mysql -u root -e "source /ruta/al/proyecto/plataforma/sql/setup.sql"
-- ============================================================

-- 1. Crear usuario de aplicación (mínimos privilegios — sin DDL)
--    EDITA 'TU_CLAVE_SEGURA' antes de ejecutar (mínimo 16 caracteres).
CREATE USER IF NOT EXISTS 'tsjplat'@'127.0.0.1' IDENTIFIED BY 'TU_CLAVE_SEGURA';

GRANT SELECT, INSERT, UPDATE, DELETE ON `kiosko_tsj`.* TO 'tsjplat'@'127.0.0.1';

FLUSH PRIVILEGES;

-- ============================================================
-- Tras ejecutar este script:
--
-- 1. Copia  plataforma/shared/config.local.example.php
--        → plataforma/shared/config.local.php
--
-- 2. En config.local.php ajusta:
--      define('DB_USER', 'tsjplat');
--      define('DB_PASS', 'TU_CLAVE_SEGURA');  // misma contraseña que arriba
--
-- 3. Genera el hash del admin global (único login del panel):
--      php -r "echo password_hash('tu_contraseña', PASSWORD_BCRYPT, ['cost'=>12]);"
--    Pégalo en config.local.php → GLOBAL_ADMIN_HASH
--    También actualiza GLOBAL_ADMIN_EMAIL con el correo real.
--
-- Nota: BIBLIOTECA_ADMIN_HASH y HORARIOS_ADMIN_HASH que aparecen en
-- config.local.example.php son constantes legacy. Los módulos de admin
-- independientes de biblioteca y horarios fueron consolidados en el
-- panel central (/admin/). No es necesario configurar esos hashes.
-- ============================================================
