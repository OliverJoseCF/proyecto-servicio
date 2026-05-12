<?php
require_once __DIR__ . '/config.php';

try {
    $pdo = getDB();
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Datos para los selectores
$carreras  = $pdo->query("SELECT id_carrera, nombre_carrera FROM Carreras ORDER BY nombre_carrera")->fetchAll();
// Traemos id_carrera también para poder filtrar por JS
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
    $nombre      = trim($_POST['nombre']    ?? '');
    $apellido    = trim($_POST['apellido']  ?? '');
    $carrera     = (int)($_POST['carrera']  ?? 0);
    $materia     = (int)($_POST['materia']  ?? 0);
    $semestre    = (int)($_POST['semestre'] ?? 0);
    $id_profesor = isset($_POST['id_profesor']) ? (int)$_POST['id_profesor'] : null;

    // Manejo del archivo
    $fileName  = basename($_FILES['horario']['name'] ?? '');
    $ext       = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed   = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

    if (!empty($fileName) && !in_array($ext, $allowed)) {
        die("Error: Solo se permiten JPG, JPEG, PNG, GIF o PDF.");
    }

    $filePathDB = null; // ruta relativa que se guarda en BD

    if (!empty($fileName)) {
        if (!is_dir(HORARIOS_DIR)) {
            mkdir(HORARIOS_DIR, 0755, true);
        }
        if (!move_uploaded_file($_FILES['horario']['tmp_name'], HORARIOS_DIR . $fileName)) {
            die("Error al subir el archivo.");
        }
        $filePathDB = HORARIOS_URL . $fileName; // ej: horarios/mi_archivo.jpg
    }

    if ($id_profesor) {
        // --- EDITAR ---
        $pdo->prepare("UPDATE Profesores SET nombre = :nom, apellido = :ape WHERE id_profesor = :id")
            ->execute(['nom' => $nombre, 'ape' => $apellido, 'id' => $id_profesor]);

        if ($filePathDB !== null) {
            // Borrar archivo anterior del disco antes de guardar el nuevo
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
        // --- INSERTAR ---
        $stmt = $pdo->prepare("INSERT INTO Profesores (nombre, apellido) VALUES (:nom, :ape)");
        $stmt->execute(['nom' => $nombre, 'ape' => $apellido]);
        $newId = $pdo->lastInsertId();

        $pdo->prepare("INSERT INTO Horarios (id_profesor, imagen_horario, id_carrera, id_materia, semestre) VALUES (:idp,:rut,:car,:mat,:sem)")
            ->execute(['idp' => $newId, 'rut' => $filePathDB, 'car' => $carrera, 'mat' => $materia, 'sem' => $semestre]);
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
        <h2><?= $titulo ?></h2>

        <div class="contenedor-formulario">
            <form method="POST" enctype="multipart/form-data" class="formulario-maestro">

                <?php if ($profesor): ?>
                    <input type="hidden" name="id_profesor" value="<?= $profesor['id_profesor'] ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required
                           value="<?= htmlspecialchars($profesor['nombre'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="apellido">Apellidos:</label>
                    <input type="text" id="apellido" name="apellido" required
                           value="<?= htmlspecialchars($profesor['apellido'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="carrera">Carrera:</label>
                    <select id="carrera" name="carrera" required>
                        <option value="">— Selecciona —</option>
                        <?php foreach ($carreras as $c): ?>
                            <option value="<?= $c['id_carrera'] ?>"
                                <?= ($profesor && $profesor['id_carrera'] == $c['id_carrera']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nombre_carrera']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="materia">Materia:</label>
                    <select id="materia" name="materia" required>
                        <option value="">— Selecciona una carrera primero —</option>
                        <?php foreach ($materias as $m): ?>
                            <option value="<?= $m['id_materia'] ?>"
                                    data-carrera="<?= $m['id_carrera'] ?? '' ?>"
                                <?= ($profesor && $profesor['id_materia'] == $m['id_materia']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nombre_materia']) ?>
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
                    <label for="horario">Subir Horario (JPG, PNG, PDF):</label>
                    <input type="file" id="horario" name="horario"
                           accept=".jpg,.jpeg,.png,.gif,.pdf"
                           <?= $profesor ? '' : 'required' ?>>
                    <?php if ($profesor && $profesor['imagen_horario']): ?>
                        <p class="archivo-actual">Archivo actual: <strong><?= basename($profesor['imagen_horario']) ?></strong></p>
                        <img src="<?= htmlspecialchars($profesor['imagen_horario']) ?>"
                             alt="Horario actual" class="preview-horario">
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
    // Filtra las materias según la carrera seleccionada
    const carreraSelect = document.getElementById('carrera');
    const materiaSelect = document.getElementById('materia');
    const todasOpciones = Array.from(materiaSelect.querySelectorAll('option[data-carrera]'));

    function filtrarMaterias(idCarrera) {
        // Guardar la materia actualmente seleccionada
        const valorActual = materiaSelect.value;

        // Limpiar y poner placeholder
        materiaSelect.innerHTML = '<option value="">— Selecciona —</option>';

        todasOpciones.forEach(opt => {
            const carreraOpt = opt.dataset.carrera;
            // Mostrar si coincide con la carrera seleccionada, o si no tiene carrera asignada
            if (!idCarrera || carreraOpt == idCarrera || carreraOpt === '') {
                const clone = opt.cloneNode(true);
                if (clone.value === valorActual) clone.selected = true;
                materiaSelect.appendChild(clone);
            }
        });
    }

    // Al cargar la página, filtrar según la carrera ya seleccionada (modo editar)
    filtrarMaterias(carreraSelect.value);

    carreraSelect.addEventListener('change', () => filtrarMaterias(carreraSelect.value));
    </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>