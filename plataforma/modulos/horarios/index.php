<?php
require_once __DIR__ . '/config.php';

try {
    $pdo = getDB();
} catch (PDOException $e) {
    error_log('horarios/index DB error: ' . $e->getMessage());
    die("Error de conexión. Contacta al administrador.");
}

// Carreras activas para el filtro
try {
    $carreras = $pdo->query("SELECT id AS id_carrera, nombre AS nombre_carrera FROM carreras WHERE activo=1 ORDER BY orden")->fetchAll();
} catch (\PDOException $e) {
    $carreras = [];
}

$busqueda         = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$id_carrera       = isset($_GET['id_carrera']) ? (int)$_GET['id_carrera'] : 0;
$items_por_pagina = 30;
$pagina_actual    = max(1, (int)($_GET['pagina'] ?? 1));
$offset           = ($pagina_actual - 1) * $items_por_pagina;

// Mostrar todos los profesores activos — si tienen horario se muestra, si no "Sin horario"
$where  = 'p.activo = 1';
$params = [];

if ($busqueda !== '') {
    $where .= ' AND (p.nombre LIKE :busqueda1 OR p.apellido LIKE :busqueda2)';
    $params[':busqueda1'] = "%$busqueda%";
    $params[':busqueda2'] = "%$busqueda%";
}
if ($id_carrera > 0) {
    $where .= ' AND h.id_carrera = :id_carrera';
    $params[':id_carrera'] = $id_carrera;
}

$sql_base = "
    SELECT h.id_horario,
           p.nombre          AS nombre_profesor,
           p.apellido        AS apellido_profesor,
           p.foto            AS foto_profesor,
           c.nombre          AS nombre_carrera,
           c.color           AS color_carrera,
           h.semestre,
           h.imagen_horario
    FROM   profesores p
    LEFT JOIN horarios  h ON h.id_profesor = p.id_profesor AND h.activo = 1
    LEFT JOIN carreras  c ON h.id_carrera  = c.id
    WHERE  $where
    ORDER BY p.apellido, p.nombre
";

$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM ($sql_base) AS t");
$stmt_count->execute($params);
$total_items   = (int)$stmt_count->fetchColumn();
$total_paginas = (int)ceil($total_items / $items_por_pagina);

