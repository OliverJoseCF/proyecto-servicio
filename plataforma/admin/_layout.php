<?php
/**
 * Layout compartido del panel de administración.
 * Incluir al INICIO de cada página admin (después de requireAuth).
 *
 * Variables esperadas antes de incluir:
 *   $adm_page      — slug de la sección activa (ej. 'biblioteca')
 *   $adm_title     — título de la página (ej. 'Gestión de Biblioteca')
 *   $adm_section   — breadcrumb secundario (opcional)
 */
if (!defined('PLATAFORMA_URL')) {
    require_once dirname(__DIR__) . '/shared/config.php';
}
$base     = PLATAFORMA_URL;
$adm_page = $adm_page    ?? 'dashboard';
$adm_title = $adm_title  ?? 'Panel de Administración';
$theme_v  = filemtime(dirname(__DIR__) . '/shared/assets/css/theme.css');
$admin_v  = filemtime(__DIR__ . '/assets/css/admin.css');

$nav = [
    'dashboard'     => ['label' => 'Resumen',                       'icon' => 'dashboard',        'href' => $base . '/admin/'],
    'inicio'        => ['label' => 'Página de inicio',              'icon' => 'home',             'href' => $base . '/admin/inicio.php'],
    'visitantes'    => ['label' => 'Directorio y carreras',         'icon' => 'groups',           'href' => $base . '/admin/visitantes.php'],
    'horarios'      => ['label' => 'Maestros y horarios',           'icon' => 'calendar_month',   'href' => $base . '/admin/horarios.php'],
    'requisitos'    => ['label' => 'Servicio social y residencias', 'icon' => 'checklist',        'href' => $base . '/admin/requisitos.php'],
    'biblioteca'    => ['label' => 'Biblioteca',                    'icon' => 'menu_book',        'href' => $base . '/admin/biblioteca.php'],
    'convenios'     => ['label' => 'Convenios',                     'icon' => 'handshake',        'href' => $base . '/admin/convenios.php'],
    'reportes'      => ['label' => 'Reportes y estadísticas',       'icon' => 'monitoring',       'href' => $base . '/admin/reportes.php'],
    'respaldos'     => ['label' => 'Respaldos de datos',            'icon' => 'backup',           'href' => $base . '/admin/respaldos.php'],
    'configuracion' => ['label' => 'Configuración',                 'icon' => 'settings',         'href' => $base . '/admin/configuracion.php'],
];

