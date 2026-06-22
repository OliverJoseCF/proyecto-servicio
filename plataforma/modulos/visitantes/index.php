<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Portal Institucional — TSJ Campus Chapala';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/config.php';
try {
    $db = getPDO(DB_NAME);
    $carrerasMenu = $db->query('SELECT clave, nombre FROM carreras WHERE activo=1 ORDER BY orden')->fetchAll();
} catch (\Throwable $e) {
    $carrerasMenu = [];
}

require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">

  <div class="tsj-page-header">
    <div class="tsj-page-header-line"></div>
    <h1>Portal <span class="tsj-accent">Institucional</span></h1>
    <p class="tsj-page-header-sub">Directorio, planes de estudio y servicios del Campus Chapala</p>
  </div>

  <div class="menu-wrapper">

    <!-- ── Servicios principales ─────────────────────────── -->
    <h2 class="menu-section-label">Servicios</h2>
    <nav class="menu" aria-label="Servicios estudiantiles">
      <a href="nuevoIngreso.php">Nuevo Ingreso</a>
      <a href="Egresados.php">Re-inscripción</a>
      <a href="Ubicacion.php">Ubicación del Campus</a>
      <a href="Directorio.php">Directorio Institucional</a>
    </nav>

    <!-- ── Planes de estudio ─────────────────────────────── -->
    <h2 class="menu-section-label">Planes de Estudio</h2>
    <nav class="menu menu--carreras" aria-label="Planes de estudio por carrera">
      <?php foreach ($carrerasMenu as $c): ?>
      <a href="materias.php?carrera=<?= urlencode($c['clave']) ?>">
        <?= htmlspecialchars($c['nombre']) ?>
      </a>
      <?php endforeach; ?>
    </nav>

  </div>

</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
