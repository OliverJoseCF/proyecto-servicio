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
    $id     = postInt('id');
    $titulo = str('titulo', 200);
    $desc   = str('descripcion', 2000);
    $fecha  = str('fecha', 10);
    if (!$id || !$titulo) jsonErr('Datos incompletos');
    $db->prepare('UPDATE avisos SET titulo=?,descripcion=?,fecha=? WHERE id=?')
       ->execute([$titulo, $desc, $fecha, $id]);
    jsonOk('Aviso actualizado');
}

if ($accion === 'aviso_eliminar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('DELETE FROM avisos WHERE id=?')->execute([$id]);
    jsonOk('Aviso eliminado');
}

if ($accion === 'aviso_toggle') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $stmt = $db->prepare('UPDATE avisos SET activo = 1 - activo WHERE id=?');
    $stmt->execute([$id]);
    $s = $db->prepare('SELECT activo FROM avisos WHERE id=?');
    $s->execute([$id]);
    $activo = (int)$s->fetchColumn();
    jsonOk($activo ? 'Aviso visible' : 'Aviso oculto', ['activo' => $activo]);
}

// ══ FAQ GENERAL ══════════════════════════════════════════════════
if ($accion === 'faq_general_guardar') {
    $faqs = $_POST['faqs'] ?? [];
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

    $db->prepare('DELETE FROM faq WHERE tipo="general"')->execute();
    $stmt = $db->prepare('INSERT INTO faq (tipo,pregunta,respuesta,orden) VALUES ("general",?,?,?)');
    foreach ($validas as $i => [$preg, $resp]) {
        $stmt->execute([$preg, $resp, $i + 1]);
    }
    jsonOk('FAQ general guardada');
}

// ══ CARRUSEL ═════════════════════════════════════════════════════

// Helper: procesa subida de archivo de imagen del carrusel
// Devuelve la URL pública guardada, o null si no se subió archivo, o lanza jsonErr
function procesarArchivoCarrusel(): ?string {
    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // no se subió archivo — usar URL de texto
    }
    if ($_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        jsonErr('Error al recibir el archivo (código ' . (int)$_FILES['archivo']['error'] . ')');
    }

    $ext   = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
    $allow = ['jpg','jpeg','png','webp','gif','svg'];
    if (!in_array($ext, $allow, true)) {
        jsonErr('Tipo de archivo no permitido. Usa JPG, PNG, WEBP, GIF o SVG.');
    }
    if ($_FILES['archivo']['size'] > 5 * 1024 * 1024) {
        jsonErr('La imagen supera el límite de 5 MB.');
    }
    // Verificar que sea imagen real (evita subida de PHP disfrazado)
    if (!in_array($ext, ['svg']) && !getimagesize($_FILES['archivo']['tmp_name'])) {
        jsonErr('El archivo no es una imagen válida.');
    }

    $dir   = dirname(__DIR__, 2) . '/shared/assets/img/carrusel/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $fname = 'car_' . bin2hex(random_bytes(8)) . '.' . $ext;
    move_uploaded_file($_FILES['archivo']['tmp_name'], $dir . $fname);

    $base = defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma';
    return $base . '/shared/assets/img/carrusel/' . $fname;
}

if ($accion === 'carrusel_agregar') {
    $titulo    = str('titulo', 200);
    $subtitulo = str('subtitulo', 300);

    $urlArchivo = procesarArchivoCarrusel();
    $urlTexto   = str('url', 1000);
    $url        = $urlArchivo ?? $urlTexto;

    if (!$url) jsonErr('Debes subir una imagen o proporcionar una URL.');

    $orden = (int)$db->query('SELECT COALESCE(MAX(orden),0)+1 FROM carrusel_fotos')->fetchColumn();
    $db->prepare('INSERT INTO carrusel_fotos (url,titulo,subtitulo,orden) VALUES (?,?,?,?)')
       ->execute([$url, $titulo, $subtitulo, $orden]);
    jsonOk('Imagen agregada al carrusel', ['id' => $db->lastInsertId()]);
}

if ($accion === 'carrusel_editar') {
    $id        = postInt('id');
    $titulo    = str('titulo', 200);
    $subtitulo = str('subtitulo', 300);
    if (!$id) jsonErr('ID inválido');

    $urlArchivo = procesarArchivoCarrusel();

    if ($urlArchivo !== null) {
        // Se subió imagen nueva — borrar archivo anterior si era local
        $row = $db->prepare('SELECT url FROM carrusel_fotos WHERE id=?');
        $row->execute([$id]);
        $anterior = $row->fetchColumn();
        if ($anterior && str_contains($anterior, '/carrusel/')) {
            $rutaLocal = dirname(__DIR__, 2) . '/shared/assets/img/carrusel/' . basename($anterior);
            if (file_exists($rutaLocal)) @unlink($rutaLocal);
        }
        $db->prepare('UPDATE carrusel_fotos SET url=?,titulo=?,subtitulo=? WHERE id=?')
           ->execute([$urlArchivo, $titulo, $subtitulo, $id]);
    } else {
        // Sin archivo nuevo — actualizar solo título/subtítulo (y URL si se escribió)
        $urlTexto = str('url', 1000);
        if ($urlTexto) {
            $db->prepare('UPDATE carrusel_fotos SET url=?,titulo=?,subtitulo=? WHERE id=?')
               ->execute([$urlTexto, $titulo, $subtitulo, $id]);
        } else {
            $db->prepare('UPDATE carrusel_fotos SET titulo=?,subtitulo=? WHERE id=?')
               ->execute([$titulo, $subtitulo, $id]);
        }
    }
    jsonOk('Imagen actualizada');
}

if ($accion === 'carrusel_eliminar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    // Borrar archivo local si existe
    $row = $db->prepare('SELECT url FROM carrusel_fotos WHERE id=?');
    $row->execute([$id]);
    $url = $row->fetchColumn();
    if ($url && str_contains($url, '/carrusel/')) {
        $rutaLocal = dirname(__DIR__, 2) . '/shared/assets/img/carrusel/' . basename($url);
        if (file_exists($rutaLocal)) @unlink($rutaLocal);
    }
    $db->prepare('DELETE FROM carrusel_fotos WHERE id=?')->execute([$id]);
    jsonOk('Imagen eliminada del carrusel');
}

if ($accion === 'carrusel_toggle') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('UPDATE carrusel_fotos SET activo = 1 - activo WHERE id=?')->execute([$id]);
    $s = $db->prepare('SELECT activo FROM carrusel_fotos WHERE id=?');
    $s->execute([$id]);
    $activo = (int)$s->fetchColumn();
    jsonOk($activo ? 'Imagen visible' : 'Imagen oculta', ['activo' => $activo]);
}

jsonErr('Acción desconocida');
