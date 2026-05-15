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
    error_log('actualizar_libro DB error: ' . $e->getMessage());
    header("Location: ../admin.php?error=db");
    exit;
}

$codigo_original = trim($_POST['codigo_original'] ?? '');
$nombre          = trim($_POST['nombre'] ?? '');
$editorial       = trim($_POST['editorial'] ?? '');
$clasificacion   = trim($_POST['clasificacion'] ?? '');
$autor           = trim($_POST['autor'] ?? '');
$codigo          = trim($_POST['codigo'] ?? '');

if (!$codigo_original || !$nombre || !$editorial || !$clasificacion || !$autor || !$codigo) {
    header("Location: ../admin.php?error=datos_incompletos");
    exit;
}

$stmt = $conexion->prepare("UPDATE libros SET nombre=?, editorial=?, clasificacion=?, autor=?, codigo=? WHERE codigo=?");
$stmt->bind_param("ssssss", $nombre, $editorial, $clasificacion, $autor, $codigo, $codigo_original);

if ($stmt->execute()) {
    header("Location: ../admin.php?exito=editado");
} else {
    header("Location: ../admin.php?error=1");
}
$stmt->close();
$conexion->close();
