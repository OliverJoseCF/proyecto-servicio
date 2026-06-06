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
$carreras = [];
try {
    require_once __DIR__ . '/../../../shared/config.php';
    $dbC = getPDO(DB_NAME);
    foreach ($dbC->query('SELECT clave, nombre FROM carreras WHERE activo=1 ORDER BY orden')->fetchAll() as $row) {
        $carreras[$row['clave']] = $row['nombre'];
    }
} catch (\Throwable $e) {}

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
if ($_SERVER['REQUEST_METHOD'] !== 'POST') $conn->close();

$tsj_module    = 'convenios';
$tsj_title     = 'Convenios — Agregar';
$tsj_extra_css = ['estilo/estilo.css', 'estilo/form-crud.css'];
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../../shared/header.php';
?>

<main id="main">

  <div class="fila-busqueda">
    <h1>Agregar Nuevo Convenio</h1>
    <div class="botones-container">
      <a href="lista.php" aria-label="Volver a la lista de convenios">
        <img src="img/icono-regresar.png" alt="" aria-hidden="true" class="btn-volver" />
      </a>
    </div>
  </div>

  <div class="form-container">
    <?php if ($error): ?>
      <div class="error-message" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form action="agregar_convenio.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token"
             value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

      <div class="form-row">
        <div class="form-group">
          <label for="agr-empresa">Nombre de la empresa <span aria-hidden="true">*</span></label>
          <input type="text" id="agr-empresa" name="empresa" class="form-control" required maxlength="200">
        </div>
        <div class="form-group">
          <label for="agr-carrera">Carrera <span aria-hidden="true">*</span></label>
          <select id="agr-carrera" name="carrera" class="form-control" required>
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
          <label for="agr-tipo">Tipo de convenio <span aria-hidden="true">*</span></label>
          <select id="agr-tipo" name="tipoConvenio" class="form-control" required>
            <option value="" disabled selected>Seleccione una opción</option>
            <option value="Servicio Social">Servicio Social</option>
            <option value="Prácticas">Prácticas</option>
            <option value="Ambos">Ambos</option>
          </select>
        </div>
        <div class="form-group">
          <label for="agr-vencimiento">Fecha de vencimiento <span aria-hidden="true">*</span></label>
          <input type="date" id="agr-vencimiento" name="vencimiento" class="form-control" required
                 min="<?= date('Y-m-d') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="agr-contacto">Persona de contacto <span aria-hidden="true">*</span></label>
          <input type="text" id="agr-contacto" name="contacto" class="form-control" required maxlength="200">
        </div>
        <div class="form-group">
          <label for="agr-telefono">Teléfono <span aria-hidden="true">*</span></label>
          <input type="tel" id="agr-telefono" name="telefono" class="form-control" required maxlength="25"
                 pattern="[0-9+\-\s()]{7,25}" title="Solo números, +, -, espacios y paréntesis">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="agr-email">Correo electrónico <span aria-hidden="true">*</span></label>
          <input type="email" id="agr-email" name="email" class="form-control" required maxlength="254">
        </div>
        <div class="form-group">
          <label for="agr-logo">Logo (JPG / PNG, máx. 10 MB) <span class="sr-only">(Opcional)</span></label>
          <input type="file" id="agr-logo" name="logo" class="form-control"
                 accept="image/jpeg,image/png,image/jpg">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="agr-website">Sitio web <span class="sr-only">(Opcional)</span></label>
          <input type="url" id="agr-website" name="website" class="form-control"
                 placeholder="https://ejemplo.com" maxlength="500">
        </div>
        <div class="form-group">
          <label for="agr-facebook">Facebook <span class="sr-only">(Opcional)</span></label>
          <input type="url" id="agr-facebook" name="facebook" class="form-control"
                 placeholder="https://facebook.com/ejemplo" maxlength="500">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="agr-youtube">YouTube <span class="sr-only">(Opcional)</span></label>
          <input type="url" id="agr-youtube" name="youtube" class="form-control"
                 placeholder="https://youtube.com/ejemplo" maxlength="500">
        </div>
        <div class="form-group">
          <label for="agr-twitter">X (Twitter) <span class="sr-only">(Opcional)</span></label>
          <input type="url" id="agr-twitter" name="twitter" class="form-control"
                 placeholder="https://x.com/ejemplo" maxlength="500">
        </div>
      </div>

      <div class="btn-container">
        <a href="lista.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar Convenio</button>
      </div>
    </form>
  </div>

</main>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
