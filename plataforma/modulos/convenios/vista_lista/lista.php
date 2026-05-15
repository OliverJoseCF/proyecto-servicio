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

$sql = 'SELECT id, nombre, convenio, logo, contacto, telefono, correo, vencimiento, carrera
        FROM convenios ORDER BY id DESC';
try {
    $resultado = $conn->query($sql);
} catch (mysqli_sql_exception $e) {
    error_log('lista.php convenios query error: ' . $e->getMessage());
    $resultado = false;
}
$conn->close();

$mensajesValidos = [
    'agregado'  => 'El convenio ha sido agregado correctamente.',
    'editado'   => 'El convenio ha sido actualizado correctamente.',
    'eliminado' => 'El convenio ha sido eliminado correctamente.',
];
$msgKey   = $_GET['mensaje'] ?? '';
$msgFlash = $mensajesValidos[$msgKey] ?? null;

$tsj_module    = 'convenios';
$tsj_title     = 'Convenios — Lista de Administración';
$tsj_extra_css = [
    'estilo/estilo.css',
    'https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css',
];
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../../shared/header.php';
?>

<main id="main">

  <h1 class="sr-only">Lista de Convenios — Administración</h1>

  <?php if ($msgFlash !== null): ?>
  <div class="alerta alerta-exito" role="alert" id="flashMsg">
    <?= htmlspecialchars($msgFlash, ENT_QUOTES, 'UTF-8') ?>
  </div>
  <?php endif; ?>

  <div class="fila-busqueda">
    <h2>Lista de convenios</h2>
    <div class="botones-container">
      <a href="../index.php" aria-label="Volver al inicio de convenios">
        <img src="img/icono-regresar.png" alt="" aria-hidden="true" class="btn-volver" />
      </a>
      <a href="agregar_convenio.php" class="btn-agregar">Agregar Convenio</a>
      <form method="POST" action="cerrar_sesion.php" style="display:inline; margin-left:8px;">
        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" class="btn-cerrar-sesion">Cerrar Sesión</button>
      </form>
    </div>
  </div>

  <div class="datatable-container">
    <table id="example" class="display" style="width:100%">
      <thead>
        <tr>
          <th scope="col">Id</th>
          <th scope="col">Nombre</th>
          <th scope="col">Convenio</th>
          <th scope="col">Carrera</th>
          <th scope="col">Marca</th>
          <th scope="col">Contacto</th>
          <th scope="col">Teléfono</th>
          <th scope="col">Correo</th>
          <th scope="col">Vencimiento</th>
          <th scope="col">Acciones</th>
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
                     width="50" height="50" style="object-fit:contain;"
                     alt="Logo de <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>" />
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($fila['contacto'],  ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['telefono'],  ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['correo'],    ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fechaVenc, ENT_QUOTES, 'UTF-8') ?></td>
            <td class="actions-cell">
              <a href="editar_convenio.php?id=<?= $id ?>" class="btn-editar"
                 title="Editar convenio de <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                 aria-label="Editar convenio de <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                <span aria-hidden="true">✏️</span>
              </a>
              <a href="eliminar_convenio.php?id=<?= $id ?>" class="btn-eliminar-btn"
                 title="Eliminar convenio de <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                 aria-label="Eliminar convenio de <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                <span aria-hidden="true">🗑️</span>
              </a>
            </td>
          </tr>
        <?php endwhile; endif; ?>
      </tbody>
    </table>
  </div>

</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous" defer></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"
        integrity="sha384-AenwROccLjIcbIsJuEZmrLlBzwrhvO94q+wm9RwETq4Kkqv9npFR2qbpdMhsehX3"
        crossorigin="anonymous" defer></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.jquery.min.js"
        integrity="sha384-ZSs6LKr2GoUPDyHrN+rCQgyHL1yUyok5xMniSrgeRG7rUvA6vTmxronM1eZOfjgz"
        crossorigin="anonymous" defer></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.min.js"
        integrity="sha384-1yo9s/77ZWiY2Xvn1BPaWyS3ErmUO+k734D+PxbLD2Iv8WJt4miQdnhv8IiMMY7j"
        crossorigin="anonymous" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"
        integrity="sha384-+mbV2IY1Zk/X1p/nWllGySJSUN8uMs+gUAN10Or95UBH0fpj6GfKgPmgC5EXieXG"
        crossorigin="anonymous" defer></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"
        integrity="sha384-MjweF+FY5MNbjB5ONlHWtlrou29MgBI/+acgSv4n5CBD79xUbMbLyka8NeCoK0D7"
        crossorigin="anonymous" defer></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"
        integrity="sha384-FvTRywo5HrkPlBKFrm2tT8aKxIcI/VU819roC/K/8UrVwrl4XsF3RKRKiCAKWNly"
        crossorigin="anonymous" defer></script>
<script src="script.js" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var flash = document.getElementById('flashMsg');
  if (flash) {
    setTimeout(function () {
      flash.style.transition = 'opacity 0.5s';
      flash.style.opacity = '0';
      setTimeout(function () { flash.remove(); }, 500);
    }, 6000);
  }
});
</script>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
