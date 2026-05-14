<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Servicios Generales — TSJ Chapala';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">
  <a href="index.php" class="top-right" aria-label="Volver al menú principal">
    <img src="imagenes/casa.png" alt="">
  </a>

  <h1 class="vis-page-title">Servicios Generales</h1>

  <div class="contenido">
    <div class="seccion">
      <h2 class="vis-h2">Contacto</h2>
      <p><a href="mailto:auditorio@chapala.tecmm.edu.mx">auditorio@chapala.tecmm.edu.mx</a></p>
    </div>

    <div class="seccion">
      <h2 class="vis-h2">Jefe de Servicios Generales</h2>
      <p><strong>Nombre:</strong> Ing. Juan Pérez López</p>
      <p><strong>Teléfono:</strong> <a href="tel:3767651234">(376) 765 1234 ext. 105</a></p>
      <p><strong>Correo:</strong> <a href="mailto:juan.perez@chapala.tecmm.edu.mx">juan.perez@chapala.tecmm.edu.mx</a></p>
    </div>

    <div class="seccion">
      <h2 class="vis-h2">Servicios Ofrecidos a los Estudiantes</h2>
      <ul style="padding-left:1.5rem;margin-top:12px;line-height:1.8;color:#374151;">
        <li>Renta del Auditorio para eventos escolares</li>
        <li>Préstamo del Auditorio para actividades académicas</li>
        <li>Atención y recuperación de objetos perdidos</li>
        <li>Apoyo logístico para eventos institucionales</li>
      </ul>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
