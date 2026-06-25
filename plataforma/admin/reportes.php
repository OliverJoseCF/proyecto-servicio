<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'reportes';
$adm_title = 'Reportes y estadísticas';

$topLibros = $atrasados = $porMes = $porCarrera = $tasaSol = $tasaSug = $bitacora = [];
$convSector = $convTipo = $convPorVencer = $actividadAdmin = [];
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

    // Convenios por sector
    $convSector = $db->query(
        "SELECT sector, COUNT(*) total FROM convenios WHERE activo=1 GROUP BY sector ORDER BY total DESC"
    )->fetchAll();

    // Convenios por tipo
    $convTipo = $db->query(
        "SELECT tipo_convenio, COUNT(*) total FROM convenios WHERE activo=1 GROUP BY tipo_convenio ORDER BY total DESC"
    )->fetchAll();

    // Convenios próximos a vencer (90 días)
    $convPorVencer = $db->query(
        "SELECT nombre, sector, tipo_convenio, vencimiento,
                DATEDIFF(vencimiento, CURDATE()) dias_restantes
         FROM convenios
         WHERE activo=1 AND vencimiento IS NOT NULL AND vencimiento >= CURDATE()
           AND vencimiento <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)
         ORDER BY vencimiento ASC LIMIT 20"
    )->fetchAll();

    // Actividad por administrador (desde bitácora)
    try {
        $actividadAdmin = $db->query(
            "SELECT COALESCE(NULLIF(admin_nombre,''), 'Cuenta maestra') admin,
                    COUNT(*) total,
                    MAX(created_at) ultima_accion
             FROM admin_log
             GROUP BY admin_nombre ORDER BY total DESC LIMIT 10"
        )->fetchAll();
    } catch (\PDOException $e) { $actividadAdmin = []; }

    // Bitácora — defensivo: la tabla puede no existir aún en BDs previas
    $bitacoraModulos = [];
    $bitacoraTotal   = 0;
    $bitacoraLimite  = 200;
    try {
        // Defensivo: admin_nombre puede no existir en BDs previas al multi-admin
        try {
            $bitacora = $db->query(
                'SELECT created_at, admin_nombre, modulo, accion, detalle FROM admin_log ORDER BY id DESC LIMIT ' . $bitacoraLimite
            )->fetchAll();
        } catch (\PDOException $eCol) {
            $bitacora = $db->query(
                'SELECT created_at, modulo, accion, detalle FROM admin_log ORDER BY id DESC LIMIT ' . $bitacoraLimite
            )->fetchAll();
        }
        $bitacoraTotal   = (int)$db->query('SELECT COUNT(*) FROM admin_log')->fetchColumn();
        $bitacoraModulos = $db->query('SELECT DISTINCT modulo FROM admin_log ORDER BY modulo')->fetchAll(PDO::FETCH_COLUMN);
    } catch (\PDOException $eLog) {
        $bitacora = [];
    }
} catch (\Throwable $e) {
    $db_ok  = false;
    $db_err = $e->getMessage();
}

$maxMes        = max(array_column($porMes, 'total') ?: [1]);
$maxCarrera    = max(array_column($porCarrera, 'total') ?: [1]);
$maxConvSector = max(array_column($convSector, 'total') ?: [1]);
$maxConvTipo   = max(array_column($convTipo, 'total') ?: [1]);
$maxAdmin      = max(array_column($actividadAdmin, 'total') ?: [1]);
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Reportes y estadísticas</h1>
    <p class="adm-page-desc">Actividad de la plataforma para apoyar decisiones de acervo, convenios y operación.</p>
  </div>
</div>

<?php if (!$db_ok): ?>
<div class="adm-pending" style="background:#fef2f2;border-color:#fca5a5;color:#991b1b">
  <span class="material-symbols-rounded">error</span>
  <span><strong>Error de base de datos:</strong> <?= htmlspecialchars($db_err ?? '') ?></span>
</div>
<?php else: ?>

