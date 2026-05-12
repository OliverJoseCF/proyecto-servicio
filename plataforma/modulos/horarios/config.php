<?php
require_once __DIR__ . '/../../shared/config.php';

// DB_NAME y DB_PASSWORD: aliases que usa getDB() de este módulo
// (DB_HOST, DB_PORT, DB_USER, DB_PASS, DB_CHARSET vienen de shared/config.php)
define('DB_NAME',     DB_HORARIOS);
define('DB_PASSWORD', DB_PASS);    // el código de Horarios usa DB_PASSWORD

// Rutas de archivos de horarios (PDFs/imágenes subidas)
define('HORARIOS_DIR', __DIR__ . '/horarios/');
define('HORARIOS_URL', 'horarios/');

function getDB(): PDO {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );
    return new PDO($dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}
