<?php
require_once __DIR__ . '/../src/security_headers.php';
require_once __DIR__ . '/../src/pages/conexion.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: 404.html'); exit(); }

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
if (!$fila) { header('Location: 404.html'); exit(); }

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

/* Determinar la carrera de esta empresa para el enlace "Volver" */
/* No tenemos la carrera en este query — redirigimos al listado general */
$tsj_module    = 'convenios';
$tsj_title     = h($fila['nombre']) . ' — Convenios TSJ Chapala';
$tsj_extra_css = ['src/output.css', 'src/css/styles.css'];
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../../shared/header.php';
?>

<main id="main">

  <div class="w-full p-4" style="background-color:#f5f5f5">
    <div class="oferta" style="position:relative;padding:10px 0;">
      <div class="w-full" style="display:flex;align-items:center;justify-content:center;position:relative;min-height:60px;">
        <a href="../vista_lista/vista_convenios.php"
           aria-label="Volver al listado de convenios"
           style="position:absolute;left:20px;top:50%;transform:translateY(-50%);">
          <img src="assets/images/logo/imagenes/icono-regresar.png" alt="" aria-hidden="true"
               style="width:32px;height:auto;cursor:pointer;padding:5px;border-radius:50%;transition:background 0.3s;">
        </a>
        <h1 class="text-convenios" style="font-size:2rem;font-weight:800;color:#32129a;margin:0;">
          Acerca de
        </h1>
      </div>
    </div>
  </div>

  <div style="display:flex;justify-content:center;padding:24px;flex-wrap:wrap;gap:24px;">
    <div style="background:#fff;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.08);padding:24px;width:100%;max-width:900px;">
      <div style="display:flex;flex-wrap:wrap;gap:24px;">

        <!-- Logo -->
        <div style="flex-shrink:0;">
          <?php if ($logoFile): ?>
            <img src="../src/pages/upload/<?= h($logoFile) ?>"
                 alt="Logo de <?= h($fila['nombre']) ?>"
                 style="width:120px;height:120px;object-fit:contain;border-radius:8px;border:1px solid #e5e7eb;">
          <?php else: ?>
            <div style="width:120px;height:120px;background:#f3f4f6;border-radius:8px;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;font-size:12px;color:#9ca3af;">
              Sin logo
            </div>
          <?php endif; ?>
        </div>

        <!-- Info -->
        <div style="flex:1;min-width:220px;">
          <h2 style="font-size:1.5rem;font-weight:700;color:#32129a;margin:0 0 16px;">
            <?= h($fila['nombre']) ?>
          </h2>
          <dl style="margin:0;">
            <dt style="font-weight:600;font-size:13px;color:#374151;margin-top:10px;">Tipo de convenio</dt>
            <dd style="margin:0;color:#4b5563;"><?= h($fila['convenio']) ?></dd>
            <dt style="font-weight:600;font-size:13px;color:#374151;margin-top:10px;">Contacto</dt>
            <dd style="margin:0;color:#4b5563;"><?= h($fila['contacto']) ?></dd>
            <dt style="font-weight:600;font-size:13px;color:#374151;margin-top:10px;">Correo</dt>
            <dd style="margin:0;"><a href="mailto:<?= h($fila['correo']) ?>" style="color:#32129a;"><?= h($fila['correo']) ?></a></dd>
            <dt style="font-weight:600;font-size:13px;color:#374151;margin-top:10px;">Teléfono</dt>
            <dd style="margin:0;color:#4b5563;"><a href="tel:<?= h($fila['telefono']) ?>" style="color:#32129a;"><?= h($fila['telefono']) ?></a></dd>
            <dt style="font-weight:600;font-size:13px;color:#374151;margin-top:10px;">Fecha de vencimiento</dt>
            <dd style="margin:0;color:#4b5563;"><?= h($fechaVenc) ?></dd>
          </dl>

          <!-- Redes sociales -->
          <div style="display:flex;align-items:center;gap:16px;margin-top:20px;" aria-label="Redes sociales">
            <?php $webUrl = safeUrl($fila['web']); ?>
            <?php if ($webUrl): ?>
              <a href="<?= $webUrl ?>" target="_blank" rel="noopener noreferrer"
                 aria-label="Sitio web de <?= h($fila['nombre']) ?>" title="Sitio web">
                <img src="assets/images/logo/link-svgrepo-com.svg" alt="Sitio web" style="width:28px;height:28px;opacity:0.8;" />
              </a>
            <?php endif; ?>

            <?php $fbUrl = safeUrl($fila['facebook']); ?>
            <?php if ($fbUrl): ?>
              <a href="<?= $fbUrl ?>" target="_blank" rel="noopener noreferrer"
                 aria-label="Facebook de <?= h($fila['nombre']) ?>" title="Facebook">
                <img src="assets/images/logo/facebook-color-svgrepo-com.svg" alt="Facebook" style="width:28px;height:28px;" />
              </a>
            <?php endif; ?>

            <?php $twUrl = safeUrl($fila['twitter']); ?>
            <?php if ($twUrl): ?>
              <a href="<?= $twUrl ?>" target="_blank" rel="noopener noreferrer"
                 aria-label="X (Twitter) de <?= h($fila['nombre']) ?>" title="X (Twitter)">
                <img src="assets/images/logo/icons8-x.svg" alt="X (Twitter)" style="width:28px;height:28px;" />
              </a>
            <?php endif; ?>

            <?php $ytUrl = safeUrl($fila['youtube']); ?>
            <?php if ($ytUrl): ?>
              <a href="<?= $ytUrl ?>" target="_blank" rel="noopener noreferrer"
                 aria-label="YouTube de <?= h($fila['nombre']) ?>" title="YouTube">
                <img src="assets/images/logo/youtube-168-svgrepo-com.svg" alt="YouTube" style="width:28px;height:28px;" />
              </a>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </div>

</main>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
