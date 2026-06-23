<?php
/**
 * Importación / restauración de un respaldo .sql completo sobre la base kiosko_tsj.
 *
 * Recibe el archivo subido desde Admin → Respaldos → Importar y ejecuta todas las
 * sentencias del dump. REEMPLAZA por completo los datos actuales (cada tabla se
 * recrea con DROP TABLE + CREATE TABLE + INSERT del respaldo).
 *
 * Por seguridad:
 *   - Solo admin global con CSRF (vía _helper.php).
 *   - Exige que el admin escriba la palabra de confirmación.
 *   - Ignora sentencias peligrosas a nivel servidor (CREATE/DROP DATABASE, USE).
 *   - Desactiva FOREIGN_KEY_CHECKS durante la carga y los restaura al final.
 */
require_once __DIR__ . '/_helper.php';

if (str('accion') !== 'importar_respaldo') {
    jsonErr('Acción no válida');
}

// Confirmación explícita: el admin debe escribir REEMPLAZAR
if (mb_strtoupper(str('confirmacion')) !== 'REEMPLAZAR') {
    jsonErr('Debes escribir la palabra REEMPLAZAR para confirmar la importación.');
}

/* ── Validación del archivo ─────────────────────────────────────────────── */
if (empty($_FILES['archivo']) || ($_FILES['archivo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    $err = $_FILES['archivo']['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
        jsonErr('El archivo es demasiado grande para el servidor. Revisa upload_max_filesize en PHP.');
    }
    jsonErr('No se recibió ningún archivo. Selecciona un respaldo .sql.');
}

$file = $_FILES['archivo'];

if ($file['size'] <= 0) {
    jsonErr('El archivo está vacío.');
}
if ($file['size'] > 50 * 1024 * 1024) { // 50 MB
    jsonErr('El archivo supera el límite de 50 MB.');
}
if (!is_uploaded_file($file['tmp_name'])) {
    jsonErr('Subida no válida.');
}
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'sql') {
    jsonErr('El archivo debe tener extensión .sql');
}

$sql = file_get_contents($file['tmp_name']);
if ($sql === false || trim($sql) === '') {
    jsonErr('No se pudo leer el contenido del archivo.');
}

// Sanidad mínima: debe parecer un dump (estructura o datos)
if (!preg_match('/\b(CREATE\s+TABLE|INSERT\s+INTO)\b/i', $sql)) {
    jsonErr('El archivo no parece un respaldo válido (no contiene tablas ni datos).');
}

/* ── Partir el dump en sentencias individuales ──────────────────────────── */
$statements = splitSqlStatements($sql);
if (!$statements) {
    jsonErr('No se encontraron sentencias SQL ejecutables en el archivo.');
}

@set_time_limit(0);

$pdo = db();
$ejecutadas = 0;
$ignoradas  = 0;

