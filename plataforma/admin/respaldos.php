<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'respaldos';
$adm_title = 'Respaldos de datos';

// Datos informativos: cuántas tablas/vistas y último respaldo registrado en la bitácora
$numTablas = null;
$numVistas = null;
$ultimoExport = null;
try {
    $db = getPDO(DB_NAME);
    $numTablas = 0;
    $numVistas = 0;
    foreach ($db->query('SHOW FULL TABLES')->fetchAll(PDO::FETCH_NUM) as $r) {
        if (strtoupper($r[1]) === 'VIEW') { $numVistas++; } else { $numTablas++; }
    }
    try {
        $stmt = $db->query("SELECT admin_nombre, created_at FROM admin_log
                            WHERE accion = 'exportar_respaldo'
                            ORDER BY id DESC LIMIT 1");
        $ultimoExport = $stmt->fetch();
    } catch (\Throwable $e) { /* columna/registro inexistente */ }
} catch (\Throwable $e) { /* BD no disponible */ }

$csrf = csrfToken();
$base = defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma';
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Respaldos de datos</h1>
    <p class="adm-page-desc">Descarga una copia completa de toda la información del portal o restaura un respaldo en otra instalación.</p>
  </div>
</div>

<!-- ══ Explicación ═══════════════════════════════════════════════ -->
<div class="adm-pending" style="margin-bottom:18px">
  <span class="material-symbols-rounded">info</span>
  <span>
    Un <strong>respaldo</strong> es un archivo <code>.sql</code> que contiene <strong>toda</strong> la
    información del portal: docentes, horarios, convenios, libros, avisos, directorio, configuración y
    cuentas de administrador. Sirve para <strong>copia de seguridad</strong> y para
    <strong>copiar todos los datos a otra computadora</strong> que use la plataforma.
    <?php if ($numTablas !== null): ?>
      Actualmente la base contiene <strong><?= $numTablas ?> tablas</strong><?php if ($numVistas): ?> y <strong><?= $numVistas ?> vistas</strong><?php endif; ?>.
    <?php endif; ?>
  </span>
</div>

<!-- ══ Exportar ══════════════════════════════════════════════════ -->
<div class="adm-form-card">
  <div class="adm-form-title"><span class="material-symbols-rounded">cloud_download</span> Exportar respaldo completo</div>
  <p style="color:var(--tsj-gray-500);font-size:14px;margin:0 0 16px">
    Descarga un archivo <code>.sql</code> con toda la información actual. Guárdalo en un lugar seguro
    (USB, nube, otra computadora). Puedes generar un respaldo nuevo cada vez que hagas cambios importantes.
  </p>
  <?php if ($ultimoExport): ?>
  <p style="font-size:13px;color:var(--tsj-gray-400);margin:0 0 16px">
    <span class="material-symbols-rounded" style="font-size:15px;vertical-align:middle">history</span>
    Último respaldo descargado: <strong><?= htmlspecialchars($ultimoExport['created_at'] ?? '') ?></strong>
    por <?= htmlspecialchars($ultimoExport['admin_nombre'] ?? 'Administrador') ?>.
  </p>
  <?php endif; ?>
  <form method="POST" action="<?= $base ?>/admin/procesos/backup_export.php">
    <input type="hidden" name="_csrf" value="<?= $csrf ?>">
    <div class="adm-form-actions" style="justify-content:flex-start">
      <button type="submit" class="adm-btn adm-btn--primary">
        <span class="material-symbols-rounded">download</span> Descargar respaldo (.sql)
      </button>
    </div>
  </form>
</div>

<!-- ══ Importar ══════════════════════════════════════════════════ -->
<div class="adm-form-card">
  <div class="adm-form-title"><span class="material-symbols-rounded">cloud_upload</span> Importar / restaurar respaldo</div>

  <div class="adm-pending" style="background:#fef2f2;border-color:#fca5a5;color:#991b1b;margin-bottom:18px">
    <span class="material-symbols-rounded">warning</span>
    <span>
      <strong>Atención:</strong> al importar un respaldo se <strong>reemplaza por completo</strong> toda la
      información actual de esta instalación con la del archivo. Lo que tengas ahora se perderá.
      Si tienes dudas, <strong>descarga primero un respaldo</strong> de los datos actuales (arriba).
    </span>
  </div>

  <form data-proc="backup_import" enctype="multipart/form-data" data-confirm="Esto BORRARÁ todos los datos actuales y los reemplazará por el contenido del respaldo. Se guardará un respaldo de seguridad automático antes. ¿Continuar?">
    <input type="hidden" name="_csrf" value="<?= $csrf ?>">
    <input type="hidden" name="accion" value="importar_respaldo">
    <div class="adm-form-grid cols-2">
      <div class="adm-field" style="grid-column:1/-1">
        <label>Archivo de respaldo (.sql) <span style="color:var(--tsj-pink)">*</span></label>
        <input type="file" name="archivo" accept=".sql" required>
        <span class="adm-field-help">Selecciona un archivo generado por el botón "Descargar respaldo".</span>
      </div>
      <div class="adm-field" style="grid-column:1/-1">
        <label>Confirmación <span style="color:var(--tsj-pink)">*</span></label>
        <input type="text" name="confirmacion" placeholder="Escribe: REEMPLAZAR" autocomplete="off" required>
        <span class="adm-field-help">Para evitar accidentes, escribe la palabra <strong>REEMPLAZAR</strong> en mayúsculas.</span>
      </div>
    </div>
    <div class="adm-form-actions" style="justify-content:flex-start">
      <button type="submit" class="adm-btn adm-btn--warning">
        <span class="material-symbols-rounded">restore</span> Restaurar desde respaldo
      </button>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
