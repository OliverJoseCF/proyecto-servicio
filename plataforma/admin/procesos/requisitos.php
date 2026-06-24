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

    // Validar/normalizar ANTES de borrar, para no vaciar la lista ante un POST vacío
    $validos = [];
    foreach ($items as $texto) {
        $texto = mb_substr(trim((string)$texto), 0, 500);
        if ($texto !== '') $validos[] = $texto;
    }
    if (!$validos) jsonErr('Debe quedar al menos un requisito. No se guardaron cambios.');

    $db->prepare('DELETE FROM requisitos_items WHERE tipo=?')->execute([$tipo]);
    $stmt = $db->prepare('INSERT INTO requisitos_items (tipo,texto,orden) VALUES (?,?,?)');
    foreach ($validos as $i => $texto) { $stmt->execute([$tipo, $texto, $i + 1]); }
    jsonOk('Requisitos guardados', [], "Requisitos guardados ($tipo): " . count($validos) . ' ítem(s)');
}

// ══ TIMELINE ═════════════════════════════════════════════════════
if ($accion === 'timeline_guardar') {
    $tipo  = str('tipo', 20);
    $fases = $_POST['fases'] ?? [];
    if (!in_array($tipo, ['residencia','servicio_social'])) jsonErr('Tipo inválido');
    if (!is_array($fases)) jsonErr('Datos inválidos');

    // Validar/normalizar ANTES de borrar, para no vaciar el timeline ante un POST vacío
    $validas = [];
    foreach ($fases as $f) {
        $titulo = mb_substr(trim($f['titulo'] ?? ''), 0, 150);
        $desc   = mb_substr(trim($f['descripcion'] ?? ''), 0, 1000);
        $tiempo = mb_substr(trim($f['tiempo'] ?? ''), 0, 100);
        if ($titulo !== '') $validas[] = [$titulo, $desc, $tiempo];
    }
    if (!$validas) jsonErr('Debe quedar al menos una fase. No se guardaron cambios.');

    $db->prepare('DELETE FROM timeline_fases WHERE tipo=?')->execute([$tipo]);
    $stmt = $db->prepare('INSERT INTO timeline_fases (tipo,titulo,descripcion,tiempo_referencia,orden) VALUES (?,?,?,?,?)');
    foreach ($validas as $i => [$titulo, $desc, $tiempo]) { $stmt->execute([$tipo, $titulo, $desc, $tiempo, $i + 1]); }
    jsonOk('Fases del proceso guardadas', [], "Fases del proceso guardadas ($tipo): " . count($validas) . ' fase(s)');
}

// ══ DOCUMENTOS ═══════════════════════════════════════════════════

