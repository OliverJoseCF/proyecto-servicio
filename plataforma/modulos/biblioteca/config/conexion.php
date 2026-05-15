<?php
require_once __DIR__ . '/../../../shared/config.php';

$servername = DB_HOST . ':' . DB_PORT;
$username   = DB_USER;
$password   = DB_PASS;
$dbname     = DB_BIBLIOTECA;

$conexion = new mysqli($servername, $username, $password, $dbname);
if ($conexion->connect_error) {
    error_log('biblioteca DB connect error: ' . $conexion->connect_error);
    die('Error de conexión. Contacta al administrador del sistema.');
}
$conexion->set_charset(DB_CHARSET);
