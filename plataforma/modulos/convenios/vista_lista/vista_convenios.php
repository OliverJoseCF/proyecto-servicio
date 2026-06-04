<?php
require_once __DIR__ . '/../src/security_headers.php';
require_once __DIR__ . '/../../../shared/config.php';

// ── Filtros GET ──────────────────────────────────────────────────
$carrerasValidas = ['IADEV','IM','ISC','II','LG','IGE'];
$tiposValidos    = ['residencia','servicio_social','practicas','otro'];
$sectoresValidos = ['privado','publico','ac','otro'];

$carrera = isset($_GET['carrera']) && in_array($_GET['carrera'], $carrerasValidas, true) ? $_GET['carrera'] : '';
$tipo    = isset($_GET['tipo'])    && in_array($_GET['tipo'],    $tiposValidos,    true) ? $_GET['tipo']    : '';
$sector  = isset($_GET['sector'])  && in_array($_GET['sector'],  $sectoresValidos,  true) ? $_GET['sector']  : '';

$carreraNombres = [
    'IADEV' => 'Ing. en Animación Digital y Efectos Visuales',
    'IM'    => 'Ingeniería Mecatrónica',
    'ISC'   => 'Ing. en Sistemas Computacionales',
    'II'    => 'Ingeniería Industrial',
    'LG'    => 'Gastronomía',
    'IGE'   => 'Ing. en Gestión Empresarial',
];
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
.filtros-bar {
    display: flex; flex-wrap: wrap; gap: 10px; align-items: center;
    padding: 16px 24px; background: #f8f9ff;
    border-bottom: 1px solid #e8eaf2; margin-bottom: 20px;
}
.filtro-label { font-size: 12px; font-weight: 700; color: #8892a8; text-transform: uppercase; letter-spacing: 1px; }
.filtro-pills { display: flex; gap: 6px; flex-wrap: wrap; }
.filtro-pill {
    padding: 5px 14px; border-radius: 99px; font-size: 12px; font-weight: 600;
    border: 1.5px solid #d0d5e8; background: #fff; color: #4a5170;
    text-decoration: none; transition: all .18s;
}
.filtro-pill:hover { border-color: #32129a; color: #32129a; }
.filtro-pill.active { background: #32129a; color: #fff; border-color: #32129a; }
.conv-table-wrap { padding: 0 24px 40px; overflow-x: auto; }
.conv-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.conv-table th {
    background: #f0f2f7; color: #4a5170; font-weight: 700;
    padding: 10px 14px; text-align: left; font-size: 12px;
    text-transform: uppercase; letter-spacing: .5px; border-bottom: 2px solid #e8eaf2;
}
.conv-table td { padding: 12px 14px; border-bottom: 1px solid #f0f2f7; vertical-align: middle; }
.conv-table tr:hover td { background: #f8f9ff; }
.badge {
    display: inline-block; padding: 3px 10px; border-radius: 99px;
    font-size: 11px; font-weight: 700;
}
.badge-tipo    { background: #ede9ff; color: #32129a; }
.badge-sector  { background: #e0f2fe; color: #0369a1; }
.badge-vence   { background: #fef3c7; color: #92400e; }
.badge-vencido { background: #fee2e2; color: #991b1b; }
.conv-empty { text-align: center; padding: 3rem; color: #9ca3af; }
.fila-busqueda { display:flex;align-items:center;gap:12px;padding:20px 24px 0; }
.fila-busqueda h1 { font-size:1.4rem;font-weight:700;color:#1a0960;margin:0; }
</style>

<main id="main">
  <div class="fila-busqueda">
    <h1>Convenios<?= $nombreCarrera ? ' — ' . htmlspecialchars($nombreCarrera) : '' ?></h1>
    <a href="../index.php" style="margin-left:auto;color:#32129a;font-size:13px;font-weight:600;text-decoration:none">
      ← Volver
    </a>
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
  <div class="conv-table-wrap">
    <table class="conv-table">
      <thead>
        <tr>
          <th>Empresa</th>
          <th>Tipo</th>
          <th>Sector</th>
          <th>Carrera</th>
          <th>Contacto</th>
          <th>Correo</th>
          <th>Vencimiento</th>
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
          <td><span class="badge badge-sector"><?= htmlspecialchars($sectorLabels[$cv['sector']] ?? $cv['sector']) ?></span></td>
          <td><?= htmlspecialchars($cv['carrera_clave'] ?? '—') ?></td>
          <td><?= htmlspecialchars($cv['nombre_contacto'] ?? '—') ?></td>
          <td>
            <?php if ($cv['correo_contacto']): ?>
              <a href="mailto:<?= htmlspecialchars($cv['correo_contacto']) ?>" style="color:#32129a">
                <?= htmlspecialchars($cv['correo_contacto']) ?>
              </a>
            <?php else: ?>—<?php endif; ?>
          </td>
          <td>
            <span class="badge <?= $vencida ? 'badge-vencido' : 'badge-vence' ?>">
              <?= htmlspecialchars($fechaStr) ?>
            </span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
