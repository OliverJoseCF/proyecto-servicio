<?php
require_once __DIR__ . '/_helper.php';

$accion = str('accion', 30);
$db     = db();

// ══ DIRECTORIO ══════════════════════════════════════════════════
if ($accion === 'directorio_agregar') {
    $nombre   = str('nombre', 150);
    $puesto   = str('puesto', 150);
    $correo   = str('correo', 254);
    $telefono = str('telefono', 30) ?: 'S/N';
    $extension= str('extension', 20);
    $ubicacion= str('ubicacion_fisica', 200);
    $foto     = str('foto', 500);
    if (!$nombre) jsonErr('El nombre es requerido');
    $db->prepare('INSERT INTO directorio (nombre,puesto,correo,telefono,extension,ubicacion_fisica,foto)
                  VALUES (?,?,?,?,?,?,?)')
       ->execute([$nombre,$puesto,$correo,$telefono,$extension ?: null,$ubicacion ?: null,$foto ?: null]);
    jsonOk('Persona agregada', ['id' => $db->lastInsertId()]);
}

if ($accion === 'directorio_editar') {
    $id       = postInt('id');
    $nombre   = str('nombre', 150);
    $puesto   = str('puesto', 150);
    $correo   = str('correo', 254);
    $telefono = str('telefono', 30) ?: 'S/N';
    $extension= str('extension', 20);
    $ubicacion= str('ubicacion_fisica', 200);
    $foto     = str('foto', 500);
    if (!$id || !$nombre) jsonErr('Datos incompletos');
    $db->prepare('UPDATE directorio SET nombre=?,puesto=?,correo=?,telefono=?,extension=?,ubicacion_fisica=?,foto=? WHERE id=?')
       ->execute([$nombre,$puesto,$correo,$telefono,$extension ?: null,$ubicacion ?: null,$foto ?: null,$id]);
    jsonOk('Persona actualizada');
}

if ($accion === 'directorio_eliminar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('DELETE FROM directorio WHERE id=?')->execute([$id]);
    jsonOk('Persona eliminada');
}

if ($accion === 'directorio_toggle') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('UPDATE directorio SET activo = 1 - activo WHERE id=?')->execute([$id]);
    $activo = (int)$db->query("SELECT activo FROM directorio WHERE id=$id")->fetchColumn();
    jsonOk($activo ? 'Persona visible' : 'Persona oculta', ['activo' => $activo]);
}

// ══ DOCENTES ════════════════════════════════════════════════════
if ($accion === 'docente_agregar') {
    $nombre     = str('nombre', 150);
    $correo     = str('correo', 254);
    $carrera_id = postInt('carrera_id');
    $foto       = str('foto', 500);
    if (!$nombre) jsonErr('El nombre es requerido');
    $db->prepare('INSERT INTO docentes (nombre,correo,carrera_id,foto) VALUES (?,?,?,?)')
       ->execute([$nombre,$correo,$carrera_id ?: null,$foto ?: null]);
    jsonOk('Docente agregado', ['id' => $db->lastInsertId()]);
}

if ($accion === 'docente_editar') {
    $id         = postInt('id');
    $nombre     = str('nombre', 150);
    $correo     = str('correo', 254);
    $carrera_id = postInt('carrera_id');
    $foto       = str('foto', 500);
    if (!$id || !$nombre) jsonErr('Datos incompletos');
    $db->prepare('UPDATE docentes SET nombre=?,correo=?,carrera_id=?,foto=? WHERE id=?')
       ->execute([$nombre,$correo,$carrera_id ?: null,$foto ?: null,$id]);
    jsonOk('Docente actualizado');
}

if ($accion === 'docente_eliminar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('DELETE FROM docentes WHERE id=?')->execute([$id]);
    jsonOk('Docente eliminado');
}

// ══ COORDINADORES ════════════════════════════════════════════════
if ($accion === 'coord_editar') {
    $id         = postInt('id');
    $nombre     = str('nombre', 150);
    $correo     = str('correo', 254);
    if (!$id || !$nombre) jsonErr('Datos incompletos');
    $db->prepare('UPDATE coordinadores SET nombre=?,correo=? WHERE id=?')
       ->execute([$nombre,$correo,$id]);
    jsonOk('Coordinador actualizado');
}

