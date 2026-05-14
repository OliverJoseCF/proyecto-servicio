<?php
require_once __DIR__ . '/../src/security_headers.php';
require_once __DIR__ . '/../src/pages/conexion.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: 404.html');
    exit();
}

$fila = null;
try {
    $stmt = $conn->prepare(
        'SELECT id, nombre, convenio, logo, contacto, telefono, correo, vencimiento, web, facebook, youtube, twitter
         FROM convenios WHERE id = ?'
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
} catch (mysqli_sql_exception $e) {
    error_log('Error prepare vista_empresa: ' . $e->getMessage());
    header('Location: 404.html');
    exit();
}

if (!$fila) {
    header('Location: 404.html');
    exit();
}

function h(string $val): string {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

function safeUrl(?string $url): string {
    if (empty($url)) return '';
    $url = trim($url);
    if (!preg_match('/^https?:\/\//i', $url)) return '';
    return h($url);
}

$logoFile  = !empty($fila['logo']) ? basename($fila['logo']) : '';
$fechaVenc = ($t = strtotime($fila['vencimiento'])) ? date('d/m/Y', $t) : '—';

$tsj_module    = 'convenios';
$tsj_title     = h($fila['nombre']) . ' — Convenios';
$tsj_extra_css = ['src/output.css', 'src/css/styles.css'];
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../../shared/header.php';
?>

    <div class="w-full p-4" style="background-color: #f5f5f5">
        <div class="oferta" title="Convenios">
            <div class="w-full relative py-2" style="background-color: #f5f5f5">
                <a href="../vista_lista/lista.php" aria-label="Volver al listado de convenios">
                    <img src="assets/images/logo/imagenes/icono-regresar.png" alt="" aria-hidden="true" class="btn-volver" />
                </a>
                <h5 class="text-convenios font-bold text-4xl text-base_blue-500">Acerca de</h5>
            </div>
        </div>
    </div>

    <main class="cards-container w-full p-6 flex justify-center gap-6 flex-wrap">
        <div class="company-card bg-white rounded-lg shadow-md p-6 w-full max-w-4xl">
            <div class="flex flex-row">
                <div class="company-logo-container">
                    <?php if ($logoFile): ?>
                        <img src="../src/pages/upload/<?= h($logoFile) ?>"
                             alt="Logo de <?= h($fila['nombre']) ?>"
                             class="company-logo">
                    <?php else: ?>
                        <div class="company-logo bg-gray-100 flex items-center justify-center text-gray-400 text-sm">Sin logo</div>
                    <?php endif; ?>
                </div>

                <div class="company-info">
                    <h2 class="company-title"><?= h($fila['nombre']) ?></h2>

                    <div class="company-details">
                        <p><strong>Tipo de convenio:</strong> <?= h($fila['convenio']) ?></p>
                        <p><strong>Contacto:</strong> <?= h($fila['contacto']) ?></p>
                        <p><strong>Correo:</strong> <?= h($fila['correo']) ?></p>
                        <p><strong>Teléfono:</strong> <?= h($fila['telefono']) ?></p>
                        <p><strong>Fecha de vencimiento:</strong> <?= h($fechaVenc) ?></p>
                    </div>

                    <div class="social-links">
                        <div class="flex items-center gap-4">

                            <?php $webUrl = safeUrl($fila['web']); ?>
                            <?php if ($webUrl): ?>
                            <a href="<?= $webUrl ?>" class="social-icon-link" title="Sitio web de <?= h($fila['nombre']) ?>" target="_blank" rel="noopener noreferrer">
                                <img src="assets/images/logo/link-svgrepo-com.svg" alt="Sitio web" class="social-icon">
                            </a>
                            <?php else: ?>
                            <span class="social-icon-link" title="Sitio web no disponible" aria-label="Sitio web no disponible">
                                <img src="assets/images/logo/link-svgrepo-com.svg" alt="" aria-hidden="true" class="social-icon opacity-50">
                            </span>
                            <?php endif; ?>

                            <?php $fbUrl = safeUrl($fila['facebook']); ?>
                            <?php if ($fbUrl): ?>
                            <a href="<?= $fbUrl ?>" class="social-icon-link" title="Facebook de <?= h($fila['nombre']) ?>" target="_blank" rel="noopener noreferrer">
                                <img src="assets/images/logo/facebook-color-svgrepo-com.svg" alt="Facebook" class="social-icon">
                            </a>
                            <?php else: ?>
                            <span class="social-icon-link" title="Facebook no disponible" aria-label="Facebook no disponible">
                                <img src="assets/images/logo/facebook-color-svgrepo-com.svg" alt="" aria-hidden="true" class="social-icon opacity-50">
                            </span>
                            <?php endif; ?>

                            <?php $twUrl = safeUrl($fila['twitter']); ?>
                            <?php if ($twUrl): ?>
                            <a href="<?= $twUrl ?>" class="social-icon-link" title="X (Twitter) de <?= h($fila['nombre']) ?>" target="_blank" rel="noopener noreferrer">
                                <img src="assets/images/logo/icons8-x.svg" alt="X (Twitter)" class="social-icon">
                            </a>
                            <?php else: ?>
                            <span class="social-icon-link" title="X (Twitter) no disponible" aria-label="X (Twitter) no disponible">
                                <img src="assets/images/logo/icons8-x.svg" alt="" aria-hidden="true" class="social-icon opacity-50">
                            </span>
                            <?php endif; ?>

                            <?php $ytUrl = safeUrl($fila['youtube']); ?>
                            <?php if ($ytUrl): ?>
                            <a href="<?= $ytUrl ?>" class="social-icon-link" title="YouTube de <?= h($fila['nombre']) ?>" target="_blank" rel="noopener noreferrer">
                                <img src="assets/images/logo/youtube-168-svgrepo-com.svg" alt="YouTube" class="social-icon">
                            </a>
                            <?php else: ?>
                            <span class="social-icon-link" title="YouTube no disponible" aria-label="YouTube no disponible">
                                <img src="assets/images/logo/youtube-168-svgrepo-com.svg" alt="" aria-hidden="true" class="social-icon opacity-50">
                            </span>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="src/js/script.js"></script>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
