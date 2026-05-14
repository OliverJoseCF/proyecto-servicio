<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Gastronomía — TSJ Chapala';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">
  <h1 class="vis-page-title">Gastronomía</h1>
  <div class="botones">
    <a href="GastronomiaMaterias.php" class="button">Materias</a>
    <a href="GastronomiaDocentes.php" class="button">Docentes</a>
    <a href="CoordinadorGastronomia.php" class="button">Coordinador/a</a>
  </div>
  <a href="index.php" class="top-right" aria-label="Volver al menú principal"><img src="imagenes/casa.png" alt=""></a>
</main>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