// Encabezado de sección que se muestra ANTES del ítem indicado (agrupa el menú).
$navSections = [
    'dashboard'  => 'Principal',
    'inicio'     => 'Contenido del portal',
    'visitantes' => 'Información académica',
    'biblioteca' => 'Servicios al estudiante',
    'reportes'   => 'Sistema',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($adm_title, ENT_QUOTES, 'UTF-8') ?> — Admin TSJ</title>
  <meta name="plataforma-url" content="<?= htmlspecialchars($base) ?>" />
  <link rel="icon" type="image/png" href="<?= $base ?>/shared/assets/img/favicon.png" />
  <!-- Fuentes self-hosted (Poppins + Material Symbols) — sin dependencia del CDN de Google -->
  <!-- Preload explícito: evita el flash de fuente al cambiar de panel (font-display:swap) -->
  <link rel="preload" as="font" type="font/woff2" crossorigin
        href="<?= $base ?>/shared/assets/fonts/poppins/poppins-400-latin.woff2" />
  <link rel="preload" as="font" type="font/woff2" crossorigin
        href="<?= $base ?>/shared/assets/fonts/poppins/poppins-500-latin.woff2" />
  <link rel="preload" as="font" type="font/woff2" crossorigin
        href="<?= $base ?>/shared/assets/fonts/poppins/poppins-600-latin.woff2" />
  <link rel="preload" as="font" type="font/woff2" crossorigin
        href="<?= $base ?>/shared/assets/fonts/poppins/poppins-700-latin.woff2" />
  <link rel="preload" as="font" type="font/woff2" crossorigin
        href="<?= $base ?>/shared/assets/fonts/material-symbols/material-symbols-rounded.woff2" />
  <link rel="stylesheet" href="<?= $base ?>/shared/assets/fonts/fonts.css" />
  <style>
    .material-symbols-rounded {
      font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;
      vertical-align: middle; line-height: 1;
      font-display: block;
    }
  </style>
  <link rel="stylesheet" href="<?= $base ?>/shared/assets/css/theme.css?v=<?= $theme_v ?>">
  <link rel="stylesheet" href="<?= $base ?>/admin/assets/css/admin.css?v=<?= $admin_v ?>">
</head>
<body>
<div class="adm-wrap">

  <!-- ── Sidebar ──────────────────────────────────────────────── -->
  <aside class="adm-sidebar" id="adm-sidebar">
    <div class="adm-sidebar-brand">
      <img class="adm-sidebar-logo"
           src="<?= $base ?>/shared/assets/img/logo.svg"
           alt="TSJ" />
      <div class="adm-sidebar-title">
        Panel Admin
        <span>TSJ Campus Chapala</span>
      </div>
    </div>

    <nav class="adm-nav" aria-label="Navegación del panel">
      <?php $_first_section = true; ?>
      <?php foreach ($nav as $key => $item): ?>
        <?php if (isset($navSections[$key])): ?>
          <span class="adm-nav-section"<?= $_first_section ? '' : ' style="margin-top:8px"' ?>><?= $navSections[$key] ?></span>
          <?php $_first_section = false; ?>
        <?php endif; ?>
        <a href="<?= $item['href'] ?>"
           class="adm-nav-link <?= $adm_page === $key ? 'active' : '' ?>">
          <span class="material-symbols-rounded" aria-hidden="true"><?= $item['icon'] ?></span>
          <?= $item['label'] ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="adm-sidebar-foot">
      <?php
        $_adm_nombre = function_exists('adminActualNombre') ? adminActualNombre() : 'Administrador';
        $_adm_email  = function_exists('adminActualEmail')  ? adminActualEmail()  : '';
        $_adm_inicial = mb_strtoupper(mb_substr($_adm_nombre, 0, 1));
        $_adm_esMaestra = (function_exists('adminActualId') && adminActualId() === 0);
      ?>
      <div class="adm-user-row">
        <div class="adm-user-avatar"><?= htmlspecialchars($_adm_inicial, ENT_QUOTES, 'UTF-8') ?></div>
        <div>
          <div class="adm-user-name"><?= htmlspecialchars($_adm_nombre, ENT_QUOTES, 'UTF-8') ?></div>
          <div class="adm-user-role"><?= $_adm_esMaestra ? 'Cuenta maestra' : htmlspecialchars($_adm_email, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      </div>
      <form method="POST" action="<?= $base ?>/logout.php">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" class="adm-logout-btn">
          <span class="material-symbols-rounded" aria-hidden="true">logout</span>
          Cerrar sesión
        </button>
      </form>
    </div>
  </aside>

  <!-- Overlay móvil -->
  <div class="adm-overlay" id="adm-overlay" onclick="closeSidebar()"></div>

  <!-- ── Main ─────────────────────────────────────────────────── -->
  <div class="adm-main">

    <!-- Topbar -->
    <header class="adm-topbar">
      <div class="adm-topbar-left">
        <button class="adm-hamburger" onclick="toggleSidebar()" aria-label="Menú">
          <span class="material-symbols-rounded">menu</span>
        </button>
        <nav class="adm-breadcrumb" aria-label="Breadcrumb">
          <a href="<?= $base ?>/admin/">Admin</a>
          <?php if ($adm_page !== 'dashboard'): ?>
            <span class="sep material-symbols-rounded" aria-hidden="true" style="font-size:16px">chevron_right</span>
            <span class="current"><?= htmlspecialchars($adm_title, ENT_QUOTES, 'UTF-8') ?></span>
          <?php endif; ?>
        </nav>
      </div>
      <div class="adm-topbar-right">
        <a href="<?= $base ?>/" class="adm-portal-link" target="_blank" rel="noopener noreferrer">
          <span class="material-symbols-rounded">open_in_new</span>
          Ver portal
        </a>
      </div>
    </header>

    <!-- Content -->
    <div class="adm-content">
