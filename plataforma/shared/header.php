<?php
/**
 * Header unificado de la plataforma TSJ.
 *
 * Variables que el módulo puede definir antes de incluir este archivo:
 *   $tsj_module  — slug del módulo activo para marcar nav: 'visitantes'|'biblioteca'|'convenios'|'horarios'
 *   $tsj_title   — texto del <title> (el módulo lo combina con " — TSJ Chapala")
 *   $tsj_extra_css  — ruta(s) adicional(es) de CSS propias del módulo (string o array)
 *   $tsj_head_extra — HTML arbitrario a inyectar dentro de <head> (p.ej. bloque <style> inline)
 */

if (!defined('PLATAFORMA_URL')) {
    require_once __DIR__ . '/config.php';
}

$tsj_module     = $tsj_module     ?? '';
$tsj_title      = $tsj_title      ?? 'Tecnológico Superior de Jalisco';
$tsj_extra_css  = $tsj_extra_css  ?? [];
$tsj_head_extra = $tsj_head_extra ?? '';
if (is_string($tsj_extra_css)) {
    $tsj_extra_css = [$tsj_extra_css];
}

$base = PLATAFORMA_URL;

$nav_items = [
    'visitantes' => ['label' => 'Visitantes',  'href' => $base . '/modulos/visitantes/index.php'],
    'biblioteca' => ['label' => 'Biblioteca',   'href' => $base . '/modulos/biblioteca/buscar.php'],
    'convenios'  => ['label' => 'Convenios',    'href' => $base . '/modulos/convenios/index.php'],
    'horarios'   => ['label' => 'Horarios',     'href' => $base . '/modulos/horarios/index.php'],
    'requisitos' => ['label' => 'Requisitos',   'href' => $base . '/modulos/requisitos/residencia.php'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($tsj_title, ENT_QUOTES, 'UTF-8') ?> — TSJ Chapala</title>
  <link rel="icon" type="image/png" href="<?= $base ?>/shared/assets/img/favicon.png" />

  <!-- Fuente Poppins (self-hosted preload) -->
  <link rel="preload" href="https://fonts.gstatic.com/s/poppins/v20/pxiEyp8kv8JHgFVrJJfecg.woff2" as="font" type="font/woff2" crossorigin />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />

  <!-- Estilos compartidos -->
  <link rel="stylesheet" href="<?= $base ?>/shared/assets/css/theme.css" />

  <!-- Estilos adicionales del módulo -->
  <?php foreach ($tsj_extra_css as $css): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($css, ENT_QUOTES, 'UTF-8') ?>" />
  <?php endforeach; ?>

  <?php if ($tsj_head_extra): ?>
  <?= $tsj_head_extra ?>
  <?php endif; ?>
</head>
<body>

<header class="tsj-header" id="tsj-header">
  <!-- Barra rosa -->
  <div class="tsj-top-bar"></div>

  <!-- Toolbar -->
  <div class="tsj-toolbar">
    <!-- Logo -->
    <a href="<?= $base ?>/" aria-label="Ir al inicio de la plataforma">
      <img class="tsj-logo" src="<?= $base ?>/shared/assets/img/logo.svg" alt="Tecnológico Superior de Jalisco" />
    </a>

    <!-- Ícono home (acceso rápido al portal) -->
    <a class="tsj-home-icon" href="<?= $base ?>/" aria-label="Portal principal">
      <img src="<?= $base ?>/shared/assets/img/home.svg" alt="Inicio" />
    </a>

    <!-- Navegación escritorio -->
    <nav aria-label="Navegación principal">
      <ul class="tsj-nav">
        <?php foreach ($nav_items as $key => $item): ?>
          <li>
            <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
               <?= $tsj_module === $key ? 'class="active" aria-current="page"' : '' ?>>
              <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <!-- Botón hamburguesa móvil -->
    <button class="tsj-menu-btn" id="tsj-menu-btn" aria-label="Abrir menú" aria-expanded="false" aria-controls="tsj-menu-panel">
      <img src="<?= $base ?>/shared/assets/img/menu.svg" alt="" aria-hidden="true" id="tsj-menu-icon" />
    </button>
  </div>
</header>

<!-- Panel lateral móvil -->
<nav class="tsj-menu-panel" id="tsj-menu-panel" aria-label="Menú móvil">
  <div class="tsj-menu-content">
    <a class="tsj-menu-item" href="<?= $base ?>/">← Portal principal</a>
    <?php foreach ($nav_items as $key => $item): ?>
      <?php if ($key === 'pendiente') continue; ?>
      <a class="tsj-menu-item <?= $tsj_module === $key ? 'active' : '' ?>"
         href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>">
        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
      </a>
    <?php endforeach; ?>
  </div>
</nav>

<!-- Offset para compensar el header fixed -->
<div class="tsj-body-offset"></div>
