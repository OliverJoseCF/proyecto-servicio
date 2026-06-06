<?php
require_once __DIR__ . '/../../shared/lib/auth.php';

$flash_ok    = $_SESSION['flash_ok']    ?? null; unset($_SESSION['flash_ok']);
$flash_error = $_SESSION['flash_error'] ?? null; unset($_SESSION['flash_error']);

$tsj_module    = 'biblioteca';
$tsj_title     = 'Biblioteca — Catálogo de Libros';
$tsj_extra_css = ['assets/css/buscar.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">

  <div class="tsj-page-header">
    <div class="tsj-page-header-line"></div>
    <h1>Catálogo de <span class="tsj-accent">Libros</span></h1>
    <p class="tsj-page-header-sub">Busca y solicita préstamos del acervo bibliotecario</p>
  </div>

  <?php if ($flash_ok): ?>
  <div style="max-width:900px;margin:0 auto 8px;padding:0 20px">
    <div class="tsj-alert tsj-alert--success" role="alert"><?= htmlspecialchars($flash_ok, ENT_QUOTES, 'UTF-8') ?></div>
  </div>
  <?php elseif ($flash_error): ?>
  <div style="max-width:900px;margin:0 auto 8px;padding:0 20px">
    <div class="tsj-alert tsj-alert--error" role="alert"><?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?></div>
  </div>
  <?php endif; ?>

  <!-- Búsqueda -->
  <div style="max-width:1100px;margin:0 auto;padding:0 20px">
    <div class="bib-search" role="search">
      <div class="bib-search-field" style="flex:2;min-width:220px">
        <label for="searchInput">Buscar libro</label>
        <input type="text" id="searchInput"
               placeholder="Título o autor…" oninput="filtrarLibros()"
               aria-label="Buscar en el catálogo">
      </div>
      <div class="bib-search-field" style="min-width:160px">
        <label for="filterField">Buscar por</label>
        <select id="filterField" onchange="filtrarLibros()" aria-label="Filtrar por campo">
          <option value="all">Todos los campos</option>
          <option value="titulo">Título</option>
          <option value="autor">Autor</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Grid de libros -->
  <div style="max-width:1100px;margin:0 auto;padding:0 20px">
    <div class="bib-grid" id="tablaLibros">
      <div class="bib-empty">
        <span class="material-symbols-rounded">menu_book</span>
        <p>Cargando catálogo…</p>
      </div>
      <!-- Paginador — el JS lo muestra/oculta según sea necesario -->
      <div id="bib-paginador" class="bib-paginador" style="display:none">
        <button id="bib-pag-prev" class="bib-pag-btn" onclick="irPagina(-1)" aria-label="Página anterior">
          <span class="material-symbols-rounded">chevron_left</span>
        </button>
        <span id="bib-pag-info" class="bib-pag-info"></span>
        <button id="bib-pag-next" class="bib-pag-btn" onclick="irPagina(1)" aria-label="Página siguiente">
          <span class="material-symbols-rounded">chevron_right</span>
        </button>
      </div>
    </div>
  </div>

</main>

<script src="assets/js/buscar.js"></script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
