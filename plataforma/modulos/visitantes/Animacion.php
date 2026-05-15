<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Ingeniería en Animación Digital y Efectos Visuales';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">
  <h1 class="vis-page-title">Ingeniería en Animación Digital y Efectos Visuales</h1>
  <div class="botones">
    <a href="MateriasAnimacion.php" class="button">Materias</a>
    <a href="DocentesAnimacion.php" class="button">Docentes</a>
    <a href="coordinadorAnimacion.php" class="button">Coordinador/a</a>
  </div>
  <a href="index.php" class="top-right" aria-label="Volver al menú principal"><img src="imagenes/casa.png" alt=""></a>
</main>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
