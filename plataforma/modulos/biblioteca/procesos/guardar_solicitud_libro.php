<?php
require_once __DIR__ . '/../../../shared/lib/auth.php';
require_once __DIR__ . '/../../../shared/lib/RateLimit.php';
require_once __DIR__ . '/../../../shared/config.php';

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
$tipo           = $_POST['tipo'] ?? 'prestamo';
$tipo           = in_array($tipo, ['prestamo','consulta_sala'], true) ? $tipo : 'prestamo';

if ($nombre_libro === '' || $codigo_libro === '' || $nombre_usuario === '') {
    $_SESSION['flash_error'] = 'Todos los campos son obligatorios.';
    header('Location: ../solicitudDeLibros.php?titulo=' . urlencode($nombre_libro) . '&codigo=' . urlencode($codigo_libro));
    exit;
}
if (mb_strlen($nombre_usuario) > 150) {
    $_SESSION['flash_error'] = 'El nombre es demasiado largo.';
    header('Location: ../solicitudDeLibros.php?titulo=' . urlencode($nombre_libro) . '&codigo=' . urlencode($codigo_libro));
    exit;
}

try {
    $db  = getPDO(DB_NAME);

    // Buscar libro por código para obtener su id
    $libro = $db->prepare('SELECT id FROM libros WHERE codigo = ? AND activo = 1 LIMIT 1');
    $libro->execute([mb_substr($codigo_libro, 0, 30)]);
    $libroRow = $libro->fetch();

    if (!$libroRow) {
        $_SESSION['flash_error'] = 'El libro no fue encontrado o ya no está disponible.';
        header('Location: ../buscar.php');
        exit;
    }

    $db->prepare(
        'INSERT INTO solicitudes_biblioteca (libro_id, estudiante_nombre, estudiante_control, tipo)
         VALUES (?, ?, ?, ?)'
    )->execute([
        $libroRow['id'],
        mb_substr($nombre_usuario, 0, 150),
        '',   // No. control — el formulario público no lo pide, se deja vacío
        $tipo,
    ]);

    $rl->record($ip);
    $_SESSION['flash_ok'] = '¡Solicitud enviada correctamente! El administrador la revisará pronto.';
    header('Location: ../solicitudDeLibros.php?titulo=' . urlencode($nombre_libro) . '&codigo=' . urlencode($codigo_libro));
    exit;

} catch (\Throwable $e) {
    error_log('guardar_solicitud_libro error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Error al procesar la solicitud. Inténtalo de nuevo.';
    header('Location: ../solicitudDeLibros.php?titulo=' . urlencode($nombre_libro) . '&codigo=' . urlencode($codigo_libro));
    exit;
}
