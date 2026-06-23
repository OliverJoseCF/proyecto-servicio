<?php
/**
 * Cabeceras de seguridad HTTP.
 * Incluir con require_once antes de cualquier output HTML.
 */
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Content-Security-Policy
// Nota: 'unsafe-inline' en style-src es necesario mientras existan bloques <style> inline.
//       Para eliminarlo, mover todos los estilos inline a archivos .css externos.
// Para añadir SRI a los recursos CDN, generarlos en: https://www.srihash.org/
$csp = implode('; ', [
    "default-src 'self'",
    "script-src 'self' https://cdn.jsdelivr.net https://code.jquery.com https://cdn.datatables.net https://cdnjs.cloudflare.com",
    "style-src 'self' https://cdn.datatables.net https://cdn.jsdelivr.net 'unsafe-inline'",
    "font-src 'self'",
    "img-src 'self' data: blob:",
    "connect-src 'self' https://cdn.datatables.net",
    "frame-src 'none'",
    "object-src 'none'",
    "base-uri 'self'",
    "form-action 'self'",
]);
header("Content-Security-Policy: $csp");
