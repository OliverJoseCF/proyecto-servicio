<?php
/**
 * Configuración de la aplicación — PLANTILLA.
 *
 * INSTRUCCIONES:
 *   1. Copia este archivo como  src/config.php
 *   2. Rellena los valores reales
 *   3. NUNCA subas src/config.php al repositorio (.gitignore ya lo excluye)
 */

// ── Base de datos ─────────────────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');       // Mínimo 16 caracteres, alfanumérico+símbolos
define('DB_NAME', 'convenios_db');

// ── Autenticación admin ───────────────────────────────────────────────────────
// Genera el hash con:  php tools/setup_password.php
define('ADMIN_EMAIL',         'admin@tecsj.edu.mx');
define('ADMIN_PASSWORD_HASH', '$2y$12$REEMPLAZA_ESTE_VALOR_CON_EL_HASH_GENERADO');

// ── Correo de notificaciones ──────────────────────────────────────────────────
define('VINCULACION_EMAIL', 'vinculacion@tecsj.edu.mx');

// ── Entorno ───────────────────────────────────────────────────────────────────
// En producción asegúrate de que php.ini tenga:
//   display_errors = Off
//   log_errors     = On
//   error_log      = /ruta/a/logs/php_error.log
