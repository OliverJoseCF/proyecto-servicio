<?php
require_once __DIR__ . '/_helper.php';

$accion = str('accion', 30);
$db     = db();

// ══ LIBROS ══════════════════════════════════════════════════════
if ($accion === 'libro_agregar') {
    $codigo    = str('codigo', 30);
    $nombre    = str('nombre', 300);
    $autor     = str('autor', 200);
    $editorial = str('editorial', 150);
    $categoria = str('categoria', 100);
    $ejemplares= intVal('ejemplares', 1);
    if (!$codigo || !$nombre) jsonErr('Código y título son requeridos');
    try {
        $db->prepare('INSERT INTO libros (codigo,nombre,autor,editorial,categoria,ejemplares) VALUES (?,?,?,?,?,?)')
           ->execute([$codigo,$nombre,$autor,$editorial,$categoria,$ejemplares]);
        jsonOk('Libro agregado', ['id' => $db->lastInsertId()]);
    } catch (\PDOException $e) {
        if ($e->getCode() === '23000') jsonErr('Ya existe un libro con ese código');
        throw $e;
    }
}

if ($accion === 'libro_editar') {
    $id        = intVal('id');
    $codigo    = str('codigo', 30);
    $nombre    = str('nombre', 300);
    $autor     = str('autor', 200);
    $editorial = str('editorial', 150);
    $categoria = str('categoria', 100);
    $ejemplares= intVal('ejemplares', 1);
    if (!$id || !$codigo || !$nombre) jsonErr('Datos incompletos');
    $db->prepare('UPDATE libros SET codigo=?,nombre=?,autor=?,editorial=?,categoria=?,ejemplares=? WHERE id=?')
       ->execute([$codigo,$nombre,$autor,$editorial,$categoria,$ejemplares,$id]);
    jsonOk('Libro actualizado');
}

if ($accion === 'libro_eliminar') {
    $id = intVal('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('DELETE FROM libros WHERE id=?')->execute([$id]);
    jsonOk('Libro eliminado');
}

// ══ PRÉSTAMOS ════════════════════════════════════════════════════
if ($accion === 'prestamo_devuelto') {
    $id = intVal('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('UPDATE prestamos SET devuelto=1, fecha_devuelto=NOW() WHERE id=?')->execute([$id]);
    jsonOk('Préstamo marcado como devuelto');
}

// ══ SOLICITUDES ════════════════════════════════════════════════
if ($accion === 'solicitud_aprobar') {
    $id = intVal('id');
    if (!$id) jsonErr('ID inválido');

    $db->beginTransaction();
    try {
        $sol = $db->prepare('SELECT * FROM solicitudes_biblioteca WHERE id=? AND estado="pendiente"');
        $sol->execute([$id]);
        $row = $sol->fetch();
        if (!$row) jsonErr('Solicitud no encontrada o ya procesada');

        $db->prepare('UPDATE solicitudes_biblioteca SET estado="aprobada", updated_at=NOW() WHERE id=?')
           ->execute([$id]);

        // Crear préstamo
        $db->prepare('INSERT INTO prestamos (libro_id,estudiante_nombre,estudiante_control,carrera,tipo,fecha_prestamo,fecha_devolucion)
                      VALUES (?,?,?,?,?,CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY))')
           ->execute([$row['libro_id'],$row['estudiante_nombre'],$row['estudiante_control'],$row['carrera'],$row['tipo']]);

        $db->commit();
        jsonOk('Solicitud aprobada y préstamo creado');
    } catch (\Throwable $e) {
        $db->rollBack();
        throw $e;
    }
}

if ($accion === 'solicitud_rechazar') {
    $id = intVal('id');
    if (!$id) jsonErr('ID inválido');
    $db->prepare('UPDATE solicitudes_biblioteca SET estado="rechazada", updated_at=NOW() WHERE id=?')
       ->execute([$id]);
    jsonOk('Solicitud rechazada');
}

jsonErr('Acción desconocida');
