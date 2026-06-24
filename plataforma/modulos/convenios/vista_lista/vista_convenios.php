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
} catch (\Throwable $e) {}

// ── Filtros GET ──────────────────────────────────────────────────
$tiposValidos    = ['residencia','servicio_social','practicas','otro'];
$sectoresValidos = ['privado','publico','ac','otro'];

$carrera = isset($_GET['carrera']) && in_array($_GET['carrera'], $carrerasValidas, true) ? $_GET['carrera'] : '';
$tipo    = isset($_GET['tipo'])    && in_array($_GET['tipo'],    $tiposValidos,    true) ? $_GET['tipo']    : '';
$sector  = isset($_GET['sector'])  && in_array($_GET['sector'],  $sectoresValidos,  true) ? $_GET['sector']  : '';

$tipoLabels = [
    'residencia'      => 'Residencia',
    'servicio_social' => 'Servicio social',
    'practicas'       => 'Prácticas',
    'otro'            => 'Otro',
];
$sectorLabels = [
    'privado' => 'Privado',
    'publico' => 'Público',
    'ac'      => 'Asoc. Civil',
    'otro'    => 'Otro',
];

// ── Query dinámica ───────────────────────────────────────────────
try {
    $db     = getPDO(DB_NAME);
    $where  = ['cv.activo = 1', '(cv.vencimiento IS NULL OR cv.vencimiento >= CURDATE())'];
    $params = [];

    if ($carrera !== '') {
        // Sin filas en convenio_carreras = aplica a todas las carreras; de lo contrario, solo si coincide
        $where[] = '(NOT EXISTS (SELECT 1 FROM convenio_carreras cc2 WHERE cc2.convenio_id = cv.id)
                     OR EXISTS  (SELECT 1 FROM convenio_carreras cc3
                                 JOIN carreras c3 ON cc3.carrera_id = c3.id
                                 WHERE cc3.convenio_id = cv.id AND c3.clave = ?))';
        $params[] = $carrera;
    }
    if ($tipo    !== '') { $where[] = 'cv.tipo_convenio = ?'; $params[] = $tipo; }
    if ($sector  !== '') { $where[] = 'cv.sector = ?';       $params[] = $sector; }

    $sql = 'SELECT cv.id, cv.nombre, cv.tipo_convenio, cv.sector,
                   cv.nombre_contacto, cv.correo_contacto, cv.telefono_contacto,
                   cv.logo, cv.vencimiento
            FROM convenios cv
            WHERE ' . implode(' AND ', $where) . '
            ORDER BY cv.nombre';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $convenios = $stmt->fetchAll();
} catch (\Throwable $e) {
    $convenios = [];
}

$nombreCarrera = $carrera !== '' && isset($carreraNombres[$carrera]) ? $carreraNombres[$carrera] : '';

$tsj_module           = 'convenios';
$tsj_title            = 'Convenios' . ($nombreCarrera ? ' — ' . $nombreCarrera : '');
$tsj_extra_css        = ['estilo/estilo.css'];
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../../shared/header.php';

function filtroUrl(string $campo, string $val): string {
    $p = array_filter([
        'carrera' => $_GET['carrera'] ?? '',
        'tipo'    => $_GET['tipo']    ?? '',
        'sector'  => $_GET['sector']  ?? '',
    ]);
    if ($val === '') unset($p[$campo]); else $p[$campo] = $val;
    return '?' . http_build_query($p);
}
?>

