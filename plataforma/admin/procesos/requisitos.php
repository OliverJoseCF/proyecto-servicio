<?php
require_once __DIR__ . '/_helper.php';

$accion = str('accion', 30);
$db     = db();

// ══ REQUISITOS ITEMS ═════════════════════════════════════════════
if ($accion === 'requisitos_guardar') {
    $tipo  = str('tipo', 20);
    $items = $_POST['items'] ?? [];
    if (!in_array($tipo, ['residencia','servicio_social'])) jsonErr('Tipo inválido');
    if (!is_array($items)) jsonErr('Datos inválidos');

    $db->prepare('DELETE FROM requisitos_items WHERE tipo=?')->execute([$tipo]);
    $stmt = $db->prepare('INSERT INTO requisitos_items (tipo,texto,orden) VALUES (?,?,?)');
    foreach ($items as $i => $texto) {
        $texto = mb_substr(trim($texto), 0, 500);
        if ($texto) $stmt->execute([$tipo, $texto, $i + 1]);
    }
    jsonOk('Requisitos guardados');
}

// ══ TIMELINE ═════════════════════════════════════════════════════
if ($accion === 'timeline_guardar') {
    $tipo  = str('tipo', 20);
    $fases = $_POST['fases'] ?? [];
    if (!in_array($tipo, ['residencia','servicio_social'])) jsonErr('Tipo inválido');
    if (!is_array($fases)) jsonErr('Datos inválidos');

    $db->prepare('DELETE FROM timeline_fases WHERE tipo=?')->execute([$tipo]);
    $stmt = $db->prepare('INSERT INTO timeline_fases (tipo,titulo,descripcion,tiempo_referencia,orden) VALUES (?,?,?,?,?)');
    foreach ($fases as $i => $f) {
        $titulo = mb_substr(trim($f['titulo'] ?? ''), 0, 150);
        $desc   = mb_substr(trim($f['descripcion'] ?? ''), 0, 1000);
        $tiempo = mb_substr(trim($f['tiempo'] ?? ''), 0, 100);
        if ($titulo) $stmt->execute([$tipo,$titulo,$desc,$tiempo,$i + 1]);
    }
    jsonOk('Fases del proceso guardadas');
}

// ══ DOCUMENTOS ═══════════════════════════════════════════════════
if ($accion === 'doc_agregar') {
    $tipo      = str('tipo', 20);
    $nombre    = str('nombre', 200);
    $url       = str('url', 1000);
    $tipo_arch = str('tipo_archivo', 30) ?: 'PDF';
    if (!in_array($tipo, ['residencia','servicio_social'])) jsonErr('Tipo inválido');
    if (!$nombre || !$url) jsonErr('Nombre y URL son requeridos');
    $stmtOrden = $db->prepare('SELECT COALESCE(MAX(orden),0)+1 FROM documentos_descargables WHERE tipo=?');
    $stmtOrden->execute([$tipo]);
    $orden = (int)$stmtOrden->fetchColumn();
    $db->prepare('INSERT INTO documentos_descargables (tipo,nombre,url,tipo_archivo,orden) VALUES (?,?,?,?,?)')
       ->execute([$tipo,$nombre,$url,$tipo_arch,$orden]);
    jsonOk('Documento agregado', ['id' => $db->lastInsertId()]);
}

if ($accion === 'doc_editar') {
    $id        = intVal('id');
    $nombre    = str('nombre', 200);
    $url       = str('url', 1000);
    $tipo_arch = str('tipo_archivo', 30) ?: 'PDF';
    if (!$id || !$nombre || !$url) jsonErr('Datos incompletos');
    $db->prepare('UPDATE documentos_descargables SET nombre=?,url=?,tipo_archivo=? WHERE id=?')
       ->execute([$nombre,$url,$tipo_arch,$id]);
    jsonOk('Documento actualizado');
}

if ($accion === 'doc_eliminar') {
    $id = intVal('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('DELETE FROM documentos_descargables WHERE id=?')->execute([$id]);
    jsonOk('Documento eliminado');
}

// ══ FAQ ══════════════════════════════════════════════════════════
if ($accion === 'faq_guardar') {
    $tipo  = str('tipo', 20);
    $faqs  = $_POST['faqs'] ?? [];
    if (!in_array($tipo, ['residencia','servicio_social'])) jsonErr('Tipo inválido');
    if (!is_array($faqs)) jsonErr('Datos inválidos');

    $db->prepare('DELETE FROM faq WHERE tipo=?')->execute([$tipo]);
    $stmt = $db->prepare('INSERT INTO faq (tipo,pregunta,respuesta,orden) VALUES (?,?,?,?)');
    foreach ($faqs as $i => $f) {
        $preg = mb_substr(trim($f['pregunta'] ?? ''), 0, 500);
        $resp = mb_substr(trim($f['respuesta'] ?? ''), 0, 2000);
        if ($preg && $resp) $stmt->execute([$tipo,$preg,$resp,$i + 1]);
    }
    jsonOk('FAQ guardadas');
}

jsonErr('Acción desconocida');
