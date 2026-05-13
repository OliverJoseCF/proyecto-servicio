<?php
/**
 * Header unificado de la plataforma TSJ.
 *
 * Variables que el módulo puede definir antes de incluir este archivo:
 *   $tsj_module     — slug activo: 'visitantes'|'biblioteca'|'convenios'|'horarios'|'requisitos'
 *   $tsj_title      — texto del <title>
 *   $tsj_extra_css  — ruta(s) adicional(es) de CSS del módulo (string o array)
 *   $tsj_head_extra — HTML arbitrario a inyectar en <head>
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
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
  <link rel="stylesheet" href="<?= $base ?>/shared/assets/css/theme.css" />
  <?php foreach ($tsj_extra_css as $css): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($css, ENT_QUOTES, 'UTF-8') ?>" />
  <?php endforeach; ?>
  <?php if ($tsj_head_extra): ?>
  <?= $tsj_head_extra ?>
  <?php endif; ?>

<style>
/* ── Header profesional TSJ ──────────────────────────────── */
.tsj-header {
  position: fixed;
  top: 0; left: 0;
  width: 100%;
  z-index: 9999;
  font-family: 'Poppins', Arial, sans-serif;
}

/* Franja superior rosa */
.tsj-top-bar {
  height: 3px;
  background: linear-gradient(90deg, #ec5a68 0%, #f5a623 50%, #ec5a68 100%);
}

/* Barra principal */
.tsj-toolbar {
  background: #1a0960;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 32px;
  height: 80px;
  box-shadow: 0 2px 20px rgba(0,0,0,.4);
}

/* Bloque izquierdo: logo + campus */
.tsj-header-brand {
  display: flex;
  align-items: center;
  gap: 16px;
  text-decoration: none;
  flex-shrink: 0;
}
.tsj-header-logo {
  height: 54px;
  width: auto;
  filter: brightness(0) invert(1);
  opacity: .92;
  transition: opacity .2s;
}
.tsj-header-brand:hover .tsj-header-logo { opacity: 1; }
.tsj-brand-sub {
  color: rgba(255,255,255,.5);
  font-size: 11px;
  font-weight: 400;
  letter-spacing: 2px;
  text-transform: uppercase;
  border-left: 1px solid rgba(255,255,255,.2);
  padding-left: 16px;
}

/* Divisor vertical */
.tsj-header-divider {
  width: 1px;
  height: 32px;
  background: rgba(255,255,255,.15);
  margin: 0 24px;
  flex-shrink: 0;
}

/* Navegación escritorio */
.tsj-nav {
  display: flex;
  align-items: center;
  gap: 4px;
  list-style: none;
  margin: 0;
  padding: 0;
  flex: 1;
}
.tsj-nav li a {
  display: block;
  color: rgba(255,255,255,.7);
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  letter-spacing: .3px;
  padding: 8px 14px;
  border-radius: 6px;
  transition: color .2s, background .2s;
  position: relative;
  white-space: nowrap;
}
.tsj-nav li a:hover {
  color: #fff;
  background: rgba(255,255,255,.08);
}
.tsj-nav li a.active {
  color: #fff;
  font-weight: 600;
  background: rgba(255,255,255,.12);
}
.tsj-nav li a.active::after {
  content: '';
  position: absolute;
  bottom: 4px;
  left: 14px;
  right: 14px;
  height: 2px;
  background: #ec5a68;
  border-radius: 2px;
}

/* Hamburguesa */
.tsj-menu-btn {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 6px;
  border-radius: 6px;
  transition: background .2s;
  margin-left: 8px;
}
.tsj-menu-btn:hover { background: rgba(255,255,255,.1); }
.tsj-menu-btn img {
  width: 22px;
  height: 22px;
  display: block;
  filter: brightness(0) invert(1);
}

/* Panel lateral móvil */
.tsj-menu-panel {
  position: fixed;
  top: 83px;
  left: -280px;
  width: 260px;
  height: calc(100vh - 71px);
  background: #fff;
  box-shadow: 4px 0 20px rgba(0,0,0,.12);
  transition: left .28s ease;
  z-index: 9998;
  overflow-y: auto;
}
.tsj-menu-panel.active { left: 0; }
.tsj-menu-content { padding: 16px 12px; }
.tsj-menu-item {
  display: block;
  color: #1a0960;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 2px;
  transition: background .18s, color .18s;
}
.tsj-menu-item:hover { background: #f0eef8; }
.tsj-menu-item.active {
  background: #1a0960;
  color: #fff;
}

/* Offset body */
.tsj-body-offset { margin-top: 83px; }

/* Responsive */
@media (max-width: 820px) {
  .tsj-nav { display: none; }
  .tsj-menu-btn { display: block; }
  .tsj-toolbar { padding: 0 20px; }
  .tsj-brand-sub { display: none; }
  .tsj-header-divider { display: none; }
}
@media (max-width: 480px) {
  .tsj-header-brand-text { display: none; }
  .tsj-header-logo { height: 36px; }
}
</style>
</head>
<body>

<header class="tsj-header" id="tsj-header">
  <div class="tsj-top-bar"></div>
  <div class="tsj-toolbar">

    <!-- Logo + campus -->
    <a class="tsj-header-brand" href="<?= $base ?>/" aria-label="Portal principal TSJ Chapala">
      <img class="tsj-header-logo"
           src="<?= $base ?>/shared/assets/img/logo.svg"
           alt="Tecnológico Superior de Jalisco" />
      <span class="tsj-brand-sub">Campus Chapala</span>
    </a>

    <div class="tsj-header-divider"></div>

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

    <!-- Hamburguesa -->
    <button class="tsj-menu-btn" id="tsj-menu-btn"
            aria-label="Abrir menú" aria-expanded="false" aria-controls="tsj-menu-panel">
      <img src="<?= $base ?>/shared/assets/img/menu.svg" alt="" aria-hidden="true" id="tsj-menu-icon" />
    </button>

  </div>
</header>

<!-- Panel lateral móvil -->
<nav class="tsj-menu-panel" id="tsj-menu-panel" aria-label="Menú móvil">
  <div class="tsj-menu-content">
    <a class="tsj-menu-item" href="<?= $base ?>/">← Portal principal</a>
    <?php foreach ($nav_items as $key => $item): ?>
      <a class="tsj-menu-item <?= $tsj_module === $key ? 'active' : '' ?>"
         href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>">
        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
      </a>
    <?php endforeach; ?>
  </div>
</nav>

<!-- Offset para header fixed -->
<div class="tsj-body-offset"></div>
