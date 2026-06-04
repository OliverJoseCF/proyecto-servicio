<?php
/**
 * Generador de hash de contraseña para el administrador.
 *
 * USO (ejecutar desde CLI, desde la raíz del repo):
 *   php plataforma/modulos/convenios/tools/setup_password.php
 *
 * Luego copia el hash generado en src/config.local.php:
 *   define('ADMIN_PASSWORD_HASH', '<hash_aqui>');
 *
 * ADVERTENCIA: Este script es solo para uso local/CLI.
 *              NO debe ser accesible desde el navegador.
 */

// Bloquear ejecución desde el navegador
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Este script solo puede ejecutarse desde la línea de comandos.');
}

echo "=== Generador de hash de contraseña de administrador ===\n\n";

// Leer contraseña de forma interactiva (sin eco en pantalla si es posible)
if (function_exists('readline')) {
    $password = readline('Introduce la nueva contraseña del administrador: ');
} else {
    echo 'Introduce la nueva contraseña del administrador: ';
    $password = trim(fgets(STDIN));
}

if (empty($password)) {
    exit("\nError: la contraseña no puede estar vacía.\n");
}

if (strlen($password) < 12) {
    echo "\nAdvertencia: la contraseña tiene menos de 12 caracteres. Se recomienda una contraseña más larga.\n";
}

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "\n\n✅ Hash generado (bcrypt, cost=12):\n";
echo $hash . "\n\n";
echo "📋 Copia la siguiente línea en src/config.local.php:\n";
echo "   define('ADMIN_PASSWORD_HASH', '" . $hash . "');\n\n";

// Verificación inmediata
if (password_verify($password, $hash)) {
    echo "✅ Verificación correcta: el hash es válido.\n\n";
} else {
    echo "❌ Error: el hash no pudo verificarse. Intenta de nuevo.\n\n";
}
