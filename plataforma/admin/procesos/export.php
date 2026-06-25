<?php
/**
 * Exportación CSV para el panel admin (GET ?tipo=prestamos|solicitudes|convenios).
 * Solo lectura — requiere sesión de admin global. Incluye BOM UTF-8 para Excel.
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

$tipo = $_GET['tipo'] ?? '';

$consultas = [
    'prestamos' => [
        'titulos' => ['ID', 'Libro', 'Código libro', 'Estudiante', 'No. control', 'Carrera', 'Tipo', 'Fecha préstamo', 'Fecha devolución', 'Devuelto', 'Fecha devuelto'],
        'sql'     => 'SELECT p.id, l.nombre, l.codigo, p.estudiante_nombre, p.estudiante_control, p.carrera, p.tipo,
                             p.fecha_prestamo, p.fecha_devolucion, IF(p.devuelto, "Sí", "No"), p.fecha_devuelto
                      FROM prestamos p JOIN libros l ON p.libro_id = l.id
                      ORDER BY p.fecha_prestamo DESC',
    ],
    'solicitudes' => [
        'titulos' => ['ID', 'Libro', 'Estudiante', 'No. control', 'Carrera', 'Tipo', 'Estado', 'Fecha solicitud'],
        'sql'     => 'SELECT s.id, l.nombre, s.estudiante_nombre, s.estudiante_control, s.carrera, s.tipo, s.estado, s.created_at
                      FROM solicitudes_biblioteca s JOIN libros l ON s.libro_id = l.id
                      ORDER BY s.created_at DESC',
    ],
    'bitacora' => [
        'titulos' => ['Fecha y hora', 'Realizó', 'Módulo', 'Acción', 'Detalle'],
        'sql'     => 'SELECT created_at, COALESCE(NULLIF(admin_nombre,""),"Cuenta maestra"), modulo, accion, COALESCE(detalle,"")
                      FROM admin_log ORDER BY id DESC',
    ],
    'convenios' => [
        'titulos' => ['ID', 'Empresa', 'Tipo de convenio', 'Sector', 'Carreras', 'Contacto', 'Correo', 'Teléfono', 'Vencimiento', 'Activo'],
        'sql'     => 'SELECT cv.id, cv.nombre, cv.tipo_convenio, cv.sector,
                             COALESCE(GROUP_CONCAT(DISTINCT c.clave ORDER BY c.clave SEPARATOR ", "), "Todas"),
                             cv.nombre_contacto, cv.correo_contacto, cv.telefono_contacto, cv.vencimiento, IF(cv.activo, "Sí", "No")
                      FROM convenios cv
                      LEFT JOIN convenio_carreras cc ON cc.convenio_id = cv.id
                      LEFT JOIN carreras c ON c.id = cc.carrera_id
                      GROUP BY cv.id
                      ORDER BY cv.nombre',
    ],
];

if (!isset($consultas[$tipo])) {
    http_response_code(400);
    exit('Tipo de exportación inválido');
}

try {
    $db   = getPDO(DB_NAME);
    $rows = $db->query($consultas[$tipo]['sql'])->fetchAll(PDO::FETCH_NUM);
} catch (\Throwable $e) {
    http_response_code(500);
    exit('Error de base de datos');
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $tipo . '_' . date('Y-m-d') . '.csv"');
header('Cache-Control: no-store');

/**
 * Neutraliza inyección de fórmulas CSV: Excel/LibreOffice ejecutan celdas que
 * empiezan con = + - @ (o tab/retorno). Se antepone un apóstrofo para forzar texto.
 */
$csvCelda = static function ($v): string {
    $v = (string) $v;
    if ($v !== '' && preg_match('/^[=+\-@\t\r]/', $v)) {
        return "'" . $v;
    }
    return $v;
};

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM para que Excel detecte UTF-8
fputcsv($out, $consultas[$tipo]['titulos']);
foreach ($rows as $row) {
    fputcsv($out, array_map($csvCelda, $row));
}
fclose($out);
