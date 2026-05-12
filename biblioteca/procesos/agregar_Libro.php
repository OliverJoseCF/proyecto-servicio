<?php
include '../config/conexion.php';

$nombre        = trim($_POST['nombre'] ?? '');
$editorial     = trim($_POST['editorial'] ?? '');
$clasificacion = trim($_POST['clasificacion'] ?? '');
$autor         = trim($_POST['autor'] ?? '');
$codigo        = trim($_POST['codigo'] ?? '');

if (!$nombre || !$editorial || !$clasificacion || !$autor || !$codigo) {
    echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
    exit;
}

$stmt = $conexion->prepare("INSERT INTO libros (nombre, editorial, clasificacion, autor, codigo) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nombre, $editorial, $clasificacion, $autor, $codigo);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conexion->close();
?>