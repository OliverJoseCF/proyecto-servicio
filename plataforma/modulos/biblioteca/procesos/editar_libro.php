<?php
require_once __DIR__ . '/../../../shared/lib/auth.php';
requireAuth('biblioteca', '../login.php');
try {
    require '../config/conexion.php';
} catch (\Throwable $e) {
    error_log('editar_libro DB error: ' . $e->getMessage());
    header("Location: ../admin.php?error=db");
    exit;
}

$codigo = trim($_GET['codigo'] ?? '');
if ($codigo === '') {
    header("Location: ../admin.php");
    exit;
}

$stmt = $conexion->prepare("SELECT * FROM libros WHERE codigo = ?");
$stmt->bind_param("s", $codigo);
$stmt->execute();
$resultado = $stmt->get_result();
$libro = $resultado->fetch_assoc();

if (!$libro) {
    header("Location: ../admin.php");
    exit;
}

$tsj_module     = 'biblioteca';
$tsj_title      = 'Biblioteca — Editar Libro';
$tsj_extra_css  = [];
$tsj_head_extra = '<link rel="stylesheet"'
    . ' href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"'
    . ' integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"'
    . ' crossorigin="anonymous" />'
    . '<style>body { background-color: #f8f9fa; }</style>';
require_once __DIR__ . '/../../../shared/header.php';
?>

<div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow p-4">
        <h2 class="mb-4 fw-bold" style="color: #3D2E81;">Editar Libro</h2>
        <form method="POST" action="actualizar_libro.php">
            <?= csrfField() ?>
            <input type="hidden" name="codigo_original" value="<?= htmlspecialchars($libro['codigo'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-3">
                <label class="form-label fw-bold">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($libro['nombre'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="255">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Editorial</label>
                <input type="text" name="editorial" class="form-control" value="<?= htmlspecialchars($libro['editorial'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="255">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Clasificación</label>
                <input type="text" name="clasificacion" class="form-control" value="<?= htmlspecialchars($libro['clasificacion'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="100">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Autor</label>
                <input type="text" name="autor" class="form-control" value="<?= htmlspecialchars($libro['autor'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="255">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Código</label>
                <input type="text" name="codigo" class="form-control" value="<?= htmlspecialchars($libro['codigo'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="50">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success fw-bold w-100">Guardar Cambios</button>
                <a href="../admin.php" class="btn btn-secondary fw-bold w-100">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
