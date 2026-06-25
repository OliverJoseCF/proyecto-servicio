<?php
require_once __DIR__ . '/_helper.php';

$accion = str('accion', 30);
$db     = db();

function subirLogoConvenio(): ?string {
    if (!isset($_FILES['logo_archivo']) || $_FILES['logo_archivo']['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($_FILES['logo_archivo']['error'] !== UPLOAD_ERR_OK) {
        jsonErr('Error al recibir la imagen (código ' . (int)$_FILES['logo_archivo']['error'] . ')');
    }
    $ext   = strtolower(pathinfo($_FILES['logo_archivo']['name'], PATHINFO_EXTENSION));
    $allow = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $allow, true)) jsonErr('Formato no permitido. Usa JPG, PNG o WEBP.');
    if ($_FILES['logo_archivo']['size'] > 2 * 1024 * 1024) jsonErr('La imagen supera 2 MB.');
    if (!getimagesize($_FILES['logo_archivo']['tmp_name'])) jsonErr('El archivo no es una imagen válida.');

    $dir = dirname(__DIR__, 2) . '/modulos/convenios/assets/images/logo/imagenes/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $fname = 'logo_' . bin2hex(random_bytes(6)) . '.' . $ext;
    move_uploaded_file($_FILES['logo_archivo']['tmp_name'], $dir . $fname);

    $base = defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma';
    return $base . '/modulos/convenios/assets/images/logo/imagenes/' . $fname;
}

