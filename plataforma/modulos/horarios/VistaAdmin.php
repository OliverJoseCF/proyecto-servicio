<?php
require_once __DIR__ . '/../../shared/lib/auth.php';
requireAuth('horarios', 'login.php');
require_once __DIR__ . '/config.php';

try {
    $pdo = getDB();
} catch (PDOException $e) {
    error_log('Horarios DB error: ' . $e->getMessage());
    die("Error de conexión. Contacta al administrador.");
}

try {
    $carreras = $pdo->query("SELECT id AS id_carrera, nombre AS nombre_carrera FROM carreras ORDER BY orden")->fetchAll();
} catch (\PDOException $e) {
    error_log('horarios/VistaAdmin carreras error: ' . $e->getMessage());
    $carreras = [];
}

$busqueda   = isset($_GET['busqueda'])   ? trim($_GET['busqueda'])   : '';
$id_carrera = isset($_GET['id_carrera']) ? (int)$_GET['id_carrera'] : 0;

/* ELIMINAR MAESTRO (POST + CSRF) */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    if (!csrfVerify()) { die('Petición inválida.'); }
    $id = (int)$_POST['eliminar_id'];

    $stmtRuta = $pdo->prepare("SELECT imagen_horario FROM horarios WHERE id_profesor = :id");
    $stmtRuta->execute(['id' => $id]);
    $rutaArchivo = $stmtRuta->fetchColumn();

    try {
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM horarios   WHERE id_profesor = :id")->execute(['id' => $id]);
        $pdo->prepare("DELETE FROM profesores WHERE id_profesor = :id")->execute(['id' => $id]);
        $pdo->commit();
        if ($rutaArchivo) {
            $safeFile = HORARIOS_DIR . basename($rutaArchivo);
            if (file_exists($safeFile)) @unlink($safeFile);
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('VistaAdmin eliminar error: ' . $e->getMessage());
    }
    header("Location: VistaAdmin.php");
    exit;
}

$where  = 'WHERE 1=1';
$params = [];
if ($busqueda !== '') {
    $where .= ' AND (p.nombre LIKE :busqueda1 OR p.apellido LIKE :busqueda2)';
    $params[':busqueda1'] = "%$busqueda%";
    $params[':busqueda2'] = "%$busqueda%";
}
if ($id_carrera > 0) {
    $where .= ' AND h.id_carrera = :id_carrera';
    $params[':id_carrera'] = $id_carrera;
}

$sql  = "SELECT p.id_profesor, p.nombre, p.apellido, h.imagen_horario, c.nombre AS nombre_carrera
         FROM horarios h
         JOIN profesores p ON h.id_profesor = p.id_profesor
         JOIN carreras   c ON h.id_carrera  = c.id
         $where ORDER BY p.apellido, p.nombre";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll();

$tsj_module    = 'horarios';
$tsj_title     = 'Horarios — Panel de Administración';
$tsj_extra_css = ['normalize.css', 'css/admin.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main" class="main-content">

  <h1 class="section-title">Administrar Maestros</h1>
  <p class="section-subtitle">Buscar Maestro</p>

  <!-- BUSCADOR -->
  <section class="buscador" aria-label="Búsqueda de maestros">
    <form method="GET" action="" class="form-busqueda">
      <label for="adm-busqueda" class="sr-only">Buscar por nombre o apellido</label>
      <input type="text" id="adm-busqueda" name="busqueda"
             placeholder="Buscar por nombre o apellido"
             value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>">
      <label for="adm-carrera" class="sr-only">Filtrar por carrera</label>
      <select id="adm-carrera" name="id_carrera" onchange="this.form.submit()">
        <option value="0">Todas las carreras</option>
        <?php foreach ($carreras as $c): ?>
          <option value="<?= (int)$c['id_carrera'] ?>"
              <?= $id_carrera == $c['id_carrera'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nombre_carrera'], ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
      <input type="submit" value="BUSCAR" aria-label="Buscar maestros">
    </form>
  </section>

  <!-- TABLA -->
  <section class="tabla-section" aria-label="Lista de maestros">
    <div class="tabla-wrapper">
      <table>
        <thead>
          <tr>
            <th scope="col">Nombre</th>
            <th scope="col">Apellido</th>
            <th scope="col">Horario</th>
            <th scope="col">Carrera</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($datos)): ?>
            <tr><td colspan="5" class="sin-resultados">No se encontraron resultados.</td></tr>
          <?php else: ?>
            <?php foreach ($datos as $dato): ?>
              <tr>
                <td><?= htmlspecialchars($dato['nombre'],        ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($dato['apellido'],       ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <a href="<?= htmlspecialchars($dato['imagen_horario'], ENT_QUOTES, 'UTF-8') ?>"
                     class="open-modal btn-horario" rel="noopener noreferrer"
                     aria-label="Ver horario de <?= htmlspecialchars($dato['nombre'] . ' ' . $dato['apellido'], ENT_QUOTES, 'UTF-8') ?>">
                    Ver Horario
                  </a>
                </td>
                <td><?= htmlspecialchars($dato['nombre_carrera'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="acciones">
                  <a href="AgregarMaestro.php?editar=<?= (int)$dato['id_profesor'] ?>"
                     class="btn-editar"
                     aria-label="Editar maestro <?= htmlspecialchars($dato['nombre'] . ' ' . $dato['apellido'], ENT_QUOTES, 'UTF-8') ?>">
                    Editar
                  </a>
                  <button type="button" class="btn-eliminar"
                          onclick="confirmarEliminacion(<?= (int)$dato['id_profesor'] ?>)"
                          aria-label="Eliminar maestro <?= htmlspecialchars($dato['nombre'] . ' ' . $dato['apellido'], ENT_QUOTES, 'UTF-8') ?>">
                    Eliminar
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <div class="agregar-btn-wrap">
    <a href="AgregarMaestro.php" class="boton-agregar">+ Agregar Maestro</a>
  </div>

  <!-- Logout -->
  <div style="text-align:right; padding: 1rem 2rem;">
    <form method="POST" action="Logout.php" style="display:inline;">
      <?= csrfField() ?>
      <button type="submit" class="tsj-btn tsj-btn--accent">Cerrar sesión</button>
    </form>
  </div>

</main>

<!-- MODAL -->
<div id="modalHorario" class="modal" role="dialog" aria-modal="true"
     aria-label="Ver horario del maestro" aria-hidden="true">
  <div class="modal-overlay"></div>
  <div class="modal-box">
    <button class="modal-close" aria-label="Cerrar horario">×</button>
    <div id="modalContent" class="modal-content"></div>
  </div>
</div>

<!-- Formulario oculto para borrado POST+CSRF -->
<form id="formEliminar" method="POST" action="VistaAdmin.php" style="display:none;">
  <?= csrfField() ?>
  <input type="hidden" name="eliminar_id" id="eliminar_id_input">
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"
        integrity="sha384-YB/DdIkloKoRpclWB8bNcYXWakt57USgtQPDzvnIDHYU0lasD5eWlXVo1S4ODukY"
        crossorigin="anonymous" defer></script>
<script src="js/modal.js" defer></script>
<script>
function confirmarEliminacion(id) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: 'Esta acción no se puede revertir.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#32129a',
    cancelButtonColor: '#ec5a68',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then(function (r) {
    if (r.isConfirmed) {
      document.getElementById('eliminar_id_input').value = id;
      document.getElementById('formEliminar').submit();
    }
  });
}
</script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
