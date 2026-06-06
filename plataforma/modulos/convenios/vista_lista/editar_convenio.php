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

$carreras = [];
try {
    require_once __DIR__ . '/../../../shared/config.php';
    $dbC = getPDO(DB_NAME);
    foreach ($dbC->query('SELECT clave, nombre FROM carreras WHERE activo=1 ORDER BY orden')->fetchAll() as $row) {
        $carreras[$row['clave']] = $row['nombre'];
    }
} catch (\Throwable $e) {}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: lista.php'); exit(); }

$error    = null;
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

if ($convenio === null && $error === null) { $conn->close(); header('Location: lista.php'); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Petición inválida. Recarga la página e intenta de nuevo.';
    } else {
        $fields = [];
        $error  = validarCamposConvenio($_POST, $fields);
        $newLogoName = ''; $logoSubido = false;
        if ($error === null && isset($_FILES['logo'])) {
            $uploadError = procesarLogo($_FILES['logo'], $newLogoName);
            if ($uploadError !== null) { $error = $uploadError; }
            elseif ($newLogoName !== '') { $logoSubido = true; }
        }
        if ($error === null) {
            $logoParaGuardar = $logoSubido ? $newLogoName : basename($convenio['logo'] ?? '');
            $sql = 'UPDATE convenios SET nombre=?,convenio=?,logo=?,contacto=?,telefono=?,correo=?,vencimiento=?,web=?,facebook=?,youtube=?,twitter=?,carrera=? WHERE id=?';
            try {
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssssssssssssi',
                    $fields['empresa'],$fields['tipoConvenio'],$logoParaGuardar,
                    $fields['contacto'],$fields['telefono'],$fields['email'],
                    $fields['vencimiento'],$fields['website'],$fields['facebook'],
                    $fields['youtube'],$fields['twitter'],$fields['carrera'],$id
                );
                $stmt->execute();
                $stmt->close();
                $conn->close();
                if ($logoSubido && !empty($convenio['logo'])) eliminarLogo($convenio['logo']);
                header('Location: lista.php?mensaje=editado');
                exit();
            } catch (mysqli_sql_exception $e) {
                error_log('Error editar_convenio update: ' . $e->getMessage());
                if ($logoSubido && !empty($newLogoName)) eliminarLogo($newLogoName);
                $error = 'Error al actualizar el convenio. Intente de nuevo.';
            }
        } else {
            if ($logoSubido && !empty($newLogoName)) eliminarLogo($newLogoName);
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') $conn->close();

$logoFile = !empty($convenio['logo']) ? basename($convenio['logo']) : '';

$tsj_module    = 'convenios';
$tsj_title     = 'Convenios — Editar';
$tsj_extra_css = ['estilo/estilo.css', 'estilo/form-crud.css'];
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../../shared/header.php';
?>

<main id="main">

  <div class="fila-busqueda">
    <h1>Editar Convenio</h1>
    <div class="botones-container">
      <a href="lista.php" aria-label="Volver a la lista de convenios">
        <img src="img/icono-regresar.png" alt="" aria-hidden="true" class="btn-volver" />
      </a>
    </div>
  </div>

  <div class="form-container">
    <?php if ($error): ?>
      <div class="alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form action="editar_convenio.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token"
             value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

      <div class="form-row">
        <div class="form-group">
          <label for="edt-empresa">Nombre de la empresa <span aria-hidden="true">*</span></label>
          <input type="text" id="edt-empresa" name="empresa" class="form-control" required maxlength="200"
                 value="<?= htmlspecialchars($convenio['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="edt-carrera">Carrera <span aria-hidden="true">*</span></label>
          <select id="edt-carrera" name="carrera" class="form-control" required>
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
          <label for="edt-tipo">Tipo de convenio <span aria-hidden="true">*</span></label>
          <select id="edt-tipo" name="tipoConvenio" class="form-control" required>
            <option value="" disabled>Seleccione una opción</option>
            <option value="Servicio Social" <?= ($convenio['convenio']??'') === 'Servicio Social' ? 'selected' : '' ?>>Servicio Social</option>
            <option value="Prácticas"       <?= ($convenio['convenio']??'') === 'Prácticas'       ? 'selected' : '' ?>>Prácticas</option>
            <option value="Ambos"           <?= ($convenio['convenio']??'') === 'Ambos'           ? 'selected' : '' ?>>Ambos</option>
          </select>
        </div>
        <div class="form-group">
          <label for="edt-vencimiento">Fecha de vencimiento <span aria-hidden="true">*</span></label>
          <input type="date" id="edt-vencimiento" name="vencimiento" class="form-control" required
                 value="<?= htmlspecialchars($convenio['vencimiento'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="edt-contacto">Persona de contacto <span aria-hidden="true">*</span></label>
          <input type="text" id="edt-contacto" name="contacto" class="form-control" required maxlength="200"
                 value="<?= htmlspecialchars($convenio['contacto'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="edt-telefono">Teléfono <span aria-hidden="true">*</span></label>
          <input type="tel" id="edt-telefono" name="telefono" class="form-control" required maxlength="25"
                 pattern="[0-9+\-\s()]{7,25}" title="Solo números, +, -, espacios y paréntesis"
                 value="<?= htmlspecialchars($convenio['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="edt-email">Correo electrónico <span aria-hidden="true">*</span></label>
          <input type="email" id="edt-email" name="email" class="form-control" required maxlength="254"
                 value="<?= htmlspecialchars($convenio['correo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="edt-logo">Logo (JPG / PNG, máx. 10 MB)</label>
          <?php if ($logoFile): ?>
            <img src="../src/pages/upload/<?= htmlspecialchars($logoFile, ENT_QUOTES, 'UTF-8') ?>"
                 alt="Logo actual de <?= htmlspecialchars($convenio['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 class="current-logo" width="100">
            <p style="font-size:13px;color:#6b7280;margin-bottom:6px;">Seleccione un nuevo logo solo si desea cambiarlo.</p>
          <?php endif; ?>
          <input type="file" id="edt-logo" name="logo" class="form-control" accept="image/jpeg,image/png,image/jpg">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="edt-website">Sitio web</label>
          <input type="url" id="edt-website" name="website" class="form-control"
                 placeholder="https://ejemplo.com" maxlength="500"
                 value="<?= htmlspecialchars($convenio['web'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="edt-facebook">Facebook</label>
          <input type="url" id="edt-facebook" name="facebook" class="form-control"
                 placeholder="https://facebook.com/ejemplo" maxlength="500"
                 value="<?= htmlspecialchars($convenio['facebook'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="edt-youtube">YouTube</label>
          <input type="url" id="edt-youtube" name="youtube" class="form-control"
                 placeholder="https://youtube.com/ejemplo" maxlength="500"
                 value="<?= htmlspecialchars($convenio['youtube'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="edt-twitter">X (Twitter)</label>
          <input type="url" id="edt-twitter" name="twitter" class="form-control"
                 placeholder="https://x.com/ejemplo" maxlength="500"
                 value="<?= htmlspecialchars($convenio['twitter'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
      </div>

      <div class="btn-container">
        <a href="lista.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
      </div>
    </form>
  </div>

</main>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
