<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'dashboard';
$adm_title = 'Dashboard';

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
    ];
    $db_ok = true;
} catch (\Throwable $e) {
    $stats = array_fill_keys(['libros','convenios','docentes','horarios','prestamos','solicitudes','sugerencias'], '—');
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
    <div class="adm-stat-value"><?= $stats['libros'] ?></div>
    <div class="adm-stat-label">Libros en catálogo</div>
  </div>
  <div class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--green"><span class="material-symbols-rounded">handshake</span></div>
    <div class="adm-stat-value"><?= $stats['convenios'] ?></div>
    <div class="adm-stat-label">Convenios activos</div>
  </div>
  <div class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--orange"><span class="material-symbols-rounded">school</span></div>
    <div class="adm-stat-value"><?= $stats['docentes'] ?></div>
    <div class="adm-stat-label">Docentes registrados</div>
  </div>
  <div class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--pink"><span class="material-symbols-rounded">calendar_month</span></div>
    <div class="adm-stat-value"><?= $stats['horarios'] ?></div>
    <div class="adm-stat-label">Horarios publicados</div>
  </div>
</div>

<?php if ($db_ok && ($stats['solicitudes'] > 0 || $stats['sugerencias'] > 0)): ?>
<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap">
  <?php if ($stats['solicitudes'] > 0): ?>
  <a href="biblioteca.php#solicitudes" class="adm-alert-badge" style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:#fef3c7;border:1.5px solid #fcd34d;border-radius:8px;color:#92400e;font-size:13px;font-weight:600;text-decoration:none">
    <span class="material-symbols-rounded" style="font-size:18px">notifications</span>
    <?= $stats['solicitudes'] ?> solicitud<?= $stats['solicitudes'] > 1 ? 'es' : '' ?> pendiente<?= $stats['solicitudes'] > 1 ? 's' : '' ?> en biblioteca
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
      <div class="adm-module-icon" style="background:#ede9ff;color:var(--tsj-blue)"><span class="material-symbols-rounded">badge</span></div>
      <p class="adm-module-name">Visitantes</p>
    </div>
    <p class="adm-module-desc">Directorio, docentes, coordinadores, materias, secretarías y nuevo ingreso.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Directorio</span><span class="adm-module-tag">Docentes</span>
      <span class="adm-module-tag">Coordinadores</span><span class="adm-module-tag">Materias</span>
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
    <p class="adm-module-desc">Catálogo de libros, préstamos activos y solicitudes pendientes.</p>
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
    <p class="adm-module-desc">Empresas vinculadas por carrera, contactos y sugerencias.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Empresas</span><span class="adm-module-tag">Por carrera</span><span class="adm-module-tag">Sugerencias</span>
    </div>
  </a>

  <a href="horarios.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#e0e7ff;color:#4338ca"><span class="material-symbols-rounded">calendar_month</span></div>
      <p class="adm-module-name">Buscar Maestro</p>
    </div>
    <p class="adm-module-desc">Gestión de maestros, horarios, fotos de perfil y correos.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Maestros</span><span class="adm-module-tag">Horarios</span><span class="adm-module-tag">Correos</span>
    </div>
  </a>

  <a href="requisitos.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#ffe4e8;color:var(--tsj-pink)"><span class="material-symbols-rounded">checklist</span></div>
      <p class="adm-module-name">Serv. Social / Residencia</p>
    </div>
    <p class="adm-module-desc">Requisitos, documentos descargables, timeline y FAQ.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Requisitos</span><span class="adm-module-tag">Documentos</span><span class="adm-module-tag">Timeline</span><span class="adm-module-tag">FAQ</span>
    </div>
  </a>

  <a href="configuracion.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:var(--tsj-gray-100);color:var(--tsj-gray-600)"><span class="material-symbols-rounded">settings</span></div>
      <p class="adm-module-name">Configuración General</p>
    </div>
    <p class="adm-module-desc">Correos del sistema, redes sociales, información del footer.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Correos</span><span class="adm-module-tag">Redes sociales</span><span class="adm-module-tag">Footer</span>
    </div>
  </a>

</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