$stmt = $pdo->prepare("$sql_base LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit',  $items_por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,           PDO::PARAM_INT);
$stmt->execute();
$resultados = $stmt->fetchAll();

$base_img = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/modulos/visitantes/imagenes/';

$tsj_module    = 'horarios';
$tsj_title     = 'Buscar Maestro';
$tsj_extra_css = ['css/Principal.css'];

require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main" class="hor-main">

  <div class="tsj-page-header">
    <div class="tsj-page-header-line"></div>
    <h1>Buscar <span class="tsj-accent">Maestro</span></h1>
    <p class="tsj-page-header-sub">Encuentra a tus maestros y consulta sus horarios por carrera</p>
  </div>

  <!-- Filtros -->
  <div class="hor-filtros">
    <form method="GET" action="" role="search" class="hor-form-busqueda">
      <div class="hor-search-wrap">
        <span class="material-symbols-rounded" aria-hidden="true">search</span>
        <input type="text" name="busqueda" id="busqueda-input"
               placeholder="Buscar por nombre del maestro…"
               value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>"
               aria-label="Buscar maestro">
        <?php if ($id_carrera > 0): ?>
          <input type="hidden" name="id_carrera" value="<?= $id_carrera ?>">
        <?php endif; ?>
      </div>
      <button type="submit" class="hor-btn-buscar">Buscar</button>
    </form>

    <form method="GET" action="" class="hor-form-carrera">
      <?php if ($busqueda !== ''): ?>
        <input type="hidden" name="busqueda" value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>">
      <?php endif; ?>
      <span class="material-symbols-rounded" aria-hidden="true">school</span>
      <select name="id_carrera" onchange="this.form.submit()" aria-label="Filtrar por carrera">
        <option value="0">Todas las carreras</option>
        <?php foreach ($carreras as $c): ?>
          <option value="<?= (int)$c['id_carrera'] ?>"
            <?= $id_carrera == $c['id_carrera'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nombre_carrera'], ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
      <noscript><button type="submit">Filtrar</button></noscript>
    </form>
  </div>

  <?php if ($busqueda !== '' || $id_carrera > 0): ?>
  <p class="hor-count">
    <?= $total_items ?> maestro<?= $total_items !== 1 ? 's' : '' ?> encontrado<?= $total_items !== 1 ? 's' : '' ?>
    <?php if ($busqueda): ?> para "<strong><?= htmlspecialchars($busqueda) ?></strong>"<?php endif; ?>
  </p>
  <?php endif; ?>

  <!-- Cards de maestros -->
  <?php if (empty($resultados)): ?>
  <div class="hor-empty">
    <span class="material-symbols-rounded">search_off</span>
    <p>No se encontraron maestros<?= ($busqueda || $id_carrera) ? ' con esos criterios' : ' con horario registrado' ?>.</p>
    <?php if ($busqueda || $id_carrera): ?>
    <a href="index.php" class="hor-reset">Ver todos</a>
    <?php endif; ?>
  </div>
  <?php else: ?>
  <div class="hor-grid">
    <?php foreach ($resultados as $h):
      $nombre = htmlspecialchars($h['nombre_profesor'] . ' ' . $h['apellido_profesor'], ENT_QUOTES, 'UTF-8');
      $color  = htmlspecialchars($h['color_carrera'] ?? '#32129a');
      $fotoSrc = $h['foto_profesor']
        ? $base_img . htmlspecialchars($h['foto_profesor'])
        : null;
      $placeholder = 'data:image/svg+xml,' . rawurlencode(
          '<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80">'
        . '<rect width="80" height="80" fill="#e5e7eb"/>'
        . '<circle cx="40" cy="30" r="16" fill="#9ca3af"/>'
        . '<path d="M10 80c0-17 13-27 30-27s30 10 30 27z" fill="#9ca3af"/>'
        . '</svg>'
      );
    ?>
    <div class="hor-card">
      <div class="hor-card-top" style="background:<?= $color ?>"></div>
      <div class="hor-card-body">
        <img class="hor-card-foto"
             src="<?= $fotoSrc ?? $placeholder ?>"
             alt="<?= $nombre ?>"
             <?= $fotoSrc ? 'onerror="this.src=\'' . $placeholder . '\'"' : '' ?>>
        <div class="hor-card-info">
          <div class="hor-card-nombre"><?= $nombre ?></div>
          <?php if ($h['nombre_carrera']): ?>
          <span class="hor-card-carrera" style="background:<?= $color ?>22;color:<?= $color ?>">
            <?= htmlspecialchars($h['nombre_carrera']) ?>
            <?= $h['semestre'] ? ' · ' . htmlspecialchars($h['semestre']) : '' ?>
          </span>
          <?php endif; ?>
        </div>
        <div class="hor-card-accion">
          <?php if ($h['imagen_horario']): ?>
            <a href="<?= htmlspecialchars($h['imagen_horario']) ?>"
               class="hor-btn-horario open-modal"
               aria-label="Ver horario de <?= $nombre ?>">
              <span class="material-symbols-rounded">calendar_month</span>
              Ver horario
            </a>
          <?php else: ?>
            <span class="hor-sin-horario">
              <span class="material-symbols-rounded">event_busy</span>
              Sin horario
            </span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Paginación -->
  <?php if ($total_paginas > 1): ?>
  <nav class="hor-paginacion" aria-label="Paginación">
    <span class="hor-pag-info">
      <?= ($offset + 1) ?>–<?= min($offset + $items_por_pagina, $total_items) ?> de <?= $total_items ?>
    </span>
    <div class="hor-pag-links">
      <?php $qp = $_GET; ?>
      <?php if ($pagina_actual > 1): ?>
        <a href="?<?= http_build_query(array_merge($qp, ['pagina' => $pagina_actual - 1])) ?>">
          <span class="material-symbols-rounded">chevron_left</span> Anterior
        </a>
      <?php endif; ?>
      <?php if ($pagina_actual < $total_paginas): ?>
        <a href="?<?= http_build_query(array_merge($qp, ['pagina' => $pagina_actual + 1])) ?>">
          Siguiente <span class="material-symbols-rounded">chevron_right</span>
        </a>
      <?php endif; ?>
    </div>
  </nav>
  <?php endif; ?>
  <?php endif; ?>

</main>

<!-- Modal horario -->
<div id="modalHorario" class="modal" role="dialog" aria-modal="true"
     aria-label="Horario del maestro" aria-hidden="true">
  <div class="modal-overlay"></div>
  <div class="modal-box">
    <button class="modal-close" aria-label="Cerrar">×</button>
    <div id="modalContent" class="modal-content"></div>
  </div>
</div>

<script src="js/modal.js?v=<?= filemtime(__DIR__ . '/js/modal.js') ?>" defer></script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
