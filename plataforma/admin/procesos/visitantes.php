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
    $stmtActivo = $db->prepare('SELECT activo FROM directorio WHERE id=?');
    $stmtActivo->execute([$id]);
    $activo = (int)$stmtActivo->fetchColumn();
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
// Helper: sube imagen de portada de carrera y devuelve URL pública
function subirImagenCarrera(): ?string {
    if (!isset($_FILES['imagen_portada']) || $_FILES['imagen_portada']['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($_FILES['imagen_portada']['error'] !== UPLOAD_ERR_OK) {
        jsonErr('Error al recibir la imagen (código ' . (int)$_FILES['imagen_portada']['error'] . ')');
    }
    $ext   = strtolower(pathinfo($_FILES['imagen_portada']['name'], PATHINFO_EXTENSION));
    $allow = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $allow, true)) jsonErr('Formato no permitido. Usa JPG, PNG o WEBP.');
    if ($_FILES['imagen_portada']['size'] > 5 * 1024 * 1024) jsonErr('La imagen supera 5 MB.');
    if (!getimagesize($_FILES['imagen_portada']['tmp_name'])) jsonErr('El archivo no es una imagen válida.');

    $dir = dirname(__DIR__, 2) . '/modulos/convenios/assets/images/logo/imagenes/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $fname = 'carrera_' . bin2hex(random_bytes(6)) . '.' . $ext;
    move_uploaded_file($_FILES['imagen_portada']['tmp_name'], $dir . $fname);

    $base = defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma';
    return $base . '/modulos/convenios/assets/images/logo/imagenes/' . $fname;
}

if ($accion === 'carrera_agregar') {
    $clave    = strtoupper(trim(str('clave', 10)));
    $nombre   = str('nombre', 150);
    $color    = str('color', 7) ?: '#32129a';
    $desc     = str('descripcion', 500);
    $reticula = str('reticula_url', 1000) ?: null;
    if (!$clave || !$nombre) jsonErr('Clave y nombre son requeridos');
    if (!preg_match('/^[A-Z0-9]{1,10}$/', $clave)) jsonErr('La clave solo puede tener letras mayúsculas y números (máx. 10)');
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) $color = '#32129a';

    $imagenUrl = subirImagenCarrera() ?? (str('imagen_url', 1000) ?: null);

    try {
        $maxOrden = (int)$db->query('SELECT COALESCE(MAX(orden),0)+1 FROM carreras')->fetchColumn();
        // Intentar con imagen_url; si la columna no existe aún, insertar sin ella
        try {
            $db->prepare('INSERT INTO carreras (clave, nombre, color, imagen_url, reticula_url, orden) VALUES (?,?,?,?,?,?)')
               ->execute([$clave, $nombre, $color, $imagenUrl, $reticula, $maxOrden]);
        } catch (\PDOException $eCol) {
            $db->prepare('INSERT INTO carreras (clave, nombre, color, orden) VALUES (?,?,?,?)')
               ->execute([$clave, $nombre, $color, $maxOrden]);
        }
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
    $id       = postInt('id');
    $nombre   = str('nombre', 150);
    $color    = str('color', 7) ?: '#32129a';
    $desc     = str('descripcion', 500);
    $reticula = str('reticula_url', 1000) ?: null;
    if (!$id || !$nombre) jsonErr('Datos incompletos');
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) $color = '#32129a';

    $imagenNueva = subirImagenCarrera();
    if ($imagenNueva === null) {
        $imagenNueva = str('imagen_url', 1000) ?: null;
    }

    // Construir UPDATE — defensivo por si imagen_url o reticula_url no existen en BD
    try {
        if ($imagenNueva !== null) {
            $db->prepare('UPDATE carreras SET nombre=?, color=?, imagen_url=?, reticula_url=? WHERE id=?')
               ->execute([$nombre, $color, $imagenNueva, $reticula, $id]);
        } else {
            $db->prepare('UPDATE carreras SET nombre=?, color=?, reticula_url=? WHERE id=?')
               ->execute([$nombre, $color, $reticula, $id]);
        }
    } catch (\PDOException $eCol) {
        // Columnas nuevas no existen aún — actualizar solo las básicas
        $db->prepare('UPDATE carreras SET nombre=?, color=? WHERE id=?')
           ->execute([$nombre, $color, $id]);
    }

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
    $stmtActivo = $db->prepare('SELECT activo FROM carreras WHERE id=?');
    $stmtActivo->execute([$id]);
    $activo = (int)$stmtActivo->fetchColumn();
    jsonOk($activo ? 'Carrera activada' : 'Carrera desactivada', ['activo' => $activo]);
}

if ($accion === 'carrera_eliminar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    // Verificar que no tenga docentes, materias o coordinadores asociados
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
