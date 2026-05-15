<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Dirección';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">
  <h1 class="vis-page-title">Dirección</h1>
  <div class="botones">
    <a href="DatosContacto.php" class="button">Datos de Contacto</a>
    <a href="SolicitarCita.php" class="button">Secretarías</a>
    <a href="Ubicacion.php" class="button">Ubicación en el Campus</a>
  </div>
  <a href="index.php" class="top-right" aria-label="Volver al menú principal">
    <img src="imagenes/casa.png" alt="">
  </a>
</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
