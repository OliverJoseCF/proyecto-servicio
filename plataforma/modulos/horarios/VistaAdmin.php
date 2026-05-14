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

$carreras = $pdo->query("SELECT id_carrera, nombre_carrera FROM Carreras")->fetchAll();

$busqueda   = isset($_GET['busqueda'])   ? trim($_GET['busqueda'])   : '';
$id_carrera = isset($_GET['id_carrera']) ? (int)$_GET['id_carrera'] : 0;

// --- ELIMINAR MAESTRO (POST + CSRF) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    if (!csrfVerify()) {
        die('Petición inválida.');
    }
    $id = (int)$_POST['eliminar_id'];

    $stmtRuta = $pdo->prepare("SELECT imagen_horario FROM Horarios WHERE id_profesor = :id");
    $stmtRuta->execute(['id' => $id]);
    $rutaArchivo = $stmtRuta->fetchColumn();

    $pdo->prepare("DELETE FROM Horarios   WHERE id_profesor = :id")->execute(['id' => $id]);
    $pdo->prepare("DELETE FROM Profesores WHERE id_profesor = :id")->execute(['id' => $id]);

    if ($rutaArchivo && file_exists(__DIR__ . '/' . $rutaArchivo)) {
        @unlink(__DIR__ . '/' . $rutaArchivo);
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

$sql  = "
    SELECT p.id_profesor, p.nombre, p.apellido,
           h.imagen_horario, c.nombre_carrera
    FROM   Horarios h
    JOIN   Profesores p ON h.id_profesor = p.id_profesor
    JOIN   Carreras   c ON h.id_carrera  = c.id_carrera
    $where
    ORDER BY p.apellido, p.nombre
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll();

$tsj_module    = 'horarios';
$tsj_title     = 'Horarios — Panel de Administración';
$tsj_extra_css = ['css/normalize.css', 'css/admin.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

    <!-- CONTENIDO -->
    <main class="main-content">

        <h1 class="section-title">ADMINISTRAR MAESTROS</h1>
        <p class="section-subtitle">BUSCAR MAESTRO</p>

        <!-- BUSCADOR -->
        <section class="buscador">
            <form method="GET" action="" class="form-busqueda">
                <input type="text" name="busqueda"
                       placeholder="Buscar por nombre o apellido"
                       value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>">
                <select name="id_carrera" onchange="this.form.submit()">
                    <option value="0">Todas las carreras</option>
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= (int)$c['id_carrera'] ?>"
                            <?= $id_carrera == $c['id_carrera'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre_carrera'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" value="BUSCAR">
            </form>
        </section>

        <!-- TABLA -->
        <section class="tabla-section">
            <div class="tabla-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Horario</th>
                            <th>Carrera</th>
                            <th>Acciones</th>
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
                                           class="open-modal btn-horario">Ver Horario</a>
                                    </td>
                                    <td><?= htmlspecialchars($dato['nombre_carrera'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="acciones">
                                        <a href="AgregarMaestro.php?editar=<?= (int)$dato['id_profesor'] ?>" class="btn-editar">Editar</a>
                                        <button type="button" class="btn-eliminar"
                                                onclick="confirmarEliminacion(<?= (int)$dato['id_profesor'] ?>)">Eliminar</button>
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
                <button type="submit" style="background:#ec5a68;color:#fff;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;font-weight:bold;">Cerrar sesión</button>
            </form>
        </div>

    </main>

    <!-- MODAL -->
    <div id="modalHorario" class="modal" role="dialog" aria-modal="true">
        <div class="modal-overlay"></div>
        <div class="modal-box">
            <button class="modal-close" aria-label="Cerrar">×</button>
            <div id="modalContent" class="modal-content"></div>
        </div>
    </div>

    <!-- Formulario oculto para borrado POST+CSRF -->
    <form id="formEliminar" method="POST" action="VistaAdmin.php" style="display:none;">
        <?= csrfField() ?>
        <input type="hidden" name="eliminar_id" id="eliminar_id_input">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/modal.js"></script>
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
        }).then(r => {
            if (r.isConfirmed) {
                document.getElementById('eliminar_id_input').value = id;
                document.getElementById('formEliminar').submit();
            }
        });
    }
    </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
