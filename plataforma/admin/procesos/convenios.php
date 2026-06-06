<?php
require_once __DIR__ . '/_helper.php';

$accion = str('accion', 30);
$db     = db();

// ══ CONVENIOS ════════════════════════════════════════════════════
if ($accion === 'convenio_agregar') {
    $nombre     = str('nombre', 300);
    $tipo       = str('tipo_convenio', 100);
    $sector     = str('sector', 20) ?: 'privado';
    $carrera_id = postInt('carrera_id');
    $contacto   = str('nombre_contacto', 200);
    $correo     = str('correo_contacto', 254);
    $telefono   = str('telefono_contacto', 30);
    $logo       = str('logo', 500);
    $vence      = str('vencimiento', 10) ?: null;
    if (!$nombre) jsonErr('El nombre de la empresa es requerido');
    $db->prepare('INSERT INTO convenios (nombre,tipo_convenio,sector,carrera_id,nombre_contacto,correo_contacto,telefono_contacto,logo,vencimiento)
                  VALUES (?,?,?,?,?,?,?,?,?)')
       ->execute([$nombre,$tipo,$sector,$carrera_id ?: null,$contacto,$correo,$telefono,$logo ?: null,$vence]);
    jsonOk('Convenio agregado', ['id' => $db->lastInsertId()]);
}

if ($accion === 'convenio_editar') {
    $id         = postInt('id');
    $nombre     = str('nombre', 300);
    $tipo       = str('tipo_convenio', 100);
    $sector     = str('sector', 20) ?: 'privado';
    $carrera_id = postInt('carrera_id');
    $contacto   = str('nombre_contacto', 200);
    $correo     = str('correo_contacto', 254);
    $telefono   = str('telefono_contacto', 30);
    $logo       = str('logo', 500);
    $vence      = str('vencimiento', 10) ?: null;
    if (!$id || !$nombre) jsonErr('Datos incompletos');
    $db->prepare('UPDATE convenios SET nombre=?,tipo_convenio=?,sector=?,carrera_id=?,nombre_contacto=?,correo_contacto=?,telefono_contacto=?,logo=?,vencimiento=? WHERE id=?')
       ->execute([$nombre,$tipo,$sector,$carrera_id ?: null,$contacto,$correo,$telefono,$logo ?: null,$vence,$id]);
    jsonOk('Convenio actualizado');
}

if ($accion === 'convenio_eliminar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    // Soft-delete para conservar referencias históricas
    $db->prepare('UPDATE convenios SET activo=0 WHERE id=?')->execute([$id]);
    jsonOk('Convenio eliminado');
}

if ($accion === 'convenio_toggle') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('UPDATE convenios SET activo = 1 - activo WHERE id=?')->execute([$id]);
    $activo = (int)$db->query("SELECT activo FROM convenios WHERE id=$id")->fetchColumn();
    jsonOk($activo ? 'Convenio activado' : 'Convenio desactivado', ['activo' => $activo]);
}

// ══ SUGERENCIAS ══════════════════════════════════════════════════
if ($accion === 'sugerencia_aceptar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('UPDATE sugerencias_empresa SET estado="aceptada", updated_at=NOW() WHERE id=?')
       ->execute([$id]);
    jsonOk('Sugerencia aceptada');
}

if ($accion === 'sugerencia_rechazar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('UPDATE sugerencias_empresa SET estado="rechazada", updated_at=NOW() WHERE id=?')
       ->execute([$id]);
    jsonOk('Sugerencia rechazada');
}

jsonErr('Acción desconocida');
