<?php
require_once __DIR__ . '/../src/session.php';
require_once __DIR__ . '/../src/security_headers.php';

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: ../index.php');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../src/pages/conexion.php';

// Columnas explícitas: evita exponer datos futuros y reduce payload
$sql = 'SELECT id, nombre, convenio, logo, contacto, telefono, correo, vencimiento, carrera
        FROM convenios
        ORDER BY id DESC';

$resultado = $conn->query($sql);
$conn->close();

// Mensaje flash por URL — solo valores conocidos para evitar XSS
$mensajesValidos = ['agregado' => 'El convenio ha sido agregado correctamente.',
                    'editado'  => 'El convenio ha sido actualizado correctamente.',
                    'eliminado'=> 'El convenio ha sido eliminado correctamente.'];
$msgKey   = $_GET['mensaje'] ?? '';
$msgFlash = $mensajesValidos[$msgKey] ?? null;

$tsj_module     = 'convenios';
$tsj_title      = 'Convenios — Lista';
$tsj_extra_css  = ['estilo/estilo.css', 'https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css'];
$tsj_head_extra = '<style>
  .data-row td:not(.actions-cell) { cursor: pointer; }
  .actions-cell { white-space: nowrap; text-align: center; }
  .btn-agregar { background-color: #32129A; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-block; margin-left: 10px; }
  .btn-editar, .btn-eliminar-btn { display: inline-block; margin: 0 5px; font-size: 18px; text-decoration: none; background: none; border: none; cursor: pointer; padding: 0; }
  .btn-editar:hover, .btn-eliminar-btn:hover { transform: scale(1.2); }
  .alerta { padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align: center; }
  .alerta-exito { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
  .btn-cerrar-sesion { background-color: #d9534f; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; }
</style>';
require_once __DIR__ . '/../../shared/header.php';
?>

  <?php if ($msgFlash !== null): ?>
  <div class="alerta alerta-exito" role="alert" id="flashMsg">
    <?= htmlspecialchars($msgFlash, ENT_QUOTES, 'UTF-8') ?>
  </div>
  <?php endif; ?>

  <div class="fila-busqueda">
    <h2>Lista de convenios</h2>
    <div class="botones-container">
      <a href="../index.php" class="btn-volver-link" aria-label="Volver al inicio">
        <img src="img/icono-regresar.png" alt="" aria-hidden="true" class="btn-volver" />
      </a>
      <a href="agregar_convenio.php" class="btn-agregar">Agregar Convenio</a>
    </div>
  </div>

  <div class="datatable-container">
    <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>Id</th>
          <th>Nombre</th>
          <th>Convenio</th>
          <th>Carrera</th>
          <th>Marca</th>
          <th>Contacto</th>
          <th>Teléfono</th>
          <th>Correo</th>
          <th>Vencimiento</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($resultado): while ($fila = $resultado->fetch_assoc()): ?>
          <?php
            $logoFile  = !empty($fila['logo']) ? basename($fila['logo']) : '';
            $id        = (int) $fila['id'];
            $fechaVenc = ($t = strtotime($fila['vencimiento'])) ? date('d/m/Y', $t) : '—';
          ?>
          <tr class="data-row" data-href="../vista_empresa/index.php?id=<?= $id ?>">
            <td><?= $id ?></td>
            <td><?= htmlspecialchars($fila['nombre'],   ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['convenio'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['carrera'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td>
              <?php if ($logoFile): ?>
                <img src="../src/pages/upload/<?= htmlspecialchars($logoFile, ENT_QUOTES, 'UTF-8') ?>"
                     width="50"
                     alt="Logo de <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>" />
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($fila['contacto'],  ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['telefono'],  ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['correo'],    ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fechaVenc, ENT_QUOTES, 'UTF-8') ?></td>
            <td class="actions-cell">
              <a href="editar_convenio.php?id=<?= $id ?>" class="btn-editar" title="Editar convenio de <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>">✏️</a>
              <a href="eliminar_convenio.php?id=<?= $id ?>" class="btn-eliminar-btn" title="Eliminar convenio de <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>">🗑️</a>
            </td>
          </tr>
        <?php endwhile; endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Scripts DataTables -->
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
  <script src="script.js" defer></script>

  <script>
  // Autoocultar mensaje flash tras 4 segundos
  document.addEventListener('DOMContentLoaded', function () {
    var flash = document.getElementById('flashMsg');
    if (flash) {
      setTimeout(function () {
        flash.style.transition = 'opacity 0.5s';
        flash.style.opacity = '0';
        setTimeout(function () { flash.remove(); }, 500);
      }, 4000);
    }
  });
  </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