// Sube doc_archivo si viene; devuelve la URL pública o null si no hay archivo.
function subirDocumento(string $tipo): ?string {
    if (!isset($_FILES['doc_archivo']) || $_FILES['doc_archivo']['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($_FILES['doc_archivo']['error'] !== UPLOAD_ERR_OK) {
        jsonErr('Error al recibir el archivo (código ' . (int)$_FILES['doc_archivo']['error'] . ')');
    }
    $ext = strtolower(pathinfo($_FILES['doc_archivo']['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') jsonErr('Solo se permiten archivos PDF.');
    if ($_FILES['doc_archivo']['size'] > 10 * 1024 * 1024) jsonErr('El PDF supera 10 MB.');
    $dir = dirname(__DIR__, 2) . '/modulos/requisitos/docs/' . $tipo . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $fname = bin2hex(random_bytes(8)) . '.pdf';
    if (!move_uploaded_file($_FILES['doc_archivo']['tmp_name'], $dir . $fname)) {
        jsonErr('No se pudo guardar el archivo. Verifica los permisos del servidor.');
    }
    $base = defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma';
    return $base . '/modulos/requisitos/docs/' . $tipo . '/' . $fname;
}

if ($accion === 'doc_agregar') {
    $tipo      = str('tipo', 20);
    $nombre    = str('nombre', 200);
    if (!in_array($tipo, ['residencia','servicio_social'])) jsonErr('Tipo inválido');
    if (!$nombre) jsonErr('El nombre es requerido');

    $urlSubida = subirDocumento($tipo);
    if ($urlSubida !== null) {
        $url       = $urlSubida;
        $tipo_arch = 'PDF';
    } else {
        $url = str('url', 1000);
        if (!$url) jsonErr('Debes subir un PDF o proporcionar una URL');
        $tipo_arch = str('tipo_archivo', 30) ?: 'Google Drive';
    }

    $stmtOrden = $db->prepare('SELECT COALESCE(MAX(orden),0)+1 FROM documentos_descargables WHERE tipo=?');
    $stmtOrden->execute([$tipo]);
    $orden = (int)$stmtOrden->fetchColumn();
    $db->prepare('INSERT INTO documentos_descargables (tipo,nombre,url,tipo_archivo,orden) VALUES (?,?,?,?,?)')
       ->execute([$tipo,$nombre,$url,$tipo_arch,$orden]);
    $newId = $db->lastInsertId();
    jsonOk('Documento agregado', ['id' => $newId], "Documento agregado ($tipo): $nombre (ID $newId)");
}

if ($accion === 'doc_editar') {
    $id    = postInt('id');
    $tipo  = str('tipo', 20);
    $nombre = str('nombre', 200);
    if (!$id || !$nombre) jsonErr('Datos incompletos');
    if (!in_array($tipo, ['residencia','servicio_social'])) jsonErr('Tipo inválido');

    $urlSubida = subirDocumento($tipo);
    if ($urlSubida !== null) {
        $url       = $urlSubida;
        $tipo_arch = 'PDF';
    } else {
        $url = str('url', 1000);
        if (!$url) {
            // Sin archivo nuevo ni URL nueva: mantener la URL actual
            $stmtUrl = $db->prepare('SELECT url, tipo_archivo FROM documentos_descargables WHERE id=?');
            $stmtUrl->execute([$id]);
            $row       = $stmtUrl->fetch();
            $url       = $row['url'] ?? '';
            $tipo_arch = $row['tipo_archivo'] ?? 'PDF';
        } else {
            $tipo_arch = str('tipo_archivo', 30) ?: 'Google Drive';
        }
    }
    if (!$url) jsonErr('El documento no tiene URL. Sube un PDF o proporciona una URL');

    $db->prepare('UPDATE documentos_descargables SET nombre=?,url=?,tipo_archivo=? WHERE id=?')
       ->execute([$nombre,$url,$tipo_arch,$id]);
    jsonOk('Documento actualizado', [], "Documento actualizado: $nombre (ID $id)");
}

if ($accion === 'doc_eliminar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $sN = $db->prepare('SELECT nombre FROM documentos_descargables WHERE id=?');
    $sN->execute([$id]);
    $nombre = $sN->fetchColumn() ?: '?';
    $db->prepare('DELETE FROM documentos_descargables WHERE id=?')->execute([$id]);
    jsonOk('Documento eliminado', [], "Documento eliminado: $nombre (ID $id)");
}

// ══ FAQ ══════════════════════════════════════════════════════════
if ($accion === 'faq_guardar') {
    $tipo  = str('tipo', 20);
    $faqs  = $_POST['faqs'] ?? [];
    if (!in_array($tipo, ['residencia','servicio_social'])) jsonErr('Tipo inválido');
    if (!is_array($faqs)) jsonErr('Datos inválidos');

    // Validar antes de borrar para no perder datos por preguntas incompletas
    $validas = [];
    foreach ($faqs as $f) {
        $preg = mb_substr(trim($f['pregunta'] ?? ''), 0, 500);
        $resp = mb_substr(trim($f['respuesta'] ?? ''), 0, 2000);
        if ($preg === '') jsonErr('Todas las preguntas deben tener texto');
        if ($resp === '') jsonErr('La pregunta "' . $preg . '" no tiene respuesta');
        $validas[] = [$preg, $resp];
    }

    $db->prepare('DELETE FROM faq WHERE tipo=?')->execute([$tipo]);
    $stmt = $db->prepare('INSERT INTO faq (tipo,pregunta,respuesta,orden) VALUES (?,?,?,?)');
    foreach ($validas as $i => [$preg, $resp]) {
        $stmt->execute([$tipo, $preg, $resp, $i + 1]);
    }
    jsonOk('FAQ guardadas', [], 'FAQ guardadas (' . $tipo . '): ' . count($validas) . ' pregunta(s)');
}

jsonErr('Acción desconocida');
