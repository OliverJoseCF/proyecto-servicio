<?php
session_start();
include '../config/conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Sincronizamos con el cambio de Workbench: entregado=1 y grabamos la fecha actual
    $sql = "UPDATE solicitud_libros SET entregado = 1, fecha_devolucion = NOW() WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../admin.php?status=success");
    } else {
        echo "Error: " . $conexion->error;
    }
}
?>