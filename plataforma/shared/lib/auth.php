<?php
/**
 * Autenticación compartida — sesión segura + CSRF + helpers.
 * Incluir con require_once antes de cualquier output.
 */

if (!defined('PLATAFORMA_URL')) {
    require_once dirname(__DIR__) . '/config.php';
}

// ── Sesión segura ──────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.gc_maxlifetime', 3600);

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}

// ── Idle-timeout (1 hora) ─────────────────────────────────────────────────────
if (!empty($_SESSION['_auth']) && isset($_SESSION['_last_activity'])) {
    if ((time() - $_SESSION['_last_activity']) > 3600) {
        session_unset();
        session_destroy();
        session_start();
    } else {
        $_SESSION['_last_activity'] = time();
    }
}

// ── Revalidación de la cuenta admin contra la BD ───────────────────────────────
// Una sesión global cuyo admin proviene de la tabla `admins` (id > 0) debe seguir
// existiendo y estar activa. Así, al desactivar o eliminar a un administrador, su
// sesión se cierra en la siguiente petición (no espera al idle-timeout de 1 h).
// La cuenta maestra (id 0, definida en config.local.php) se omite: no vive en la
// tabla. Si la BD no responde, NO se cierra la sesión (resiliencia) y se reintenta
// en la siguiente petición.
if (!empty($_SESSION['_auth'])
    && ($_SESSION['_module'] ?? '') === 'global'
    && (int)($_SESSION['_admin_id'] ?? 0) > 0
) {
    if (!function_exists('getPDO') && file_exists(dirname(__DIR__) . '/config.php')) {
        require_once dirname(__DIR__) . '/config.php';
    }
    if (function_exists('getPDO') && defined('DB_NAME')) {
        try {
            $_stmt = getPDO(DB_NAME)->prepare('SELECT activo FROM admins WHERE id = ?');
            $_stmt->execute([(int)$_SESSION['_admin_id']]);
            $_activo = $_stmt->fetchColumn();
            // false = cuenta eliminada; 0 = cuenta desactivada → cerrar sesión.
            if ($_activo === false || (int)$_activo !== 1) {
                session_unset();
                session_destroy();
                session_start();
            }
            unset($_stmt, $_activo);
        } catch (\Throwable $_e) {
            // BD no disponible: se conserva la sesión y se revalida más tarde.
        }
    }
}

// ── CSRF ──────────────────────────────────────────────────────────────────────
if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
}

function csrfToken(): string {
    return $_SESSION['_csrf'];
}

function csrfVerify(): bool {
    $token = $_POST['_csrf'] ?? '';
    return !empty($token) && hash_equals($_SESSION['_csrf'] ?? '', $token);
}

function csrfField(): string {
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

// ── Login / logout helpers ─────────────────────────────────────────────────────

/**
 * Login global (toda la plataforma) con rol admin.
 *
 * @param string $role        Rol (siempre 'admin' por ahora).
 * @param int    $adminId     ID en la tabla `admins`. 0 = cuenta maestra (config.local.php).
 * @param string $adminNombre Nombre para mostrar y registrar en la bitácora.
 * @param string $adminEmail  Correo de la cuenta autenticada.
 */
function globalLogin(string $role = 'admin', int $adminId = 0, string $adminNombre = 'Administrador', string $adminEmail = ''): void {
    session_regenerate_id(true);
    $_SESSION['_csrf']          = bin2hex(random_bytes(32));
    $_SESSION['_auth']          = true;
    $_SESSION['_module']        = 'global';
    $_SESSION['_role']          = $role;
    $_SESSION['_admin_id']      = $adminId;
    $_SESSION['_admin_nombre']  = $adminNombre;
    $_SESSION['_admin_email']   = $adminEmail;
    $_SESSION['_last_activity'] = time();
}

/**
 * Devuelve true si hay sesión global activa.
 */
function isGlobalAdmin(): bool {
    return !empty($_SESSION['_auth'])
        && ($_SESSION['_module'] ?? '') === 'global'
        && ($_SESSION['_role']   ?? '') === 'admin';
}

/** ID del admin con sesión activa (0 = cuenta maestra de config.local.php). */
function adminActualId(): int {
    return (int)($_SESSION['_admin_id'] ?? 0);
}

/** Nombre del admin con sesión activa. */
function adminActualNombre(): string {
    $n = $_SESSION['_admin_nombre'] ?? '';
    return $n !== '' ? $n : 'Administrador';
}

/** Correo del admin con sesión activa. */
function adminActualEmail(): string {
    return $_SESSION['_admin_email'] ?? '';
}

/**
 * Login de módulo individual (legacy — se mantiene hasta migración BD).
 */
function authLogin(string $module): void {
    session_regenerate_id(true);
    $_SESSION['_csrf']          = bin2hex(random_bytes(32));
    $_SESSION['_auth']          = true;
    $_SESSION['_module']        = $module;
    $_SESSION['_last_activity'] = time();
}

function authLogout(string $redirectTo = ''): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    if ($redirectTo) {
        header('Location: ' . $redirectTo);
        exit;
    }
}

/**
 * Require autenticación para un módulo O sesión global admin.
 * Si hay sesión global activa, se considera autorizado para cualquier módulo.
 */
function requireAuth(string $module, string $loginUrl): void {
    if (empty($_SESSION['_auth'])) {
        header('Location: ' . $loginUrl);
        exit;
    }
    $sessionModule = $_SESSION['_module'] ?? '';
    // Sesión global admin: acceso a todo
    if ($sessionModule === 'global' && ($_SESSION['_role'] ?? '') === 'admin') {
        return;
    }
    // Sesión de módulo específico
    if ($sessionModule !== $module) {
        header('Location: ' . $loginUrl);
        exit;
    }
}

/**
 * Require método POST y token CSRF válido o aborta con 403.
 */
function requirePost(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrfVerify()) {
        http_response_code(403);
        exit('Petición inválida.');
    }
}
