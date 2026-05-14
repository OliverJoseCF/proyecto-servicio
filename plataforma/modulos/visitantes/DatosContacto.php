<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Datos de Contacto — TSJ Chapala';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">
  <h1 class="vis-page-title">Datos de Contacto</h1>

  <div class="contenido">
    <address class="cuadro" style="font-style:normal;">
      <p><strong>Nombre:</strong> Iliana Janett Hernández Partida</p>
      <p style="margin-top:8px;overflow-wrap:break-word;word-break:break-all;">
        <strong>Correo:</strong>
        <a href="mailto:IlianaJanettHernandezPartida@chapala.tecmm.edu.mx">
          IlianaJanettHernandezPartida@chapala.tecmm.edu.mx
        </a>
      </p>
    </address>
  </div>

  <a href="index.php" class="top-right" aria-label="Volver al menú principal">
    <img src="imagenes/casa.png" alt="">
  </a>
</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
