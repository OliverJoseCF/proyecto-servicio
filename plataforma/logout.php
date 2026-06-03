<?php
/**
 * Logout central — cierra la sesión global y redirige al portal.
 * Solo acepta POST con CSRF para evitar logout por imagen/enlace externo.
 */
require_once __DIR__ . '/shared/lib/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfVerify()) {
    authLogout(PLATAFORMA_URL . '/');
}

// GET o CSRF inválido: redirigir sin cerrar sesión
header('Location: ' . PLATAFORMA_URL . '/');
exit;
