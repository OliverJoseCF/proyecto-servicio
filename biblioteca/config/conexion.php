<?php
$servername = "127.0.0.1:3306";
$username   = "root";
$password   = "root";
$dbname     = "biblioteca_escolar";

$conexion = new mysqli($servername, $username, $password, $dbname);
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}
?>