<?php
/**
 * Genera el hash bcrypt para la contraseña del admin de Horarios.
 *
 * USO (ejecutar desde CLI, NO desde navegador):
 *   php plataforma/modulos/horarios/tools/setup_password.php
 *
 * Luego copia la constante resultante en horarios/login.php:
 *   define('HORARIOS_ADMIN_HASH', '<hash_aqui>');
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Este script solo puede ejecutarse desde la línea de comandos.');
}

if (function_exists('readline')) {
    $password = readline('Introduce la nueva contraseña del administrador de Horarios: ');
} else {
    echo 'Introduce la nueva contraseña del administrador de Horarios: ';
    $password = trim(fgets(STDIN));
}

if (empty($password)) {
    exit("\nError: la contraseña no puede estar vacía.\n");
}
if (strlen($password) < 12) {
    echo "\nAdvertencia: la contraseña tiene menos de 12 caracteres.\n";
}

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
echo "\n✅ Hash generado:\n$hash\n\n";
echo "Copia esto en horarios/login.php:\n";
echo "   define('HORARIOS_ADMIN_HASH', '$hash');\n\n";

if (password_verify($password, $hash)) {
    echo "✅ Verificación correcta.\n\n";
}
