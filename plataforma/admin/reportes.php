<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'reportes';
$adm_title = 'Reportes';

$topLibros = $atrasados = $porMes = $porCarrera = $tasaSol = $tasaSug = $bitacora = [];
$db_ok = true;

try {
    $db = getPDO(DB_NAME);

    // Libros más prestados (histórico completo)
    $topLibros = $db->query(
        'SELECT l.codigo, l.nombre, COUNT(*) total
         FROM prestamos p JOIN libros l ON p.libro_id = l.id
         GROUP BY p.libro_id ORDER BY total DESC LIMIT 10'
    )->fetchAll();

    // Préstamos atrasados (no devueltos con fecha vencida)
    $atrasados = $db->query(
        'SELECT p.*, l.nombre libro_nombre, DATEDIFF(CURDATE(), p.fecha_devolucion) dias_atraso
         FROM prestamos p JOIN libros l ON p.libro_id = l.id
         WHERE p.devuelto = 0 AND p.fecha_devolucion < CURDATE()
         ORDER BY dias_atraso DESC LIMIT 100'
    )->fetchAll();

    // Préstamos por mes (últimos 12 meses)
    $porMes = $db->query(
        "SELECT DATE_FORMAT(fecha_prestamo, '%Y-%m') mes, COUNT(*) total
         FROM prestamos
         WHERE fecha_prestamo >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
         GROUP BY mes ORDER BY mes"
    )->fetchAll();

    // Préstamos por carrera (histórico completo)
    $porCarrera = $db->query(
        "SELECT COALESCE(NULLIF(carrera,''), 'Sin especificar') carrera, COUNT(*) total
         FROM prestamos GROUP BY 1 ORDER BY total DESC LIMIT 15"
    )->fetchAll();

    // Tasa de aprobación de solicitudes de biblioteca y sugerencias de empresa
    $tasaSol = $db->query(
        'SELECT estado, COUNT(*) total FROM solicitudes_biblioteca GROUP BY estado'
    )->fetchAll(PDO::FETCH_KEY_PAIR);
    $tasaSug = $db->query(
        'SELECT estado, COUNT(*) total FROM sugerencias_empresa GROUP BY estado'
    )->fetchAll(PDO::FETCH_KEY_PAIR);

    // Bitácora — defensivo: la tabla puede no existir aún en BDs previas
    try {
        $bitacora = $db->query(
            'SELECT * FROM admin_log ORDER BY id DESC LIMIT 50'
        )->fetchAll();
    } catch (\PDOException $eLog) {
        $bitacora = [];
    }
} catch (\Throwable $e) {
    $db_ok  = false;
    $db_err = $e->getMessage();
}

$maxMes     = max(array_column($porMes, 'total') ?: [1]);
$maxCarrera = max(array_column($porCarrera, 'total') ?: [1]);
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Reportes y métricas</h1>
    <p class="adm-page-desc">Actividad de la plataforma para apoyar decisiones de acervo, convenios y operación.</p>
  </div>
</div>

<?php if (!$db_ok): ?>
<div class="adm-pending" style="background:#fef2f2;border-color:#fca5a5;color:#991b1b">
  <span class="material-symbols-rounded">error</span>
  <span><strong>Error de base de datos:</strong> <?= htmlspecialchars($db_err ?? '') ?></span>
