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
    error_log('estado_control DB error: ' . $e->getMessage());
    header("Location: ../admin.php?error=db");
    exit;
}

$id     = isset($_POST['id'])     ? (int)$_POST['id']     : 0;
$accion = trim($_POST['accion']   ?? '');

$accionesValidas = ['Aceptado', 'Rechazado'];
if ($id <= 0 || !in_array($accion, $accionesValidas, true)) {
    header("Location: ../admin.php?error=datos_invalidos");
    exit;
}

$stmt = $conexion->prepare("UPDATE solicitud_controles SET estado = ? WHERE id = ?");
$stmt->bind_param("si", $accion, $id);
$stmt->execute();
$stmt->close();
$conexion->close();

header("Location: ../admin.php");
exit;
