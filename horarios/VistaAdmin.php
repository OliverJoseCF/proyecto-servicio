<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config.php';

try {
    $pdo = getDB();
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$carreras = $pdo->query("SELECT id_carrera, nombre_carrera FROM Carreras")->fetchAll();

$busqueda   = isset($_GET['busqueda'])   ? trim($_GET['busqueda'])   : '';
$id_carrera = isset($_GET['id_carrera']) ? (int)$_GET['id_carrera'] : 0;

// --- ELIMINAR MAESTRO ---
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];

    // Obtener la ruta del archivo antes de borrar el registro
    $stmtRuta = $pdo->prepare("SELECT imagen_horario FROM Horarios WHERE id_profesor = :id");
    $stmtRuta->execute(['id' => $id]);
    $rutaArchivo = $stmtRuta->fetchColumn();

    // Borrar registros de BD
    $pdo->prepare("DELETE FROM Horarios   WHERE id_profesor = :id")->execute(['id' => $id]);
    $pdo->prepare("DELETE FROM Profesores WHERE id_profesor = :id")->execute(['id' => $id]);

    // Borrar archivo físico del servidor si existe
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador — TSJ Chapala</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <!-- BARRA ROSA -->
    <div class="up-header"></div>

    <!-- TOOLBAR -->
    <nav class="toolbar">
        <div class="toolbar-logo">
            <img class="imgJalisco" src="Imagenes/logoblacno.svg" alt="Logo TSJ">
        </div>

        <div class="toolbar-center">
            <img class="logos2" src="Imagenes/logos2.png" alt="Logos institucionales">
        </div>

        <div class="toolbar-right">
            <div class="home-icon">
                <a href="index.php" title="Ir a búsqueda">
                    <img src="Imagenes/home.svg" alt="Inicio" width="26" height="26">
                </a>
            </div>
            <!-- logout.php destruye la sesión correctamente -->
            <a class="cerrar-sesion texto-inter" href="logout.php">Cerrar Sesión</a>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <main class="main-content">

        <h1 class="section-title">ADMINISTRAR MAESTROS</h1>
        <p class="section-subtitle">BUSCAR MAESTRO</p>

        <!-- BUSCADOR -->
        <section class="buscador">
            <form method="GET" action="" class="form-busqueda">
                <input type="text" name="busqueda"
                       placeholder="Buscar por nombre o apellido"
                       value="<?= htmlspecialchars($busqueda) ?>">
                <select name="id_carrera" onchange="this.form.submit()">
                    <option value="0">Todas las carreras</option>
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>"
                            <?= $id_carrera == $c['id_carrera'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre_carrera']) ?>
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
                                    <td><?= htmlspecialchars($dato['nombre']) ?></td>
                                    <td><?= htmlspecialchars($dato['apellido']) ?></td>
                                    <td>
                                        <a href="<?= htmlspecialchars($dato['imagen_horario']) ?>"
                                           class="open-modal btn-horario">Ver Horario</a>
                                    </td>
                                    <td><?= htmlspecialchars($dato['nombre_carrera']) ?></td>
                                    <td class="acciones">
                                        <a href="AgregarMaestro.php?editar=<?= $dato['id_profesor'] ?>" class="btn-editar">Editar</a>
                                        <a href="#" class="btn-eliminar" onclick="confirmarEliminacion('<?= $dato['id_profesor'] ?>')">Eliminar</a>
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

    </main>

    <!-- MODAL -->
    <div id="modalHorario" class="modal" role="dialog" aria-modal="true">
        <div class="modal-overlay"></div>
        <div class="modal-box">
            <button class="modal-close" aria-label="Cerrar">×</button>
            <div id="modalContent" class="modal-content"></div>
        </div>
    </div>

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
        }).then(r => { if (r.isConfirmed) window.location.href = 'VistaAdmin.php?eliminar=' + id; });
    }
    </script>
</body>
</html>