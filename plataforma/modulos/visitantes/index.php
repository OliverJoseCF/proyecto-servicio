<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Portal Institucional — TSJ Campus Chapala';
$tsj_extra_css  = ['style.css'];
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
      <a href="MateriasSistemas.php">Ing. en Sistemas Computacionales</a>
      <a href="MateriasIndustrial.php">Ing. Industrial</a>
      <a href="MateriasMecatronica.php">Ing. Mecatrónica</a>
      <a href="MateriasAnimacion.php">Ing. en Animación Digital y Efectos Visuales</a>
      <a href="MateriasGestion.php">Ing. en Gestión Empresarial</a>
      <a href="GastronomiaMaterias.php">Gastronomía</a>
    </nav>

  </div>

</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
