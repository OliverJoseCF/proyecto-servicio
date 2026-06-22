<?php
require_once __DIR__ . '/_helper.php';

$accion = str('accion', 30);
$db     = db();

// ══ HORARIOS ════════════════════════════════════════════════════
if ($accion === 'horario_guardar') {
    $profesor_id = postInt('profesor_id');
    $carrera_id  = postInt('carrera_id');
    $semestre    = str('semestre', 10);
    $imagen      = null; // se asigna solo si hay upload
    if (!$profesor_id) jsonErr('Selecciona un profesor');

    // Nombre del profesor para la bitácora
    $pN = $db->prepare('SELECT nombre FROM docentes WHERE id=?');
    $pN->execute([$profesor_id]);
    $profNombre = $pN->fetchColumn() ?: ('ID ' . $profesor_id);

    $dir    = dirname(__DIR__, 2) . '/modulos/horarios/horarios/';
    $urlBase = PLATAFORMA_URL . '/modulos/horarios/horarios/';

    // Upload de archivo si viene
    if (isset($_FILES['archivo_horario']) && $_FILES['archivo_horario']['error'] === UPLOAD_ERR_OK) {
        $ext     = strtolower(pathinfo($_FILES['archivo_horario']['name'], PATHINFO_EXTENSION));
        $allow   = ['pdf','jpg','jpeg','png'];
        $maxSize = 5 * 1024 * 1024; // 5 MB

        if (!in_array($ext, $allow))
            jsonErr('Tipo de archivo no permitido. Usa PDF, JPG o PNG');
        if ($_FILES['archivo_horario']['size'] > $maxSize)
            jsonErr('El archivo supera el límite de 5 MB');

        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $fname = 'horario_' . $profesor_id . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        move_uploaded_file($_FILES['archivo_horario']['tmp_name'], $dir . $fname);
        // Guardar URL absoluta para que el frontend no dependa del directorio actual
        $imagen = $urlBase . $fname;
    }

    // Buscar si ya existe un horario para ese profesor+carrera
    $carreraParam = $carrera_id ?: null;
    $existe = $db->prepare(
        'SELECT id_horario, imagen_horario FROM horarios WHERE id_profesor=?
         AND (id_carrera = ? OR (id_carrera IS NULL AND ? IS NULL))'
    );
    $existe->execute([$profesor_id, $carreraParam, $carreraParam]);
    if ($row = $existe->fetch()) {
        // Si se sube archivo nuevo, borrar el archivo viejo del disco
        if ($imagen !== null && $row['imagen_horario']) {
            $oldFile = $dir . basename($row['imagen_horario']);
            if (file_exists($oldFile)) @unlink($oldFile);
        }
        $imagenFinal = $imagen ?? $row['imagen_horario'];
        $db->prepare('UPDATE horarios SET semestre=?,imagen_horario=?,updated_at=NOW() WHERE id_horario=?')
           ->execute([$semestre, $imagenFinal, $row['id_horario']]);
        jsonOk('Horario actualizado', [], "Horario actualizado: $profNombre (ID {$row['id_horario']})");
    } else {
        $db->prepare('INSERT INTO horarios (id_profesor,id_carrera,semestre,imagen_horario) VALUES (?,?,?,?)')
           ->execute([$profesor_id, $carrera_id ?: null, $semestre, $imagen]);
        $newId = $db->lastInsertId();
        jsonOk('Horario guardado', ['id' => $newId], "Horario guardado: $profNombre (ID $newId)");
    }
}

if ($accion === 'horario_eliminar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $row = $db->prepare('SELECT h.imagen_horario, d.nombre profesor
                         FROM horarios h LEFT JOIN docentes d ON h.id_profesor=d.id
                         WHERE h.id_horario=?');
    $row->execute([$id]);
    $h = $row->fetch();
    $profNombre = $h['profesor'] ?? '?';
    $db->prepare('DELETE FROM horarios WHERE id_horario=?')->execute([$id]);
    // Borrar archivo del disco si existe
    if ($h && $h['imagen_horario']) {
        $dir  = dirname(__DIR__, 2) . '/modulos/horarios/horarios/';
        $file = $dir . basename($h['imagen_horario']);
        if (file_exists($file)) @unlink($file);
    }
    jsonOk('Horario eliminado', [], "Horario eliminado: $profNombre (ID $id)");
}

jsonErr('Acción desconocida');
