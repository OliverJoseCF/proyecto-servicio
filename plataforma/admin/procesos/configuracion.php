<?php
require_once __DIR__ . '/_helper.php';

$accion = str('accion', 30);

if ($accion === 'guardar_config') {
    $campos = ['nombre_institucion','campus','descripcion_portal','eslogan','sitio_oficial_url',
                'direccion','correo_general','telefono','horario_atencion',
                'maps_embed_url','maps_link_url'];
    $db = db();
    $stmt = $db->prepare('INSERT INTO configuracion (clave, valor) VALUES (:k,:v)
                          ON DUPLICATE KEY UPDATE valor = VALUES(valor)');
    // Solo persistir los campos efectivamente enviados — esta acción es compartida por
    // varios formularios (datos generales y dirección/contacto). Guardar campos ausentes
    // los sobrescribiría con cadena vacía y borraría los datos del otro formulario.
    $guardados = 0;
    foreach ($campos as $c) {
        if (!array_key_exists($c, $_POST)) continue;
        // maps_embed_url puede ser muy larga
        $max = in_array($c, ['maps_embed_url','maps_link_url','descripcion_portal']) ? 5000 : 500;
        $v   = str($c, $max);
        if ($c === 'telefono' && $v !== '' && !preg_match('/^[0-9+\-\s()]{7,25}$/', $v)) {
            jsonErr('El teléfono solo puede contener números, +, -, espacios y paréntesis (7 a 25 caracteres)');
        }
        if (in_array($c, ['sitio_oficial_url','maps_embed_url','maps_link_url'], true) && !urlSegura($v)) {
            jsonErr('La URL de "' . $c . '" no es válida (debe iniciar con http(s):// o /).');
        }
        if ($c === 'correo_general' && $v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
            jsonErr('El correo general no tiene un formato válido.');
        }
        $stmt->execute([':k' => $c, ':v' => $v]);
        $guardados++;
    }
    if ($guardados === 0) jsonErr('No se recibió ningún campo válido');
    $camposGuardados = array_filter($campos, fn($c) => array_key_exists($c, $_POST));
    jsonOk('Configuración guardada', [], 'Configuración guardada: ' . implode(', ', $camposGuardados));
}

if ($accion === 'guardar_correos') {
    $campos = ['correo_general','correo_biblioteca','correo_vinculacion','correo_facturacion',
                'correo_escolares','correo_direccion','correo_servicios'];
    $db = db();
    $stmt = $db->prepare('INSERT INTO configuracion (clave, valor) VALUES (:k,:v)
                          ON DUPLICATE KEY UPDATE valor = VALUES(valor)');
    foreach ($campos as $c) {
        $v = str($c, 254);
        if ($v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
            jsonErr('El correo de "' . str_replace('correo_', '', $c) . '" no tiene un formato válido.');
        }
        $stmt->execute([':k' => $c, ':v' => $v]);
    }
    jsonOk('Correos guardados', [], 'Correos institucionales actualizados (' . count($campos) . ' correos)');
}

if ($accion === 'guardar_redes') {
    $campos = ['facebook_url','youtube_url','instagram_url','twitter_url','linkedin_url'];
    $db = db();
    $stmt = $db->prepare('INSERT INTO configuracion (clave, valor) VALUES (:k,:v)
                          ON DUPLICATE KEY UPDATE valor = VALUES(valor)');
    foreach ($campos as $c) {
        $v = str($c, 500);
        if ($v !== '' && !preg_match('#^https?://#i', $v)) {
            jsonErr('La URL de "' . str_replace('_url', '', $c) . '" debe iniciar con http:// o https://');
        }
        $stmt->execute([':k' => $c, ':v' => $v]);
    }
    jsonOk('Redes sociales guardadas', [], 'Redes sociales actualizadas');
}

if ($accion === 'cambiar_password') {
    $nueva    = $_POST['nueva_password']    ?? '';
    $confirma = $_POST['confirma_password'] ?? '';
    if (strlen($nueva) < 8)            jsonErr('La contraseña debe tener al menos 8 caracteres');
    if (!preg_match('/[A-Z]/', $nueva)) jsonErr('La contraseña debe incluir al menos una mayúscula');
    if (!preg_match('/[0-9]/', $nueva)) jsonErr('La contraseña debe incluir al menos un número');
    if (!preg_match('/[^A-Za-z0-9]/', $nueva)) jsonErr('La contraseña debe incluir al menos un símbolo');
    if ($nueva !== $confirma)          jsonErr('Las contraseñas no coinciden');

    $hash    = password_hash($nueva, PASSWORD_BCRYPT, ['cost' => 12]);
    $cfgPath = dirname(__DIR__, 2) . '/shared/config.local.php';

    if (!file_exists($cfgPath)) jsonErr('No existe config.local.php — créalo desde config.local.example.php');

    // Bloqueo exclusivo sobre un lock dedicado: serializa el ciclo leer-modificar-escribir
    // para que dos admins guardando a la vez no se pisen. Se libera solo al terminar el
    // script (incluso si jsonErr() hace exit) porque PHP cierra el handle en el shutdown.
    $lock = fopen($cfgPath . '.lock', 'c');
    if ($lock === false || !flock($lock, LOCK_EX)) {
        jsonErr('No se pudo bloquear la configuración para escribir. Intenta de nuevo.');
    }

    $content = file_get_contents($cfgPath);
    $pattern = "/define\s*\(\s*'GLOBAL_ADMIN_HASH'\s*,\s*'[^']*'\s*\)/";
    $replace  = "define('GLOBAL_ADMIN_HASH', '" . $hash . "')";

    if (!preg_match($pattern, $content)) jsonErr('No se encontró GLOBAL_ADMIN_HASH en config.local.php');

    // IMPORTANTE: el hash bcrypt contiene '$2y$12$...'. Si se pasara como string de
    // reemplazo a preg_replace, las secuencias $2/$12 se interpretarían como
    // retro-referencias y corromperían el hash. preg_replace_callback NO interpreta
    // el valor devuelto por el callback, así que se inserta literalmente.
    $newContent = preg_replace_callback($pattern, static fn() => $replace, $content);
    if ($newContent === null) jsonErr('Error interno al procesar el archivo de configuración');

    // Escritura atómica: evita corrupción ante corte de energía a mitad de escritura
    $tmp = $cfgPath . '.tmp.' . getmypid();
    if (file_put_contents($tmp, $newContent, LOCK_EX) === false) {
        jsonErr('No se pudo escribir el archivo temporal. Verifica permisos en shared/.');
    }
    if (!rename($tmp, $cfgPath)) {
        @unlink($tmp);
        jsonErr('No se pudo reemplazar config.local.php. Verifica permisos.');
    }
    flock($lock, LOCK_UN);
    fclose($lock);
    jsonOk('Contraseña actualizada. Cierra sesión y prueba el nuevo acceso.', [], 'Contraseña del admin actualizada');
}

jsonErr('Acción desconocida');
