<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'dashboard';
$adm_title = 'Resumen';

// Stats desde BD
try {
    $db = getPDO(DB_NAME);
    $stats = [
        'libros'     => $db->query('SELECT COUNT(*) FROM libros WHERE activo=1')->fetchColumn(),
        'convenios'  => $db->query('SELECT COUNT(*) FROM convenios WHERE activo=1')->fetchColumn(),
        'docentes'   => $db->query('SELECT COUNT(*) FROM docentes WHERE activo=1')->fetchColumn(),
        'horarios'   => $db->query('SELECT COUNT(*) FROM horarios WHERE activo=1')->fetchColumn(),
        'prestamos'  => $db->query('SELECT COUNT(*) FROM prestamos WHERE devuelto=0')->fetchColumn(),
        'solicitudes'=> $db->query('SELECT COUNT(*) FROM solicitudes_biblioteca WHERE estado="pendiente"')->fetchColumn(),
        'sugerencias'=> $db->query('SELECT COUNT(*) FROM sugerencias_empresa WHERE estado="pendiente"')->fetchColumn(),
        'atrasados'  => $db->query('SELECT COUNT(*) FROM prestamos WHERE devuelto=0 AND fecha_devolucion < CURDATE()')->fetchColumn(),
        'por_vencer' => $db->query('SELECT COUNT(*) FROM convenios WHERE activo=1 AND vencimiento IS NOT NULL AND vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)')->fetchColumn(),
    ];
    $db_ok = true;
} catch (\Throwable $e) {
    $stats = array_fill_keys(['libros','convenios','docentes','horarios','prestamos','solicitudes','sugerencias','atrasados','por_vencer'], '—');
    $db_ok = false;
    $db_err = $e->getMessage();
}

require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Bienvenido al Panel de Administración</h1>
    <p class="adm-page-desc">Gestiona todo el contenido de la plataforma desde aquí.</p>
  </div>
</div>

<?php if (!$db_ok): ?>
<div class="adm-pending" style="background:#fef2f2;border-color:#fca5a5;color:#991b1b">
  <span class="material-symbols-rounded">error</span>
  <span><strong>Error de base de datos:</strong> <?= htmlspecialchars($db_err ?? '') ?> — Verifica que kiosko_tsj exista y que las credenciales en config.local.php sean correctas.</span>
</div>
<?php endif; ?>

<div class="adm-stats">
  <div class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--blue"><span class="material-symbols-rounded">menu_book</span></div>
    <div class="adm-stat-value" id="stat-libros"><?= $stats['libros'] ?></div>
    <div class="adm-stat-label">Libros en catálogo</div>
  </div>
  <div class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--green"><span class="material-symbols-rounded">handshake</span></div>
    <div class="adm-stat-value" id="stat-convenios"><?= $stats['convenios'] ?></div>
    <div class="adm-stat-label">Convenios activos</div>
  </div>
  <div class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--orange"><span class="material-symbols-rounded">school</span></div>
    <div class="adm-stat-value" id="stat-docentes"><?= $stats['docentes'] ?></div>
    <div class="adm-stat-label">Docentes registrados</div>
  </div>
  <div class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--pink"><span class="material-symbols-rounded">calendar_month</span></div>
    <div class="adm-stat-value" id="stat-horarios"><?= $stats['horarios'] ?></div>
    <div class="adm-stat-label">Horarios publicados</div>
  </div>
</div>

<?php if ($db_ok && ($stats['solicitudes'] > 0 || $stats['sugerencias'] > 0 || $stats['atrasados'] > 0 || $stats['por_vencer'] > 0)): ?>
<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap">
  <?php if ($stats['solicitudes'] > 0): ?>
  <a href="biblioteca.php#solicitudes" class="adm-alert-badge" style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:#fef3c7;border:1.5px solid #fcd34d;border-radius:8px;color:#92400e;font-size:13px;font-weight:600;text-decoration:none">
    <span class="material-symbols-rounded" style="font-size:18px">notifications</span>
    <?= $stats['solicitudes'] ?> solicitud<?= $stats['solicitudes'] > 1 ? 'es' : '' ?> pendiente<?= $stats['solicitudes'] > 1 ? 's' : '' ?> en biblioteca
  </a>
  <?php endif; ?>
  <?php if ($stats['atrasados'] > 0): ?>
  <a href="biblioteca.php#prestamos" style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:#fef2f2;border:1.5px solid #fca5a5;border-radius:8px;color:#991b1b;font-size:13px;font-weight:600;text-decoration:none">
    <span class="material-symbols-rounded" style="font-size:18px">assignment_late</span>
    <?= $stats['atrasados'] ?> préstamo<?= $stats['atrasados'] > 1 ? 's' : '' ?> atrasado<?= $stats['atrasados'] > 1 ? 's' : '' ?>
  </a>
  <?php endif; ?>
  <?php if ($stats['por_vencer'] > 0): ?>
  <a href="convenios.php#vencimientos" style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:#fff7ed;border:1.5px solid #fdba74;border-radius:8px;color:#9a3412;font-size:13px;font-weight:600;text-decoration:none">
    <span class="material-symbols-rounded" style="font-size:18px">event_busy</span>
    <?= $stats['por_vencer'] ?> convenio<?= $stats['por_vencer'] > 1 ? 's' : '' ?> vencido<?= $stats['por_vencer'] > 1 ? 's' : '' ?> o por vencer en 30 días
  </a>
  <?php endif; ?>
  <?php if ($stats['sugerencias'] > 0): ?>
  <a href="convenios.php#sugerencias" style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:#f0fdf4;border:1.5px solid #86efac;border-radius:8px;color:#166534;font-size:13px;font-weight:600;text-decoration:none">
    <span class="material-symbols-rounded" style="font-size:18px">business</span>
    <?= $stats['sugerencias'] ?> sugerencia<?= $stats['sugerencias'] > 1 ? 's' : '' ?> de empresa<?= $stats['sugerencias'] > 1 ? 's' : '' ?> pendiente<?= $stats['sugerencias'] > 1 ? 's' : '' ?>
  </a>
  <?php endif; ?>
