<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../../../shared/lib/RateLimit.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

// Rate limiting: máx. 5 sugerencias por IP cada hora
$rl = new RateLimit(5, 3600);
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if ($rl->isBlocked($ip)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Has enviado demasiadas sugerencias. Por favor espera antes de intentarlo de nuevo.']);
    exit();
}

// Validar CSRF
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Petición inválida. Recarga la página e intenta de nuevo.']);
    exit();
}

$nombre_empresa  = trim($_POST['nombre_empresa']  ?? '');
$correo_empresa  = trim($_POST['correo_empresa']  ?? '');
$nombre_contacto = trim($_POST['nombre_contacto'] ?? '');

// Validaciones de presencia y longitud
if ($nombre_empresa === '' || $correo_empresa === '' || $nombre_contacto === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos.']);
    exit();
}
if (mb_strlen($nombre_empresa) > 200 || mb_strlen($nombre_contacto) > 200) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Los campos exceden la longitud máxima permitida.']);
    exit();
}
if (!filter_var($correo_empresa, FILTER_VALIDATE_EMAIL) || mb_strlen($correo_empresa) > 254) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El correo de contacto no es válido.']);
    exit();
}

// Sanitizar: eliminar caracteres de control y saltos de línea (previene header injection)
$nombre_empresa  = preg_replace('/[\r\n\x00]/', '', $nombre_empresa);
$correo_empresa  = preg_replace('/[\r\n\x00]/', '', $correo_empresa);
$nombre_contacto = preg_replace('/[\r\n\x00]/', '', $nombre_contacto);

$to      = VINCULACION_EMAIL;
$subject = '=?UTF-8?B?' . base64_encode('Nueva sugerencia de empresa: ' . $nombre_empresa) . '?=';
$body    = "Se ha recibido una nueva sugerencia de empresa para establecer un convenio.\n\n"
         . "Nombre de la empresa: " . $nombre_empresa  . "\n"
         . "Correo de contacto con la empresa: " . $correo_empresa  . "\n"
         . "Nombre de la persona con la que se contactará: " . $nombre_contacto . "\n\n"
         . "-- Plataforma de Convenios TecSJ --";

$headers = implode("\r\n", [
    'From: no-reply@tecsj.edu.mx',
    'Reply-To: ' . $correo_empresa,
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: base64',
]);

$sent = mail($to, $subject, base64_encode($body), $headers);

if ($sent) {
    $rl->record($ip); // contar envíos exitosos también
    echo json_encode(['success' => true, 'message' => 'Sugerencia enviada correctamente.']);
} else {
    error_log('sugerir_empresa: mail() falló para IP ' . $ip);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo enviar el correo. Intenta más tarde.']);
}
