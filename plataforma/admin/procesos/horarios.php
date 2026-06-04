<?php
require_once __DIR__ . '/_helper.php';

$accion = str('accion', 30);
$db     = db();

// ══ PROFESORES ══════════════════════════════════════════════════
if ($accion === 'profesor_agregar') {
    $nombre   = str('nombre', 100);
    $apellido = str('apellido', 100);
    $correo   = str('correo', 254);
    $foto     = str('foto', 500);
    if (!$nombre || !$apellido) jsonErr('Nombre y apellido son requeridos');
    $db->prepare('INSERT INTO profesores (nombre,apellido,correo,foto) VALUES (?,?,?,?)')
       ->execute([$nombre,$apellido,$correo,$foto ?: null]);
    jsonOk('Profesor agregado', ['id' => $db->lastInsertId()]);
}

if ($accion === 'profesor_editar') {
    $id       = intVal('id');
    $nombre   = str('nombre', 100);
    $apellido = str('apellido', 100);
    $correo   = str('correo', 254);
    $foto     = str('foto', 500);
    if (!$id || !$nombre || !$apellido) jsonErr('Datos incompletos');
    $db->prepare('UPDATE profesores SET nombre=?,apellido=?,correo=?,foto=? WHERE id_profesor=?')
       ->execute([$nombre,$apellido,$correo,$foto ?: null,$id]);
    jsonOk('Profesor actualizado');
}

if ($accion === 'profesor_eliminar') {
    $id = intVal('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('DELETE FROM profesores WHERE id_profesor=?')->execute([$id]);
    jsonOk('Profesor eliminado');
}

// ══ HORARIOS ════════════════════════════════════════════════════
if ($accion === 'horario_guardar') {
    $profesor_id = intVal('profesor_id');
    $carrera_id  = intVal('carrera_id');
    $semestre    = str('semestre', 10);
    $imagen      = null; // se asigna solo si hay upload
    if (!$profesor_id) jsonErr('Selecciona un profesor');

    // Upload de archivo si viene
    if (isset($_FILES['archivo_horario']) && $_FILES['archivo_horario']['error'] === UPLOAD_ERR_OK) {
        $ext   = strtolower(pathinfo($_FILES['archivo_horario']['name'], PATHINFO_EXTENSION));
        $allow = ['pdf','jpg','jpeg','png'];
        if (!in_array($ext, $allow)) jsonErr('Tipo de archivo no permitido. Usa PDF, JPG o PNG');
        $dir   = dirname(__DIR__, 2) . '/modulos/horarios/horarios/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $fname = 'horario_' . $profesor_id . '_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['archivo_horario']['tmp_name'], $dir . $fname);
        $imagen = $fname;
    }

    // Buscar si ya existe un horario para ese profesor+carrera
    $carreraParam = $carrera_id ?: null;
    $existe = $db->prepare(
        'SELECT id_horario, imagen_horario FROM horarios WHERE id_profesor=?
         AND (id_carrera = ? OR (id_carrera IS NULL AND ? IS NULL))'
    );
    $existe->execute([$profesor_id, $carreraParam, $carreraParam]);
    if ($row = $existe->fetch()) {
        // Si no se subió archivo nuevo, conservar el existente
        $imagenFinal = $imagen ?? $row['imagen_horario'];
        $db->prepare('UPDATE horarios SET semestre=?,imagen_horario=?,updated_at=NOW() WHERE id_horario=?')
           ->execute([$semestre, $imagenFinal, $row['id_horario']]);
        jsonOk('Horario actualizado');
    } else {
        $db->prepare('INSERT INTO horarios (id_profesor,id_carrera,semestre,imagen_horario) VALUES (?,?,?,?)')
           ->execute([$profesor_id, $carrera_id ?: null, $semestre, $imagen]);
        jsonOk('Horario guardado', ['id' => $db->lastInsertId()]);
    }
}

if ($accion === 'horario_eliminar') {
    $id = intVal('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('DELETE FROM horarios WHERE id_horario=?')->execute([$id]);
    jsonOk('Horario eliminado');
}

jsonErr('Acción desconocida');
