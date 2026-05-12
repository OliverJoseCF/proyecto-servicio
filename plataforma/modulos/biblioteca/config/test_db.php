<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $conexion = new mysqli("localhost", "root", "", "biblioteca");

    if ($conexion->connect_error) {
        throw new Exception("Conexión fallida: " . $conexion->connect_error);
    }

    // Verificar si la tabla existe
    $sql = "SHOW TABLES LIKE 'inventario'";
    $resultado = $conexion->query($sql);
    
    if ($resultado->num_rows === 0) {
        echo "La tabla 'inventario' no existe.<br>";
        
        // Crear la tabla si no existe
        $sql = "CREATE TABLE IF NOT EXISTS inventario (
            id INT AUTO_INCREMENT PRIMARY KEY,
            folio VARCHAR(50) NOT NULL,
            titulo VARCHAR(255) NOT NULL,
            autor VARCHAR(255) NOT NULL,
            editorial VARCHAR(255) NOT NULL
        )";
        
        if ($conexion->query($sql)) {
            echo "Tabla 'inventario' creada exitosamente.<br>";
        } else {
            echo "Error al crear la tabla: " . $conexion->error . "<br>";
        }
    } else {
        echo "La tabla 'inventario' existe.<br>";
        
        // Contar registros
        $sql = "SELECT COUNT(*) as total FROM inventario";
        $resultado = $conexion->query($sql);
        $fila = $resultado->fetch_assoc();
        echo "Número de registros en la tabla: " . $fila['total'] . "<br>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    if (isset($conexion)) {
        $conexion->close();
    }
}
?>
