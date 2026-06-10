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
    $carreras = $pdo->query("SELECT id AS id_carrera, nombre AS nombre_carrera FROM carreras ORDER BY nombre")->fetchAll();
    $materias = $pdo->query("SELECT id AS id_materia, nombre AS nombre_materia, carrera_id AS id_carrera FROM materias ORDER BY nombre")->fetchAll();
} catch (\PDOException $e) {
    error_log('horarios/AgregarMaestro catalog error: ' . $e->getMessage());
    $carreras = [];
    $materias = [];
}
$semestres = range(1, 8);

$profesor  = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("
        SELECT p.id_profesor, p.nombre, p.apellido,
               h.imagen_horario, h.id_carrera, h.semestre
        FROM   profesores p
        JOIN   horarios h ON p.id_profesor = h.id_profesor
        WHERE  p.id_profesor = :id
    ");
    $stmt->execute(['id' => (int)$_GET['editar']]);
    $profesor = $stmt->fetch();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify()) { die('Petición inválida.'); }

    $nombre      = trim($_POST['nombre']    ?? '');
    $apellido    = trim($_POST['apellido']  ?? '');
    $carrera     = (int)($_POST['carrera']  ?? 0);
    $materia     = (int)($_POST['materia']  ?? 0);
    $semestre    = (int)($_POST['semestre'] ?? 0);
    $id_profesor = isset($_POST['id_profesor']) ? (int)$_POST['id_profesor'] : null;

    if ($nombre === '' || $apellido === '') {
        $error = 'El nombre y apellido son obligatorios.';
    }

    $filePathDB = null;

    if ($error === null && !empty($_FILES['horario']['name']) && $_FILES['horario']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['horario']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Error al recibir el archivo (código ' . (int)$_FILES['horario']['error'] . ').';
        } elseif (!is_uploaded_file($_FILES['horario']['tmp_name'])) {
            $error = 'Archivo inválido.';
        } elseif ($_FILES['horario']['size'] > 10 * 1024 * 1024) {
            $error = 'El archivo supera el tamaño máximo permitido (10 MB).';
        } else {
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['horario']['tmp_name']);
            finfo_close($finfo);
            $allowedMimes = ['image/jpeg','image/png','image/gif','application/pdf'];
            $mimeToExt    = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','application/pdf'=>'pdf'];
            if (!in_array($mimeType, $allowedMimes, true)) {
                $error = 'Tipo de archivo no permitido. Solo JPG, PNG, GIF y PDF.';
            } else {
                $ext      = $mimeToExt[$mimeType];
                $fileName = bin2hex(random_bytes(16)) . '.' . $ext;
                if (!is_dir(HORARIOS_DIR)) mkdir(HORARIOS_DIR, 0755, true);
                if (!move_uploaded_file($_FILES['horario']['tmp_name'], HORARIOS_DIR . $fileName)) {
                    $error = 'Error al guardar el archivo. Verifica permisos del directorio.';
                } else {
                    $filePathDB = HORARIOS_URL . $fileName;
                }
            }
        }
    }

    if ($error === null) {
        try {
            $pdo->beginTransaction();
            if ($id_profesor) {
                $pdo->prepare("UPDATE profesores SET nombre=:nom,apellido=:ape WHERE id_profesor=:id")
                    ->execute(['nom'=>$nombre,'ape'=>$apellido,'id'=>$id_profesor]);
                if ($filePathDB !== null) {
                    $stmtOld = $pdo->prepare("SELECT imagen_horario FROM horarios WHERE id_profesor=:id");
                    $stmtOld->execute(['id'=>$id_profesor]);
                    $oldPath = $stmtOld->fetchColumn();
                    if ($oldPath && file_exists(HORARIOS_DIR . basename($oldPath))) @unlink(HORARIOS_DIR . basename($oldPath));
                    $pdo->prepare("UPDATE horarios SET imagen_horario=:ruta,id_carrera=:car,semestre=:sem WHERE id_profesor=:id")
                        ->execute(['ruta'=>$filePathDB,'car'=>$carrera,'sem'=>$semestre,'id'=>$id_profesor]);
                } else {
                    $pdo->prepare("UPDATE horarios SET id_carrera=:car,semestre=:sem WHERE id_profesor=:id")
                        ->execute(['car'=>$carrera,'sem'=>$semestre,'id'=>$id_profesor]);
                }
            } else {
                $stmt = $pdo->prepare("INSERT INTO profesores (nombre,apellido) VALUES (:nom,:ape)");
                $stmt->execute(['nom'=>$nombre,'ape'=>$apellido]);
                $newId = $pdo->lastInsertId();
                $pdo->prepare("INSERT INTO horarios (id_profesor,imagen_horario,id_carrera,semestre) VALUES (:idp,:rut,:car,:sem)")
                    ->execute(['idp'=>$newId,'rut'=>$filePathDB,'car'=>$carrera,'sem'=>$semestre]);
            }
            $pdo->commit();
            header("Location: VistaAdmin.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            if ($filePathDB && file_exists(HORARIOS_DIR . basename($filePathDB))) @unlink(HORARIOS_DIR . basename($filePathDB));
            error_log('AgregarMaestro error: ' . $e->getMessage());
            $error = 'Error al guardar los datos. Contacta al administrador.';
        }
    }
}

