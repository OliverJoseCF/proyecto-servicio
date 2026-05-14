<?php
include '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../buscar.php");
    exit;
}

$fecha  = trim($_POST['fecha']          ?? '');
$nombre = trim($_POST['nombre_docente'] ?? '');
$aula   = trim($_POST['aula']           ?? '');
$recibo = trim($_POST['recibo']         ?? '');
$h_p    = trim($_POST['hora_prestamo']  ?? '');
$h_e    = trim($_POST['hora_entrega']   ?? '');

// Validaciones básicas
if (!$fecha || !$nombre || !$aula || !$recibo || !$h_p || !$h_e) {
    echo "<script>alert('Todos los campos son obligatorios.'); window.history.back();</script>";
    exit;
}
if (strtotime($h_e) <= strtotime($h_p)) {
    echo "<script>alert('La hora de entrega debe ser posterior a la hora de préstamo.'); window.history.back();</script>";
    exit;
}

$sql  = "INSERT INTO solicitud_controles (fecha, nombre_docente, aula, recibo, hora_prestamo, hora_entrega, estado)
         VALUES (?, ?, ?, ?, ?, ?, 'Pendiente')";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssssss", $fecha, $nombre, $aula, $recibo, $h_p, $h_e);

if ($stmt->execute()) {
    echo "<script>alert('Solicitud enviada con éxito. Espere aprobación.'); window.location.href='../buscar.php';</script>";
} else {
    error_log("guardarSolControles error: " . $conexion->error);
    echo "<script>alert('Error al procesar la solicitud.'); window.history.back();</script>";
}
$stmt->close();
$conexion->close();
