<?php
require_once __DIR__ . '/../../../shared/lib/auth.php';
requireAuth('biblioteca', '../login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

if (!csrfVerify()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Token inválido']);
    exit;
}

include '../config/conexion.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}

$stmt = $conexion->prepare("DELETE FROM libros WHERE id = ?");
$stmt->bind_param("i", $id);

header('Content-Type: application/json');
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al eliminar']);
}
$stmt->close();
$conexion->close();