<main id="main">
<div class="cvl-page">

  <!-- Título -->
  <div class="tsj-page-header">
    <div class="tsj-page-header-line"></div>
    <h1><?php if ($nombreCarrera): ?>
      <?= htmlspecialchars($nombreCarrera) ?>
    <?php else: ?>
      Convenios <span class="tsj-accent">Empresariales</span>
    <?php endif; ?>
    </h1>
    <p class="tsj-page-header-sub">Empresas vinculadas para residencia profesional, servicio social y prácticas</p>
  </div>

  <!-- Botón volver -->
  <div class="cvl-nav">
    <a href="../index.php" class="cvl-volver">
      <span class="material-symbols-rounded">arrow_back</span>
      Volver a carreras
    </a>
  </div>

  <!-- Filtros -->
  <div class="cvl-filtros-bar">
    <div class="cvl-filtro-grupo">
      <span class="cvl-filtro-label">Tipo</span>
      <div class="cvl-pills">
        <a href="<?= filtroUrl('tipo','') ?>" class="cvl-pill <?= $tipo===''?'active':'' ?>">Todos</a>
        <?php foreach ($tipoLabels as $k => $l): ?>
        <a href="<?= filtroUrl('tipo',$k) ?>" class="cvl-pill <?= $tipo===$k?'active':'' ?>"><?= $l ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="cvl-filtro-grupo">
      <span class="cvl-filtro-label">Sector</span>
      <div class="cvl-pills">
        <a href="<?= filtroUrl('sector','') ?>" class="cvl-pill <?= $sector===''?'active':'' ?>">Todos</a>
        <?php foreach ($sectorLabels as $k => $l): ?>
        <a href="<?= filtroUrl('sector',$k) ?>" class="cvl-pill <?= $sector===$k?'active':'' ?>"><?= $l ?></a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Contador -->
  <?php if (!empty($convenios)): ?>
  <p class="cvl-count"><?= count($convenios) ?> empresa<?= count($convenios) !== 1 ? 's' : '' ?> encontrada<?= count($convenios) !== 1 ? 's' : '' ?></p>
  <?php endif; ?>

  <!-- Grid de cards -->
  <?php if (empty($convenios)): ?>
  <div class="cvl-empty">
    <span class="material-symbols-rounded">handshake</span>
    <p>No hay convenios con los filtros seleccionados.</p>
  </div>
  <?php else: ?>
  <div class="cvl-grid">
    <?php foreach ($convenios as $cv):
      $venceTs  = $cv['vencimiento'] ? strtotime($cv['vencimiento']) : null;
      $vencida  = $venceTs && $venceTs < strtotime('today'); // compara solo fecha
      $fechaStr = $venceTs ? date('d/m/Y', $venceTs) : null;
      $tipoLabel   = $tipoLabels[$cv['tipo_convenio']]   ?? $cv['tipo_convenio'];
      $sectorLabel = $sectorLabels[$cv['sector']] ?? $cv['sector'];
    ?>
    <div class="cvl-card">
      <div class="cvl-card-head">
        <div class="cvl-card-nombre"><?= htmlspecialchars($cv['nombre']) ?></div>
        <div class="cvl-card-badges">
          <span class="cvl-badge cvl-badge--tipo"><?= htmlspecialchars($tipoLabel) ?></span>
          <span class="cvl-badge cvl-badge--sector"><?= htmlspecialchars($sectorLabel) ?></span>
        </div>
      </div>

      <?php if ($cv['nombre_contacto'] || $cv['correo_contacto'] || $cv['telefono_contacto']): ?>
      <div class="cvl-card-body">
        <?php if ($cv['nombre_contacto']): ?>
        <div class="cvl-card-dato">
          <span class="material-symbols-rounded">person</span>
          <?= htmlspecialchars($cv['nombre_contacto']) ?>
        </div>
        <?php endif; ?>
        <?php if ($cv['correo_contacto']): ?>
        <div class="cvl-card-dato">
          <span class="material-symbols-rounded">mail</span>
          <a href="mailto:<?= htmlspecialchars($cv['correo_contacto']) ?>" class="cvl-mailto">
            <?= htmlspecialchars($cv['correo_contacto']) ?>
          </a>
        </div>
        <?php endif; ?>
        <?php if ($cv['telefono_contacto']): ?>
        <div class="cvl-card-dato">
          <span class="material-symbols-rounded">call</span>
          <?= htmlspecialchars($cv['telefono_contacto']) ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <?php if ($fechaStr): ?>
      <div class="cvl-card-footer">
        <span class="cvl-badge <?= $vencida ? 'cvl-badge--vencido' : 'cvl-badge--vence' ?>">
          <span class="material-symbols-rounded">event</span>
          Vence <?= htmlspecialchars($fechaStr) ?>
        </span>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</div>
</main>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
