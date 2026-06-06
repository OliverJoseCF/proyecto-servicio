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

// Estado de sesión global para mostrar botón admin en el header
if (!function_exists('isGlobalAdmin')) {
    require_once __DIR__ . '/lib/auth.php';
}
$_tsj_is_admin = isGlobalAdmin();

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
    'visitantes' => ['label' => 'Directorio',               'href' => $base . '/modulos/visitantes/Directorio.php', 'icon' => 'contacts'],
    'biblioteca' => ['label' => 'Biblioteca',               'href' => $base . '/modulos/biblioteca/buscar.php',     'icon' => 'menu_book'],
    'convenios'  => ['label' => 'Convenios',                'href' => $base . '/modulos/convenios/index.php',       'icon' => 'handshake'],
    'horarios'   => ['label' => 'Buscar Maestro',           'href' => $base . '/modulos/horarios/index.php',        'icon' => 'manage_search'],
    'requisitos' => ['label' => 'Serv. Social / Residencia','href' => $base . '/modulos/requisitos/residencia.php', 'icon' => 'checklist'],
];

// Sub-items de Oferta Académica (dropdown)
$oferta_carreras = [
    ['label' => 'Ing. en Sistemas Computacionales',           'href' => $base . '/modulos/visitantes/MateriasSistemas.php',   'icon' => 'computer'],
    ['label' => 'Ingeniería Industrial',                       'href' => $base . '/modulos/visitantes/MateriasIndustrial.php',  'icon' => 'precision_manufacturing'],
    ['label' => 'Ingeniería Mecatrónica',                      'href' => $base . '/modulos/visitantes/MateriasMecatronica.php', 'icon' => 'settings_suggest'],
    ['label' => 'Animación Digital y Efectos Visuales',        'href' => $base . '/modulos/visitantes/MateriasAnimacion.php',   'icon' => 'animation'],
    ['label' => 'Ing. en Gestión Empresarial',                 'href' => $base . '/modulos/visitantes/MateriasGestion.php',     'icon' => 'business_center'],
    ['label' => 'Gastronomía',                                 'href' => $base . '/modulos/visitantes/GastronomiaMaterias.php', 'icon' => 'restaurant'],
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

  <!-- Poppins + Material Symbols: carga directa (compatible con CSP sin unsafe-inline) -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <style>
    .material-symbols-rounded {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      vertical-align: middle;
      line-height: 1;
    }
  </style>

  <?php
  /* Cache-busting: la URL cambia cada vez que el archivo se modifica.
     SCRIPT_FILENAME es el .php del módulo que incluyó este header. */
  $theme_v   = filemtime(__DIR__ . '/assets/css/theme.css');
  $module_dir = dirname($_SERVER['SCRIPT_FILENAME']);
  ?>
  <link rel="stylesheet" href="<?= $base ?>/shared/assets/css/theme.css?v=<?= $theme_v ?>" />

  <?php foreach ($tsj_extra_css as $css): ?>
  <?php
    $abs    = $module_dir . '/' . ltrim($css, '/');
    $css_v  = file_exists($abs) ? filemtime($abs) : $theme_v;
  ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($css, ENT_QUOTES, 'UTF-8') ?>?v=<?= $css_v ?>" />
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

        <!-- Dropdown Oferta Académica -->
        <li class="tsj-has-dropdown">
          <a href="<?= $base ?>/modulos/visitantes/ofertaAcademica.php"
             <?= $tsj_module === 'oferta' ? 'class="active" aria-current="page"' : '' ?>
             aria-haspopup="true">
            Oferta Académica
            <span class="material-symbols-rounded tsj-dropdown-arrow" aria-hidden="true">expand_more</span>
          </a>
          <div class="tsj-dropdown" role="menu">
            <?php foreach ($oferta_carreras as $carrera): ?>
            <a href="<?= htmlspecialchars($carrera['href'], ENT_QUOTES, 'UTF-8') ?>" role="menuitem">
              <span class="material-symbols-rounded" aria-hidden="true"><?= $carrera['icon'] ?></span>
              <?= htmlspecialchars($carrera['label'], ENT_QUOTES, 'UTF-8') ?>
            </a>
            <?php endforeach; ?>
          </div>
        </li>
      </ul>
    </nav>

    <!-- Auth: botón de login o badge de admin -->
    <div class="tsj-header-auth">
      <?php if ($_tsj_is_admin): ?>
        <!-- Admin activo: badge enlaza al panel + logout -->
        <a href="<?= $base ?>/admin/" class="tsj-admin-badge" title="Ir al panel de administración">
          <span class="material-symbols-rounded" aria-hidden="true">shield_person</span>
          <span class="tsj-admin-badge-text">Admin</span>
        </a>
        <form method="POST" action="<?= $base ?>/logout.php" class="tsj-logout-form">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
          <button type="submit" class="tsj-logout-btn" aria-label="Cerrar sesión">
            <span class="material-symbols-rounded" aria-hidden="true">logout</span>
          </button>
        </form>
      <?php else: ?>
        <!-- Sin sesión: botón de acceso admin -->
        <a href="<?= $base ?>/login.php"
           class="tsj-login-btn"
           aria-label="Acceso administrador">
          <span class="material-symbols-rounded" aria-hidden="true">admin_panel_settings</span>
          <span class="tsj-login-btn-text">Admin</span>
        </a>
      <?php endif; ?>
    </div>

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
        <span class="material-symbols-rounded" aria-hidden="true"><?= htmlspecialchars($item['icon'] ?? 'circle') ?></span>
        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
      </a>
    <?php endforeach; ?>

    <!-- Oferta Académica con toggle en móvil -->
    <button class="tsj-menu-toggle <?= $tsj_module === 'oferta' ? 'open' : '' ?>"
            id="tsj-oferta-toggle" aria-expanded="<?= $tsj_module === 'oferta' ? 'true' : 'false' ?>">
      <span class="material-symbols-rounded" aria-hidden="true">school</span>
      Oferta Académica
      <span class="material-symbols-rounded tsj-toggle-arrow" aria-hidden="true">expand_more</span>
    </button>
    <div class="tsj-menu-subitems <?= $tsj_module === 'oferta' ? 'open' : '' ?>" id="tsj-oferta-subitems">
      <?php foreach ($oferta_carreras as $carrera): ?>
      <a class="tsj-menu-item" href="<?= htmlspecialchars($carrera['href'], ENT_QUOTES, 'UTF-8') ?>">
        <span class="material-symbols-rounded" aria-hidden="true"><?= $carrera['icon'] ?></span>
        <?= htmlspecialchars($carrera['label'], ENT_QUOTES, 'UTF-8') ?>
      </a>
      <?php endforeach; ?>
    </div>

  </div>
</nav>

<!-- Offset para header fixed (omitido en páginas con hero propio) -->
<?php if (!$tsj_has_hero): ?>
<div class="tsj-body-offset" aria-hidden="true"></div>
<?php endif; ?>

<script>
(function () {
  // ── Dropdown escritorio: toggle con click ────────────────────
  var liDropdown = document.querySelector('.tsj-nav li.tsj-has-dropdown');
  if (liDropdown) {
    var triggerLink = liDropdown.querySelector(':scope > a');

    triggerLink.addEventListener('click', function (e) {
      // Si ya está abierto y se hace clic, navegar a la página de oferta
      if (liDropdown.classList.contains('open')) {
        liDropdown.classList.remove('open');
        return; // deja que el href funcione normalmente
      }
      // Si está cerrado, abrir el dropdown sin navegar
      e.preventDefault();
      liDropdown.classList.add('open');
    });

    // Cerrar al hacer clic fuera
    document.addEventListener('click', function (e) {
      if (!liDropdown.contains(e.target)) {
        liDropdown.classList.remove('open');
      }
    });

    // Cerrar con ESC
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') liDropdown.classList.remove('open');
    });

    // Cerrar al seleccionar una carrera
    liDropdown.querySelectorAll('.tsj-dropdown a').forEach(function (a) {
      a.addEventListener('click', function () {
        liDropdown.classList.remove('open');
      });
    });
  }

  // ── Toggle Oferta Académica en menú móvil ───────────────────
  var toggle   = document.getElementById('tsj-oferta-toggle');
  var subitems = document.getElementById('tsj-oferta-subitems');
  if (toggle && subitems) {
    toggle.addEventListener('click', function () {
      var open = subitems.classList.toggle('open');
      toggle.classList.toggle('open', open);
      toggle.setAttribute('aria-expanded', open);
    });
  }
})();
</script>
