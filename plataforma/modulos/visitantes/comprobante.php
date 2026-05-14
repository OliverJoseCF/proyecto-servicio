<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Comprobantes — TSJ Chapala';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">
  <h1 class="vis-page-title">Comprobantes</h1>

  <div class="menu-wrapper">
    <h2 class="menu-section-label">Selecciona el comprobante que necesitas</h2>
    <nav class="menu menu--admin" aria-label="Selección de comprobante">
      <a href="Egresados.php">Comprobante de Reinscripción</a>
      <a href="nuevoIngreso.php">Comprobante de Examen de Admisión</a>
    </nav>
  </div>

  <a href="index.php" class="top-right" aria-label="Volver al menú principal">
    <img src="imagenes/casa.png" alt="">
  </a>
</main>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