if ($accion === 'coord_eliminar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('DELETE FROM coordinadores WHERE id=?')->execute([$id]);
    jsonOk('Coordinador eliminado');
}

// ══ MATERIAS ════════════════════════════════════════════════════
if ($accion === 'materias_guardar') {
    $carrera_id = postInt('carrera_id');
    $nombres    = $_POST['materias'] ?? [];
    if (!$carrera_id) jsonErr('Carrera inválida');
    if (!is_array($nombres)) jsonErr('Datos inválidos');

    $db->prepare('DELETE FROM materias WHERE carrera_id=?')->execute([$carrera_id]);
    $stmt = $db->prepare('INSERT INTO materias (carrera_id,nombre,orden) VALUES (?,?,?)');
    foreach ($nombres as $i => $nombre) {
        $nombre = mb_substr(trim($nombre), 0, 200);
        if ($nombre) $stmt->execute([$carrera_id, $nombre, $i + 1]);
    }
    jsonOk('Materias guardadas');
}

// ══ SECRETARÍAS ════════════════════════════════════════════════
if ($accion === 'secretaria_agregar') {
    $nombre   = str('nombre', 150);
    $rol      = str('rol', 150);
    $correo   = str('correo', 254);
    $telefono = str('telefono', 30);
    if (!$nombre) jsonErr('El nombre es requerido');
    $db->prepare('INSERT INTO secretarias (nombre,rol,correo,telefono) VALUES (?,?,?,?)')
       ->execute([$nombre,$rol,$correo,$telefono]);
    jsonOk('Secretaria agregada', ['id' => $db->lastInsertId()]);
}

if ($accion === 'secretaria_editar') {
    $id       = postInt('id');
    $nombre   = str('nombre', 150);
    $rol      = str('rol', 150);
    $correo   = str('correo', 254);
    $telefono = str('telefono', 30);
    if (!$id || !$nombre) jsonErr('Datos incompletos');
    $db->prepare('UPDATE secretarias SET nombre=?,rol=?,correo=?,telefono=? WHERE id=?')
       ->execute([$nombre,$rol,$correo,$telefono,$id]);
    jsonOk('Secretaria actualizada');
}

if ($accion === 'secretaria_eliminar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('DELETE FROM secretarias WHERE id=?')->execute([$id]);
    jsonOk('Secretaria eliminada');
}

// ══ CARRERAS ════════════════════════════════════════════════════
if ($accion === 'carrera_agregar') {
    $clave  = strtoupper(trim(str('clave', 10)));
    $nombre = str('nombre', 150);
    $color  = str('color', 7) ?: '#32129a';
    $desc   = str('descripcion', 500);
    if (!$clave || !$nombre) jsonErr('Clave y nombre son requeridos');
    if (!preg_match('/^[A-Z0-9]{1,10}$/', $clave)) jsonErr('La clave solo puede tener letras mayúsculas y números (máx. 10)');
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) $color = '#32129a';
    try {
        $maxOrden = (int)$db->query('SELECT COALESCE(MAX(orden),0)+1 FROM carreras')->fetchColumn();
        $db->prepare('INSERT INTO carreras (clave, nombre, color, orden) VALUES (?,?,?,?)')
           ->execute([$clave, $nombre, $color, $maxOrden]);
        if ($desc !== '') {
            $db->prepare('INSERT INTO configuracion (clave, valor) VALUES (?,?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)')
               ->execute(['desc_' . $clave, $desc]);
        }
        jsonOk('Carrera agregada', ['id' => $db->lastInsertId()]);
    } catch (\PDOException $e) {
        if ($e->getCode() === '23000') jsonErr('Ya existe una carrera con esa clave');
        throw $e;
    }
}

if ($accion === 'carrera_editar') {
    $id     = postInt('id');
    $nombre = str('nombre', 150);
    $color  = str('color', 7) ?: '#32129a';
    $desc   = str('descripcion', 500);
    if (!$id || !$nombre) jsonErr('Datos incompletos');
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) $color = '#32129a';
    $db->prepare('UPDATE carreras SET nombre=?, color=? WHERE id=?')->execute([$nombre, $color, $id]);
    $claveRow = $db->prepare('SELECT clave FROM carreras WHERE id=?');
    $claveRow->execute([$id]);
    $clave = $claveRow->fetchColumn();
    if ($clave) {
        $db->prepare('INSERT INTO configuracion (clave, valor) VALUES (?,?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)')
           ->execute(['desc_' . $clave, $desc]);
    }
    jsonOk('Carrera actualizada');
}

