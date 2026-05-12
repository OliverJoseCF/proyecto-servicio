<?php
include '../config/conexion.php';

if (isset($_GET['id']) && isset($_GET['accion'])) {
    $id = $_GET['id'];
    $accion = $_GET['accion']; // Recibe 'Aceptado' o 'Rechazado'

    $stmt = $conexion->prepare("UPDATE solicitud_controles SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $accion, $id);

    if ($stmt->execute()) {
        // Regresamos al admin.php
        header("Location: ../admin.php");
    } else {
        echo "Error al actualizar estado: " . $conexion->error;
    }
    $stmt->close();
}
$conexion->close();
?>