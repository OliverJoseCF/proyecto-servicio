<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Secretarías — TSJ Chapala';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>

  <h1>Secretarías</h1>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Rol</th>
          <th>Correo</th>
          <th>Teléfono</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="nombre">Laura Martínez</td>
          <td>Secretaria Administrativa</td>
          <td class="correo">laura.martinez@chapala.tecmm.edu.mx</td>
          <td>331-234-5678</td>
        </tr>
        <tr>
          <td class="nombre">María López</td>
          <td>Recepcionista</td>
          <td class="correo">maria.lopez@chapala.tecmm.edu.mx</td>
          <td>331-456-7890</td>
        </tr>
        <tr>
          <td class="nombre">Patricia Gómez</td>
          <td>Secretaria de Dirección</td>
          <td class="correo">patricia.gomez@chapala.tecmm.edu.mx</td>
          <td>332-567-8901</td>
        </tr>
        <tr>
          <td class="nombre">Ana Rivera</td>
          <td>Asistente Académica</td>
          <td class="correo">ana.rivera@chapala.tecmm.edu.mx</td>
          <td>333-678-9012</td>
        </tr>
        <tr>
          <td class="nombre">Gabriela Torres</td>
          <td>Secretaria de Control Escolar</td>
          <td class="correo">gabriela.torres@chapala.tecmm.edu.mx</td>
          <td>334-789-0123</td>
        </tr>
      </tbody>
    </table>
  </div>

  <a href="index.php" class="top-right">
    <img src="imagenes/casa.png" alt="Ir a inicio" style="width: 80px; height: auto;">
  </a>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