if ($accion === 'carrera_toggle') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('UPDATE carreras SET activo = 1 - activo WHERE id=?')->execute([$id]);
    $activo = (int)$db->query("SELECT activo FROM carreras WHERE id=$id")->fetchColumn();
    jsonOk($activo ? 'Carrera activada' : 'Carrera desactivada', ['activo' => $activo]);
}

if ($accion === 'carrera_eliminar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    // Verificar que no tenga docentes, materias o coordinadores activos
    $docentes = (int)$db->prepare('SELECT COUNT(*) FROM docentes WHERE carrera_id=?')->execute([$id]) ?
                $db->prepare('SELECT COUNT(*) FROM docentes WHERE carrera_id=?') : null;
    $stmtD = $db->prepare('SELECT COUNT(*) FROM docentes WHERE carrera_id=?');
    $stmtD->execute([$id]);
    $stmtM = $db->prepare('SELECT COUNT(*) FROM materias WHERE carrera_id=?');
    $stmtM->execute([$id]);
    $stmtC = $db->prepare('SELECT COUNT(*) FROM coordinadores WHERE carrera_id=?');
    $stmtC->execute([$id]);
    $total = (int)$stmtD->fetchColumn() + (int)$stmtM->fetchColumn() + (int)$stmtC->fetchColumn();
    if ($total > 0) jsonErr('No se puede eliminar: la carrera tiene docentes, materias o coordinadores asociados. Elimínalos primero.');
    // Obtener clave para borrar descripción
    $claveRow = $db->prepare('SELECT clave FROM carreras WHERE id=?');
    $claveRow->execute([$id]);
    $clave = $claveRow->fetchColumn();
    $db->prepare('DELETE FROM carreras WHERE id=?')->execute([$id]);
    if ($clave) {
        $db->prepare('DELETE FROM configuracion WHERE clave=?')->execute(['desc_' . $clave]);
    }
    jsonOk('Carrera eliminada');
}

// ══ OFERTA ACADÉMICA ════════════════════════════════════════════
if ($accion === 'oferta_guardar') {
    // Obtener claves válidas desde BD
    $clavesValidas = array_column(
        $db->query('SELECT clave FROM carreras WHERE activo=1')->fetchAll(),
        'clave'
    );
    $desc = $_POST['desc'] ?? [];
    if (!is_array($desc)) jsonErr('Datos inválidos');

    $stmt = $db->prepare('INSERT INTO configuracion (clave, valor) VALUES (:k,:v)
                          ON DUPLICATE KEY UPDATE valor = VALUES(valor)');
    foreach ($clavesValidas as $clave) {
        $valor = mb_substr(trim($desc[$clave] ?? ''), 0, 500);
        $stmt->execute([':k' => 'desc_' . $clave, ':v' => $valor]);
    }
    jsonOk('Descripciones de oferta académica guardadas');
}

// ══ NUEVO INGRESO ══════════════════════════════════════════════
if ($accion === 'nuevo_ingreso_guardar') {
    $dia    = postInt('dia_examen');
    $hora   = str('hora_examen', 10);
    $lugar  = str('lugar_examen', 200);
    $rawReq = str('requisitos', 5000);
    if ($dia < 1 || $dia > 31) jsonErr('Día inválido');

    // Convertir texto (uno por línea) a JSON array
    $arr        = array_values(array_filter(array_map('trim', explode("\n", $rawReq))));
    $requisitos = json_encode($arr, JSON_UNESCAPED_UNICODE);

    $existe = $db->query('SELECT COUNT(*) FROM nuevo_ingreso_config')->fetchColumn();
    if ($existe) {
        $db->prepare('UPDATE nuevo_ingreso_config SET dia_examen=?,hora_examen=?,lugar_examen=?,requisitos=? WHERE id=1')
           ->execute([$dia, $hora, $lugar, $requisitos]);
    } else {
        $db->prepare('INSERT INTO nuevo_ingreso_config (dia_examen,hora_examen,lugar_examen,requisitos) VALUES (?,?,?,?)')
           ->execute([$dia, $hora, $lugar, $requisitos]);
    }
    jsonOk('Configuración de nuevo ingreso guardada');
}

jsonErr('Acción desconocida');
