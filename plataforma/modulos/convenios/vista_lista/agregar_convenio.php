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

$error = null;

$carreras = [
    'IADEV' => 'Ingeniería en Animación Digital y Efectos Visuales',
    'IM'    => 'Ingeniería Mecatrónica',
    'ISC'   => 'Ingeniería en Sistemas Computacionales',
    'II'    => 'Ingeniería Industrial',
    'LG'    => 'Gastronomía',
    'IGE'   => 'Ingeniería en Gestión Empresarial',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Petición inválida. Recarga la página e intenta de nuevo.';
    } else {
        $fields = [];
        $error  = validarCamposConvenio($_POST, $fields);

        $logoName = '';
        if ($error === null && isset($_FILES['logo'])) {
            $error = procesarLogo($_FILES['logo'], $logoName);
        }

        if ($error === null) {
            $sql = 'INSERT INTO convenios
                        (nombre, convenio, logo, contacto, telefono, correo, vencimiento, web, facebook, youtube, twitter, carrera)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

            try {
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssssssssssss',
                    $fields['empresa'], $fields['tipoConvenio'], $logoName,
                    $fields['contacto'], $fields['telefono'], $fields['email'],
                    $fields['vencimiento'], $fields['website'], $fields['facebook'],
                    $fields['youtube'], $fields['twitter'], $fields['carrera']
                );
                $stmt->execute();
                $stmt->close();
                $conn->close();
                header('Location: lista.php?mensaje=agregado');
                exit();
            } catch (mysqli_sql_exception $e) {
                error_log('Error agregar_convenio: ' . $e->getMessage());
                if (!empty($logoName)) eliminarLogo($logoName);
                $error = 'Error al registrar el convenio. Intente de nuevo.';
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $conn->close();
}

$tsj_module     = 'convenios';
$tsj_title      = 'Convenios — Agregar';
$tsj_extra_css  = ['estilo/estilo.css'];
$tsj_head_extra = '<style>
    .form-container { max-width:800px; margin:30px auto; padding:20px; background:#fff; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,.1); }
    .form-group { margin-bottom:15px; }
    .form-group label { display:block; margin-bottom:5px; font-weight:500; }
    .form-control { width:100%; padding:8px 12px; border:1px solid #ddd; border-radius:4px; font-family:\'Poppins\',sans-serif; }
    .btn-container { display:flex; justify-content:space-between; margin-top:20px; }
    .btn { padding:10px 20px; border:none; border-radius:4px; cursor:pointer; font-weight:bold; text-decoration:none; display:inline-block; }
    .btn-primary { background-color:#32129A; color:white; }
    .btn-secondary { background-color:#6c757d; color:white; }
    .error-message { color:#dc3545; margin-bottom:15px; text-align:center; padding:10px; background:#f8d7da; border:1px solid #f5c6cb; border-radius:4px; }
    .form-row { display:flex; gap:15px; }
    .form-row .form-group { flex:1; }
    @media(max-width:768px){ .form-row{ flex-direction:column; gap:0; } }
</style>';
require_once __DIR__ . '/../../shared/header.php';
?>

    <div class="fila-busqueda">
        <h2>Agregar Nuevo Convenio</h2>
        <div class="botones-container">
            <a href="lista.php" aria-label="Volver a la lista">
                <img src="img/icono-regresar.png" alt="" aria-hidden="true" class="btn-volver" />
            </a>
        </div>
    </div>

    <div class="form-container">
        <?php if ($error): ?>
            <div class="error-message" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="agregar_convenio.php" method="POST" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-row">
                <div class="form-group">
                    <label for="empresa">Nombre de la empresa *</label>
                    <input type="text" id="empresa" name="empresa" class="form-control" required maxlength="200">
                </div>
                <div class="form-group">
                    <label for="carrera">Carrera *</label>
                    <select id="carrera" name="carrera" class="form-control" required>
                        <option value="" disabled selected>Seleccione una carrera</option>
                        <?php foreach ($carreras as $clave => $nombre): ?>
                            <option value="<?= htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') ?>">
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
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="Servicio Social">Servicio Social</option>
                        <option value="Prácticas">Prácticas</option>
                        <option value="Ambos">Ambos</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="vencimiento">Fecha de vencimiento *</label>
                    <input type="date" id="vencimiento" name="vencimiento" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contacto">Persona de contacto *</label>
                    <input type="text" id="contacto" name="contacto" class="form-control" required maxlength="200">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono *</label>
                    <input type="tel" id="telefono" name="telefono" class="form-control" required maxlength="25"
                           pattern="[0-9+\-\s()]{7,25}" title="Solo números, +, -, espacios y paréntesis">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Correo electrónico *</label>
                    <input type="email" id="email" name="email" class="form-control" required maxlength="254">
                </div>
                <div class="form-group">
                    <label for="logo">Logo (JPG / PNG, máx. 10 MB)</label>
                    <input type="file" id="logo" name="logo" class="form-control" accept="image/jpeg,image/png,image/jpg">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="website">Sitio web</label>
                    <input type="url" id="website" name="website" class="form-control" placeholder="https://ejemplo.com" maxlength="500">
                </div>
                <div class="form-group">
                    <label for="facebook">Facebook</label>
                    <input type="url" id="facebook" name="facebook" class="form-control" placeholder="https://facebook.com/ejemplo" maxlength="500">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="youtube">YouTube</label>
                    <input type="url" id="youtube" name="youtube" class="form-control" placeholder="https://youtube.com/ejemplo" maxlength="500">
                </div>
                <div class="form-group">
                    <label for="twitter">X (Twitter)</label>
                    <input type="url" id="twitter" name="twitter" class="form-control" placeholder="https://x.com/ejemplo" maxlength="500">
                </div>
            </div>

            <div class="btn-container">
                <a href="lista.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Convenio</button>
            </div>
        </form>
    </div>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
