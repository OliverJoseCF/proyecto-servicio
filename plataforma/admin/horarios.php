<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'horarios';
$adm_title = 'Maestros y horarios';

try {
    $db        = getPDO(DB_NAME);
    $carreras  = $db->query('SELECT * FROM carreras ORDER BY orden')->fetchAll();
    $profesores= $db->query(
        'SELECT d.id, d.nombre, d.correo, d.foto, d.activo,
                GROUP_CONCAT(c.clave ORDER BY c.orden SEPARATOR "/") AS carrera_clave
           FROM docentes d
           LEFT JOIN docente_carrera dc ON dc.docente_id = d.id
           LEFT JOIN carreras c ON c.id = dc.carrera_id
          GROUP BY d.id ORDER BY d.activo DESC, d.nombre'
    )->fetchAll();
    $horarios  = $db->query('SELECT h.*,d.nombre,\'\' AS apellido,d.foto,c.clave carrera_clave,c.nombre carrera_nombre
                              FROM horarios h
                              JOIN docentes d ON h.id_profesor=d.id
                              LEFT JOIN carreras c ON h.id_carrera=c.id
                              ORDER BY d.nombre')->fetchAll();
    $db_ok = true;
} catch (\Throwable $e) {
    $carreras = $profesores = $horarios = [];
    $db_ok    = false;
}

$csrf      = csrfToken();
$base_img  = PLATAFORMA_URL . '/modulos/visitantes/imagenes/';
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Gestión de Maestros y Horarios</h1>
    <p class="adm-page-desc">Consulta la lista de maestros y sube o reemplaza sus archivos de horario.</p>
  </div>
</div>

<div class="adm-pending" style="margin-bottom:18px">
  <span class="material-symbols-rounded">info</span>
  <span>
    <strong>Maestros</strong> muestra la lista (solo lectura): para agregar o editar un maestro ve a
    <a href="visitantes.php" style="font-weight:600">Directorio y carreras → Docentes</a>.
    En <strong>Horarios y archivos</strong> subes el PDF o imagen del horario de cada maestro por carrera y semestre.
  </span>
</div>

<div class="adm-tabs">
  <?php foreach (['maestros'=>'Maestros (lista)','horarios_tab'=>'Horarios y archivos'] as $k=>$l): ?>
  <button class="adm-tab <?= $k==='maestros'?'active':'' ?>"
          data-tab-group="hor" data-tab="<?= $k ?>" onclick="showTab('hor','<?= $k ?>')">
    <?= $l ?>
  </button>
  <?php endforeach; ?>
</div>

<!-- ══ Maestros ══════════════════════════════════════════════════ -->
<div class="adm-tab-panel active" data-tab-group="hor" data-tab="maestros">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Foto</th><th>Nombre</th><th>Carrera</th><th>Correo</th><th>Estado</th></tr></thead>
      <tbody>
        <?php if (empty($profesores)): ?>
        <tr><td colspan="5" class="adm-table-empty">Sin docentes registrados. Agrégalos en <a href="visitantes.php" style="color:var(--tsj-blue)">Directorio y carreras → Docentes</a>.</td></tr>
        <?php endif; ?>
        <?php foreach ($profesores as $p): ?>
        <tr id="prof-<?= $p['id'] ?>" <?= !$p['activo'] ? 'style="opacity:.5"' : '' ?>>
          <td class="col-photo">
            <img src="<?= $p['foto'] ? $base_img.htmlspecialchars($p['foto']) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='38' height='38'%3E%3Crect width='38' height='38' fill='%23e5e7eb'/%3E%3C/svg%3E" ?>"
                 style="width:38px;height:38px;border-radius:50%;object-fit:cover" alt="">
          </td>
          <td style="font-weight:600"><?= htmlspecialchars($p['nombre']) ?></td>
          <td><span class="adm-status adm-status--info" style="font-size:11px"><?= htmlspecialchars($p['carrera_clave'] ?? '—') ?></span></td>
          <td><?= $p['correo'] ? '<a href="mailto:'.htmlspecialchars($p['correo']).'" style="color:var(--tsj-blue);font-size:12.5px">'.htmlspecialchars($p['correo']).'</a>' : '—' ?></td>
          <td>
            <?php if ($p['activo']): ?>
              <span class="adm-status adm-status--ok">Activo</span>
            <?php else: ?>
              <span class="adm-status adm-status--warn">Inactivo</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div style="margin-top:14px;padding:12px 16px;background:var(--tsj-blue-50);border-radius:10px;border:1.5px solid #e0dcff;font-size:13px;color:var(--tsj-blue-dark)">
    <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">info</span>
    Para agregar o editar docentes, ve a <a href="visitantes.php" style="color:var(--tsj-blue);font-weight:600">Directorio y carreras → Docentes</a>. Los cambios se reflejan automáticamente aquí y en el portal público.
  </div>
</div>

<!-- ══ Horarios / Archivos ════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="hor" data-tab="horarios_tab">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Maestro</th><th>Carrera</th><th>Semestre</th><th>Archivo</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($horarios)): ?>
        <tr><td colspan="5" class="adm-table-empty">Sin horarios registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($horarios as $h): ?>
        <tr id="hor-<?= $h['id_horario'] ?>">
          <td style="font-weight:600"><?= htmlspecialchars($h['nombre'].' '.$h['apellido']) ?></td>
          <td><?= htmlspecialchars($h['carrera_clave'] ?? '—') ?></td>
          <td><?= htmlspecialchars($h['semestre'] ?? '—') ?></td>
          <td>
            <?php if ($h['imagen_horario']): ?>
              <span class="adm-status adm-status--ok">
                <span class="material-symbols-rounded" style="font-size:14px">attach_file</span>
                <?= htmlspecialchars(basename($h['imagen_horario'])) ?>
              </span>
            <?php else: ?>
              <span class="adm-status adm-status--warn">Sin archivo</span>
            <?php endif; ?>
          </td>
          <td class="actions">
            <button class="adm-btn adm-btn--danger adm-btn--sm"
                    onclick="confirmarEliminar('horarios','horario_eliminar',<?= $h['id_horario'] ?>,'hor-<?= $h['id_horario'] ?>')">
              <span class="material-symbols-rounded">delete</span>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">upload_file</span> Agregar / actualizar horario</div>
    <form data-proc="horarios" data-reload id="form-hor" enctype="multipart/form-data">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="horario_guardar">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Maestro <span style="color:var(--tsj-pink)">*</span></label>
          <select name="profesor_id" required>
            <option value="">— Seleccionar —</option>
            <?php foreach ($profesores as $p): ?>
            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?><?= $p['carrera_clave'] ? ' (' . htmlspecialchars($p['carrera_clave']) . ')' : '' ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="adm-field"><label>Carrera</label>
          <select name="carrera_id">
            <option value="">Sin asignar</option>
            <?php foreach ($carreras as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['clave'].' — '.$c['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="adm-field"><label>Semestre</label>
          <select name="semestre">
            <option value="">—</option>
            <?php foreach (['1er','2do','3er','4to','5to','6to','7mo','8vo'] as $sem): ?>
            <option><?= $sem ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="adm-field" style="grid-column:1/-1"><label>Archivo de horario (PDF/imagen)</label>
          <input type="file" name="archivo_horario" accept=".pdf,.jpg,.jpeg,.png">
          <span class="adm-field-help">Si ya existe un horario para este maestro+carrera, se reemplazará.</span>
        </div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar horario</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
