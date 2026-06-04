<?php
require_once __DIR__ . '/_helper.php';

$accion = str('accion', 30);
$db     = db();

// ══ AVISOS ═══════════════════════════════════════════════════════
if ($accion === 'aviso_agregar') {
    $titulo = str('titulo', 200);
    $desc   = str('descripcion', 2000);
    $fecha  = str('fecha', 10);
    if (!$titulo) jsonErr('El título es requerido');
    if (!$fecha)  $fecha = date('Y-m-d');
    $db->prepare('INSERT INTO avisos (titulo,descripcion,fecha) VALUES (?,?,?)')
       ->execute([$titulo, $desc, $fecha]);
    jsonOk('Aviso agregado', ['id' => $db->lastInsertId()]);
}

if ($accion === 'aviso_editar') {
    $id     = intVal('id');
    $titulo = str('titulo', 200);
    $desc   = str('descripcion', 2000);
    $fecha  = str('fecha', 10);
    if (!$id || !$titulo) jsonErr('Datos incompletos');
    $db->prepare('UPDATE avisos SET titulo=?,descripcion=?,fecha=? WHERE id=?')
       ->execute([$titulo, $desc, $fecha, $id]);
    jsonOk('Aviso actualizado');
}

if ($accion === 'aviso_eliminar') {
    $id = intVal('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('DELETE FROM avisos WHERE id=?')->execute([$id]);
    jsonOk('Aviso eliminado');
}

// ══ FAQ GENERAL ══════════════════════════════════════════════════
if ($accion === 'faq_general_guardar') {
    $faqs = $_POST['faqs'] ?? [];
    if (!is_array($faqs)) jsonErr('Datos inválidos');
    $db->prepare('DELETE FROM faq WHERE tipo="general"')->execute();
    $stmt = $db->prepare('INSERT INTO faq (tipo,pregunta,respuesta,orden) VALUES ("general",?,?,?)');
    foreach ($faqs as $i => $f) {
        $preg = mb_substr(trim($f['pregunta'] ?? ''), 0, 500);
        $resp = mb_substr(trim($f['respuesta'] ?? ''), 0, 2000);
        if ($preg && $resp) $stmt->execute([$preg, $resp, $i + 1]);
    }
    jsonOk('FAQ general guardada');
}

// ══ CARRUSEL ═════════════════════════════════════════════════════
if ($accion === 'carrusel_agregar') {
    $url       = str('url', 1000);
    $titulo    = str('titulo', 200);
    $subtitulo = str('subtitulo', 300);
    if (!$url) jsonErr('La URL de la imagen es requerida');
    $orden = (int)$db->query('SELECT COALESCE(MAX(orden),0)+1 FROM carrusel_fotos')->fetchColumn();
    $db->prepare('INSERT INTO carrusel_fotos (url,titulo,subtitulo,orden) VALUES (?,?,?,?)')
       ->execute([$url, $titulo, $subtitulo, $orden]);
    jsonOk('Imagen agregada al carrusel', ['id' => $db->lastInsertId()]);
}

if ($accion === 'carrusel_editar') {
    $id        = intVal('id');
    $url       = str('url', 1000);
    $titulo    = str('titulo', 200);
    $subtitulo = str('subtitulo', 300);
    if (!$id || !$url) jsonErr('Datos incompletos');
    $db->prepare('UPDATE carrusel_fotos SET url=?,titulo=?,subtitulo=? WHERE id=?')
       ->execute([$url, $titulo, $subtitulo, $id]);
    jsonOk('Imagen actualizada');
}

if ($accion === 'carrusel_eliminar') {
    $id = intVal('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('DELETE FROM carrusel_fotos WHERE id=?')->execute([$id]);
    jsonOk('Imagen eliminada del carrusel');
}

jsonErr('Acción desconocida');