// ══ CONVENIOS ════════════════════════════════════════════════════
if ($accion === 'convenio_agregar') {
    $nombre      = str('nombre', 300);
    $_tipo_raw   = str('tipo_convenio', 100);
    $tipo        = in_array($_tipo_raw, ['residencia','servicio_social','practicas','otro'], true) ? $_tipo_raw : 'residencia';
    $_sector_raw = str('sector', 20);
    $sector      = in_array($_sector_raw, ['privado','publico','ac','otro'], true) ? $_sector_raw : 'privado';
    $carrerasIds = array_values(array_filter(array_map('intval', (array)($_POST['carreras_ids'] ?? []))));
    $contacto   = str('nombre_contacto', 200);
    $correo     = str('correo_contacto', 254);
    $telefono   = telefono('telefono_contacto');
    $vence      = str('vencimiento', 10) ?: null;
    if (!$nombre) jsonErr('El nombre de la empresa es requerido');
    $logo = subirLogoConvenio() ?? (str('logo', 500) ?: null);
    if ($logo !== null && !urlSegura($logo)) jsonErr('La URL del logo no es válida (usa http(s):// o sube un archivo).');
    $db->prepare('INSERT INTO convenios (nombre,tipo_convenio,sector,nombre_contacto,correo_contacto,telefono_contacto,logo,vencimiento)
                  VALUES (?,?,?,?,?,?,?,?)')
       ->execute([$nombre,$tipo,$sector,$contacto,$correo,$telefono,$logo,$vence]);
    $newId = (int)$db->lastInsertId();
    if (!empty($carrerasIds)) {
        $stmtC = $db->prepare('INSERT IGNORE INTO convenio_carreras (convenio_id, carrera_id) VALUES (?, ?)');
        foreach ($carrerasIds as $cid) $stmtC->execute([$newId, $cid]);
    }
    jsonOk('Convenio agregado', ['id' => $newId], "Convenio agregado: $nombre (ID $newId)");
}

if ($accion === 'convenio_editar') {
    $id          = postInt('id');
    $nombre      = str('nombre', 300);
    $_tipo_raw   = str('tipo_convenio', 100);
    $tipo        = in_array($_tipo_raw, ['residencia','servicio_social','practicas','otro'], true) ? $_tipo_raw : 'residencia';
    $_sector_raw = str('sector', 20);
    $sector      = in_array($_sector_raw, ['privado','publico','ac','otro'], true) ? $_sector_raw : 'privado';
    $carrerasIds = array_values(array_filter(array_map('intval', (array)($_POST['carreras_ids'] ?? []))));
    $contacto   = str('nombre_contacto', 200);
    $correo     = str('correo_contacto', 254);
    $telefono   = telefono('telefono_contacto');
    $vence      = str('vencimiento', 10) ?: null;
    if (!$id || !$nombre) jsonErr('Datos incompletos');
    $logoNuevo = subirLogoConvenio();
    if ($logoNuevo === null) {
        // El campo oculto `logo` guarda la URL actual al editar; si está vacío el admin
        // eligió quitarlo (botón "Quitar"), así que se guarda NULL sin fallback a BD.
        $logoNuevo = str('logo', 500) ?: null;
        if ($logoNuevo !== null && !urlSegura($logoNuevo)) jsonErr('La URL del logo no es válida (usa http(s):// o sube un archivo).');
    }
    $db->prepare('UPDATE convenios SET nombre=?,tipo_convenio=?,sector=?,nombre_contacto=?,correo_contacto=?,telefono_contacto=?,logo=?,vencimiento=? WHERE id=?')
       ->execute([$nombre,$tipo,$sector,$contacto,$correo,$telefono,$logoNuevo,$vence,$id]);
    // Reemplazar carreras asociadas
    $db->prepare('DELETE FROM convenio_carreras WHERE convenio_id=?')->execute([$id]);
    if (!empty($carrerasIds)) {
        $stmtC = $db->prepare('INSERT IGNORE INTO convenio_carreras (convenio_id, carrera_id) VALUES (?, ?)');
        foreach ($carrerasIds as $cid) $stmtC->execute([$id, $cid]);
    }
    jsonOk('Convenio actualizado', [], "Convenio actualizado: $nombre (ID $id)");
}

if ($accion === 'convenio_eliminar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $sN = $db->prepare('SELECT nombre FROM convenios WHERE id=?');
    $sN->execute([$id]);
    $nombre = $sN->fetchColumn() ?: '?';
    $db->prepare('DELETE FROM convenios WHERE id=?')->execute([$id]);
    jsonOk('Convenio eliminado', [], "Convenio eliminado: $nombre (ID $id)");
}

if ($accion === 'convenio_toggle') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('UPDATE convenios SET activo = 1 - activo WHERE id=?')->execute([$id]);
    $s = $db->prepare('SELECT nombre, activo FROM convenios WHERE id=?');
    $s->execute([$id]);
    $row    = $s->fetch();
    $activo = (int)($row['activo'] ?? 0);
    $nombre = $row['nombre'] ?? '?';
    jsonOk($activo ? 'Convenio activado' : 'Convenio desactivado', ['activo' => $activo],
        ($activo ? 'Convenio activado' : 'Convenio desactivado') . ": $nombre (ID $id)");
}

// ══ SUGERENCIAS ══════════════════════════════════════════════════
if ($accion === 'sugerencia_aceptar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $s = $db->prepare('SELECT nombre_empresa, correo_empresa, nombre_contacto FROM sugerencias_empresa WHERE id=?');
    $s->execute([$id]);
    $sug = $s->fetch();
    $db->prepare('UPDATE sugerencias_empresa SET estado="aceptada", updated_at=NOW() WHERE id=?')->execute([$id]);
    $empNom = $sug['nombre_empresa'] ?? '?';
    jsonOk('Sugerencia aceptada', [
        'nombre'   => $sug['nombre_empresa']  ?? '',
        'correo'   => $sug['correo_empresa']  ?? '',
        'contacto' => $sug['nombre_contacto'] ?? '',
    ], "Sugerencia aceptada: $empNom (ID $id)");
}

if ($accion === 'sugerencia_rechazar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $sN = $db->prepare('SELECT nombre_empresa FROM sugerencias_empresa WHERE id=?');
    $sN->execute([$id]);
    $empNom = $sN->fetchColumn() ?: '?';
    $db->prepare('UPDATE sugerencias_empresa SET estado="rechazada", updated_at=NOW() WHERE id=?')
       ->execute([$id]);
    jsonOk('Sugerencia rechazada', [], "Sugerencia rechazada: $empNom (ID $id)");
}

jsonErr('Acción desconocida');
