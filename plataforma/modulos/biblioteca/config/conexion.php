<?php
require_once __DIR__ . '/../../../shared/config.php';

$servername = DB_HOST . ':' . DB_PORT;
$username   = DB_USER;
$password   = DB_PASS;
$dbname     = DB_BIBLIOTECA;

$conexion = new mysqli($servername, $username, $password, $dbname);
if ($conexion->connect_error) {
    die('Error en la conexión: ' . $conexion->connect_error);
}
$conexion->set_charset(DB_CHARSET);
