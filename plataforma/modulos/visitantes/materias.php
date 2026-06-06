<?php
/**
 * Página dinámica de plan de estudios.
 * Recibe ?carrera=CLAVE y carga la carrera desde BD.
 * Reemplaza los archivos estáticos por carrera (MateriasSistemas.php, etc.)
 * que siguen funcionando para retrocompatibilidad con el menú del header.
 */
require_once __DIR__ . '/../../shared/config.php';

$claveGet = strtoupper(trim($_GET['carrera'] ?? ''));

// Validar que la clave exista en BD y leer su color
try {
    $db      = getPDO(DB_NAME);
    $stmt    = $db->prepare('SELECT id, clave, nombre, color FROM carreras WHERE clave=? AND activo=1 LIMIT 1');
    $stmt->execute([$claveGet]);
    $carrera = $stmt->fetch();
} catch (\Throwable $e) {
    $carrera = null;
}

if (!$carrera) {
    header('Location: ofertaAcademica.php');
    exit;
}

$_carrera_clave  = $carrera['clave'];
$_carrera_nombre = $carrera['nombre'];
$_color_acento   = $carrera['color'] ?: '#33179c';

require_once __DIR__ . '/_materias_template.php';
