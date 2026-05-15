<?php
require_once __DIR__ . '/../src/security_headers.php';
require_once __DIR__ . '/../src/pages/conexion.php';

$carrerasValidas = ['IADEV','IM','ISC','II','LG','IGE'];
$carreraNombres  = [
    'IADEV' => 'Ingeniería en Animación Digital y Efectos Visuales',
    'IM'    => 'Ingeniería Mecatrónica',
    'ISC'   => 'Ingeniería en Sistemas Computacionales',
    'II'    => 'Ingeniería Industrial',
    'LG'    => 'Gastronomía',
    'IGE'   => 'Ingeniería en Gestión Empresarial',
];

$carrera = isset($_GET['carrera']) ? trim($_GET['carrera']) : '';
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
    }
}
if ($resultado === null) {
    try {
        $resultado = $conn->query('SELECT id, nombre, convenio, logo, contacto, vencimiento FROM convenios');
    } catch (mysqli_sql_exception $e) {
        error_log('Error vista_convenios fallback: ' . $e->getMessage());
    }
}
$conn->close();

$nombreCarrera = ($carrera !== '' && isset($carreraNombres[$carrera])) ? $carreraNombres[$carrera] : '';

$tsj_module    = 'convenios';
$tsj_title     = 'Convenios' . ($nombreCarrera ? ' — ' . $nombreCarrera : '');
$tsj_extra_css = [
    'estilo/estilo.css',
    'https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css',
];
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../../shared/header.php';
?>

<main id="main">

  <div class="fila-busqueda">
    <h1>Convenios<?= $nombreCarrera ? ' — ' . htmlspecialchars($nombreCarrera, ENT_QUOTES, 'UTF-8') : '' ?></h1>
    <div class="botones-container">
      <a href="../index.php" aria-label="Volver al inicio de convenios">
        <img src="img/icono-regresar.png" alt="" aria-hidden="true" class="btn-volver" />
      </a>
    </div>
  </div>

  <div class="datatable-container">
    <table id="example" class="display" style="width:100%">
      <thead>
        <tr>
          <th scope="col">Id</th>
          <th scope="col">Nombre</th>
          <th scope="col">Convenio</th>
          <th scope="col">Marca</th>
          <th scope="col">Contacto</th>
          <th scope="col">Vencimiento</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($resultado): ?>
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
                     width="50" height="50" style="object-fit:contain;"
                     alt="Logo de <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>" />
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($fila['contacto'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fechaVenc, ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
        <?php endwhile; ?>
      <?php endif; ?>
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
<script src="script_convenios.js" defer></script>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
