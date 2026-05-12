<?php
include '../config/conexion.php';
$id = $_GET['id'];
$accion = $_GET['accion']; // Aceptado o Rechazado

$sql = "UPDATE solicitud_libros SET estado = ? WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $accion, $id);
$stmt->execute();
header("Location: ../admin.php");
?>