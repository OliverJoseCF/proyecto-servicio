<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Titulación';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';

/* Verificar si el PDF de solicitud existe */
$pdfPath  = __DIR__ . '/documentos/SolicitudTitulacion.pdf';
$pdfExiste = file_exists($pdfPath);
?>
<main id="main">
  <h1 class="vis-page-title">Titulación</h1>

  <div class="contenido">
    <div class="titulacion">
      <p>Bienvenidos a la sección de egresados. Aquí encontrarás toda la información necesaria para tu proceso de titulación.</p>

      <h2 class="vis-h3">Requisitos de Titulación</h2>
      <p>Para obtener tu título, es necesario cumplir con los siguientes requisitos:</p>
      <ul>
        <li>Haber completado todos los créditos requeridos por tu programa educativo.</li>
        <li>Tener el servicio social y residencia profesional liberados.</li>
        <li>Presentar un historial académico actualizado.</li>
        <li>Realizar el pago de los derechos de titulación.</li>
        <li>Contar con identificación oficial vigente.</li>
      </ul>

      <p style="margin-top:20px;">Para iniciar el proceso de titulación:</p>

      <?php if ($pdfExiste): ?>
        <a href="documentos/SolicitudTitulacion.pdf" class="download-button"
           download="Solicitud_Titulacion.pdf"
           aria-label="Descargar Solicitud de Titulación en PDF">
          Descargar Solicitud de Titulación
        </a>
      <?php else: ?>
        <p style="background:#fffbeb;border-left:4px solid #f59e0b;color:#92400e;padding:12px 16px;border-radius:6px;margin-top:12px;">
          La solicitud aún no está disponible para descarga. Por favor acude al Departamento de Servicios Escolares para obtenerla.
        </p>
      <?php endif; ?>
    </div>
  </div>

  <a href="index.php" class="top-right" aria-label="Volver al menú principal">
    <img src="imagenes/casa.png" alt="">
  </a>
</main>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
