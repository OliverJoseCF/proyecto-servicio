<?php
// Plantilla para src/config.local.php — INSTRUCCIONES:
//   1. Copia este archivo como  src/config.local.php
//   2. Rellena los valores reales
//   3. NUNCA subas config.local.php al repositorio (.gitignore ya lo excluye)
//
// Genera el hash de la contraseña admin con:
//   php plataforma/modulos/convenios/tools/setup_password.php

define('ADMIN_EMAIL',         'admin@tecsj.edu.mx');
define('ADMIN_PASSWORD_HASH', '$2y$12$REEMPLAZA_ESTE_VALOR_CON_EL_HASH_GENERADO');
define('VINCULACION_EMAIL',   'vinculacion@tecsj.edu.mx');