</div>
<?php else: ?>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));gap:20px">

  <!-- ── Préstamos atrasados ── -->
  <div class="adm-form-card" style="grid-column:1/-1">
    <div class="adm-form-title">
      <span class="material-symbols-rounded">assignment_late</span> Préstamos atrasados (<?= count($atrasados) ?>)
      <a href="procesos/export.php?tipo=prestamos" class="adm-btn adm-btn--ghost adm-btn--sm" style="margin-left:auto">
        <span class="material-symbols-rounded">download</span> Exportar préstamos
      </a>
    </div>
    <?php if (empty($atrasados)): ?>
      <p style="font-size:13px;color:var(--tsj-gray-500);margin:0">No hay préstamos atrasados. 🎉</p>
    <?php else: ?>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead><tr><th>Estudiante</th><th>No. Control</th><th>Libro</th><th>Venció</th><th>Días de atraso</th></tr></thead>
        <tbody>
          <?php foreach ($atrasados as $a): ?>
          <tr>
            <td style="font-weight:600"><?= htmlspecialchars($a['estudiante_nombre']) ?></td>
            <td><?= htmlspecialchars($a['estudiante_control']) ?></td>
            <td><?= htmlspecialchars($a['libro_nombre']) ?></td>
            <td><?= htmlspecialchars($a['fecha_devolucion']) ?></td>
            <td><span class="adm-status adm-status--danger"><?= (int)$a['dias_atraso'] ?> día<?= $a['dias_atraso'] == 1 ? '' : 's' ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- ── Top libros ── -->
  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">trending_up</span> Libros más prestados (top 10)</div>
    <?php if (empty($topLibros)): ?>
      <p style="font-size:13px;color:var(--tsj-gray-500);margin:0">Aún no hay préstamos registrados.</p>
    <?php else: ?>
    <table class="adm-table">
      <thead><tr><th>#</th><th>Código</th><th>Título</th><th style="text-align:right">Préstamos</th></tr></thead>
      <tbody>
        <?php foreach ($topLibros as $i => $t): ?>
        <tr>
          <td style="color:var(--tsj-gray-400);font-weight:700"><?= $i + 1 ?></td>
          <td><code style="font-size:12px;background:var(--tsj-gray-100);padding:2px 6px;border-radius:4px"><?= htmlspecialchars($t['codigo']) ?></code></td>
          <td style="font-weight:600"><?= htmlspecialchars($t['nombre']) ?></td>
          <td style="text-align:right;font-weight:700;color:var(--tsj-blue)"><?= (int)$t['total'] ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

  <!-- ── Préstamos por carrera ── -->
  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">school</span> Préstamos por carrera</div>
    <?php if (empty($porCarrera)): ?>
      <p style="font-size:13px;color:var(--tsj-gray-500);margin:0">Aún no hay préstamos registrados.</p>
    <?php else: ?>
      <?php foreach ($porCarrera as $c): ?>
      <div style="margin-bottom:10px">
        <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:3px">
          <span style="font-weight:600;color:var(--tsj-gray-700)"><?= htmlspecialchars($c['carrera']) ?></span>
          <span style="font-weight:700;color:var(--tsj-blue)"><?= (int)$c['total'] ?></span>
        </div>
        <div style="height:8px;background:var(--tsj-gray-100);border-radius:99px;overflow:hidden">
          <div style="height:100%;width:<?= round($c['total'] / $maxCarrera * 100) ?>%;background:var(--tsj-blue);border-radius:99px"></div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- ── Préstamos por mes ── -->
  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">calendar_month</span> Préstamos por mes (últimos 12 meses)</div>
    <?php if (empty($porMes)): ?>
      <p style="font-size:13px;color:var(--tsj-gray-500);margin:0">Sin préstamos en los últimos 12 meses.</p>
    <?php else: ?>
      <?php foreach ($porMes as $m): ?>
      <div style="margin-bottom:10px">
        <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:3px">
          <span style="font-weight:600;color:var(--tsj-gray-700)"><?= htmlspecialchars($m['mes']) ?></span>
          <span style="font-weight:700;color:var(--tsj-blue)"><?= (int)$m['total'] ?></span>
        </div>
        <div style="height:8px;background:var(--tsj-gray-100);border-radius:99px;overflow:hidden">
          <div style="height:100%;width:<?= round($m['total'] / $maxMes * 100) ?>%;background:#16a34a;border-radius:99px"></div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- ── Tasas de aprobación ── -->
  <div style="grid-column:1/-1">
    <div class="adm-form-title" style="margin-bottom:12px">
      <span class="material-symbols-rounded">percent</span> Solicitudes y sugerencias
      <div style="margin-left:auto;display:flex;gap:8px;flex-wrap:wrap">
        <a href="procesos/export.php?tipo=solicitudes" class="adm-btn adm-btn--ghost adm-btn--sm"><span class="material-symbols-rounded">download</span> Solicitudes CSV</a>
        <a href="procesos/export.php?tipo=convenios" class="adm-btn adm-btn--ghost adm-btn--sm"><span class="material-symbols-rounded">download</span> Convenios CSV</a>
        <a href="procesos/export.php?tipo=controles" class="adm-btn adm-btn--ghost adm-btn--sm"><span class="material-symbols-rounded">download</span> Controles CSV</a>
      </div>
    </div>
    <?php
      $solTotal = array_sum($tasaSol);
      $sugTotal = array_sum($tasaSug);
    ?>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Pendientes</th>
            <th>Aprobadas</th>
            <th>Rechazadas</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="font-weight:600">Solicitudes de biblioteca</td>
            <td><?= (int)($tasaSol['pendiente'] ?? 0) ?></td>
            <td><span class="adm-status adm-status--ok"><?= (int)($tasaSol['aprobada'] ?? 0) ?></span></td>
            <td><span class="adm-status adm-status--danger"><?= (int)($tasaSol['rechazada'] ?? 0) ?></span></td>
            <td style="font-weight:700"><?= $solTotal ?></td>
          </tr>
          <tr>
            <td style="font-weight:600">Sugerencias de empresa</td>
            <td><?= (int)($tasaSug['pendiente'] ?? 0) ?></td>
            <td><span class="adm-status adm-status--ok"><?= (int)($tasaSug['aceptada'] ?? 0) ?></span></td>
            <td><span class="adm-status adm-status--danger"><?= (int)($tasaSug['rechazada'] ?? 0) ?></span></td>
            <td style="font-weight:700"><?= $sugTotal ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ── Bitácora ── -->
  <div style="grid-column:1/-1">
    <div class="adm-form-title" style="margin-bottom:12px"><span class="material-symbols-rounded">history</span> Últimas acciones del panel (bitácora)</div>
    <?php if (empty($bitacora)): ?>
    <div class="adm-table-wrap">
      <p class="adm-table-empty" style="margin:0">
        Sin registros todavía. La bitácora se llena automáticamente con cada acción del panel
        (requiere la tabla <code>admin_log</code> — ver kiosko_tsj.sql).
      </p>
    </div>
    <?php else: ?>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead><tr><th>Fecha y hora</th><th>Módulo</th><th>Acción</th><th>Detalle</th></tr></thead>
        <tbody>
          <?php foreach ($bitacora as $b): ?>
          <tr>
            <td style="white-space:nowrap;font-size:12.5px"><?= htmlspecialchars($b['created_at']) ?></td>
            <td><span class="adm-status adm-status--info"><?= htmlspecialchars($b['modulo']) ?></span></td>
            <td><code style="font-size:12px;background:var(--tsj-gray-100);padding:2px 6px;border-radius:4px"><?= htmlspecialchars($b['accion']) ?></code></td>
            <td style="font-size:12.5px;color:var(--tsj-gray-600)"><?= htmlspecialchars($b['detalle'] ?? '') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

</div>

<?php endif; ?>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
