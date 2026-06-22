<?php
require_once __DIR__ . '/_helper.php';

$accion = str('accion', 30);
$db     = db();

// Política de contraseña: misma que el cambio de la cuenta maestra.
const ADMIN_PW_MIN = 12;

// ══ AGREGAR ADMINISTRADOR ════════════════════════════════════════
if ($accion === 'admin_agregar') {
    $nombre = str('nombre', 150);
    $email  = mb_strtolower(str('email', 254));
    $pass   = $_POST['password'] ?? '';

    if ($nombre === '')                              jsonErr('El nombre es requerido');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))  jsonErr('Correo electrónico inválido');
    if (strlen($pass) < ADMIN_PW_MIN)                jsonErr('La contraseña debe tener al menos ' . ADMIN_PW_MIN . ' caracteres');

    // Email único (incluida la cuenta maestra)
    if (defined('GLOBAL_ADMIN_EMAIL') && $email === mb_strtolower(GLOBAL_ADMIN_EMAIL)) {
        jsonErr('Ese correo pertenece a la cuenta maestra. Usa otro.');
    }
    $dup = $db->prepare('SELECT id FROM admins WHERE email = ?');
    $dup->execute([$email]);
    if ($dup->fetchColumn()) jsonErr('Ya existe un administrador con ese correo');

    $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
    $db->prepare('INSERT INTO admins (nombre, email, password_hash) VALUES (?,?,?)')
       ->execute([$nombre, $email, $hash]);
    $newId = $db->lastInsertId();
    jsonOk('Administrador agregado', ['id' => $newId], "Administrador agregado: $nombre <$email> (ID $newId)");
}

// ══ EDITAR ADMINISTRADOR ═════════════════════════════════════════
if ($accion === 'admin_editar') {
    $id     = postInt('id');
    $nombre = str('nombre', 150);
    $email  = mb_strtolower(str('email', 254));
    $pass   = $_POST['password'] ?? ''; // opcional: solo cambia si se escribe

    if (!$id)                                        jsonErr('ID inválido');
    if ($nombre === '')                              jsonErr('El nombre es requerido');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))  jsonErr('Correo electrónico inválido');

    // Email único entre otros admins y distinto de la cuenta maestra
    if (defined('GLOBAL_ADMIN_EMAIL') && $email === mb_strtolower(GLOBAL_ADMIN_EMAIL)) {
        jsonErr('Ese correo pertenece a la cuenta maestra. Usa otro.');
    }
    $dup = $db->prepare('SELECT id FROM admins WHERE email = ? AND id <> ?');
    $dup->execute([$email, $id]);
    if ($dup->fetchColumn()) jsonErr('Otro administrador ya usa ese correo');

    if ($pass !== '') {
        if (strlen($pass) < ADMIN_PW_MIN) jsonErr('La contraseña debe tener al menos ' . ADMIN_PW_MIN . ' caracteres');
        $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
        $db->prepare('UPDATE admins SET nombre=?, email=?, password_hash=? WHERE id=?')
           ->execute([$nombre, $email, $hash, $id]);
    } else {
        $db->prepare('UPDATE admins SET nombre=?, email=? WHERE id=?')
           ->execute([$nombre, $email, $id]);
    }
    jsonOk('Administrador actualizado', [], "Administrador actualizado: $nombre <$email> (ID $id)");
}

// ══ ELIMINAR ADMINISTRADOR ═══════════════════════════════════════
if ($accion === 'admin_eliminar') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    // No permitir que el admin se elimine a sí mismo (evita quedarse sin acceso)
    if ($id === adminActualId()) jsonErr('No puedes eliminar la cuenta con la que iniciaste sesión');

    $sN = $db->prepare('SELECT nombre, email FROM admins WHERE id = ?');
    $sN->execute([$id]);
    $row = $sN->fetch();
    if (!$row) jsonErr('El administrador no existe');

    $db->prepare('DELETE FROM admins WHERE id = ?')->execute([$id]);
    jsonOk('Administrador eliminado', [], "Administrador eliminado: {$row['nombre']} <{$row['email']}> (ID $id)");
}

// ══ ACTIVAR / DESACTIVAR ═════════════════════════════════════════
if ($accion === 'admin_toggle') {
    $id = postInt('id');
    if (!$id) jsonErr('ID inválido');
    if ($id === adminActualId()) jsonErr('No puedes desactivar la cuenta con la que iniciaste sesión');

    $db->prepare('UPDATE admins SET activo = 1 - activo WHERE id = ?')->execute([$id]);
    $s = $db->prepare('SELECT nombre, email, activo FROM admins WHERE id = ?');
    $s->execute([$id]);
    $row = $s->fetch();
    if (!$row) jsonErr('El administrador no existe');
    $activo = (int)$row['activo'];
    jsonOk($activo ? 'Administrador activado' : 'Administrador desactivado', ['activo' => $activo],
        ($activo ? 'Administrador activado' : 'Administrador desactivado') . ": {$row['nombre']} <{$row['email']}> (ID $id)");
}

jsonErr('Acción desconocida');
