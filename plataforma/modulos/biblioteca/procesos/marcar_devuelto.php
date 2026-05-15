<?php
require_once __DIR__ . '/../../../shared/lib/auth.php';
requireAuth('biblioteca', '../login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin.php");
    exit;
}

if (!csrfVerify()) {
    header("Location: ../admin.php?error=csrf");
    exit;
}

try {
    require '../config/conexion.php';
} catch (\Throwable $e) {
    error_log('marcar_devuelto DB error: ' . $e->getMessage());
    header("Location: ../admin.php?error=db");
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    header("Location: ../admin.php?error=id_invalido");
    exit;
}

$sql  = "UPDATE solicitud_libros SET entregado = 1, fecha_devolucion = NOW() WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ../admin.php?status=success");
} else {
    error_log("marcar_devuelto error: " . $conexion->error);
    header("Location: ../admin.php?error=db");
}
$stmt->close();
$conexion->close();
exit;
