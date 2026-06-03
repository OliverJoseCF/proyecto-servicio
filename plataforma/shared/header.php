<?php
/**
 * Header unificado de la plataforma TSJ.
 *
 * Variables que el módulo puede definir antes de incluir este archivo:
 *   $tsj_module      — slug activo: 'visitantes'|'biblioteca'|'convenios'|'horarios'|'requisitos'
 *   $tsj_title       — texto del <title> (sin el sufijo "— TSJ Chapala")
 *   $tsj_description — meta description de la página (opcional)
 *   $tsj_extra_css   — ruta(s) adicional(es) de CSS del módulo (string o array)
 *   $tsj_head_extra  — HTML arbitrario de confianza a inyectar en <head>
 *   $tsj_has_hero    — true cuando la página tiene hero propio (header translúcido, sin offset)
 *   $tsj_no_security_headers — true para omitir security_headers (Convenios los emite propio)
 */

if (!defined('PLATAFORMA_URL')) {
    require_once __DIR__ . '/config.php';
}

if (empty($tsj_no_security_headers)) {
    require_once __DIR__ . '/security_headers.php';
}

$tsj_module      = $tsj_module      ?? '';
$tsj_title       = $tsj_title       ?? 'Tecnológico Superior de Jalisco';
$tsj_description = $tsj_description ?? 'Portal de servicios estudiantiles del Tecnológico Superior de Jalisco — Chapala.';
$tsj_extra_css   = $tsj_extra_css   ?? [];
$tsj_head_extra  = $tsj_head_extra  ?? '';
$tsj_has_hero    = $tsj_has_hero    ?? false;
/* Compatibilidad hacia atrás: $tsj_no_offset era true cuando había hero */
if (!isset($tsj_has_hero) && isset($tsj_no_offset)) {
    $tsj_has_hero = (bool)$tsj_no_offset;
}
if (is_string($tsj_extra_css)) {
    $tsj_extra_css = [$tsj_extra_css];
}

$base = PLATAFORMA_URL;

$nav_items = [
    'visitantes' => ['label' => 'Visitantes',  'href' => $base . '/modulos/visitantes/index.php'],
    'biblioteca' => ['label' => 'Biblioteca',   'href' => $base . '/modulos/biblioteca/buscar.php'],
    'convenios'  => ['label' => 'Convenios',    'href' => $base . '/modulos/convenios/index.php'],
    'horarios'   => ['label' => 'Buscar Maestro',              'href' => $base . '/modulos/horarios/index.php'],
    'requisitos' => ['label' => 'Serv. Social / Residencia', 'href' => $base . '/modulos/requisitos/residencia.php'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?= htmlspecialchars($tsj_description, ENT_QUOTES, 'UTF-8') ?>" />
  <title><?= htmlspecialchars($tsj_title, ENT_QUOTES, 'UTF-8') ?> — TSJ Chapala</title>
  <link rel="icon" type="image/png" href="<?= $base ?>/shared/assets/img/favicon.png" />

  <!-- Poppins + Material Icons: no bloquean render -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet" media="print" onload="this.media='all'" />
  <noscript>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
          rel="stylesheet" />
  </noscript>
  <style>
    .material-symbols-rounded {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      vertical-align: middle;
      line-height: 1;
    }
  </style>

  <link rel="stylesheet" href="<?= $base ?>/shared/assets/css/theme.css" />

  <?php foreach ($tsj_extra_css as $css): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($css, ENT_QUOTES, 'UTF-8') ?>" />
  <?php endforeach; ?>

  <?php if ($tsj_head_extra): /* Solo inyectar HTML de confianza (generado por el propio módulo) */ ?>
  <?= $tsj_head_extra ?>
  <?php endif; ?>
</head>
<body>

<!-- Skip link para navegación por teclado (WCAG 2.4.1) -->
<a class="tsj-skip-link" href="#main">Saltar al contenido principal</a>

<header class="tsj-header <?= $tsj_has_hero ? '' : 'tsj-header--solid' ?>" id="tsj-header">

  <!-- Franja de acento (firma del brand oficial) -->
  <div class="tsj-topbar" aria-hidden="true"></div>

  <div class="tsj-toolbar">

    <!-- Marca / logo -->
    <a class="tsj-header-brand" href="<?= $base ?>/"
       aria-label="Portal principal TSJ Chapala — Inicio">
      <img class="tsj-header-logo"
           src="<?= $base ?>/shared/assets/img/logo.svg"
           alt="" aria-hidden="true" />
      <span class="tsj-brand-sub">Campus Chapala</span>
    </a>

    <!-- Navegación escritorio (centrada absolutamente) -->
    <nav aria-label="Navegación principal">
      <ul class="tsj-nav">
        <?php foreach ($nav_items as $key => $item): ?>
          <li>
            <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
               <?php if ($tsj_module === $key): ?>
                 class="active" aria-current="page"
               <?php endif; ?>>
              <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <!-- Botón hamburguesa (móvil) -->
    <button class="tsj-menu-btn" id="tsj-menu-btn"
            aria-label="Abrir menú" aria-expanded="false" aria-controls="tsj-menu-panel">
      <span class="material-symbols-rounded" aria-hidden="true">menu</span>
    </button>

  </div>
</header>

<!-- Panel lateral móvil -->
<nav class="tsj-menu-panel" id="tsj-menu-panel"
     aria-label="Menú de navegación móvil">
  <div class="tsj-menu-content">

    <!-- Logo dentro del panel -->
    <div class="tsj-menu-logo">
      <img src="<?= $base ?>/shared/assets/img/logo.svg"
           alt="TSJ Chapala" height="36" />
      <span>TSJ<br>Campus Chapala</span>
    </div>

    <p class="tsj-menu-section">Portal</p>
    <a class="tsj-menu-item" href="<?= $base ?>/">
      <span class="material-symbols-rounded" aria-hidden="true">home</span>
      Inicio
    </a>

    <p class="tsj-menu-section">Módulos</p>
    <?php foreach ($nav_items as $key => $item): ?>
      <a class="tsj-menu-item <?= $tsj_module === $key ? 'active' : '' ?>"
         href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
         <?= $tsj_module === $key ? 'aria-current="page"' : '' ?>>
        <span class="material-symbols-rounded" aria-hidden="true"><?php
          $icons = [
            'visitantes' => 'badge',
            'biblioteca' => 'menu_book',
            'convenios'  => 'handshake',
            'horarios'   => 'calendar_month',
            'requisitos' => 'checklist',
          ];
          echo $icons[$key] ?? 'circle';
        ?></span>
        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
      </a>
    <?php endforeach; ?>

  </div>
</nav>

<!-- Offset para header fixed (omitido en páginas con hero propio) -->
<?php if (!$tsj_has_hero): ?>
<div class="tsj-body-offset" aria-hidden="true"></div>
<?php endif; ?>
