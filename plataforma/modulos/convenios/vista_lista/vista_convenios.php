<?php
require_once __DIR__ . '/../src/security_headers.php';
require_once __DIR__ . '/../../../shared/config.php';

// ── Carreras desde BD ────────────────────────────────────────────
$carreraNombres  = [];
$carrerasValidas = [];
try {
    $dbC = getPDO(DB_NAME);
    foreach ($dbC->query('SELECT clave, nombre FROM carreras WHERE activo=1 ORDER BY orden')->fetchAll() as $row) {
        $carrerasValidas[]              = $row['clave'];
        $carreraNombres[$row['clave']]  = $row['nombre'];
    }
} catch (\Throwable $e) {
    // fallback vacío — no rompe la página
}

// ── Filtros GET ──────────────────────────────────────────────────
$tiposValidos    = ['residencia','servicio_social','practicas','otro'];
$sectoresValidos = ['privado','publico','ac','otro'];

$carrera = isset($_GET['carrera']) && in_array($_GET['carrera'], $carrerasValidas, true) ? $_GET['carrera'] : '';
$tipo    = isset($_GET['tipo'])    && in_array($_GET['tipo'],    $tiposValidos,    true) ? $_GET['tipo']    : '';
$sector  = isset($_GET['sector'])  && in_array($_GET['sector'],  $sectoresValidos,  true) ? $_GET['sector']  : '';
$tipoLabels = [
    'residencia'     => 'Residencia profesional',
    'servicio_social'=> 'Servicio social',
    'practicas'      => 'Prácticas profesionales',
    'otro'           => 'Otro',
];
$sectorLabels = [
    'privado' => 'Privado',
    'publico' => 'Público',
    'ac'      => 'Asociación Civil',
    'otro'    => 'Otro',
];

// ── Query dinámica ───────────────────────────────────────────────
try {
    $db     = getPDO(DB_NAME);
    $where  = ['cv.activo = 1'];
    $params = [];

    if ($carrera !== '') {
        $where[]  = 'c.clave = ?';
        $params[] = $carrera;
    }
    if ($tipo !== '') {
        $where[]  = 'cv.tipo_convenio = ?';
        $params[] = $tipo;
    }
    if ($sector !== '') {
        $where[]  = 'cv.sector = ?';
        $params[] = $sector;
    }

    $sql = 'SELECT cv.id, cv.nombre, cv.tipo_convenio, cv.sector,
                   cv.nombre_contacto, cv.correo_contacto, cv.telefono_contacto,
                   cv.logo, cv.vencimiento,
                   c.clave AS carrera_clave, c.nombre AS carrera_nombre
            FROM convenios cv
            LEFT JOIN carreras c ON cv.carrera_id = c.id
            WHERE ' . implode(' AND ', $where) . '
            ORDER BY cv.nombre';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $convenios = $stmt->fetchAll();
    $db_ok = true;
} catch (\Throwable $e) {
    $convenios = [];
    $db_ok     = false;
}

$nombreCarrera = $carrera !== '' && isset($carreraNombres[$carrera]) ? $carreraNombres[$carrera] : '';

$tsj_module    = 'convenios';
$tsj_title     = 'Convenios' . ($nombreCarrera ? ' — ' . $nombreCarrera : '');
$tsj_extra_css = ['estilo/estilo.css'];
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../../shared/header.php';
?>

