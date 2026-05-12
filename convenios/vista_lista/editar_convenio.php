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

$carreras = [
    'IADEV' => 'Ingeniería en Animación Digital y Efectos Visuales',
    'IM'    => 'Ingeniería Mecatrónica',
    'ISC'   => 'Ingeniería en Sistemas Computacionales',
    'II'    => 'Ingeniería Industrial',
    'LG'    => 'Gastronomía',
    'IGE'   => 'Ingeniería en Gestión Empresarial',
];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: lista.php');
    exit();
}

$error   = null;

// Obtener datos actuales del convenio
$convenio = null;
try {
    $stmt = $conn->prepare('SELECT id, nombre, convenio, logo, contacto, telefono, correo, vencimiento, web, facebook, youtube, twitter, carrera FROM convenios WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $convenio  = $resultado->fetch_assoc();
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('Error prepare editar fetch: ' . $e->getMessage());
    $error = 'Error interno. Contacte al administrador.';
}

if ($convenio === null && $error === null) {
    $conn->close();
    header('Location: lista.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Petición inválida. Recarga la página e intenta de nuevo.';
    } else {
        $fields = [];
        $error  = validarCamposConvenio($_POST, $fields);

        // Procesar logo nuevo si se subió
        $newLogoName   = '';
        $logoSubido    = false;
        if ($error === null && isset($_FILES['logo'])) {
            $uploadError = procesarLogo($_FILES['logo'], $newLogoName);
            if ($uploadError !== null) {
                $error = $uploadError;
            } elseif ($newLogoName !== '') {
                $logoSubido = true;
            }
        }

        if ($error === null) {
            // Logo a guardar: nuevo si se subió, anterior si no
            $logoParaGuardar = $logoSubido ? $newLogoName : basename($convenio['logo'] ?? '');

            $sql = 'UPDATE convenios
                    SET nombre = ?, convenio = ?, logo = ?, contacto = ?, telefono = ?,
                        correo = ?, vencimiento = ?, web = ?, facebook = ?, youtube = ?, twitter = ?, carrera = ?
                    WHERE id = ?';

            try {
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssssssssssssi',
                    $fields['empresa'], $fields['tipoConvenio'], $logoParaGuardar,
                    $fields['contacto'], $fields['telefono'], $fields['email'],
                    $fields['vencimiento'], $fields['website'], $fields['facebook'],
                    $fields['youtube'], $fields['twitter'], $fields['carrera'], $id
                );
                $stmt->execute();
                $stmt->close();
                $conn->close();

                // Eliminar logo anterior SOLO si el UPDATE fue exitoso
                if ($logoSubido && !empty($convenio['logo'])) {
                    eliminarLogo($convenio['logo']);
                }

                // PRG: evita resubmisión al refrescar
                header('Location: lista.php?mensaje=editado');
                exit();
            } catch (mysqli_sql_exception $e) {
                error_log('Error editar_convenio update: ' . $e->getMessage());
                // Revertir logo nuevo si el UPDATE falló
                if ($logoSubido && !empty($newLogoName)) {
                    eliminarLogo($newLogoName);
                }
                $error = 'Error al actualizar el convenio. Intente de nuevo.';
            }
        } else {
            // Validación falló — limpiar logo nuevo si se subió
            if ($logoSubido && !empty($newLogoName)) {
                eliminarLogo($newLogoName);
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $conn->close();
}

$logoFile = !empty($convenio['logo']) ? basename($convenio['logo']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Convenio — TecSJ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="estilo/estilo.css">
    <style>
        .form-container { max-width:800px; margin:30px auto; padding:20px; background:#fff; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,.1); }
        .form-group { margin-bottom:15px; }
        .form-group label { display:block; margin-bottom:5px; font-weight:500; }
        .form-control { width:100%; padding:8px 12px; border:1px solid #ddd; border-radius:4px; font-family:'Poppins',sans-serif; }
        .btn-container { display:flex; justify-content:space-between; margin-top:20px; }
        .btn { padding:10px 20px; border:none; border-radius:4px; cursor:pointer; font-weight:bold; text-decoration:none; display:inline-block; }
        .btn-primary { background-color:#32129A; color:white; }
        .btn-secondary { background-color:#6c757d; color:white; }
        .alert { padding:15px; margin-bottom:20px; border-radius:4px; text-align:center; }
        .alert-danger  { background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        .current-logo { max-width:100px; margin-bottom:10px; display:block; }
        .form-row { display:flex; gap:15px; }
        .form-row .form-group { flex:1; }
        .btn-cerrar-sesion { background-color:#d9534f; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; font-weight:bold; }
        @media(max-width:768px){ .form-row{ flex-direction:column; gap:0; } }
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
        <h2>Editar Convenio</h2>
        <div class="botones-container">
            <a href="lista.php" aria-label="Volver a la lista">
                <img src="img/icono-regresar.png" alt="" aria-hidden="true" class="btn-volver" />
            </a>
        </div>
    </div>

    <div class="form-container">
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="editar_convenio.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-row">
                <div class="form-group">
                    <label for="empresa">Nombre de la empresa *</label>
                    <input type="text" id="empresa" name="empresa" class="form-control" required maxlength="200"
                           value="<?= htmlspecialchars($convenio['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label for="carrera">Carrera *</label>
                    <select id="carrera" name="carrera" class="form-control" required>
                        <option value="" disabled>Seleccione una carrera</option>
                        <?php foreach ($carreras as $clave => $nombre): ?>
                            <option value="<?= htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') ?>"
                                <?= ($convenio['carrera'] ?? '') === $clave ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="tipoConvenio">Tipo de convenio *</label>
                    <select id="tipoConvenio" name="tipoConvenio" class="form-control" required>
                        <option value="" disabled>Seleccione una opción</option>
                        <option value="Servicio Social" <?= ($convenio['convenio'] ?? '') === 'Servicio Social' ? 'selected' : '' ?>>Servicio Social</option>
                        <option value="Prácticas"       <?= ($convenio['convenio'] ?? '') === 'Prácticas'       ? 'selected' : '' ?>>Prácticas</option>
                        <option value="Ambos"           <?= ($convenio['convenio'] ?? '') === 'Ambos'           ? 'selected' : '' ?>>Ambos</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="vencimiento">Fecha de vencimiento *</label>
                    <input type="date" id="vencimiento" name="vencimiento" class="form-control" required
                           value="<?= htmlspecialchars($convenio['vencimiento'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contacto">Persona de contacto *</label>
                    <input type="text" id="contacto" name="contacto" class="form-control" required maxlength="200"
                           value="<?= htmlspecialchars($convenio['contacto'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono *</label>
                    <input type="tel" id="telefono" name="telefono" class="form-control" required maxlength="25"
                           pattern="[0-9+\-\s()]{7,25}" title="Solo números, +, -, espacios y paréntesis"
                           value="<?= htmlspecialchars($convenio['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Correo electrónico *</label>
                    <input type="email" id="email" name="email" class="form-control" required maxlength="254"
                           value="<?= htmlspecialchars($convenio['correo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label for="logo">Logo (JPG / PNG, máx. 10 MB)</label>
                    <?php if ($logoFile): ?>
                        <img src="../src/pages/upload/<?= htmlspecialchars($logoFile, ENT_QUOTES, 'UTF-8') ?>"
                             alt="Logo actual de <?= htmlspecialchars($convenio['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                             class="current-logo">
                        <p class="text-sm text-gray-500">Seleccione un nuevo logo solo si desea cambiarlo.</p>
                    <?php endif; ?>
                    <input type="file" id="logo" name="logo" class="form-control" accept="image/jpeg,image/png,image/jpg">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="website">Sitio web</label>
                    <input type="url" id="website" name="website" class="form-control" placeholder="https://ejemplo.com" maxlength="500"
                           value="<?= htmlspecialchars($convenio['web'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label for="facebook">Facebook</label>
                    <input type="url" id="facebook" name="facebook" class="form-control" placeholder="https://facebook.com/ejemplo" maxlength="500"
                           value="<?= htmlspecialchars($convenio['facebook'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="youtube">YouTube</label>
                    <input type="url" id="youtube" name="youtube" class="form-control" placeholder="https://youtube.com/ejemplo" maxlength="500"
                           value="<?= htmlspecialchars($convenio['youtube'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label for="twitter">X (Twitter)</label>
                    <input type="url" id="twitter" name="twitter" class="form-control" placeholder="https://x.com/ejemplo" maxlength="500"
                           value="<?= htmlspecialchars($convenio['twitter'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <div class="btn-container">
                <a href="lista.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</body>
</html>