try {
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    $pdo->exec("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");

    foreach ($statements as $i => $stmt) {
        // Bloquear sentencias que afectan a otras bases / al servidor
        if (preg_match('/^\s*(USE|CREATE\s+DATABASE|DROP\s+DATABASE|CREATE\s+SCHEMA|DROP\s+SCHEMA)\b/i', $stmt)) {
            $ignoradas++;
            continue;
        }
        // SET FOREIGN_KEY_CHECKS / SET NAMES / SET SQL_MODE del propio dump: ya gestionados
        if (preg_match('/^\s*SET\s+(FOREIGN_KEY_CHECKS|NAMES|SQL_MODE)\b/i', $stmt)) {
            // Reejecutamos SET NAMES por compatibilidad, el resto lo ignoramos
            if (preg_match('/^\s*SET\s+NAMES\b/i', $stmt)) {
                $pdo->exec($stmt);
            } else {
                $ignoradas++;
            }
            continue;
        }
        $pdo->exec($stmt);
        $ejecutadas++;
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
} catch (\Throwable $e) {
    // Reactivar checks aunque haya fallado
    try { $pdo->exec('SET FOREIGN_KEY_CHECKS = 1'); } catch (\Throwable $e2) {}
    error_log('[TSJ] Error importando respaldo: ' . $e->getMessage());
    jsonErr('Error al restaurar (sentencia ' . ($ejecutadas + 1) . '): ' . $e->getMessage()
        . '. La base puede haber quedado a medias; vuelve a importar un respaldo válido.', 500);
}

jsonOk(
    "Respaldo restaurado correctamente: $ejecutadas sentencias aplicadas.",
    ['ejecutadas' => $ejecutadas, 'ignoradas' => $ignoradas],
    "Importación de respaldo: $ejecutadas sentencias ejecutadas, $ignoradas ignoradas (archivo: " . mb_substr($file['name'], 0, 120) . ')'
);

/* ───────────────────────────────────────────────────────────────────────── */

/**
 * Divide un script SQL en sentencias individuales, respetando:
 *   - Cadenas entre comillas simples y dobles (con escapes \\ y '' duplicada)
 *   - Identificadores entre backticks
 *   - Comentarios de línea (-- y #) y de bloque (slash-asterisco)
 * El delimitador es siempre ';'.
 *
 * @return string[] Sentencias no vacías (sin el ';' final).
 */
function splitSqlStatements(string $sql): array
{
    $statements = [];
    $buffer     = '';
    $len        = strlen($sql);
    $i          = 0;

    $inSingle = false; // '
    $inDouble = false; // "
    $inBack   = false; // `

    while ($i < $len) {
        $ch   = $sql[$i];
        $next = $i + 1 < $len ? $sql[$i + 1] : '';

        if ($inSingle) {
            $buffer .= $ch;
            if ($ch === '\\') {                       // escape: copiar el siguiente char tal cual
                if ($next !== '') { $buffer .= $next; $i += 2; continue; }
            } elseif ($ch === "'") {
                if ($next === "'") { $buffer .= $next; $i += 2; continue; } // '' literal
                $inSingle = false;
            }
            $i++;
            continue;
        }
        if ($inDouble) {
            $buffer .= $ch;
            if ($ch === '\\') {
                if ($next !== '') { $buffer .= $next; $i += 2; continue; }
            } elseif ($ch === '"') {
                if ($next === '"') { $buffer .= $next; $i += 2; continue; }
                $inDouble = false;
            }
            $i++;
            continue;
        }
        if ($inBack) {
            $buffer .= $ch;
            if ($ch === '`') { $inBack = false; }
            $i++;
            continue;
        }

        // ── Fuera de cadenas ──
        // Comentario de línea: -- (seguido de espacio/fin) o #
        if ($ch === '-' && $next === '-') {
            $third = $i + 2 < $len ? $sql[$i + 2] : "\n";
            if ($third === ' ' || $third === "\t" || $third === "\n" || $third === "\r" || $i + 2 >= $len) {
                while ($i < $len && $sql[$i] !== "\n") { $i++; }
                continue;
            }
        }
        if ($ch === '#') {
            while ($i < $len && $sql[$i] !== "\n") { $i++; }
            continue;
        }
        // Comentario de bloque
        if ($ch === '/' && $next === '*') {
            $i += 2;
            while ($i < $len && !($sql[$i] === '*' && ($i + 1 < $len ? $sql[$i + 1] : '') === '/')) { $i++; }
            $i += 2;
            continue;
        }

        if ($ch === "'") { $inSingle = true; $buffer .= $ch; $i++; continue; }
        if ($ch === '"') { $inDouble = true; $buffer .= $ch; $i++; continue; }
        if ($ch === '`') { $inBack   = true; $buffer .= $ch; $i++; continue; }

        if ($ch === ';') {
            $trimmed = trim($buffer);
            if ($trimmed !== '') { $statements[] = $trimmed; }
            $buffer = '';
            $i++;
            continue;
        }

        $buffer .= $ch;
        $i++;
    }

    $trimmed = trim($buffer);
    if ($trimmed !== '') { $statements[] = $trimmed; }

    return $statements;
}
