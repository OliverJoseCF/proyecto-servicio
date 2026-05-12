<?php
/**
 * Configuración centralizada de la plataforma TSJ.
 * Todos los módulos deben obtener sus conexiones desde aquí.
 * Credenciales reales: ajusta DB_USER / DB_PASS si difieren en tu entorno.
 */

define('DB_HOST',    '127.0.0.1');
define('DB_PORT',    3306);
define('DB_USER',    'root');
define('DB_PASS',    'root');
define('DB_CHARSET', 'utf8mb4');

// URL base de la plataforma (sin barra final) — ajusta si cambias el alias de Apache
define('PLATAFORMA_URL', '/plataforma');

// Nombres de BD por módulo
define('DB_BIBLIOTECA', 'biblioteca_escolar');
define('DB_CONVENIOS',  'convenios_db');
define('DB_HORARIOS',   'horarios_db');    // renombrada de 'churumuske'

/**
 * Devuelve un objeto PDO listo para usar.
 * Usado por: Horarios, Convenios.
 */
function getPDO(string $dbName): PDO {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, $dbName, DB_CHARSET
    );
    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}

/**
 * Devuelve una conexión mysqli lista para usar.
 * Usado por: Biblioteca.
 */
function getMysqli(string $dbName): mysqli {
    $conn = new mysqli(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASS, $dbName);
    if ($conn->connect_error) {
        die('Error de conexión: ' . $conn->connect_error);
    }
    $conn->set_charset(DB_CHARSET);
    return $conn;
}
