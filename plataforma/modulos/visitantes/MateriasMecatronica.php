<?php
// Redirige al template dinámico que lee nombre, color y materias desde BD
require_once __DIR__ . '/../../shared/config.php';
$_clave = 'IM';
try {
    $db = getPDO(DB_NAME);
    $r  = $db->prepare('SELECT clave, nombre, color FROM carreras WHERE clave=? AND activo=1 LIMIT 1');
    $r->execute([$_clave]);
    $_c = $r->fetch();
} catch (\Throwable $e) { $_c = null; }
$_carrera_clave  = $_c['clave']  ?? $_clave;
$_carrera_nombre = $_c['nombre'] ?? '';
$_color_acento   = $_c['color']  ?? '#33179c';
$tsj_module    = 'visitantes';
$tsj_title     = 'Materias — ' . $_carrera_nombre;
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/_materias_template.php';
