<?php
include '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = $_POST['fecha'];
    $nombre = $_POST['nombre_docente'];
    $aula = $_POST['aula'];
    $recibo = $_POST['recibo'];
    $h_p = $_POST['hora_prestamo'];
    $h_e = $_POST['hora_entrega'];

    $sql = "INSERT INTO solicitud_controles (fecha, nombre_docente, aula, recibo, hora_prestamo, hora_entrega, estado) 
            VALUES (?, ?, ?, ?, ?, ?, 'Pendiente')";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssss", $fecha, $nombre, $aula, $recibo, $h_p, $h_e);

    if ($stmt->execute()) {
        echo "<script>alert('Solicitud enviada con éxito. Espere aprobación.'); window.location.href='../index.html';</script>";
    } else {
        echo "Error: " . $conexion->error;
    }
}
?>s