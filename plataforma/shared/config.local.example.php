<?php
/**
 * Overrides de configuración para PRODUCCIÓN.
 *
 * INSTRUCCIONES:
 *   1. Copia este archivo como  shared/config.local.php
 *   2. Rellena todos los valores (nunca los dejes vacíos en producción)
 *   3. NUNCA subas config.local.php al repositorio (.gitignore ya lo excluye)
 *
 * Este archivo se carga ANTES de los defaults de config.php, por lo que
 * las constantes aquí definidas tienen prioridad.
 */

// ── Base de datos ──────────────────────────────────────────────────────────────
define('DB_HOST',    '127.0.0.1');
define('DB_PORT',    3306);
define('DB_USER',    'tsjplat');          // usuario con mínimos privilegios (ver sql/setup.sql)
define('DB_PASS',    'TU_CLAVE_REAL');    // mínimo 16 caracteres, alfanumérico+símbolos
define('DB_CHARSET', 'utf8mb4');

// ── Base de datos unificada ────────────────────────────────────────────────────
define('DB_NAME', 'kiosko_tsj');

// ── URL base (sin barra final) ─────────────────────────────────────────────────
define('PLATAFORMA_URL', '/plataforma');

// ── Login admin global (toda la plataforma) ────────────────────────────────────
// Requerido para iniciar sesión en /plataforma/login.php
// Genera con:
//   php -r "echo password_hash('tu_contraseña', PASSWORD_BCRYPT, ['cost'=>12]);"
define('GLOBAL_ADMIN_EMAIL', 'admin@chapala.tecmm.edu.mx');
define('GLOBAL_ADMIN_HASH',  '$2y$12$REEMPLAZA_ESTE_VALOR');

// ── LEGACY (OPCIONAL) — logins por módulo ───────────────────────────────────────
// La administración usa el login global de arriba (plataforma/login.php). Las
// constantes de abajo solo aplican a los logins legacy de los módulos Biblioteca
// y Horarios; NO son necesarias para operar el panel unificado. Déjalas tal cual
// si no usas esos accesos directos.
//
// Hash de admin Biblioteca — genera con:
//   php plataforma/modulos/biblioteca/tools/setup_password.php
define('BIBLIOTECA_ADMIN_USER', 'admin');
define('BIBLIOTECA_ADMIN_HASH', '$2y$12$REEMPLAZA_ESTE_VALOR');

// Hash de admin Horarios — genera con:
//   php plataforma/modulos/horarios/tools/setup_password.php
define('HORARIOS_ADMIN_EMAIL', 'admin@tecsj.edu.mx');
define('HORARIOS_ADMIN_HASH',  '$2y$12$REEMPLAZA_ESTE_VALOR');
