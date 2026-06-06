<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'horarios';
$adm_title = 'Buscar Maestro';

try {
    $db        = getPDO(DB_NAME);
    $carreras  = $db->query('SELECT * FROM carreras ORDER BY orden')->fetchAll();
    $profesores= $db->query('SELECT * FROM profesores ORDER BY activo DESC, apellido, nombre')->fetchAll();
    $horarios  = $db->query('SELECT h.*,p.nombre,p.apellido,p.foto,c.clave carrera_clave,c.nombre carrera_nombre
                              FROM horarios h
                              JOIN profesores p ON h.id_profesor=p.id_profesor
                              LEFT JOIN carreras c ON h.id_carrera=c.id
                              ORDER BY p.apellido,p.nombre')->fetchAll();
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
    <p class="adm-page-desc">Agrega, edita o elimina maestros y sus archivos de horario.</p>
  </div>
</div>

<div class="adm-tabs">
  <?php foreach (['maestros'=>'Maestros','horarios_tab'=>'Horarios / Archivos'] as $k=>$l): ?>
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
      <thead><tr><th>Foto</th><th>Nombre</th><th>Apellido</th><th>Correo</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($profesores)): ?>
        <tr><td colspan="6" class="adm-table-empty">Sin maestros registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($profesores as $p): ?>
        <tr id="prof-<?= $p['id_profesor'] ?>" <?= !$p['activo'] ? 'style="opacity:.5"' : '' ?>>
          <td class="col-photo">
            <img src="<?= $p['foto'] ? $base_img.htmlspecialchars($p['foto']) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='38' height='38'%3E%3Crect width='38' height='38' fill='%23e5e7eb'/%3E%3C/svg%3E" ?>"
                 style="width:38px;height:38px;border-radius:50%;object-fit:cover" alt="">
          </td>
          <td style="font-weight:600"><?= htmlspecialchars($p['nombre']) ?></td>
          <td><?= htmlspecialchars($p['apellido']) ?></td>
          <td><?= $p['correo'] ? '<a href="mailto:'.htmlspecialchars($p['correo']).'" style="color:var(--tsj-blue);font-size:12.5px">'.htmlspecialchars($p['correo']).'</a>' : '—' ?></td>
          <td>
            <?php if ($p['activo']): ?>
              <span class="adm-status adm-status--ok">Activo</span>
            <?php else: ?>
              <span class="adm-status adm-status--warn">Inactivo</span>
            <?php endif; ?>
          </td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm" title="<?= $p['activo'] ? 'Desactivar' : 'Activar' ?>"
                    onclick="toggleActivo('horarios','profesor_toggle',<?= $p['id_profesor'] ?>,this)">
              <span class="material-symbols-rounded"><?= $p['activo'] ? 'visibility_off' : 'visibility' ?></span>
            </button>
            <button class="adm-btn adm-btn--ghost adm-btn--sm"
                    onclick="abrirEditarProf(<?= htmlspecialchars(json_encode($p)) ?>)">
              <span class="material-symbols-rounded">edit</span>
            </button>
            <button class="adm-btn adm-btn--danger adm-btn--sm"
                    onclick="confirmarEliminar('horarios','profesor_eliminar',<?= $p['id_profesor'] ?>,'prof-<?= $p['id_profesor'] ?>')">
              <span class="material-symbols-rounded">delete</span>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">school</span> <span id="form-prof-titulo">Agregar maestro</span></div>
    <form data-proc="horarios" data-reload id="form-prof">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="profesor_agregar" id="prof-accion">
      <input type="hidden" name="id" id="prof-id">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Nombre(s) <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="nombre" id="prof-nombre" required></div>
        <div class="adm-field"><label>Apellido(s) <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="apellido" id="prof-apellido" required></div>
        <div class="adm-field"><label>Correo electrónico</label><input type="email" name="correo" id="prof-correo"></div>
        <div class="adm-field"><label>Foto (nombre de archivo)</label><input type="text" name="foto" id="prof-foto" placeholder="ej. miguel.png"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar maestro</button>
        <button type="button" class="adm-btn adm-btn--ghost" onclick="resetFormProf()">Cancelar</button>
      </div>
    </form>
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
    <form data-proc="horarios" data-accion="horario_guardar" id="form-hor" enctype="multipart/form-data">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="horario_guardar">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Maestro <span style="color:var(--tsj-pink)">*</span></label>
          <select name="profesor_id" required>
            <option value="">— Seleccionar —</option>
            <?php foreach ($profesores as $p): ?>
            <option value="<?= $p['id_profesor'] ?>"><?= htmlspecialchars($p['apellido'].', '.$p['nombre']) ?></option>
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

<script>
function toggleActivo(modulo, accion, id, btn) {
  var csrfEl = document.querySelector('input[name="_csrf"]');
  var csrf   = csrfEl ? csrfEl.value : '';
  adminFetch(modulo, { _csrf: csrf, accion: accion, id: id })
    .then(function (json) {
      if (json.ok) {
        var row   = btn.closest('tr');
        var icon  = btn.querySelector('.material-symbols-rounded');
        var badge = row.querySelector('.adm-status');
        var activo = json.activo;
        row.style.opacity = activo ? '' : '0.5';
        icon.textContent  = activo ? 'visibility_off' : 'visibility';
        btn.title         = activo ? 'Desactivar' : 'Activar';
        if (badge) {
          badge.textContent = activo ? 'Activo' : 'Inactivo';
          badge.className   = 'adm-status ' + (activo ? 'adm-status--ok' : 'adm-status--warn');
        }
      }
    });
}

function abrirEditarProf(p){
  document.getElementById('prof-accion').value   = 'profesor_editar';
  document.getElementById('prof-id').value       = p.id_profesor;
  document.getElementById('prof-nombre').value   = p.nombre||'';
  document.getElementById('prof-apellido').value = p.apellido||'';
  document.getElementById('prof-correo').value   = p.correo||'';
  document.getElementById('prof-foto').value     = p.foto||'';
  document.getElementById('form-prof-titulo').textContent='Editar: '+p.nombre+' '+p.apellido;
  document.getElementById('form-prof').scrollIntoView({behavior:'smooth'});
}
function resetFormProf(){
  document.getElementById('prof-accion').value='profesor_agregar';
  document.getElementById('prof-id').value='';
  document.getElementById('form-prof').reset();
  document.getElementById('form-prof-titulo').textContent='Agregar maestro';
}
</script>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
