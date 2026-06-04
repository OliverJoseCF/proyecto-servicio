<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'convenios';
$adm_title = 'Convenios';

try {
    $db          = getPDO(DB_NAME);
    $carreras    = $db->query('SELECT * FROM carreras ORDER BY orden')->fetchAll();
    $convenios   = $db->query('SELECT cv.*,c.clave carrera_clave,c.nombre carrera_nombre FROM convenios cv LEFT JOIN carreras c ON cv.carrera_id=c.id ORDER BY cv.nombre')->fetchAll();
    $sugerencias = $db->query('SELECT * FROM sugerencias_empresa WHERE estado="pendiente" ORDER BY created_at DESC')->fetchAll();
    $db_ok = true;
} catch (\Throwable $e) {
    $carreras = $convenios = $sugerencias = [];
    $db_ok    = false;
}

$csrf = getCsrfToken();
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Gestión de Convenios</h1>
    <p class="adm-page-desc">Empresas vinculadas por carrera, contactos y sugerencias de alumnos.</p>
  </div>
</div>

<div class="adm-tabs">
  <button class="adm-tab active" data-tab-group="conv" data-tab="convenios" onclick="showTab('conv','convenios')">
    Convenios por carrera
  </button>
  <button class="adm-tab" data-tab-group="conv" data-tab="sugerencias" onclick="showTab('conv','sugerencias')" id="tab-sugerencias">
    Sugerencias de empresas
    <?php if (count($sugerencias)): ?><span style="background:#16a34a;color:#fff;font-size:11px;padding:1px 7px;border-radius:99px;margin-left:4px"><?= count($sugerencias) ?></span><?php endif; ?>
  </button>
</div>

