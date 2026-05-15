<?php
require_once __DIR__ . '/../../../shared/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASS, DB_BIBLIOTECA);
    $conexion->set_charset(DB_CHARSET);
} catch (\mysqli_sql_exception $e) {
    error_log('biblioteca DB connect error: ' . $e->getMessage());
    die('Error de conexión. Contacta al administrador del sistema.');
}
