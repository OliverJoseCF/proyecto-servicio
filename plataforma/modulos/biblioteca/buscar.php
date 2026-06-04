<?php
require_once __DIR__ . '/../../shared/lib/auth.php';

$flash_ok    = $_SESSION['flash_ok']    ?? null; unset($_SESSION['flash_ok']);
$flash_error = $_SESSION['flash_error'] ?? null; unset($_SESSION['flash_error']);

$tsj_module    = 'biblioteca';
$tsj_title     = 'Biblioteca — Catálogo de Libros';
$tsj_extra_css = ['assets/css/buscar.css'];
$tsj_head_extra = '<link rel="stylesheet"'
    . ' href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"'
    . ' integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"'
    . ' crossorigin="anonymous" />'
    . '<link rel="stylesheet"'
    . ' href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"'
    . ' integrity="sha384-3B6NwesSXE7YJlcLI9RpRqGf2p/EgVH8BgoKTaUrmKNDkHPStTQ3EyoYjCGXaOTS"'
    . ' crossorigin="anonymous" />'
    . '<link rel="stylesheet"'
    . ' href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" />';
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">

  <?php if ($flash_ok): ?>
  <div class="container" style="padding-top:16px;">
    <div class="tsj-alert tsj-alert--success" role="alert"><?= htmlspecialchars($flash_ok, ENT_QUOTES, 'UTF-8') ?></div>
  </div>
  <?php elseif ($flash_error): ?>
  <div class="container" style="padding-top:16px;">
    <div class="tsj-alert tsj-alert--error" role="alert"><?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?></div>
  </div>
  <?php endif; ?>

  <div class="tsj-page-header">
    <div class="tsj-page-header-line"></div>
    <h1>Catálogo de <span class="tsj-accent">Libros</span></h1>
    <p class="tsj-page-header-sub">Busca y solicita préstamos del acervo bibliotecario</p>
  </div>

  <div class="container pb-5">

    <!-- Búsqueda -->
    <div class="search-card" role="search">
      <div class="row g-3 align-items-end">
        <div class="col-md-3 col-sm-4">
          <label for="filterField" class="search-label">Filtrar por</label>
          <select id="filterField" class="form-select" onchange="filtrarLibros()">
            <option value="all">Todos los campos</option>
            <option value="titulo">Título</option>
            <option value="autor">Autor</option>
          </select>
        </div>
        <div class="col-md-9 col-sm-8">
          <label for="searchInput" class="search-label">Buscar libro</label>
          <input type="text" id="searchInput" class="form-control"
                 placeholder="Escriba título o autor..." oninput="filtrarLibros()">
        </div>
      </div>
    </div>

    <!-- Tabla -->
    <div class="table-wrapper">
      <div class="table-responsive">
        <table class="table align-middle text-center" id="tablaLibros">
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Código</th>
              <th scope="col" class="text-start">Título / Estado</th>
              <th scope="col">Autor</th>
              <th scope="col">Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="5" class="empty-state">
                <i class="fas fa-spinner fa-spin d-block" aria-hidden="true"></i>
                Cargando catálogo…
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>

</main>

<script src="assets/js/buscar.js"></script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
