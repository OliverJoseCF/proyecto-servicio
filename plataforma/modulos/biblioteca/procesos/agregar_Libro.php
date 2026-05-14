<?php
require_once __DIR__ . '/../../../shared/lib/auth.php';
requireAuth('biblioteca', '../login.php');
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$nombre        = trim($_POST['nombre'] ?? '');
$editorial     = trim($_POST['editorial'] ?? '');
$clasificacion = trim($_POST['clasificacion'] ?? '');
$autor         = trim($_POST['autor'] ?? '');
$codigo        = trim($_POST['codigo'] ?? '');

if (!$nombre || !$editorial || !$clasificacion || !$autor || !$codigo) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
    exit;
}

$stmt = $conexion->prepare("INSERT INTO libros (nombre, editorial, clasificacion, autor, codigo) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nombre, $editorial, $clasificacion, $autor, $codigo);

header('Content-Type: application/json');
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar el libro']);
}
$stmt->close();
$conexion->close();
