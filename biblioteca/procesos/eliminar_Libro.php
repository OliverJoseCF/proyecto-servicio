<?php
include '../config/conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conexion->prepare("DELETE FROM libros WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "OK";
    } else {
        echo "Error al eliminar: " . $conexion->error;
    }
    $stmt->close();
} else {
    echo "ID no recibido";
}

$conexion->close();
?>