<div style="display:flex;flex-direction:column;gap:20px">

  <!-- ── Préstamos atrasados ── -->
  <div class="adm-form-card">
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
            <td><?= $a['fecha_devolucion'] ? date('d/m/Y', strtotime($a['fecha_devolucion'])) : '—' ?></td>
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
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead><tr><th>#</th><th>Código</th><th>Título</th><th style="text-align:right">Préstamos</th></tr></thead>
        <tbody>
          <?php foreach ($topLibros as $i => $t): ?>
          <tr>
            <td style="color:var(--tsj-gray-400);font-weight:700;width:40px"><?= $i + 1 ?></td>
            <td style="width:120px"><code style="font-size:12px;background:var(--tsj-gray-100);padding:2px 6px;border-radius:4px"><?= htmlspecialchars($t['codigo']) ?></code></td>
            <td style="font-weight:600"><?= htmlspecialchars($t['nombre']) ?></td>
            <td style="text-align:right;font-weight:700;color:var(--tsj-blue);width:100px"><?= (int)$t['total'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- ── Préstamos por carrera y por mes en dos columnas ── -->
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px">

    <!-- Préstamos por carrera -->
    <div class="adm-form-card" style="min-width:0">
      <div class="adm-form-title"><span class="material-symbols-rounded">school</span> Préstamos por carrera</div>
      <?php if (empty($porCarrera)): ?>
        <p style="font-size:13px;color:var(--tsj-gray-500);margin:0">Aún no hay préstamos registrados.</p>
      <?php else: ?>
        <?php foreach ($porCarrera as $c): ?>
        <div style="margin-bottom:10px">
          <div style="display:flex;justify-content:space-between;gap:8px;font-size:12.5px;margin-bottom:4px">
            <span style="font-weight:600;color:var(--tsj-gray-700);overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($c['carrera']) ?></span>
            <span style="font-weight:700;color:var(--tsj-blue);flex-shrink:0"><?= (int)$c['total'] ?></span>
          </div>
          <div style="height:8px;background:var(--tsj-gray-100);border-radius:99px;overflow:hidden">
            <div style="height:100%;width:<?= round($c['total'] / $maxCarrera * 100) ?>%;background:var(--tsj-blue);border-radius:99px"></div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Préstamos por mes -->
    <div class="adm-form-card" style="min-width:0">
      <div class="adm-form-title"><span class="material-symbols-rounded">calendar_month</span> Préstamos por mes (últimos 12 meses)</div>
      <?php if (empty($porMes)): ?>
        <p style="font-size:13px;color:var(--tsj-gray-500);margin:0">Sin préstamos en los últimos 12 meses.</p>
      <?php else: ?>
        <?php
        $mesesEs = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
                    '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
        foreach ($porMes as $m):
          [$anio, $numMes] = explode('-', $m['mes']);
          $mesLabel = ($mesesEs[$numMes] ?? $numMes) . ' ' . $anio;
        ?>
        <div style="margin-bottom:10px">
          <div style="display:flex;justify-content:space-between;gap:8px;font-size:12.5px;margin-bottom:4px">
            <span style="font-weight:600;color:var(--tsj-gray-700)"><?= htmlspecialchars($mesLabel) ?></span>
            <span style="font-weight:700;color:#16a34a;flex-shrink:0"><?= (int)$m['total'] ?></span>
          </div>
          <div style="height:8px;background:var(--tsj-gray-100);border-radius:99px;overflow:hidden">
            <div style="height:100%;width:<?= round($m['total'] / $maxMes * 100) ?>%;background:#16a34a;border-radius:99px"></div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>

  <!-- ── Convenios: distribución por sector y tipo ── -->
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px">

    <div class="adm-form-card" style="min-width:0">
      <div class="adm-form-title"><span class="material-symbols-rounded">domain</span> Convenios activos por sector</div>
      <?php
      $sectorLabels = ['privado'=>'Empresa privada','publico'=>'Sector público','ac'=>'Asociación civil','otro'=>'Otro'];
      $sectorColors = ['privado'=>'var(--tsj-blue)','publico'=>'#16a34a','ac'=>'#b45309','otro'=>'var(--tsj-gray-400)'];
      if (empty($convSector)): ?>
        <p style="font-size:13px;color:var(--tsj-gray-500);margin:0">Sin convenios activos.</p>
      <?php else: ?>
        <?php foreach ($convSector as $cs): ?>
        <div style="margin-bottom:10px">
          <div style="display:flex;justify-content:space-between;gap:8px;font-size:12.5px;margin-bottom:4px">
            <span style="font-weight:600;color:var(--tsj-gray-700)"><?= htmlspecialchars($sectorLabels[$cs['sector']] ?? $cs['sector']) ?></span>
            <span style="font-weight:700;color:<?= $sectorColors[$cs['sector']] ?? 'var(--tsj-blue)' ?>;flex-shrink:0"><?= (int)$cs['total'] ?></span>
          </div>
          <div style="height:8px;background:var(--tsj-gray-100);border-radius:99px;overflow:hidden">
            <div style="height:100%;width:<?= round($cs['total'] / $maxConvSector * 100) ?>%;background:<?= $sectorColors[$cs['sector']] ?? 'var(--tsj-blue)' ?>;border-radius:99px"></div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="adm-form-card" style="min-width:0">
      <div class="adm-form-title"><span class="material-symbols-rounded">handshake</span> Convenios activos por tipo</div>
      <?php
      $tipoLabels = ['residencia'=>'Residencia profesional','servicio_social'=>'Servicio social','practicas'=>'Prácticas','otro'=>'Otro'];
      if (empty($convTipo)): ?>
        <p style="font-size:13px;color:var(--tsj-gray-500);margin:0">Sin convenios activos.</p>
      <?php else: ?>
        <?php foreach ($convTipo as $ct): ?>
        <div style="margin-bottom:10px">
          <div style="display:flex;justify-content:space-between;gap:8px;font-size:12.5px;margin-bottom:4px">
            <span style="font-weight:600;color:var(--tsj-gray-700)"><?= htmlspecialchars($tipoLabels[$ct['tipo_convenio']] ?? $ct['tipo_convenio']) ?></span>
            <span style="font-weight:700;color:#b45309;flex-shrink:0"><?= (int)$ct['total'] ?></span>
          </div>
          <div style="height:8px;background:var(--tsj-gray-100);border-radius:99px;overflow:hidden">
            <div style="height:100%;width:<?= round($ct['total'] / $maxConvTipo * 100) ?>%;background:#b45309;border-radius:99px"></div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>

  <!-- ── Convenios próximos a vencer (90 días) ── -->
  <div class="adm-form-card">
    <div class="adm-form-title">
      <span class="material-symbols-rounded">event_busy</span> Convenios por vencer en los próximos 90 días
      <a href="convenios.php" class="adm-btn adm-btn--ghost adm-btn--sm" style="margin-left:auto">Ver todos</a>
    </div>
    <?php if (empty($convPorVencer)): ?>
      <p style="font-size:13px;color:var(--tsj-gray-500);margin:0">Ningún convenio vence en los próximos 90 días.</p>
    <?php else: ?>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead><tr><th>Empresa</th><th>Tipo</th><th>Sector</th><th>Vence</th><th>Días restantes</th></tr></thead>
        <tbody>
          <?php foreach ($convPorVencer as $cv):
            $dias = (int)$cv['dias_restantes'];
            $urgencia = $dias <= 14 ? 'danger' : ($dias <= 30 ? 'warn' : 'ok');
          ?>
          <tr>
            <td style="font-weight:600"><?= htmlspecialchars($cv['nombre']) ?></td>
            <td><?= htmlspecialchars($tipoLabels[$cv['tipo_convenio']] ?? $cv['tipo_convenio']) ?></td>
            <td><?= htmlspecialchars($sectorLabels[$cv['sector']] ?? $cv['sector']) ?></td>
            <td><?= date('d/m/Y', strtotime($cv['vencimiento'])) ?></td>
            <td><span class="adm-status adm-status--<?= $urgencia ?>"><?= $dias ?> día<?= $dias === 1 ? '' : 's' ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- ── Actividad por administrador ── -->
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px">

    <div class="adm-form-card" style="min-width:0">
      <div class="adm-form-title"><span class="material-symbols-rounded">manage_accounts</span> Actividad por administrador</div>
      <?php if (empty($actividadAdmin)): ?>
        <p style="font-size:13px;color:var(--tsj-gray-500);margin:0">Sin registros en la bitácora todavía.</p>
      <?php else: ?>
        <?php foreach ($actividadAdmin as $aa): ?>
        <div style="margin-bottom:12px">
          <div style="display:flex;justify-content:space-between;gap:8px;font-size:12.5px;margin-bottom:2px">
            <span style="font-weight:600;color:var(--tsj-gray-700);overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($aa['admin']) ?></span>
            <span style="font-weight:700;color:var(--tsj-blue);flex-shrink:0"><?= (int)$aa['total'] ?> acciones</span>
          </div>
          <div style="font-size:11.5px;color:var(--tsj-gray-400);margin-bottom:4px">
            Última acción: <?= $aa['ultima_accion'] ? date('d/m/Y g:i A', strtotime($aa['ultima_accion'])) : '—' ?>
          </div>
          <div style="height:6px;background:var(--tsj-gray-100);border-radius:99px;overflow:hidden">
            <div style="height:100%;width:<?= round($aa['total'] / $maxAdmin * 100) ?>%;background:var(--tsj-blue);border-radius:99px"></div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- ── Tasas de aprobación ── -->
    <div style="min-width:0">
      <div class="adm-form-title" style="margin-bottom:12px">
        <span class="material-symbols-rounded">percent</span> Solicitudes y sugerencias
        <div style="margin-left:auto;display:flex;gap:8px;flex-wrap:wrap">
          <a href="procesos/export.php?tipo=solicitudes" class="adm-btn adm-btn--ghost adm-btn--sm"><span class="material-symbols-rounded">download</span> Solicitudes CSV</a>
          <a href="procesos/export.php?tipo=convenios" class="adm-btn adm-btn--ghost adm-btn--sm"><span class="material-symbols-rounded">download</span> Convenios CSV</a>
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

  </div>

  <!-- ── Bitácora ── -->
  <div>
    <div class="adm-form-title" style="margin-bottom:12px">
      <span class="material-symbols-rounded">history</span> Últimas acciones del panel (bitácora)
      <a href="procesos/export.php?tipo=bitacora" class="adm-btn adm-btn--ghost adm-btn--sm" style="margin-left:auto">
        <span class="material-symbols-rounded">download</span> Exportar CSV
      </a>
    </div>
    <?php if (empty($bitacora)): ?>
    <div class="adm-table-wrap">
      <p class="adm-table-empty" style="margin:0">
        Sin registros todavía. La bitácora se llena automáticamente con cada acción del panel
        (requiere la tabla <code>admin_log</code> — ver kiosko_tsj.sql).
      </p>
    </div>
    <?php else: ?>

    <?php $moduloLabels = [
        'login'         => 'Acceso',
        'biblioteca'    => 'Biblioteca',
        'convenios'     => 'Convenios',
        'admins'        => 'Administradores',
        'configuracion' => 'Configuración',
        'inicio'        => 'Inicio',
        'visitantes'    => 'Visitantes',
        'requisitos'    => 'Requisitos',
        'horarios'      => 'Horarios',
        'respaldos'     => 'Respaldo',
        'backup_export' => 'Respaldo',
        'backup_import' => 'Importación',
    ]; ?>

    <!-- Filtros de bitácora -->
    <div style="display:flex;gap:12px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
      <input type="text" id="log-buscar" placeholder="Buscar por acción o detalle…" oninput="filtrarBitacora()"
             style="flex:1;min-width:220px;max-width:420px;padding:9px 12px;border:1.5px solid var(--tsj-gray-200);border-radius:8px;font-size:13px;font-family:inherit">
      <select id="log-modulo" onchange="filtrarBitacora()"
              style="padding:9px 12px;border:1.5px solid var(--tsj-gray-200);border-radius:8px;font-size:13px;font-family:inherit;background:#fff">
        <option value="">Todos los módulos</option>
        <?php foreach ($bitacoraModulos as $mod):
          $lbl = $moduloLabels[$mod] ?? $mod; ?>
        <option value="<?= htmlspecialchars($mod) ?>"><?= htmlspecialchars($lbl) ?></option>
        <?php endforeach; ?>
      </select>
      <span id="log-contador" style="font-size:12.5px;color:var(--tsj-gray-500);margin-left:auto"></span>
    </div>

    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead><tr><th>Fecha y hora</th><th>Realizó</th><th>Módulo</th><th>Detalle</th></tr></thead>
        <tbody id="log-tbody">
          <?php foreach ($bitacora as $b):
            $autor     = trim($b['admin_nombre'] ?? '');
            if ($autor === '') $autor = 'Cuenta maestra';
            $modLabel  = $moduloLabels[$b['modulo']] ?? $b['modulo'];
            $isLogin   = $b['modulo'] === 'login';
            $fechaFmt  = $b['created_at'] ? date('d/m/Y g:i A', strtotime($b['created_at'])) : '—';
          ?>
          <tr data-modulo="<?= htmlspecialchars($b['modulo']) ?>"
              data-search="<?= htmlspecialchars(mb_strtolower($autor . ' ' . $modLabel . ' ' . ($b['accion'] ?? '') . ' ' . ($b['detalle'] ?? ''))) ?>">
            <td style="white-space:nowrap;font-size:12.5px"><?= htmlspecialchars($fechaFmt) ?></td>
            <td style="font-size:12.5px;font-weight:600;white-space:nowrap"><?= htmlspecialchars($autor) ?></td>
            <td><span class="adm-status <?= $isLogin ? 'adm-status--ok' : 'adm-status--info' ?>"><?= htmlspecialchars($modLabel) ?></span></td>
            <td style="font-size:12.5px;color:var(--tsj-gray-600)"><?= htmlspecialchars($b['detalle'] ?? '') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p style="font-size:12px;color:var(--tsj-gray-400);margin:8px 0 0">
      <?php if ($bitacoraTotal > $bitacoraLimite): ?>
        Mostrando las <?= $bitacoraLimite ?> acciones más recientes de <?= number_format($bitacoraTotal) ?> registradas.
        Para el histórico completo usa la exportación.
      <?php else: ?>
        <?= number_format($bitacoraTotal) ?> acción<?= $bitacoraTotal == 1 ? '' : 'es' ?> registrada<?= $bitacoraTotal == 1 ? '' : 's' ?> en total.
      <?php endif; ?>
    </p>

    <script>
    function filtrarBitacora(){
      var q   = document.getElementById('log-buscar').value.trim().toLowerCase();
      var mod = document.getElementById('log-modulo').value;
      var visibles = 0, total = 0;
      document.querySelectorAll('#log-tbody tr[data-modulo]').forEach(function(tr){
        total++;
        var okM = !mod || tr.dataset.modulo === mod;
        var okQ = !q   || (tr.dataset.search || '').indexOf(q) !== -1;
        var show = okM && okQ;
        tr.style.display = show ? '' : 'none';
        if (show) visibles++;
      });
      var cont = document.getElementById('log-contador');
      cont.textContent = (visibles === total)
        ? (total + ' acciones')
        : (visibles + ' de ' + total + ' acciones');
      var tbody = document.getElementById('log-tbody');
      var empty = document.getElementById('log-empty-row');
      if (visibles === 0){
        if (!empty){
          empty = document.createElement('tr');
          empty.id = 'log-empty-row';
          empty.innerHTML = '<td colspan="4" class="adm-table-empty">No hay acciones que coincidan con el filtro.</td>';
          tbody.appendChild(empty);
        }
        empty.style.display = '';
      } else if (empty){
        empty.style.display = 'none';
      }
    }
    filtrarBitacora();
    </script>
    <?php endif; ?>
  </div>

</div>

<?php endif; ?>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
