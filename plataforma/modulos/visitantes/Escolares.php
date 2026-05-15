<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Escolares';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">
  <h1 class="vis-page-title">Escolares</h1>
  <div class="botones">
    <a href="nuevoIngreso.php" class="button">Nuevo Ingreso</a>
    <a href="Egresados.php" class="button">Reinscripción</a>
    <a href="Titulacion.php" class="button">Titulación</a>
  </div>
  <a href="index.php" class="top-right" aria-label="Volver al menú principal">
    <img src="imagenes/casa.png" alt="">
  </a>
</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
