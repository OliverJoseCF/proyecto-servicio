
<?php
include '../config/conexion.php';

$codigo_original = $_POST['codigo_original'] ?? '';
$nombre          = trim($_POST['nombre'] ?? '');
$editorial       = trim($_POST['editorial'] ?? '');
$clasificacion   = trim($_POST['clasificacion'] ?? '');
$autor           = trim($_POST['autor'] ?? '');
$codigo          = trim($_POST['codigo'] ?? '');

$stmt = $conexion->prepare("UPDATE libros SET nombre=?, editorial=?, clasificacion=?, autor=?, codigo=? WHERE codigo=?");
$stmt->bind_param("ssssss", $nombre, $editorial, $clasificacion, $autor, $codigo, $codigo_original);

if ($stmt->execute()) {
    header("Location: ../admin.php?exito=editado");
} else {
    header("Location: ../admin.php?error=1");
}

$stmt->close();
$conexion->close();
?>