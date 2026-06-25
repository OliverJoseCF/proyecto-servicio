<?php
/**
 * Helper compartido para todos los procesos del admin.
 * Verifica sesión, CSRF y devuelve conexión PDO a kiosko_tsj.
 */

// Buffer de salida desde el inicio para capturar cualquier output inesperado
ob_start();

// Suprimir display de errores — se loguean pero no salen al output
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

require_once dirname(__DIR__, 2) . '/shared/lib/auth.php';
require_once dirname(__DIR__, 2) . '/shared/config.php';

// Limpiar cualquier output generado y fijar Content-Type JSON
ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

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

/**
 * Registra la acción exitosa en la bitácora admin_log.
 * Defensivo: nunca interrumpe la respuesta si la tabla no existe o la BD falla.
 */
function adminLog(string $detalle): void {
    $accion = mb_substr(trim($_POST['accion'] ?? ''), 0, 50);
    if ($accion === '') return;
    $modulo  = basename($_SERVER['SCRIPT_NAME'] ?? '', '.php');
    $detalle = mb_substr($detalle, 0, 500);
    $adminId = function_exists('adminActualId')     ? adminActualId()     : 0;
    $adminNom= function_exists('adminActualNombre') ? adminActualNombre() : 'Administrador';
    try {
        // Esquema nuevo: registra también quién ejecutó la acción
        db()->prepare('INSERT INTO admin_log (modulo, accion, detalle, admin_id, admin_nombre) VALUES (?,?,?,?,?)')
            ->execute([$modulo, $accion, $detalle, $adminId ?: null, $adminNom]);
    } catch (\Throwable $e) {
        // Fallback: BD con esquema viejo (sin columnas de identidad)
        try {
            db()->prepare('INSERT INTO admin_log (modulo, accion, detalle) VALUES (?,?,?)')
                ->execute([$modulo, $accion, $detalle]);
        } catch (\Throwable $e2) {
            // Sin bitácora disponible — la operación principal no se ve afectada
        }
    }
}

/**
 * Responde éxito al frontend y registra en bitácora.
 * @param string      $msg      Mensaje amigable mostrado al admin (toast).
 * @param array       $extra    Datos extra para el JSON de respuesta.
 * @param string|null $detalle  Detalle específico para la bitácora (ej. nombre + ID
 *                              del registro afectado). Si es null, se usa $msg.
 */
function jsonOk(string $msg = 'OK', array $extra = [], ?string $detalle = null): never {
    adminLog($detalle ?? $msg);
    echo json_encode(array_merge(['ok' => true, 'msg' => $msg], $extra));
    exit;
}

function jsonErr(string $msg, int $code = 400): never {
    http_response_code($code);
    echo json_encode(['ok' => false, 'msg' => $msg]);
    exit;
}

function str(string $key, int $max = 500): string {
    return mb_substr(trim($_POST[$key] ?? ''), 0, $max);
}

function postInt(string $key, int $default = 0): int {
    return isset($_POST[$key]) ? (int)$_POST[$key] : $default;
}

/**
 * Valida un teléfono: solo dígitos, +, -, espacios y paréntesis (7–25 chars).
 * Cadena vacía se considera válida (campo opcional); usa $obligatorio para exigirlo.
 * Termina la petición con jsonErr() si es inválido.
 */
function telefono(string $key, bool $obligatorio = false): string {
    $val = str($key, 25);
    if ($val === '') {
        if ($obligatorio) jsonErr('El teléfono es requerido');
        return '';
    }
    if (!preg_match('/^[0-9+\-\s()]{7,25}$/', $val)) {
        jsonErr('El teléfono solo puede contener números, +, -, espacios y paréntesis (7 a 25 caracteres)');
    }
    return $val;
}

/**
 * Valida una URL que más tarde se imprimirá en un href/src.
 * Acepta: cadena vacía (campo opcional), URL absoluta http(s):// o ruta del propio
 * sitio que empiece por '/'. Rechaza esquemas peligrosos (javascript:, data:, vbscript:).
 */
function urlSegura(string $url): bool {
    if ($url === '') return true;
    return (bool) preg_match('#^(https?://|/)#i', $url);
}
