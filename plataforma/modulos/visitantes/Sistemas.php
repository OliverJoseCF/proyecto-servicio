<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Ingeniería en Sistemas Computacionales';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">
  <h1 class="vis-page-title">Ingeniería en Sistemas Computacionales</h1>
  <div class="botones">
    <a href="MateriasSistemas.php" class="button">Materias</a>
    <a href="DocentesSistemas.php" class="button">Docentes</a>
    <a href="CoordinadorSistemas.php" class="button">Coordinador/a</a>
  </div>
  <a href="index.php" class="top-right" aria-label="Volver al menú principal">
    <img src="imagenes/casa.png" alt="">
  </a>
</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
