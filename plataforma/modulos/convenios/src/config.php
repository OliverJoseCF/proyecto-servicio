<?php
require_once __DIR__ . '/../../../shared/config.php';

// DB_NAME alias que usa conexion.php de este módulo
define('DB_NAME', DB_CONVENIOS);

// Correo institucional de notificaciones (ajusta en producción)
if (!defined('ADMIN_EMAIL'))       define('ADMIN_EMAIL',       'admin@tecsj.edu.mx');
if (!defined('VINCULACION_EMAIL')) define('VINCULACION_EMAIL', 'vinculacion@tecsj.edu.mx');

// Hash de admin — DEBE definirse en src/config.local.php.
// Genera el hash con:  php tools/setup_password.php
if (!defined('ADMIN_PASSWORD_HASH')) define('ADMIN_PASSWORD_HASH', '');

// Carga overrides locales/producción (hash real, correos, etc.)
$_tsj_conv_local = __DIR__ . '/config.local.php';
if (file_exists($_tsj_conv_local)) {
    require_once $_tsj_conv_local;
}
unset($_tsj_conv_local);
