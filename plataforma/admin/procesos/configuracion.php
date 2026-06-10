<?php
require_once __DIR__ . '/_helper.php';

$accion = str('accion', 30);

if ($accion === 'guardar_config') {
    $campos = ['nombre_institucion','campus','descripcion_portal','plataforma_url','eslogan',
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
        $stmt->execute([':k' => $c, ':v' => $v]);
        $guardados++;
    }
    if ($guardados === 0) jsonErr('No se recibió ningún campo válido');
    jsonOk('Configuración guardada');
}

if ($accion === 'guardar_correos') {
    $campos = ['correo_general','correo_biblioteca','correo_vinculacion','correo_facturacion',
                'correo_escolares','correo_direccion','correo_servicios'];
    $db = db();
    $stmt = $db->prepare('INSERT INTO configuracion (clave, valor) VALUES (:k,:v)
                          ON DUPLICATE KEY UPDATE valor = VALUES(valor)');
    foreach ($campos as $c) {
        $v = str($c, 254);
        $stmt->execute([':k' => $c, ':v' => $v]);
    }
    jsonOk('Correos guardados');
}

if ($accion === 'guardar_redes') {
    $campos = ['facebook_url','youtube_url','instagram_url','twitter_url','linkedin_url','sitio_oficial_url'];
    $db = db();
    $stmt = $db->prepare('INSERT INTO configuracion (clave, valor) VALUES (:k,:v)
                          ON DUPLICATE KEY UPDATE valor = VALUES(valor)');
    foreach ($campos as $c) {
        $v = str($c, 500);
        $stmt->execute([':k' => $c, ':v' => $v]);
    }
    jsonOk('Redes sociales guardadas');
}

if ($accion === 'cambiar_password') {
    $nueva    = $_POST['nueva_password']    ?? '';
    $confirma = $_POST['confirma_password'] ?? '';
    if (strlen($nueva) < 12)          jsonErr('La contraseña debe tener al menos 12 caracteres');
    if ($nueva !== $confirma)          jsonErr('Las contraseñas no coinciden');

    $hash    = password_hash($nueva, PASSWORD_BCRYPT, ['cost' => 12]);
    $cfgPath = dirname(__DIR__, 2) . '/shared/config.local.php';

    if (!file_exists($cfgPath)) jsonErr('No existe config.local.php — créalo desde config.local.example.php');

    $content = file_get_contents($cfgPath);
    $pattern = "/define\s*\(\s*'GLOBAL_ADMIN_HASH'\s*,\s*'[^']*'\s*\)/";
    $replace  = "define('GLOBAL_ADMIN_HASH', '" . $hash . "')";

    if (!preg_match($pattern, $content)) jsonErr('No se encontró GLOBAL_ADMIN_HASH en config.local.php');

    $newContent = preg_replace($pattern, $replace, $content);
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
    jsonOk('Contraseña actualizada. Cierra sesión y prueba el nuevo acceso.');
}

jsonErr('Acción desconocida');
