<?php
require_once __DIR__ . '/../../shared/config.php';
$base = defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma';
header('Location: ' . $base . '/modulos/visitantes/Directorio.php', true, 301);
exit;
