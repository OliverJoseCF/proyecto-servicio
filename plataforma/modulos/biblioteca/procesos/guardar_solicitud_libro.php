<?php
include '../config/conexion.php';

// Verificamos que los datos lleguen por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recibimos los datos del formulario (deben coincidir con el 'name' de tus inputs)
    $nombre_libro   = $_POST['nombre_libro'];
    $codigo_libro   = $_POST['codigo_libro'];
    $nombre_usuario = $_POST['nombre_usuario'];
    $fecha          = $_POST['fecha_solicitud'];
    $estado         = 'Pendiente'; 
    $entregado      = 0; // Importante para la lógica de disponibilidad que creamos

    // Consulta SQL para insertar la solicitud
    $sql = "INSERT INTO solicitud_libros (nombre_libro, codigo_libro, nombre_usuario, fecha_solicitud, estado, entregado) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    
    // "sssssi" -> 5 strings y 1 entero (entregado)
    $stmt->bind_param("sssssi", $nombre_libro, $codigo_libro, $nombre_usuario, $fecha, $estado, $entregado);

    if ($stmt->execute()) {
        echo "<script>
                alert('¡Solicitud enviada con éxito! El administrador la revisará pronto.');
                window.location.href = '../buscar.php';
              </script>";
    } else {
        echo "Error al procesar la solicitud: " . $conexion->error;
    }

    $stmt->close();
    $conexion->close();
} else {
    header("Location: ../buscar.php");
}
?>