$titulo = $profesor ? 'Editar Maestro' : 'Agregar Maestro';

$tsj_module    = 'horarios';
$tsj_title     = 'Horarios — ' . $titulo;
$tsj_extra_css = ['normalize.css', 'css/agregarMaestro.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">
  <div class="margenVista">
    <h1><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if ($error): ?>
      <div role="alert" style="background:#fef2f2;border-left:4px solid #dc2626;color:#991b1b;padding:12px 16px;border-radius:6px;margin-bottom:16px;width:100%;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <div class="contenedor-formulario">
      <form method="POST" enctype="multipart/form-data" class="formulario-maestro">
        <?= csrfField() ?>

        <?php if ($profesor): ?>
          <input type="hidden" name="id_profesor" value="<?= (int)$profesor['id_profesor'] ?>">
        <?php endif; ?>

        <div class="form-group">
          <label for="hor-nombre">Nombre:</label>
          <input type="text" id="hor-nombre" name="nombre" required maxlength="50"
                 value="<?= htmlspecialchars($profesor['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
          <label for="hor-apellido">Apellidos:</label>
          <input type="text" id="hor-apellido" name="apellido" required maxlength="50"
                 value="<?= htmlspecialchars($profesor['apellido'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
          <label for="hor-carrera">Carrera:</label>
          <select id="hor-carrera" name="carrera" required>
            <option value="">— Selecciona —</option>
            <?php foreach ($carreras as $c): ?>
              <option value="<?= (int)$c['id_carrera'] ?>"
                  <?= ($profesor && $profesor['id_carrera'] == $c['id_carrera']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nombre_carrera'], ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="hor-materia">Materia:</label>
          <select id="hor-materia" name="materia" required>
            <option value="">— Selecciona una carrera primero —</option>
            <?php foreach ($materias as $m): ?>
              <option value="<?= (int)$m['id_materia'] ?>"
                      data-carrera="<?= (int)($m['id_carrera'] ?? 0) ?>"
                  <?= ($profesor && $profesor['id_materia'] == $m['id_materia']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['nombre_materia'], ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="hor-semestre">Semestre:</label>
          <select id="hor-semestre" name="semestre" required>
            <option value="">— Selecciona —</option>
            <?php foreach ($semestres as $s): ?>
              <option value="<?= $s ?>"
                  <?= ($profesor && $profesor['semestre'] == $s) ? 'selected' : '' ?>>
                <?= $s ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="hor-horario">
            Subir Horario
            <span aria-hidden="true">(JPG, PNG, GIF, PDF — máx 10 MB)</span>
            <span class="sr-only">. Formatos aceptados: JPG, PNG, GIF, PDF. Tamaño máximo 10 megabytes.</span>
          </label>
          <input type="file" id="hor-horario" name="horario"
                 accept="image/jpeg,image/png,image/gif,application/pdf"
                 <?= $profesor ? '' : 'required' ?>>
          <?php if ($profesor && $profesor['imagen_horario']): ?>
            <p class="archivo-actual">Archivo actual: <strong><?= htmlspecialchars(basename($profesor['imagen_horario']), ENT_QUOTES, 'UTF-8') ?></strong></p>
            <?php $ext = strtolower(pathinfo($profesor['imagen_horario'], PATHINFO_EXTENSION)); ?>
            <?php if ($ext === 'pdf'): ?>
              <p><a href="<?= htmlspecialchars($profesor['imagen_horario'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">Ver PDF actual</a></p>
            <?php else: ?>
              <img src="<?= htmlspecialchars($profesor['imagen_horario'], ENT_QUOTES, 'UTF-8') ?>"
                   alt="Vista previa del horario actual de <?= htmlspecialchars(($profesor['nombre'] ?? '') . ' ' . ($profesor['apellido'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                   class="preview-horario" width="160">
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <div class="form-actions">
          <input type="submit" class="boton-agregar"
                 value="<?= $profesor ? 'GUARDAR CAMBIOS' : 'AGREGAR MAESTRO' ?>">
          <a href="VistaAdmin.php" class="boton-volver">← Volver</a>
        </div>

      </form>
    </div>
  </div>
</main>

<script>
(function () {
  'use strict';
  var carreraSelect = document.getElementById('hor-carrera');
  var materiaSelect = document.getElementById('hor-materia');
  var todasOpciones = Array.from(materiaSelect.querySelectorAll('option[data-carrera]'));

  function filtrarMaterias(idCarrera) {
    var valorActual = materiaSelect.value;
    materiaSelect.innerHTML = '<option value="">— Selecciona —</option>';
    todasOpciones.forEach(function (opt) {
      var carreraOpt = opt.dataset.carrera;
      if (!idCarrera || carreraOpt == idCarrera || carreraOpt === '0' || carreraOpt === '') {
        var clone = opt.cloneNode(true);
        if (clone.value === valorActual) clone.selected = true;
        materiaSelect.appendChild(clone);
      }
    });
  }

  filtrarMaterias(carreraSelect.value);
  carreraSelect.addEventListener('change', function () { filtrarMaterias(this.value); });
})();
</script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
