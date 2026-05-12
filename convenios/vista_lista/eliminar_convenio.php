<?php
require_once __DIR__ . '/../src/session.php';
require_once __DIR__ . '/../src/security_headers.php';
require_once __DIR__ . '/../src/lib/helpers.php';

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: ../index.php');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../src/pages/conexion.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: lista.php');
    exit();
}

$error          = null;
$nombreConvenio = '';

// POST: confirmación real de eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Petición inválida. Recarga la página e intenta de nuevo.';
    } else {
        $logoPath = '';
        try {
            $stmt = $conn->prepare('SELECT logo FROM convenios WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $logoPath = $row['logo'] ?? '';
            $stmt->close();

            $stmt = $conn->prepare('DELETE FROM convenios WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            // Eliminar logo DESPUÉS de confirmar el DELETE exitoso
            if (!empty($logoPath)) {
                eliminarLogo($logoPath);
            }

            header('Location: lista.php?mensaje=eliminado');
            exit();
        } catch (mysqli_sql_exception $e) {
            error_log('Error eliminar_convenio: ' . $e->getMessage());
            $error = 'Error al eliminar el convenio. Intente de nuevo.';
        }
    }
}

// GET: pantalla de confirmación
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $conn->prepare('SELECT nombre FROM convenios WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            $nombreConvenio = $resultado->fetch_assoc()['nombre'];
        } else {
            $stmt->close();
            $conn->close();
            header('Location: lista.php');
            exit();
        }
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        error_log('Error prepare eliminar fetch: ' . $e->getMessage());
        $error = 'Error interno. Contacte al administrador.';
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Convenio — TecSJ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="estilo/estilo.css">
    <style>
        .confirm-container { max-width:600px; margin:50px auto; padding:30px; background:#fff; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,.1); text-align:center; }
        .confirm-title   { color:#32129A; margin-bottom:20px; }
        .confirm-message { margin-bottom:30px; font-size:18px; line-height:1.6; }
        .btn-container   { display:flex; justify-content:center; gap:20px; }
        .btn             { padding:10px 20px; border:none; border-radius:4px; cursor:pointer; font-weight:bold; text-decoration:none; display:inline-block; }
        .btn-danger      { background-color:#dc3545; color:white; }
        .btn-secondary   { background-color:#6c757d; color:white; }
        .error-message   { color:#dc3545; margin-bottom:20px; padding:10px; background:#f8d7da; border:1px solid #f5c6cb; border-radius:4px; }
        .btn-cerrar-sesion { background-color:#d9534f; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; font-weight:bold; }
    </style>
</head>
<body>
    <div class="barra-rosa"></div>

    <header class="barra-purpura">
        <div class="logo-institucion">
            <img src="images/Grupo_10491.svg" alt="Tecnológico Superior de Jalisco" />
        </div>
        <nav class="menu-principal" aria-label="Menú principal">
            <a href="../index.php" style="color:white; text-decoration:none;">Inicio</a>
            <form method="POST" action="cerrar_sesion.php" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn-cerrar-sesion">Cerrar Sesión</button>
            </form>
        </nav>
    </header>

    <div class="fila-busqueda">
        <h2>Eliminar Convenio</h2>
        <div class="botones-container">
            <a href="lista.php" aria-label="Volver a la lista">
                <img src="img/icono-regresar.png" alt="" aria-hidden="true" class="btn-volver" />
            </a>
        </div>
    </div>

    <div class="confirm-container">
        <?php if ($error): ?>
            <div class="error-message" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <h2 class="confirm-title">Confirmar Eliminación</h2>
        <p class="confirm-message">
            ¿Está seguro de que desea eliminar el convenio con
            <strong><?= htmlspecialchars($nombreConvenio, ENT_QUOTES, 'UTF-8') ?></strong>?
        </p>
        <p class="confirm-message">Esta acción no se puede deshacer.</p>

        <div class="btn-container">
            <a href="lista.php" class="btn btn-secondary">Cancelar</a>
            <form action="eliminar_convenio.php?id=<?= $id ?>" method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </form>
        </div>
    </div>
</body>
</html>
