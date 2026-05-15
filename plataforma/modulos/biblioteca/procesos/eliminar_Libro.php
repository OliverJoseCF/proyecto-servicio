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

header('Content-Type: application/json');

try {
    require '../config/conexion.php';

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        exit;
    }

    $stmt = $conexion->prepare("DELETE FROM libros WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conexion->close();
    echo json_encode(['success' => true]);
} catch (\Throwable $e) {
    error_log('eliminar_Libro error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al eliminar. Contacta al administrador.']);
}
