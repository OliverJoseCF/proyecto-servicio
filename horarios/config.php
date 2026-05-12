<?php
// ============================================================
//  config.php  —  Configuración centralizada de la aplicación
//  Cambia aquí el puerto, nombre de BD y contraseña una sola vez
// ============================================================

define('DB_HOST',     'localhost');
define('DB_PORT',     3307);          // Cambia a 3306 si usas el puerto por defecto
define('DB_NAME',     'churumuske');
define('DB_USER',     'root');
define('DB_PASSWORD', 'Admin');       // Cambia si cambias la contraseña de MySQL
define('DB_CHARSET',  'utf8');

// --- Rutas de horarios (dinámicas, no hardcodeadas) ---
// HORARIOS_DIR : ruta física donde se guardan los archivos
// HORARIOS_URL : ruta relativa que se guarda en la BD y se usa en <a href> / <img src>
define('HORARIOS_DIR', __DIR__ . '/horarios/');
define('HORARIOS_URL', 'horarios/');   // relativa al index del módulo

/**
 * Retorna una conexión PDO lista para usar.
 * Lanza excepción si no puede conectar.
 */
function getDB(): PDO {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}