<!-- ══ Convenios ══════════════════════════════════════════════════ -->
<div class="adm-tab-panel active" data-tab-group="conv" data-tab="convenios">
  <div class="adm-career-pills" id="conv-pills">
    <button class="adm-career-pill active" onclick="filtrarConvenios('')">Todos</button>
    <?php foreach ($carreras as $c): ?>
    <button class="adm-career-pill" onclick="filtrarConvenios('<?= $c['clave'] ?>')"><?= htmlspecialchars($c['clave']) ?></button>
    <?php endforeach; ?>
  </div>

  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Empresa</th><th>Tipo</th><th>Carrera</th><th>Contacto</th><th>Correo</th><th>Vencimiento</th><th>Acciones</th></tr></thead>
      <tbody id="conv-tbody">
        <?php if (empty($convenios)): ?>
        <tr><td colspan="7" class="adm-table-empty">Sin convenios registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($convenios as $cv):
          $vence   = $cv['vencimiento'] ? new DateTime($cv['vencimiento']) : null;
          $hoy     = new DateTime();
          $status  = !$vence ? 'info' : ($hoy > $vence ? 'danger' : ($vence->diff($hoy)->days <= 30 ? 'warn' : 'ok'));
          $estLabel= ['ok'=>'Vigente','warn'=>'Por vencer','danger'=>'Vencido','info'=>'Sin fecha'];
        ?>
        <tr id="cv-<?= $cv['id'] ?>" data-carrera="<?= htmlspecialchars($cv['carrera_clave'] ?? '') ?>">
          <td style="font-weight:600"><?= htmlspecialchars($cv['nombre']) ?></td>
          <td><span class="adm-status adm-status--info"><?= htmlspecialchars($cv['tipo_convenio']) ?></span></td>
          <td><?= htmlspecialchars($cv['carrera_clave'] ?? '—') ?></td>
          <td><?= htmlspecialchars($cv['nombre_contacto'] ?? '') ?></td>
          <td><?= $cv['correo_contacto'] ? '<a href="mailto:'.htmlspecialchars($cv['correo_contacto']).'" style="color:var(--tsj-blue)">'.htmlspecialchars($cv['correo_contacto']).'</a>' : '—' ?></td>
          <td><span class="adm-status adm-status--<?= $status ?>"><?= $cv['vencimiento'] ? htmlspecialchars($cv['vencimiento']) : 'Sin fecha' ?></span></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm"
                    onclick="abrirEditarConv(<?= htmlspecialchars(json_encode($cv)) ?>)">
              <span class="material-symbols-rounded">edit</span>
            </button>
            <button class="adm-btn adm-btn--danger adm-btn--sm"
                    onclick="confirmarEliminar('convenios','convenio_eliminar',<?= $cv['id'] ?>,'cv-<?= $cv['id'] ?>')">
              <span class="material-symbols-rounded">delete</span>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">handshake</span> <span id="form-conv-titulo">Agregar convenio</span></div>
    <form data-proc="convenios" data-accion="convenio_agregar" id="form-conv">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="convenio_agregar" id="conv-accion">
      <input type="hidden" name="id" id="conv-id">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Nombre de la empresa <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="nombre" id="conv-nombre" required></div>
        <div class="adm-field"><label>Tipo de convenio</label>
          <select name="tipo_convenio" id="conv-tipo">
            <option value="residencia">Residencia profesional</option>
            <option value="servicio_social">Servicio social</option>
            <option value="practicas">Prácticas profesionales</option>
            <option value="otro">Otro</option>
          </select>
        </div>
        <div class="adm-field"><label>Carrera</label>
          <select name="carrera_id" id="conv-carrera">
            <option value="">Todas las carreras</option>
            <?php foreach ($carreras as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['clave'].' — '.$c['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="adm-field"><label>Nombre del contacto</label><input type="text" name="nombre_contacto" id="conv-contacto"></div>
        <div class="adm-field"><label>Correo del contacto</label><input type="email" name="correo_contacto" id="conv-correo"></div>
        <div class="adm-field"><label>Teléfono</label><input type="tel" name="telefono_contacto" id="conv-telefono"></div>
        <div class="adm-field"><label>Fecha de vencimiento</label><input type="date" name="vencimiento" id="conv-vence"></div>
        <div class="adm-field"><label>Logo (URL)</label><input type="url" name="logo" id="conv-logo"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar convenio</button>
        <button type="button" class="adm-btn adm-btn--ghost" onclick="resetFormConv()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ Sugerencias ════════════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="conv" data-tab="sugerencias">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Empresa sugerida</th><th>Correo empresa</th><th>Contacto</th><th>Fecha</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($sugerencias)): ?>
        <tr><td colspan="5" class="adm-table-empty">Sin sugerencias pendientes.</td></tr>
        <?php endif; ?>
        <?php foreach ($sugerencias as $s): ?>
        <tr id="sug-<?= $s['id'] ?>">
          <td style="font-weight:600"><?= htmlspecialchars($s['nombre_empresa']) ?></td>
          <td><a href="mailto:<?= htmlspecialchars($s['correo_empresa']) ?>" style="color:var(--tsj-blue)"><?= htmlspecialchars($s['correo_empresa']) ?></a></td>
          <td><?= htmlspecialchars($s['nombre_contacto'] ?? '') ?></td>
          <td><?= htmlspecialchars(substr($s['created_at'],0,10)) ?></td>
          <td class="actions">
            <button class="adm-btn adm-btn--primary adm-btn--sm"
                    onclick="procesarSug(<?= $s['id'] ?>,'aceptar','sug-<?= $s['id'] ?>','<?= $csrf ?>')">
              <span class="material-symbols-rounded">add_task</span> Aceptar
            </button>
            <button class="adm-btn adm-btn--danger adm-btn--sm"
                    onclick="procesarSug(<?= $s['id'] ?>,'rechazar','sug-<?= $s['id'] ?>','<?= $csrf ?>')">
              <span class="material-symbols-rounded">close</span> Rechazar
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function filtrarConvenios(clave){
  document.querySelectorAll('#conv-pills .adm-career-pill').forEach(b=>{
    b.classList.toggle('active', b.textContent.trim()===(clave||'Todos'));
  });
  document.querySelectorAll('#conv-tbody tr[data-carrera]').forEach(tr=>{
    tr.style.display = (!clave || tr.dataset.carrera===clave) ? '' : 'none';
  });
}

function abrirEditarConv(cv){
  document.getElementById('conv-accion').value  = 'convenio_editar';
  document.getElementById('conv-id').value      = cv.id;
  document.getElementById('conv-nombre').value  = cv.nombre||'';
  document.getElementById('conv-contacto').value= cv.nombre_contacto||'';
  document.getElementById('conv-correo').value  = cv.correo_contacto||'';
  document.getElementById('conv-telefono').value= cv.telefono_contacto||'';
  document.getElementById('conv-vence').value   = cv.vencimiento||'';
  document.getElementById('conv-logo').value    = cv.logo||'';
  const tipo = document.getElementById('conv-tipo');
  for(let o of tipo.options){ if(o.value===cv.tipo_convenio){ o.selected=true; break; } }
  const car = document.getElementById('conv-carrera');
  for(let o of car.options){ if(o.value==cv.carrera_id){ o.selected=true; break; } }
  document.getElementById('form-conv-titulo').textContent='Editar: '+cv.nombre;
  document.getElementById('form-conv').scrollIntoView({behavior:'smooth'});
}
function resetFormConv(){
  document.getElementById('conv-accion').value='convenio_agregar';
  document.getElementById('conv-id').value='';
  document.getElementById('form-conv').reset();
  document.getElementById('form-conv-titulo').textContent='Agregar convenio';
}

function procesarSug(id, tipo, rowId, csrf){
  const accion = tipo==='aceptar'?'sugerencia_aceptar':'sugerencia_rechazar';
  if(!confirm(tipo==='aceptar'?'¿Aceptar esta sugerencia?':'¿Rechazar esta sugerencia?')) return;
  adminFetch('convenios',{csrf,accion,id})
    .then(r=>{ if(r.ok) document.getElementById(rowId)?.remove(); });
}

if(location.hash==='#sugerencias') showTab('conv','sugerencias');
</script>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
