<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Solicitar una Cita';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


  <h1>Secretarías</h1>

  <div class="table-container">
    <table>
      <tr>
        <th>Nombre</th>
        <th>Rol</th>
        <th>Correo</th>
        <th>Teléfono</th>
      </tr>
      <tr>
        <td class="nombre" contenteditable="true">Laura Martínez</td>
        <td contenteditable="true">Secretaria Administrativa</td>
        <td class="correo" contenteditable="true">laura.martinez@chapala.tecmm.edu.mx</td>
        <td contenteditable="true">331-234-5678</td>
      </tr>
      <tr>
        <td class="nombre" contenteditable="true">María López</td>
        <td contenteditable="true">Recepcionista</td>
        <td class="correo" contenteditable="true">maria.lopez@chapala.tecmm.edu.mx</td>
        <td contenteditable="true">331-456-7890</td>
      </tr>
      <tr>
        <td class="nombre" contenteditable="true">Patricia Gómez</td>
        <td contenteditable="true">Secretaria de Dirección</td>
        <td class="correo" contenteditable="true">patricia.gomez@chapala.tecmm.edu.mx</td>
        <td contenteditable="true">332-567-8901</td>
      </tr>
      <tr>
        <td class="nombre" contenteditable="true">Ana Rivera</td>
        <td contenteditable="true">Asistente Académica</td>
        <td class="correo" contenteditable="true">ana.rivera@chapala.tecmm.edu.mx</td>
        <td contenteditable="true">333-678-9012</td>
      </tr>
      <tr>
        <td class="nombre" contenteditable="true">Gabriela Torres</td>
        <td contenteditable="true">Secretaria de Control Escolar</td>
        <td class="correo" contenteditable="true">gabriela.torres@chapala.tecmm.edu.mx</td>
        <td contenteditable="true">334-789-0123</td>
      </tr>
    </table>
  </div>

  <a href="index.php" class="top-right">
    <img src="imagenes/casa.png" alt="Ir a otra página" style="width: 80px; height: auto;">
  </a>

  
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>