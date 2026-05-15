<?php
require_once __DIR__ . '/../../../shared/lib/auth.php';
require_once __DIR__ . '/../../../shared/lib/RateLimit.php';

try {
    require '../config/conexion.php';
} catch (\Throwable $e) {
    error_log('guardarSolControles DB error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Error de conexión. Inténtalo de nuevo más tarde.';
    header('Location: ../solicitudDeControles.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../buscar.php');
    exit;
}

// Rate-limit: máx. 5 solicitudes por IP cada hora
$rl = new RateLimit(5, 3600);
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if ($rl->isBlocked($ip)) {
    $_SESSION['flash_error'] = 'Has enviado demasiadas solicitudes. Espera antes de intentarlo de nuevo.';
    header('Location: ../solicitudDeControles.php');
    exit;
}

// CSRF
if (!csrfVerify()) {
    $_SESSION['flash_error'] = 'Petición inválida. Recarga la página e inténtalo de nuevo.';
    header('Location: ../solicitudDeControles.php');
    exit;
}

$fecha  = trim($_POST['fecha']          ?? '');
$nombre = trim($_POST['nombre_docente'] ?? '');
$aula   = trim($_POST['aula']           ?? '');
$recibo = trim($_POST['recibo']         ?? '');
$h_p    = trim($_POST['hora_prestamo']  ?? '');
$h_e    = trim($_POST['hora_entrega']   ?? '');

// Validaciones
if (!$fecha || !$nombre || !$aula || !$recibo || !$h_p || !$h_e) {
    $_SESSION['flash_error'] = 'Todos los campos son obligatorios.';
    header('Location: ../solicitudDeControles.php');
    exit;
}
$fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
    $_SESSION['flash_error'] = 'La fecha no tiene un formato válido.';
    header('Location: ../solicitudDeControles.php');
    exit;
}
if (strtotime($h_e) <= strtotime($h_p)) {
    $_SESSION['flash_error'] = 'La hora de entrega debe ser posterior a la hora de préstamo.';
    header('Location: ../solicitudDeControles.php');
    exit;
}

$sql  = 'INSERT INTO solicitud_controles (fecha, nombre_docente, aula, recibo, hora_prestamo, hora_entrega, estado)
         VALUES (?, ?, ?, ?, ?, ?, \'Pendiente\')';
$stmt = $conexion->prepare($sql);
$stmt->bind_param('ssssss', $fecha, $nombre, $aula, $recibo, $h_p, $h_e);

if ($stmt->execute()) {
    $rl->record($ip);
    $_SESSION['flash_ok'] = 'Solicitud enviada con éxito. Espere aprobación del administrador.';
} else {
    error_log('guardarSolControles error: ' . $conexion->error);
    $_SESSION['flash_error'] = 'Error al procesar la solicitud. Inténtalo de nuevo.';
}

$stmt->close();
$conexion->close();
header('Location: ../solicitudDeControles.php');
exit;
