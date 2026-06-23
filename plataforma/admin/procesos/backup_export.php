<?php
/**
 * Exportación de respaldo COMPLETO de la base kiosko_tsj (estructura + datos).
 *
 * Genera un archivo .sql estándar (compatible con phpMyAdmin) y lo entrega como
 * descarga. Es un "mysqldump en PHP puro": no depende de que el binario mysqldump
 * esté disponible en el servidor, por lo que funciona en cualquier hosting.
 *
 * Requiere sesión de admin global + POST con CSRF (formulario normal, no AJAX:
 * el navegador descarga el archivo y permanece en la página).
 */
ob_start();
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

require_once dirname(__DIR__, 2) . '/shared/lib/auth.php';
require_once dirname(__DIR__, 2) . '/shared/config.php';

ob_end_clean();

if (!isGlobalAdmin()) {
    http_response_code(403);
    exit('No autorizado');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrfVerify()) {
    http_response_code(403);
    exit('Petición inválida.');
}

try {
    $db = getPDO(DB_NAME);
} catch (\Throwable $e) {
    http_response_code(500);
    exit('No se pudo conectar a la base de datos.');
}

// Separar tablas base de vistas: las vistas no llevan datos y deben recrearse
// DESPUÉS de las tablas de las que dependen.
$baseTables = [];
$views      = [];
foreach ($db->query('SHOW FULL TABLES')->fetchAll(PDO::FETCH_NUM) as $r) {
    if (strtoupper($r[1]) === 'VIEW') {
        $views[] = $r[0];
    } else {
        $baseTables[] = $r[0];
    }
}
if (!$baseTables && !$views) {
    http_response_code(500);
    exit('La base de datos no tiene tablas que exportar.');
}
$totalObjetos = count($baseTables) + count($views);

// Sin límite de tiempo: bases grandes pueden tardar
@set_time_limit(0);

$filename = 'kiosko_tsj_respaldo_' . date('Y-m-d_His') . '.sql';

header('Content-Type: application/sql; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-store');
header('Pragma: no-cache');

$out = fopen('php://output', 'w');

/* ── Cabecera del dump ──────────────────────────────────────────────────── */
fwrite($out, "-- Respaldo de la plataforma TSJ Campus Chapala\n");
fwrite($out, "-- Base de datos: " . DB_NAME . "\n");
fwrite($out, "-- Generado: " . date('Y-m-d H:i:s') . "\n");
fwrite($out, "-- Por: " . (function_exists('adminActualNombre') ? adminActualNombre() : 'Administrador') . "\n");
fwrite($out, "--\n");
fwrite($out, "-- Para restaurar: usa el panel Admin → Respaldos → Importar,\n");
fwrite($out, "-- o impórtalo en phpMyAdmin sobre la base kiosko_tsj.\n");
fwrite($out, "-- ------------------------------------------------------------\n\n");

fwrite($out, "SET NAMES utf8mb4;\n");
fwrite($out, "SET FOREIGN_KEY_CHECKS = 0;\n");
fwrite($out, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n");

foreach ($baseTables as $table) {
    // ── Estructura ──────────────────────────────────────────────────────
    fwrite($out, "-- ------------------------------------------------------------\n");
    fwrite($out, "-- Tabla: `$table`\n");
    fwrite($out, "-- ------------------------------------------------------------\n\n");
    fwrite($out, "DROP TABLE IF EXISTS `$table`;\n");

    $createRow = $db->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
    fwrite($out, ($createRow[1] ?? '') . ";\n\n");

    // ── Datos ───────────────────────────────────────────────────────────
    $count = (int) $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
    if ($count === 0) {
        fwrite($out, "-- (sin datos)\n\n");
        continue;
    }

    // Streaming fila por fila para no agotar memoria
    $stmt = $db->query("SELECT * FROM `$table`");
    $rowsInBatch = 0;
    $first = true;
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $vals = [];
        foreach ($row as $v) {
            if ($v === null) {
                $vals[] = 'NULL';
            } elseif (is_int($v) || is_float($v)) {
                $vals[] = (string) $v;
            } else {
                $vals[] = $db->quote((string) $v);
            }
        }
        if ($first) {
            fwrite($out, "INSERT INTO `$table` VALUES\n");
            $first = false;
        }
        // Lotes de 100 filas por sentencia INSERT
        $prefix = ($rowsInBatch === 0) ? '' : ",\n";
        fwrite($out, $prefix . '(' . implode(',', $vals) . ')');
        $rowsInBatch++;
        if ($rowsInBatch >= 100) {
            fwrite($out, ";\n");
            $rowsInBatch = 0;
            $first = true; // forzará un nuevo "INSERT INTO ... VALUES" en la próxima fila
        }
    }
    if ($rowsInBatch > 0) {
        fwrite($out, ";\n");
    }
    fwrite($out, "\n");
}

// ── Vistas (después de las tablas base de las que dependen) ──────────────────
foreach ($views as $view) {
    fwrite($out, "-- ------------------------------------------------------------\n");
    fwrite($out, "-- Vista: `$view`\n");
    fwrite($out, "-- ------------------------------------------------------------\n\n");
    fwrite($out, "DROP VIEW IF EXISTS `$view`;\n");

    $createRow = $db->query("SHOW CREATE VIEW `$view`")->fetch(PDO::FETCH_NUM);
    $createSql = $createRow[1] ?? '';
    // Quitar la cláusula DEFINER=`usuario`@`host` para que la vista se cree con el
    // usuario que importa (en otro servidor ese usuario puede no existir).
    $createSql = preg_replace('/\sDEFINER=`[^`]*`@`[^`]*`/', '', $createSql);
    fwrite($out, $createSql . ";\n\n");
}

fwrite($out, "SET FOREIGN_KEY_CHECKS = 1;\n");
fclose($out);

/* Bitácora (en su propia petición; no afecta la descarga ya enviada) */
try {
    $adminId  = function_exists('adminActualId')     ? adminActualId()     : 0;
    $adminNom = function_exists('adminActualNombre') ? adminActualNombre() : 'Administrador';
    $db->prepare('INSERT INTO admin_log (modulo, accion, detalle, admin_id, admin_nombre) VALUES (?,?,?,?,?)')
       ->execute(['respaldos', 'exportar_respaldo', 'Respaldo completo descargado (' . $totalObjetos . ' objetos: ' . count($baseTables) . ' tablas, ' . count($views) . ' vistas)', $adminId ?: null, $adminNom]);
} catch (\Throwable $e) {
    // sin bitácora disponible — no afecta el respaldo
}
