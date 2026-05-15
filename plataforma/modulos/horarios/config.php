<?php
require_once __DIR__ . '/../../shared/config.php';

define('DB_NAME', DB_HORARIOS);

// Rutas de archivos de horarios (PDFs/imágenes subidas)
define('HORARIOS_DIR', __DIR__ . '/horarios/');
define('HORARIOS_URL', 'horarios/');

function getDB(): PDO {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );
    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}
