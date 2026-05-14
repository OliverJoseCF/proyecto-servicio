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

// ── Nombres de base de datos ───────────────────────────────────────────────────
define('DB_BIBLIOTECA', 'biblioteca_escolar');
define('DB_CONVENIOS',  'convenios_db');
define('DB_HORARIOS',   'horarios_db');

// ── URL base (sin barra final) ─────────────────────────────────────────────────
define('PLATAFORMA_URL', '/plataforma');

// ── Hash de admin Biblioteca ───────────────────────────────────────────────────
// Genera con: php plataforma/modulos/biblioteca/tools/setup_password.php
define('BIBLIOTECA_ADMIN_USER', 'admin');
define('BIBLIOTECA_ADMIN_HASH', '$2y$12$REEMPLAZA_ESTE_VALOR');

// ── Hash de admin Horarios ─────────────────────────────────────────────────────
// Genera con: php plataforma/modulos/horarios/tools/setup_password.php
define('HORARIOS_ADMIN_EMAIL', 'admin@tecsj.edu.mx');
define('HORARIOS_ADMIN_HASH',  '$2y$12$REEMPLAZA_ESTE_VALOR');