</div>
<?php endif; ?>

<h2 style="font-size:1rem;font-weight:700;color:var(--tsj-gray-700);margin:0 0 14px">Gestión por módulo</h2>
<div class="adm-modules">

  <a href="visitantes.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#ede9ff;color:var(--tsj-blue)"><span class="material-symbols-rounded">groups</span></div>
      <p class="adm-module-name">Directorio y carreras</p>
    </div>
    <p class="adm-module-desc">Directorio de personal, docentes, coordinadores, carreras (oferta académica), planes de estudio, secretarías y nuevo ingreso.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Directorio</span><span class="adm-module-tag">Docentes</span>
      <span class="adm-module-tag">Carreras</span><span class="adm-module-tag">Materias</span>
    </div>
  </a>

  <a href="horarios.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#e0e7ff;color:#4338ca"><span class="material-symbols-rounded">calendar_month</span></div>
      <p class="adm-module-name">Maestros y horarios</p>
    </div>
    <p class="adm-module-desc">Lista de maestros y carga de sus archivos de horario (PDF o imagen) por carrera y semestre.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Maestros</span><span class="adm-module-tag">Horarios</span><span class="adm-module-tag">Archivos</span>
    </div>
  </a>

  <a href="requisitos.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#ffe4e8;color:var(--tsj-pink)"><span class="material-symbols-rounded">checklist</span></div>
      <p class="adm-module-name">Servicio social y residencias</p>
    </div>
    <p class="adm-module-desc">Requisitos, documentos descargables, fases del proceso y preguntas frecuentes.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Requisitos</span><span class="adm-module-tag">Documentos</span><span class="adm-module-tag">Fases</span><span class="adm-module-tag">FAQ</span>
    </div>
  </a>

  <a href="biblioteca.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#dcfce7;color:#16a34a"><span class="material-symbols-rounded">menu_book</span></div>
      <p class="adm-module-name">Biblioteca</p>
      <?php if ($db_ok && $stats['solicitudes'] > 0): ?>
        <span style="margin-left:auto;background:#f59e0b;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px"><?= $stats['solicitudes'] ?></span>
      <?php endif; ?>
    </div>
    <p class="adm-module-desc">Catálogo de libros, préstamos activos y solicitudes de estudiantes.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Catálogo</span><span class="adm-module-tag">Préstamos</span><span class="adm-module-tag">Solicitudes</span>
    </div>
  </a>

  <a href="convenios.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#fef3c7;color:#b45309"><span class="material-symbols-rounded">handshake</span></div>
      <p class="adm-module-name">Convenios</p>
      <?php if ($db_ok && $stats['sugerencias'] > 0): ?>
        <span style="margin-left:auto;background:#16a34a;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px"><?= $stats['sugerencias'] ?></span>
      <?php endif; ?>
    </div>
    <p class="adm-module-desc">Empresas vinculadas por carrera, contactos y sugerencias de alumnos.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Empresas</span><span class="adm-module-tag">Por carrera</span><span class="adm-module-tag">Sugerencias</span>
    </div>
  </a>

  <a href="reportes.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#ecfeff;color:#0e7490"><span class="material-symbols-rounded">monitoring</span></div>
      <p class="adm-module-name">Reportes y estadísticas</p>
    </div>
    <p class="adm-module-desc">Libros más prestados, atrasos, actividad por mes y bitácora del panel.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Top libros</span><span class="adm-module-tag">Atrasados</span><span class="adm-module-tag">CSV</span><span class="adm-module-tag">Bitácora</span>
    </div>
  </a>

  <a href="respaldos.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#eef2ff;color:#4338ca"><span class="material-symbols-rounded">backup</span></div>
      <p class="adm-module-name">Respaldos de datos</p>
    </div>
    <p class="adm-module-desc">Exporta toda la información a un archivo o restáurala en otra computadora.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Exportar</span><span class="adm-module-tag">Importar</span><span class="adm-module-tag">Copia de seguridad</span>
    </div>
  </a>

  <a href="configuracion.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:var(--tsj-gray-100);color:var(--tsj-gray-600)"><span class="material-symbols-rounded">settings</span></div>
      <p class="adm-module-name">Configuración</p>
    </div>
    <p class="adm-module-desc">Datos del portal, correos de contacto, redes sociales, administradores y seguridad.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Correos</span><span class="adm-module-tag">Redes sociales</span><span class="adm-module-tag">Administradores</span>
    </div>
  </a>

</div>

<script>
(function () {
  var base = (document.querySelector('meta[name="plataforma-url"]')?.content || '/plataforma');
  var url  = base + '/admin/procesos/stats.php';
  var map  = { libros: 'stat-libros', convenios: 'stat-convenios', docentes: 'stat-docentes', horarios: 'stat-horarios' };

  function actualizarStats() {
    fetch(url)
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (data) {
        if (!data || !data.ok) return;
        for (var key in map) {
          var el = document.getElementById(map[key]);
          if (el && el.textContent !== String(data[key])) el.textContent = data[key];
        }
      })
      .catch(function () {});
  }

  setInterval(actualizarStats, 30000);
})();
</script>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
