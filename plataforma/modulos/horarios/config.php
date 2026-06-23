<?php
require_once __DIR__ . '/../../shared/config.php';

// DB_NAME ya está definido en shared/config.php apuntando a kiosko_tsj

// Rutas de archivos de horarios (PDFs/imágenes subidas)
define('HORARIOS_DIR', __DIR__ . '/horarios/');
define('HORARIOS_URL', 'horarios/');

// Wrapper retrocompatible: delega en el helper central getPDO() de shared/config.php.
function getDB(): PDO {
    return getPDO(DB_NAME);
}
