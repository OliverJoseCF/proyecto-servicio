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

include '../config/conexion.php';

$id     = isset($_POST['id'])     ? (int)$_POST['id']     : 0;
$accion = trim($_POST['accion']   ?? '');

$accionesValidas = ['Aceptado', 'Rechazado'];
if ($id <= 0 || !in_array($accion, $accionesValidas, true)) {
    header("Location: ../admin.php?error=datos_invalidos");
    exit;
}

$sql  = "UPDATE solicitud_libros SET estado = ? WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $accion, $id);
$stmt->execute();
$stmt->close();
$conexion->close();

header("Location: ../admin.php");
exit;
