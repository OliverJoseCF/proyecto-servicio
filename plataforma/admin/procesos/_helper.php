<?php
/**
 * Helper compartido para todos los procesos del admin.
 * Verifica sesión, CSRF y devuelve conexión PDO a kiosko_tsj.
 */

require_once dirname(__DIR__, 2) . '/shared/lib/auth.php';
require_once dirname(__DIR__, 2) . '/shared/config.php';

// Solo peticiones POST desde el admin
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['ok' => false, 'msg' => 'Método no permitido']));
}

$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) {
    http_response_code(403);
    exit(json_encode(['ok' => false, 'msg' => 'No autorizado']));
}

if (!csrfVerify()) {
    http_response_code(403);
    exit(json_encode(['ok' => false, 'msg' => 'Token CSRF inválido']));
}

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = getPDO(DB_NAME);
    }
    return $pdo;
}

function jsonOk(string $msg = 'OK', array $extra = []): never {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['ok' => true, 'msg' => $msg], $extra));
    exit;
}

function jsonErr(string $msg, int $code = 400): never {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'msg' => $msg]);
    exit;
}

function str(string $key, int $max = 500): string {
    return mb_substr(trim($_POST[$key] ?? ''), 0, $max);
}

function intVal(string $key, int $default = 0): int {
    return isset($_POST[$key]) ? (int)$_POST[$key] : $default;
}
