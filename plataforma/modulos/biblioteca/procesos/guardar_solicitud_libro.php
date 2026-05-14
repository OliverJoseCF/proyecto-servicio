<?php
require_once __DIR__ . '/../../../shared/lib/auth.php';
require_once __DIR__ . '/../../../shared/lib/RateLimit.php';
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../buscar.php');
    exit;
}

// Rate-limit: máx. 5 solicitudes por IP cada hora
$rl = new RateLimit(5, 3600);
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if ($rl->isBlocked($ip)) {
    $_SESSION['flash_error'] = 'Has enviado demasiadas solicitudes. Espera antes de intentarlo de nuevo.';
    header('Location: ../buscar.php');
    exit;
}

// CSRF
if (!csrfVerify()) {
    $_SESSION['flash_error'] = 'Petición inválida. Recarga la página e inténtalo de nuevo.';
    header('Location: ../buscar.php');
    exit;
}

$nombre_libro   = trim($_POST['nombre_libro']   ?? '');
$codigo_libro   = trim($_POST['codigo_libro']   ?? '');
$nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
$fecha          = trim($_POST['fecha_solicitud'] ?? '');

// Validaciones
if ($nombre_libro === '' || $codigo_libro === '' || $nombre_usuario === '' || $fecha === '') {
    $_SESSION['flash_error'] = 'Todos los campos son obligatorios.';
    header('Location: ../solicitudDeLibros.php?titulo=' . urlencode($nombre_libro) . '&codigo=' . urlencode($codigo_libro));
    exit;
}
if (mb_strlen($nombre_usuario) > 255) {
    $_SESSION['flash_error'] = 'El nombre de usuario es demasiado largo.';
    header('Location: ../solicitudDeLibros.php?titulo=' . urlencode($nombre_libro) . '&codigo=' . urlencode($codigo_libro));
    exit;
}
$fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
    $_SESSION['flash_error'] = 'La fecha no tiene un formato válido.';
    header('Location: ../solicitudDeLibros.php?titulo=' . urlencode($nombre_libro) . '&codigo=' . urlencode($codigo_libro));
    exit;
}

$sql  = 'INSERT INTO solicitud_libros (nombre_libro, codigo_libro, nombre_usuario, fecha_solicitud, estado, entregado)
         VALUES (?, ?, ?, ?, ?, ?)';
$stmt = $conexion->prepare($sql);
$estado    = 'Pendiente';
$entregado = 0;
$stmt->bind_param('sssssi', $nombre_libro, $codigo_libro, $nombre_usuario, $fecha, $estado, $entregado);

if ($stmt->execute()) {
    $rl->record($ip);
    $_SESSION['flash_ok'] = '¡Solicitud enviada! El administrador la revisará pronto.';
} else {
    error_log('guardar_solicitud_libro error: ' . $conexion->error);
    $_SESSION['flash_error'] = 'Error al procesar la solicitud. Inténtalo de nuevo.';
}

$stmt->close();
$conexion->close();
header('Location: ../buscar.php');
exit;
