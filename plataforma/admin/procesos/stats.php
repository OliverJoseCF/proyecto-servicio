<?php
ob_start();
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

require_once dirname(__DIR__, 2) . '/shared/lib/auth.php';
require_once dirname(__DIR__, 2) . '/shared/config.php';

ob_end_clean();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!isGlobalAdmin()) {
    http_response_code(403);
    exit(json_encode(['ok' => false, 'msg' => 'No autorizado']));
}

try {
    $db = getPDO(DB_NAME);
    echo json_encode([
        'ok'          => true,
        'libros'      => (int) $db->query('SELECT COUNT(*) FROM libros WHERE activo=1')->fetchColumn(),
        'convenios'   => (int) $db->query('SELECT COUNT(*) FROM convenios WHERE activo=1')->fetchColumn(),
        'docentes'    => (int) $db->query('SELECT COUNT(*) FROM docentes WHERE activo=1')->fetchColumn(),
        'horarios'    => (int) $db->query('SELECT COUNT(*) FROM horarios WHERE activo=1')->fetchColumn(),
        'prestamos'   => (int) $db->query('SELECT COUNT(*) FROM prestamos WHERE devuelto=0')->fetchColumn(),
        'solicitudes' => (int) $db->query('SELECT COUNT(*) FROM solicitudes_biblioteca WHERE estado="pendiente"')->fetchColumn(),
        'sugerencias' => (int) $db->query('SELECT COUNT(*) FROM sugerencias_empresa WHERE estado="pendiente"')->fetchColumn(),
    ]);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
}
