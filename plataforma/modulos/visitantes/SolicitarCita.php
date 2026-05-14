<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Secretarías — TSJ Chapala';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">
  <h1 class="vis-page-title">Secretarías</h1>

  <div class="seccion" role="note" style="background:#fffbeb;border-left:4px solid #f59e0b;border-top:none;text-align:left;max-width:1000px;">
    <p style="color:#92400e;margin:0;font-size:0.92rem;">
      <strong>Aviso:</strong> La información mostrada a continuación es de referencia y puede actualizarse. Para datos verificados, contacta directamente al Departamento Académico.
    </p>
  </div>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th scope="col">Nombre</th>
          <th scope="col">Rol</th>
          <th scope="col">Correo</th>
          <th scope="col">Teléfono</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="nombre">Laura Martínez</td>
          <td>Secretaria Administrativa</td>
          <td class="correo"><a href="mailto:laura.martinez@chapala.tecmm.edu.mx">laura.martinez@chapala.tecmm.edu.mx</a></td>
          <td><a href="tel:+523312345678">331-234-5678</a></td>
        </tr>
        <tr>
          <td class="nombre">María López</td>
          <td>Recepcionista</td>
          <td class="correo"><a href="mailto:maria.lopez@chapala.tecmm.edu.mx">maria.lopez@chapala.tecmm.edu.mx</a></td>
          <td><a href="tel:+523314567890">331-456-7890</a></td>
        </tr>
        <tr>
          <td class="nombre">Patricia Gómez</td>
          <td>Secretaria de Dirección</td>
          <td class="correo"><a href="mailto:patricia.gomez@chapala.tecmm.edu.mx">patricia.gomez@chapala.tecmm.edu.mx</a></td>
          <td><a href="tel:+523325678901">332-567-8901</a></td>
        </tr>
        <tr>
          <td class="nombre">Ana Rivera</td>
          <td>Asistente Académica</td>
          <td class="correo"><a href="mailto:ana.rivera@chapala.tecmm.edu.mx">ana.rivera@chapala.tecmm.edu.mx</a></td>
          <td><a href="tel:+523336789012">333-678-9012</a></td>
        </tr>
        <tr>
          <td class="nombre">Gabriela Torres</td>
          <td>Secretaria de Control Escolar</td>
          <td class="correo"><a href="mailto:gabriela.torres@chapala.tecmm.edu.mx">gabriela.torres@chapala.tecmm.edu.mx</a></td>
          <td><a href="tel:+523347890123">334-789-0123</a></td>
        </tr>
      </tbody>
    </table>
  </div>

  <a href="index.php" class="top-right" aria-label="Volver al menú principal">
    <img src="imagenes/casa.png" alt="">
  </a>
</main>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
