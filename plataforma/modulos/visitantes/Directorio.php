<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Directorio Institucional';
$tsj_extra_css = ['style.css'];

require_once __DIR__ . '/../../shared/config.php';

try {
    $db         = getPDO(DB_NAME);
    $personas      = $db->query('SELECT nombre, puesto, ubicacion_fisica, extension, correo, foto FROM directorio WHERE activo=1 ORDER BY orden, nombre')->fetchAll();
    $secretarias   = $db->query('SELECT nombre, rol, telefono, correo FROM secretarias WHERE activo=1 ORDER BY orden, nombre')->fetchAll();
    $coordinadores = $db->query('SELECT co.nombre, co.correo, c.nombre AS carrera_nombre FROM coordinadores co JOIN carreras c ON co.carrera_id = c.id WHERE co.activo=1 ORDER BY c.orden, co.nombre')->fetchAll();
    $db_ok         = true;
} catch (\Throwable $e) {
    $personas      = [];
    $secretarias   = [];
    $coordinadores = [];
    $db_ok         = false;
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
.dir-tabs {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin: 0 auto 28px;
  flex-wrap: wrap;
}
.dir-tab-btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 10px 22px;
  border-radius: 10px;
  font-family: var(--tsj-font, "Poppins", sans-serif);
  font-size: .88rem;
  font-weight: 700;
  border: 1.5px solid #e8eaf2;
  background: #fff;
  color: #4a5170;
  cursor: pointer;
  transition: all .18s;
}
.dir-tab-btn.active {
  background: var(--tsj-blue-dark, #1a0960);
  color: #fff;
  border-color: var(--tsj-blue-dark, #1a0960);
  box-shadow: 0 4px 14px rgba(26,9,96,.18);
}
.dir-tab-btn:not(.active):hover {
  border-color: var(--tsj-blue-dark, #1a0960);
  color: var(--tsj-blue-dark, #1a0960);
}
.dir-panel { display: none; }
.dir-panel.active { display: block; }
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

  <?php
  $placeholder = "data:image/svg+xml," . rawurlencode(
    '<svg xmlns="http://www.w3.org/2000/svg" width="220" height="200">'
    . '<rect width="220" height="200" fill="#e5e7eb"/>'
    . '<circle cx="110" cy="80" r="38" fill="#9ca3af"/>'
    . '<path d="M30 200c0-44 36-70 80-70s80 26 80 70z" fill="#9ca3af"/>'
    . '</svg>'
  );
  ?>

  <!-- Tabs -->
  <div class="dir-tabs">
    <button class="dir-tab-btn active" onclick="dirTab('directivo',this)">
      <span class="material-symbols-rounded" style="font-size:18px">badge</span>
      Personal Directivo
    </button>
    <?php if (!empty($coordinadores)): ?>
    <button class="dir-tab-btn" onclick="dirTab('coordinadores',this)">
      <span class="material-symbols-rounded" style="font-size:18px">school</span>
      Coordinadores
    </button>
    <?php endif; ?>
    <?php if (!empty($secretarias)): ?>
    <button class="dir-tab-btn" onclick="dirTab('secretarias',this)">
      <span class="material-symbols-rounded" style="font-size:18px">support_agent</span>
      Secretarías
    </button>
    <?php endif; ?>
  </div>

  <!-- Panel: Personal Directivo -->
  <div class="dir-panel active" id="dir-panel-directivo">
    <?php if (empty($personas)): ?>
    <div class="dir-empty">
      <?= $db_ok ? 'No hay personas registradas en el directorio.' : 'Error al cargar el directorio.' ?>
    </div>
    <?php else: ?>
    <div class="dir-grid">
      <?php foreach ($personas as $p):
        $foto_src = $p['foto'] ? $base_img . htmlspecialchars($p['foto']) : null;
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
              <a href="mailto:<?= htmlspecialchars($p['correo']) ?>"><?= htmlspecialchars($p['correo']) ?></a>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Panel: Coordinadores -->
  <?php if (!empty($coordinadores)): ?>
  <div class="dir-panel" id="dir-panel-coordinadores">
    <div class="dir-grid">
      <?php foreach ($coordinadores as $co): ?>
      <div class="dir-card">
        <div style="width:100%;height:200px;background:linear-gradient(135deg,#edf2ff 0%,#dce4ff 100%);display:flex;align-items:center;justify-content:center">
          <span class="material-symbols-rounded" style="font-size:64px;color:#5b73d4">manage_accounts</span>
        </div>
        <div class="dir-card-body">
          <div class="dir-card-nombre"><?= htmlspecialchars($co['nombre']) ?></div>
          <div class="dir-card-puesto">Coordinador<?php if ($co['carrera_nombre']): ?> — <?= htmlspecialchars($co['carrera_nombre']) ?><?php endif; ?></div>
          <?php if ($co['correo']): ?>
          <div class="dir-card-info">
            <div class="dir-card-row">
              <span class="material-symbols-rounded" aria-hidden="true">mail</span>
              <a href="mailto:<?= htmlspecialchars($co['correo']) ?>"><?= htmlspecialchars($co['correo']) ?></a>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Panel: Secretarías -->
  <?php if (!empty($secretarias)): ?>
  <div class="dir-panel" id="dir-panel-secretarias">
    <div class="dir-grid">
      <?php foreach ($secretarias as $s): ?>
      <div class="dir-card">
        <div style="width:100%;height:200px;background:linear-gradient(135deg,#f0edff 0%,#e0dcff 100%);display:flex;align-items:center;justify-content:center">
          <span class="material-symbols-rounded" style="font-size:64px;color:#8b7fd4">support_agent</span>
        </div>
        <div class="dir-card-body">
          <div class="dir-card-nombre"><?= htmlspecialchars($s['nombre']) ?></div>
          <?php if ($s['rol']): ?>
          <div class="dir-card-puesto"><?= htmlspecialchars($s['rol']) ?></div>
          <?php endif; ?>
          <div class="dir-card-info">
            <?php if ($s['telefono']): ?>
            <div class="dir-card-row">
              <span class="material-symbols-rounded" aria-hidden="true">call</span>
              <span><?= htmlspecialchars($s['telefono']) ?></span>
            </div>
            <?php endif; ?>
            <?php if ($s['correo']): ?>
            <div class="dir-card-row">
              <span class="material-symbols-rounded" aria-hidden="true">mail</span>
              <a href="mailto:<?= htmlspecialchars($s['correo']) ?>"><?= htmlspecialchars($s['correo']) ?></a>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <script>
  function dirTab(panel, btn) {
    document.querySelectorAll('.dir-panel').forEach(function(p){ p.classList.remove('active'); });
    document.querySelectorAll('.dir-tab-btn').forEach(function(b){ b.classList.remove('active'); });
    document.getElementById('dir-panel-' + panel).classList.add('active');
    btn.classList.add('active');
  }
  </script>

</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