<style>
.conv-page { max-width: 960px; margin: 0 auto; padding: 24px 20px 56px; }
.conv-header { display:flex; align-items:center; gap:12px; margin-bottom:24px; }
.conv-header h1 { font-size:1.4rem; font-weight:700; color:#1a0960; margin:0; }
.conv-volver { margin-left:auto; color:#32129a; font-size:13px; font-weight:600; text-decoration:none; white-space:nowrap; }
.conv-volver:hover { text-decoration:underline; }

.filtros-bar {
    display: flex; flex-wrap: wrap; gap: 8px; align-items: center;
    padding: 14px 18px; background: #f8f9ff;
    border: 1px solid #e8eaf2; border-radius: 10px; margin-bottom: 20px;
}
.filtro-label { font-size: 11px; font-weight: 700; color: #8892a8; text-transform: uppercase; letter-spacing: 1px; white-space:nowrap; }
.filtro-pills { display: flex; gap: 6px; flex-wrap: wrap; }
.filtro-pill {
    padding: 4px 12px; border-radius: 99px; font-size: 12px; font-weight: 600;
    border: 1.5px solid #d0d5e8; background: #fff; color: #4a5170;
    text-decoration: none; transition: all .18s;
}
.filtro-pill:hover { border-color: #32129a; color: #32129a; }
.filtro-pill.active { background: #32129a; color: #fff; border-color: #32129a; }

.conv-table-wrap { overflow-x: auto; border-radius: 10px; border: 1px solid #e8eaf2; }
.conv-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.conv-table th {
    background: #f0f2f7; color: #4a5170; font-weight: 700;
    padding: 10px 14px; text-align: left; font-size: 11px;
    text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 2px solid #e8eaf2; white-space: nowrap;
}
.conv-table td { padding: 11px 14px; border-bottom: 1px solid #f0f2f7; vertical-align: middle; }
.conv-table tbody tr:last-child td { border-bottom: none; }
.conv-table tr:hover td { background: #f8f9ff; }
.badge {
    display: inline-block; padding: 3px 9px; border-radius: 99px;
    font-size: 11px; font-weight: 700; white-space: nowrap;
}
.badge-tipo    { background: #ede9ff; color: #32129a; }
.badge-sector  { background: #e0f2fe; color: #0369a1; }
.badge-vence   { background: #fef3c7; color: #92400e; }
.badge-vencido { background: #fee2e2; color: #991b1b; }
.conv-empty { text-align: center; padding: 3rem; color: #9ca3af; font-size: 14px; }
.conv-count { font-size: 12px; color: #8892a8; margin-bottom: 10px; }

/* Ocultar columnas menos importantes en pantallas pequeñas */
@media (max-width: 768px) {
  .conv-table .col-correo,
  .conv-table .col-vence { display: none; }
}
@media (max-width: 540px) {
  .conv-table .col-sector,
  .conv-table .col-carrera { display: none; }
}
</style>

<main id="main">
<div class="conv-page">
  <div class="conv-header">
    <h1>Convenios<?= $nombreCarrera ? ' — ' . htmlspecialchars($nombreCarrera) : '' ?></h1>
    <a href="../index.php" class="conv-volver">← Volver</a>
  </div>

  <!-- ── Filtros ────────────────────────────────────────────── -->
  <?php
  // Construir URL base manteniendo los filtros actuales excepto el que se cambia
  function filtroUrl(string $campo, string $val): string {
      $params = array_filter([
          'carrera' => $_GET['carrera'] ?? '',
          'tipo'    => $_GET['tipo']    ?? '',
          'sector'  => $_GET['sector']  ?? '',
      ]);
      if ($val === '') {
          unset($params[$campo]);
      } else {
          $params[$campo] = $val;
      }
      return '?' . http_build_query($params);
  }
  ?>
  <div class="filtros-bar">
    <span class="filtro-label">Tipo:</span>
    <div class="filtro-pills">
      <a href="<?= filtroUrl('tipo','') ?>" class="filtro-pill <?= $tipo===''?'active':'' ?>">Todos</a>
      <?php foreach ($tipoLabels as $k => $l): ?>
      <a href="<?= filtroUrl('tipo',$k) ?>" class="filtro-pill <?= $tipo===$k?'active':'' ?>"><?= $l ?></a>
      <?php endforeach; ?>
    </div>
    <span class="filtro-label" style="margin-left:16px">Sector:</span>
    <div class="filtro-pills">
      <a href="<?= filtroUrl('sector','') ?>" class="filtro-pill <?= $sector===''?'active':'' ?>">Todos</a>
      <?php foreach ($sectorLabels as $k => $l): ?>
      <a href="<?= filtroUrl('sector',$k) ?>" class="filtro-pill <?= $sector===$k?'active':'' ?>"><?= $l ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ── Tabla ─────────────────────────────────────────────── -->
  <?php if (!empty($convenios)): ?>
  <p class="conv-count"><?= count($convenios) ?> convenio<?= count($convenios) !== 1 ? 's' : '' ?> encontrado<?= count($convenios) !== 1 ? 's' : '' ?></p>
  <?php endif; ?>
  <div class="conv-table-wrap">
    <table class="conv-table">
      <thead>
        <tr>
          <th>Empresa</th>
          <th>Tipo</th>
          <th class="col-sector">Sector</th>
          <th class="col-carrera">Carrera</th>
          <th>Contacto</th>
          <th class="col-correo">Correo</th>
          <th class="col-vence">Vencimiento</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($convenios)): ?>
        <tr><td colspan="7" class="conv-empty">No hay convenios con los filtros seleccionados.</td></tr>
        <?php endif; ?>
        <?php foreach ($convenios as $cv):
          $venceTs = $cv['vencimiento'] ? strtotime($cv['vencimiento']) : null;
          $hoyTs   = time();
          $vencida = $venceTs && $venceTs < $hoyTs;
          $fechaStr= $venceTs ? date('d/m/Y', $venceTs) : '—';
        ?>
        <tr>
          <td style="font-weight:600;color:#1a0960"><?= htmlspecialchars($cv['nombre']) ?></td>
          <td><span class="badge badge-tipo"><?= htmlspecialchars($tipoLabels[$cv['tipo_convenio']] ?? $cv['tipo_convenio']) ?></span></td>
          <td class="col-sector"><span class="badge badge-sector"><?= htmlspecialchars($sectorLabels[$cv['sector']] ?? $cv['sector']) ?></span></td>
          <td class="col-carrera"><?= htmlspecialchars($cv['carrera_clave'] ?? '—') ?></td>
          <td><?= htmlspecialchars($cv['nombre_contacto'] ?? '—') ?></td>
          <td class="col-correo">
            <?php if ($cv['correo_contacto']): ?>
              <a href="mailto:<?= htmlspecialchars($cv['correo_contacto']) ?>" style="color:#32129a;font-size:12.5px">
                <?= htmlspecialchars($cv['correo_contacto']) ?>
              </a>
            <?php else: ?>—<?php endif; ?>
          </td>
          <td class="col-vence">
            <span class="badge <?= $vencida ? 'badge-vencido' : 'badge-vence' ?>">
              <?= htmlspecialchars($fechaStr) ?>
            </span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div><!-- /conv-page -->
</main>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
