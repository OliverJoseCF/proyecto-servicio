<?php
/**
 * Cabeceras de seguridad HTTP globales.
 * Incluido automáticamente por shared/header.php.
 */
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

$csp = implode('; ', [
    "default-src 'self'",
    "script-src 'self' https://cdn.jsdelivr.net https://code.jquery.com https://cdn.datatables.net https://cdnjs.cloudflare.com",
    "style-src 'self' https://fonts.googleapis.com https://cdn.datatables.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com 'unsafe-inline'",
    "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
    "img-src 'self' data: blob:",
    "connect-src 'self'",
    "frame-src 'self' https://www.google.com",
    "object-src 'none'",
    "base-uri 'self'",
    "form-action 'self'",
]);
header("Content-Security-Policy: $csp");
