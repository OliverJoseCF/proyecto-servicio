<?php
/**
 * Inicialización segura de sesión con hardening y idle-timeout.
 * Incluir este archivo (require_once) en lugar de llamar session_start() directamente.
 * Debe incluirse ANTES de cualquier output HTML.
 */
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

// Idle-timeout: si la sesión autenticada lleva más de 1 hora sin actividad, se invalida.
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    $idleTimeout = 3600;

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $idleTimeout) {
        // Invalidar sesión — la comprobación de auth a nivel de página redirigirá al login.
        unset($_SESSION['authenticated'], $_SESSION['user'], $_SESSION['last_activity'], $_SESSION['csrf_token']);
    } else {
        $_SESSION['last_activity'] = time();
    }
}
