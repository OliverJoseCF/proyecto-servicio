<?php
require_once __DIR__ . '/../../../shared/config.php';

// DB_NAME: shared/config.php ya lo define; solo lo fijamos si por algún motivo no existe,
// para evitar el warning "Constant DB_NAME already defined".
if (!defined('DB_NAME')) {
    define('DB_NAME', DB_CONVENIOS);
}

// Carga overrides locales/producción PRIMERO (hash real, correos, etc.)
// Así los define() de config.local.php tienen precedencia sobre los defaults de abajo.
$_tsj_conv_local = __DIR__ . '/config.local.php';
if (file_exists($_tsj_conv_local)) {
    require_once $_tsj_conv_local;
}
unset($_tsj_conv_local);

// Defaults (solo si config.local.php no los definió)
if (!defined('ADMIN_EMAIL'))         define('ADMIN_EMAIL',         'admin@tecsj.edu.mx');
if (!defined('VINCULACION_EMAIL'))   define('VINCULACION_EMAIL',   'vinculacion@tecsj.edu.mx');
if (!defined('ADMIN_PASSWORD_HASH')) define('ADMIN_PASSWORD_HASH', '');
