<?php
/**
 * Configuración centralizada de la plataforma TSJ.
 *
 * DESARROLLO LOCAL: este archivo usa credenciales vacías de XAMPP por defecto.
 * PRODUCCIÓN: copia shared/config.local.example.php → shared/config.local.php
 *             y define allí las credenciales reales. Ese archivo está en .gitignore.
 *
 * El archivo config.local.php se carga al final de este script y puede sobreescribir
 * cualquier constante usando define() si no está ya definida (o redefinir con
 * runkit/uopz en entornos que lo permitan). El patrón aquí es: config.local.php
 * define sus propias constantes ANTES de que este archivo las defina, por eso se
 * carga PRIMERO si existe.
 */

// Cargar overrides locales/producción antes de los defaults
$_tsj_local = __DIR__ . '/config.local.php';
if (file_exists($_tsj_local)) {
    require_once $_tsj_local;
}
unset($_tsj_local);

// Defaults de desarrollo (XAMPP). En producción se sobreescriben vía config.local.php
if (!defined('DB_HOST'))    define('DB_HOST',    'localhost');
if (!defined('DB_PORT'))    define('DB_PORT',    3306);
if (!defined('DB_USER'))    define('DB_USER',    'root');
if (!defined('DB_PASS'))    define('DB_PASS',    '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// URL base de la plataforma (sin barra final) — ajusta si cambias el alias de Apache
if (!defined('PLATAFORMA_URL')) define('PLATAFORMA_URL', '/plataforma');

// Nombres de BD por módulo
if (!defined('DB_BIBLIOTECA')) define('DB_BIBLIOTECA', 'biblioteca_escolar');
if (!defined('DB_CONVENIOS'))  define('DB_CONVENIOS',  'convenios_db');
if (!defined('DB_HORARIOS'))   define('DB_HORARIOS',   'horarios_db');

// ── Login global (único para toda la plataforma) ─────────────────────────────
// Sobreescribir en config.local.php con un hash real generado por:
//   php -r "echo password_hash('tu_contraseña', PASSWORD_BCRYPT, ['cost'=>12]);"
if (!defined('GLOBAL_ADMIN_EMAIL')) define('GLOBAL_ADMIN_EMAIL', 'admin@chapala.tecmm.edu.mx');
if (!defined('GLOBAL_ADMIN_HASH'))  define('GLOBAL_ADMIN_HASH',  '');   // vacío → sin login hasta configurar

// ── Hashes de admin por módulo (legacy — se mantendrán hasta migración BD) ───
if (!defined('BIBLIOTECA_ADMIN_USER')) define('BIBLIOTECA_ADMIN_USER', 'admin');
if (!defined('BIBLIOTECA_ADMIN_HASH')) define('BIBLIOTECA_ADMIN_HASH', '');
if (!defined('HORARIOS_ADMIN_EMAIL'))  define('HORARIOS_ADMIN_EMAIL',  'admin@tecsj.edu.mx');
if (!defined('HORARIOS_ADMIN_HASH'))   define('HORARIOS_ADMIN_HASH',   '');

/**
 * Devuelve un objeto PDO listo para usar.
 * Usado por: Horarios, Convenios.
 */
function getPDO(string $dbName): PDO {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, $dbName, DB_CHARSET
    );
    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}

/**
 * Devuelve una conexión mysqli lista para usar.
 * Usado por: Biblioteca.
 */
function getMysqli(string $dbName): mysqli {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_init();
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    $conn->real_connect(DB_HOST, DB_USER, DB_PASS, $dbName, DB_PORT);
    $conn->set_charset(DB_CHARSET);
    return $conn;
}
