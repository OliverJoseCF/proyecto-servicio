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

$carreras  = $pdo->query("SELECT id_carrera, nombre_carrera FROM Carreras ORDER BY nombre_carrera")->fetchAll();
$materias  = $pdo->query("SELECT id_materia, nombre_materia, id_carrera FROM Materias ORDER BY nombre_materia")->fetchAll();
$semestres = range(1, 8);

// Cargar datos del profesor si estamos editando
$profesor = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("
        SELECT p.id_profesor, p.nombre, p.apellido,
               h.imagen_horario, h.id_carrera, h.id_materia, h.semestre
        FROM   Profesores p
        JOIN   Horarios h ON p.id_profesor = h.id_profesor
        WHERE  p.id_profesor = :id
    ");
    $stmt->execute(['id' => (int)$_GET['editar']]);
    $profesor = $stmt->fetch();
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify()) {
        die('Petición inválida.');
    }

    $nombre      = trim($_POST['nombre']    ?? '');
    $apellido    = trim($_POST['apellido']  ?? '');
    $carrera     = (int)($_POST['carrera']  ?? 0);
    $materia     = (int)($_POST['materia']  ?? 0);
    $semestre    = (int)($_POST['semestre'] ?? 0);
    $id_profesor = isset($_POST['id_profesor']) ? (int)$_POST['id_profesor'] : null;

    // Validaciones básicas
    if ($nombre === '' || $apellido === '') {
        die('El nombre y apellido son obligatorios.');
    }

    $filePathDB = null;

    // Manejo seguro del archivo
    if (!empty($_FILES['horario']['name']) && $_FILES['horario']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['horario']['error'] !== UPLOAD_ERR_OK) {
            die('Error al recibir el archivo (código ' . (int)$_FILES['horario']['error'] . ').');
        }

        $tmpName = $_FILES['horario']['tmp_name'];
        if (!is_uploaded_file($tmpName)) {
            die('Archivo inválido.');
        }

        // Límite: 10 MB
        if ($_FILES['horario']['size'] > 10 * 1024 * 1024) {
            die('El archivo supera el tamaño máximo permitido (10 MB).');
        }

        // Validar MIME real (no solo extensión)
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpName);
        finfo_close($finfo);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        $mimeToExt    = [
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            'image/gif'       => 'gif',
            'application/pdf' => 'pdf',
        ];

        if (!in_array($mimeType, $allowedMimes, true)) {
            die('Tipo de archivo no permitido. Solo JPG, PNG, GIF y PDF.');
        }

        $ext      = $mimeToExt[$mimeType];
        $fileName = bin2hex(random_bytes(16)) . '.' . $ext;

        if (!is_dir(HORARIOS_DIR)) {
            mkdir(HORARIOS_DIR, 0755, true);
        }
        if (!move_uploaded_file($tmpName, HORARIOS_DIR . $fileName)) {
            die('Error al guardar el archivo. Verifica permisos del directorio.');
        }
        $filePathDB = HORARIOS_URL . $fileName;
    }

    try {
        $pdo->beginTransaction();

        if ($id_profesor) {
            // EDITAR
            $pdo->prepare("UPDATE Profesores SET nombre = :nom, apellido = :ape WHERE id_profesor = :id")
                ->execute(['nom' => $nombre, 'ape' => $apellido, 'id' => $id_profesor]);

            if ($filePathDB !== null) {
                $stmtOld = $pdo->prepare("SELECT imagen_horario FROM Horarios WHERE id_profesor = :id");
                $stmtOld->execute(['id' => $id_profesor]);
                $oldPath = $stmtOld->fetchColumn();
                if ($oldPath && file_exists(__DIR__ . '/' . $oldPath)) {
                    @unlink(__DIR__ . '/' . $oldPath);
                }
                $pdo->prepare("UPDATE Horarios SET imagen_horario=:ruta, id_carrera=:car, id_materia=:mat, semestre=:sem WHERE id_profesor=:id")
                    ->execute(['ruta' => $filePathDB, 'car' => $carrera, 'mat' => $materia, 'sem' => $semestre, 'id' => $id_profesor]);
            } else {
                $pdo->prepare("UPDATE Horarios SET id_carrera=:car, id_materia=:mat, semestre=:sem WHERE id_profesor=:id")
                    ->execute(['car' => $carrera, 'mat' => $materia, 'sem' => $semestre, 'id' => $id_profesor]);
            }
        } else {
            // INSERTAR
            $stmt = $pdo->prepare("INSERT INTO Profesores (nombre, apellido) VALUES (:nom, :ape)");
            $stmt->execute(['nom' => $nombre, 'ape' => $apellido]);
            $newId = $pdo->lastInsertId();

            $pdo->prepare("INSERT INTO Horarios (id_profesor, imagen_horario, id_carrera, id_materia, semestre) VALUES (:idp,:rut,:car,:mat,:sem)")
                ->execute(['idp' => $newId, 'rut' => $filePathDB, 'car' => $carrera, 'mat' => $materia, 'sem' => $semestre]);
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        // Revertir archivo subido si el INSERT/UPDATE falló
        if ($filePathDB && file_exists(HORARIOS_DIR . basename($filePathDB))) {
            @unlink(HORARIOS_DIR . basename($filePathDB));
        }
        error_log('AgregarMaestro error: ' . $e->getMessage());
        die('Error al guardar los datos. Contacta al administrador.');
    }

    header("Location: VistaAdmin.php");
    exit;
}

