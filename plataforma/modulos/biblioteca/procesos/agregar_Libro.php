<?php
require_once __DIR__ . '/../../../shared/lib/auth.php';
requireAuth('biblioteca', '../login.php');
requirePost();

header('Content-Type: application/json');

try {
    require '../config/conexion.php';

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
    $stmt->execute();
    $stmt->close();
    $conexion->close();
    echo json_encode(['success' => true]);
} catch (\Throwable $e) {
    error_log('agregar_Libro error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al guardar el libro. Contacta al administrador.']);
}
