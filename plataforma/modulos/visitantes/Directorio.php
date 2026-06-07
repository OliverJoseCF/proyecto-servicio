<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Directorio Institucional';
$tsj_extra_css = ['style.css'];

require_once __DIR__ . '/../../shared/config.php';

try {
    $db       = getPDO(DB_NAME);
    $personas = $db->query('SELECT * FROM directorio WHERE activo=1 ORDER BY orden, nombre')->fetchAll();
    $db_ok    = true;
} catch (\Throwable $e) {
    $personas = [];
    $db_ok    = false;
}

$base_img = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/modulos/visitantes/imagenes/';

$tsj_head_extra = '<style>
.dir-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 24px;
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 20px 56px;
}
.dir-card {
  background: var(--tsj-white);
  border-radius: var(--tsj-radius-lg);
  box-shadow: 0 2px 10px rgba(20,10,80,.06);
  border: 1px solid var(--tsj-gray-200);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  position: relative;
  transition: transform .22s ease, box-shadow .22s ease;
}
.dir-card::before {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--tsj-blue-dark), var(--tsj-blue));
  opacity: 0;
  transition: opacity .22s;
  z-index: 1;
}
.dir-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(26,9,96,.12);
}
.dir-card:hover::before { opacity: 1; }
.dir-card-foto {
  width: 100%;
  height: 200px;
  object-fit: cover;
  object-position: top;
  background: var(--tsj-gray-100);
  display: block;
}
.dir-card-body {
  padding: 16px 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  width: 100%;
}
.dir-card-nombre {
  font-size: .97rem;
  font-weight: 700;
  color: var(--tsj-blue-dark);
  line-height: 1.3;
}
.dir-card-puesto {
  font-size: .78rem;
  font-weight: 600;
  color: var(--tsj-blue);
  background: var(--tsj-blue-50);
  border-radius: 999px;
  padding: 3px 10px;
  display: inline-block;
  align-self: center;
}
.dir-card-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-top: 4px;
}
.dir-card-row {
  display: flex;
  align-items: flex-start;
  gap: 7px;
  font-size: .78rem;
  color: var(--tsj-gray-600);
  text-align: left;
}
.dir-card-row .material-symbols-rounded {
  font-size: 15px;
  color: var(--tsj-gray-400);
  flex-shrink: 0;
  margin-top: 1px;
}
.dir-card-row a {
  color: var(--tsj-blue);
  text-decoration: none;
  word-break: break-all;
}
.dir-card-row a:hover { text-decoration: underline; }
.dir-empty {
  text-align: center;
  padding: 4rem 1rem;
  color: var(--tsj-gray-400);
  font-size: .95rem;
}
@media (max-width: 600px) {
  .dir-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 16px; }
  .dir-card-foto { height: 160px; }
}
</style>';

require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">

  <div class="tsj-page-header">
    <div class="tsj-page-header-line"></div>
    <h1>Directorio <span class="tsj-accent">Institucional</span></h1>
    <p class="tsj-page-header-sub">Personal del campus: puesto, ubicación y correo de contacto</p>
  </div>

  <?php if (empty($personas)): ?>
  <div class="dir-empty">
    <?= $db_ok ? 'No hay personas registradas en el directorio.' : 'Error al cargar el directorio.' ?>
  </div>
  <?php else: ?>
  <div class="dir-grid">
    <?php foreach ($personas as $p):
      $foto_src = $p['foto']
        ? $base_img . htmlspecialchars($p['foto'])
        : null;
      $placeholder = "data:image/svg+xml," . rawurlencode(
        '<svg xmlns="http://www.w3.org/2000/svg" width="220" height="200">'
        . '<rect width="220" height="200" fill="#e5e7eb"/>'
        . '<circle cx="110" cy="80" r="38" fill="#9ca3af"/>'
        . '<path d="M30 200c0-44 36-70 80-70s80 26 80 70z" fill="#9ca3af"/>'
        . '</svg>'
      );
    ?>
    <div class="dir-card">
      <img class="dir-card-foto"
           src="<?= $foto_src ?? $placeholder ?>"
           alt="<?= htmlspecialchars($p['nombre']) ?>"
           <?= $foto_src ? 'onerror="this.src=\'' . $placeholder . '\'"' : '' ?>>

      <div class="dir-card-body">
        <div class="dir-card-nombre"><?= htmlspecialchars($p['nombre']) ?></div>

        <?php if ($p['puesto']): ?>
        <div class="dir-card-puesto"><?= htmlspecialchars($p['puesto']) ?></div>
        <?php endif; ?>

        <div class="dir-card-info">
          <?php if ($p['ubicacion_fisica']): ?>
          <div class="dir-card-row">
            <span class="material-symbols-rounded" aria-hidden="true">location_on</span>
            <span><?= htmlspecialchars($p['ubicacion_fisica']) ?></span>
          </div>
          <?php endif; ?>

          <?php if ($p['extension'] && $p['extension'] !== 'S/N'): ?>
          <div class="dir-card-row">
            <span class="material-symbols-rounded" aria-hidden="true">call</span>
            <span>Ext. <?= htmlspecialchars($p['extension']) ?></span>
          </div>
          <?php endif; ?>

          <?php if ($p['correo']): ?>
          <div class="dir-card-row">
            <span class="material-symbols-rounded" aria-hidden="true">mail</span>
            <a href="mailto:<?= htmlspecialchars($p['correo']) ?>">
              <?= htmlspecialchars($p['correo']) ?>
            </a>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