$titulo = $profesor ? 'Editar Maestro' : 'Agregar Maestro';

$tsj_module    = 'horarios';
$tsj_title     = 'Horarios — ' . $titulo;
$tsj_extra_css = ['css/normalize.css', 'css/agregarMaestro.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

    <main class="margenVista">
        <h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>

        <div class="contenedor-formulario">
            <form method="POST" enctype="multipart/form-data" class="formulario-maestro">
                <?= csrfField() ?>

                <?php if ($profesor): ?>
                    <input type="hidden" name="id_profesor" value="<?= (int)$profesor['id_profesor'] ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required maxlength="50"
                           value="<?= htmlspecialchars($profesor['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="form-group">
                    <label for="apellido">Apellidos:</label>
                    <input type="text" id="apellido" name="apellido" required maxlength="50"
                           value="<?= htmlspecialchars($profesor['apellido'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="form-group">
                    <label for="carrera">Carrera:</label>
                    <select id="carrera" name="carrera" required>
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
                    <label for="materia">Materia:</label>
                    <select id="materia" name="materia" required>
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
                    <label for="semestre">Semestre:</label>
                    <select id="semestre" name="semestre" required>
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
                    <label for="horario">Subir Horario (JPG, PNG, GIF, PDF — máx 10 MB):</label>
                    <input type="file" id="horario" name="horario"
                           accept="image/jpeg,image/png,image/gif,application/pdf"
                           <?= $profesor ? '' : 'required' ?>>
                    <?php if ($profesor && $profesor['imagen_horario']): ?>
                        <p class="archivo-actual">Archivo actual: <strong><?= htmlspecialchars(basename($profesor['imagen_horario']), ENT_QUOTES, 'UTF-8') ?></strong></p>
                        <?php
                        $ext = strtolower(pathinfo($profesor['imagen_horario'], PATHINFO_EXTENSION));
                        if ($ext === 'pdf'): ?>
                            <p><a href="<?= htmlspecialchars($profesor['imagen_horario'], ENT_QUOTES, 'UTF-8') ?>" target="_blank">Ver PDF actual</a></p>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($profesor['imagen_horario'], ENT_QUOTES, 'UTF-8') ?>"
                                 alt="Horario actual" class="preview-horario">
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
    </main>

    <script>
    const carreraSelect = document.getElementById('carrera');
    const materiaSelect = document.getElementById('materia');
    const todasOpciones = Array.from(materiaSelect.querySelectorAll('option[data-carrera]'));

    function filtrarMaterias(idCarrera) {
        const valorActual = materiaSelect.value;
        materiaSelect.innerHTML = '<option value="">— Selecciona —</option>';
        todasOpciones.forEach(opt => {
            const carreraOpt = opt.dataset.carrera;
            if (!idCarrera || carreraOpt == idCarrera || carreraOpt === '0' || carreraOpt === '') {
                const clone = opt.cloneNode(true);
                if (clone.value === valorActual) clone.selected = true;
                materiaSelect.appendChild(clone);
            }
        });
    }

    filtrarMaterias(carreraSelect.value);
    carreraSelect.addEventListener('change', () => filtrarMaterias(carreraSelect.value));
    </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
