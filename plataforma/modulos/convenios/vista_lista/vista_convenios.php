<?php
require_once __DIR__ . '/../src/security_headers.php';
require_once __DIR__ . '/../src/pages/conexion.php';

// Lista blanca de carreras válidas
$carrerasValidas = ['IADEV', 'IM', 'ISC', 'II', 'LG', 'IGE'];
$carrera = isset($_GET['carrera']) ? trim($_GET['carrera']) : '';

// Rechazar carreras no válidas antes de consultar
if ($carrera !== '' && !in_array($carrera, $carrerasValidas, true)) {
    $carrera = '';
}

$resultado = null;
if ($carrera !== '') {
    try {
        $stmt = $conn->prepare('SELECT id, nombre, convenio, logo, contacto, vencimiento FROM convenios WHERE carrera = ?');
        $stmt->bind_param('s', $carrera);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        error_log('Error vista_convenios: ' . $e->getMessage());
        $resultado = null;
    }
}

// Fallback: mostrar todos si no hay carrera o falló la consulta
if ($resultado === null) {
    try {
        $resultado = $conn->query('SELECT id, nombre, convenio, logo, contacto, vencimiento FROM convenios');
    } catch (mysqli_sql_exception $e) {
        error_log('Error vista_convenios fallback: ' . $e->getMessage());
    }
}

$conn->close();

$tituloCarrera = $carrera !== '' ? htmlspecialchars($carrera, ENT_QUOTES, 'UTF-8') : '';

$tsj_module     = 'convenios';
$tsj_title      = 'Convenios' . ($tituloCarrera ? ' — ' . $tituloCarrera : '');
$tsj_extra_css  = ['estilo/estilo.css', 'https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css'];
$tsj_head_extra = '<style>.data-row td { cursor: pointer; } .btn-volver { cursor: pointer; }</style>';
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../../shared/header.php';
?>

  <div class="fila-busqueda">
    <h2>Lista de convenios<?= $tituloCarrera ? ' — ' . $tituloCarrera : '' ?></h2>
    <div class="botones-container">
      <a href="../index.php" aria-label="Volver al inicio">
        <img src="img/icono-regresar.png" alt="" aria-hidden="true" class="btn-volver" />
      </a>
    </div>
  </div>

  <div class="datatable-container">
    <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>Id</th>
          <th>Nombre</th>
          <th>Convenio</th>
          <th>Marca</th>
          <th>Contacto</th>
          <th>Vencimiento</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($resultado && $resultado->num_rows > 0): ?>
        <?php while ($fila = $resultado->fetch_assoc()): ?>
          <?php
            $logoFile  = !empty($fila['logo']) ? basename($fila['logo']) : '';
            $id        = (int) $fila['id'];
            $fechaVenc = ($t = strtotime($fila['vencimiento'])) ? date('d/m/Y', $t) : '—';
          ?>
          <tr class="data-row" data-href="../vista_empresa/index.php?id=<?= $id ?>">
            <td><?= $id ?></td>
            <td><?= htmlspecialchars($fila['nombre'],   ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['convenio'], ENT_QUOTES, 'UTF-8') ?></td>
            <td>
              <?php if ($logoFile): ?>
                <img src="../src/pages/upload/<?= htmlspecialchars($logoFile, ENT_QUOTES, 'UTF-8') ?>"
                     width="50"
                     alt="Logo de <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>" />
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($fila['contacto'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fechaVenc, ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center;">No se encontraron convenios<?= $tituloCarrera ? ' para esta carrera' : '' ?>.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
          integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
          crossorigin="anonymous" defer></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js" defer></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.jquery.min.js" defer></script>
  <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>
  <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js" defer></script>
  <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js" defer></script>
  <script src="script_convenios.js" defer></script>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
