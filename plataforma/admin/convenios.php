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
    $convenios   = $db->query(
        'SELECT cv.*,
                GROUP_CONCAT(DISTINCT c.id    ORDER BY c.id    SEPARATOR ",") AS carreras_ids_str,
                GROUP_CONCAT(DISTINCT c.clave ORDER BY c.clave SEPARATOR ",") AS carreras_claves
         FROM convenios cv
         LEFT JOIN convenio_carreras cc ON cc.convenio_id = cv.id
         LEFT JOIN carreras c ON c.id = cc.carrera_id
         GROUP BY cv.id
         ORDER BY cv.activo DESC, cv.nombre
         LIMIT 500'
    )->fetchAll();
    $sugerencias = $db->query('SELECT * FROM sugerencias_empresa WHERE estado="pendiente" ORDER BY created_at DESC')->fetchAll();
    $db_ok = true;
} catch (\Throwable $e) {
    $carreras = $convenios = $sugerencias = [];
    $db_ok    = false;
}

$csrf = csrfToken();
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Gestión de Convenios</h1>
    <p class="adm-page-desc">Empresas vinculadas por carrera, contactos y sugerencias de alumnos.</p>
  </div>
</div>

<div class="adm-pending" style="margin-bottom:18px">
  <span class="material-symbols-rounded">info</span>
  <span>
    Dos pestañas:
    <strong>Convenios por carrera</strong> (empresas con convenio: agregar, editar y revisar vencimientos) ·
    <strong>Sugerencias de empresas</strong> (propuestas enviadas por alumnos, en espera de revisión).
    Filtra por carrera con las pastillas y marca <em>“Solo vencidos / por vencer”</em> para detectar los que necesitan renovación.
  </span>
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

  <div style="display:flex;gap:12px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <input type="text" id="conv-buscar" placeholder="Buscar por empresa o contacto…" oninput="aplicarFiltroConv()"
           style="flex:1;min-width:220px;max-width:420px;padding:9px 12px;border:1.5px solid var(--tsj-gray-200);border-radius:8px;font-size:13px;font-family:inherit">
    <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--tsj-gray-600);cursor:pointer">
      <input type="checkbox" id="conv-vencidos" onchange="aplicarFiltroConv()"> Solo vencidos / por vencer
    </label>
    <a href="procesos/export.php?tipo=convenios" class="adm-btn adm-btn--ghost adm-btn--sm" style="margin-left:auto">
      <span class="material-symbols-rounded">download</span> Exportar CSV
    </a>
  </div>

  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Empresa</th><th>Tipo</th><th>Carreras</th><th>Contacto</th><th>Correo</th><th>Vencimiento</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody id="conv-tbody">
        <?php if (empty($convenios)): ?>
        <tr><td colspan="8" class="adm-table-empty">Sin convenios registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($convenios as $cv):
          $vence   = $cv['vencimiento'] ? new DateTime($cv['vencimiento']) : null;
          $hoy     = new DateTime('today'); // medianoche — compara solo fecha, no hora
          $status  = !$vence ? 'info' : ($hoy > $vence ? 'danger' : ($vence->diff($hoy)->days <= 30 ? 'warn' : 'ok'));
          $cvClaves = $cv['carreras_claves'] ?? '';
        ?>
        <tr id="cv-<?= $cv['id'] ?>" data-carreras="<?= htmlspecialchars($cvClaves) ?>"
            data-search="<?= htmlspecialchars(mb_strtolower($cv['nombre'] . ' ' . ($cv['nombre_contacto'] ?? '') . ' ' . ($cv['correo_contacto'] ?? ''))) ?>"
            data-vence="<?= in_array($status, ['danger', 'warn'], true) ? '1' : '0' ?>"
            data-status="<?= $status ?>"
            <?= !$cv['activo'] ? 'style="opacity:.5"' : '' ?>>
          <td style="font-weight:600"><?= htmlspecialchars($cv['nombre']) ?></td>
          <td><span class="adm-status adm-status--info"><?= htmlspecialchars($cv['tipo_convenio']) ?></span></td>
          <td style="font-size:12px"><?= $cvClaves ? htmlspecialchars($cvClaves) : '<span style="color:var(--tsj-gray-400)">Todas</span>' ?></td>
          <td><?= htmlspecialchars($cv['nombre_contacto'] ?? '') ?></td>
          <td><?= $cv['correo_contacto'] ? '<a href="mailto:'.htmlspecialchars($cv['correo_contacto']).'" style="color:var(--tsj-blue)">'.htmlspecialchars($cv['correo_contacto']).'</a>' : '—' ?></td>
          <td><span class="adm-status adm-status--<?= $status ?>"><?= $cv['vencimiento'] ? date('d/m/Y', strtotime($cv['vencimiento'])) : 'Sin fecha' ?></span></td>
          <td><?php
            if (!$cv['activo']):
              echo '<span class="adm-status adm-status--warn conv-estado-badge">Inactivo</span>';
            elseif ($status === 'danger'):
              echo '<span class="adm-status adm-status--danger conv-estado-badge">Vencido</span>';
            elseif ($status === 'warn'):
              echo '<span class="adm-status adm-status--warn conv-estado-badge">Por vencer</span>';
            else:
              echo '<span class="adm-status adm-status--ok conv-estado-badge">Activo</span>';
            endif;
          ?></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm" title="<?= $cv['activo'] ? 'Desactivar' : 'Activar' ?>"
                    onclick="toggleActivo('convenios','convenio_toggle',<?= $cv['id'] ?>,this)">
              <span class="material-symbols-rounded"><?= $cv['activo'] ? 'visibility_off' : 'visibility' ?></span>
            </button>
            <button class="adm-btn adm-btn--ghost adm-btn--sm"
                    onclick="abrirEditarConv(<?= htmlspecialchars(json_encode($cv), ENT_QUOTES, 'UTF-8') ?>)">
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
    <form data-proc="convenios" data-reload id="form-conv" enctype="multipart/form-data">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
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
        <div class="adm-field"><label>Sector</label>
          <select name="sector" id="conv-sector">
            <option value="privado">Privado</option>
            <option value="publico">Público</option>
            <option value="ac">Asociación Civil</option>
            <option value="otro">Otro</option>
          </select>
        </div>
        <div class="adm-field" style="grid-column:1/-1">
          <label>Carreras <span style="font-weight:400;color:var(--tsj-gray-400);font-size:11px">(sin selección = aplica a todas)</span></label>
          <div style="display:flex;flex-wrap:wrap;gap:10px 20px;margin-top:6px">
            <?php foreach ($carreras as $c): ?>
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;font-weight:400">
              <input type="checkbox" name="carreras_ids[]" value="<?= $c['id'] ?>" class="conv-carrera-chk"
                     style="width:15px;height:15px;accent-color:var(--tsj-blue);cursor:pointer">
              <span><strong><?= htmlspecialchars($c['clave']) ?></strong> — <?= htmlspecialchars($c['nombre']) ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="adm-field"><label>Nombre del contacto</label><input type="text" name="nombre_contacto" id="conv-contacto"></div>
        <div class="adm-field"><label>Correo del contacto</label><input type="email" name="correo_contacto" id="conv-correo"></div>
        <div class="adm-field"><label>Teléfono</label><input type="tel" name="telefono_contacto" id="conv-telefono" inputmode="tel" maxlength="25" pattern="[0-9+\-\s()]{7,25}" title="Solo números, +, -, espacios y paréntesis (entre 7 y 25 caracteres)"></div>
        <div class="adm-field"><label>Fecha de vencimiento</label><input type="date" name="vencimiento" id="conv-vence"></div>
        <div class="adm-field">
          <label>Logo de la empresa</label>
          <input type="file" name="logo_archivo" id="conv-logo-file" accept="image/png,image/jpeg,image/webp,image/gif"
                 onchange="previewLogoConv(this)" style="font-size:13px">
          <input type="hidden" name="logo" id="conv-logo">
          <div id="conv-logo-preview" style="margin-top:6px;display:none">
            <img id="conv-logo-img" src="" alt="preview" style="max-height:56px;max-width:120px;border-radius:6px;border:1px solid var(--tsj-gray-200);object-fit:contain;padding:4px">
            <button type="button" onclick="quitarLogoConv()" style="margin-left:8px;font-size:12px;color:var(--tsj-pink);background:none;border:none;cursor:pointer">Quitar</button>
          </div>
        </div>
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
function toggleActivo(modulo, accion, id, btn) {
  var csrfEl = document.querySelector('input[name="_csrf"]');
  var csrf   = csrfEl ? csrfEl.value : '';
  adminFetch(modulo, { _csrf: csrf, accion: accion, id: id })
    .then(function (json) {
      if (json.ok) {
        var row   = btn.closest('tr');
        var icon  = btn.querySelector('.material-symbols-rounded');
        var badge  = row.querySelector('.conv-estado-badge');
        var activo = json.activo;
        row.style.opacity = activo ? '' : '0.5';
        icon.textContent  = activo ? 'visibility_off' : 'visibility';
        btn.title         = activo ? 'Desactivar' : 'Activar';
        if (badge) {
          if (!activo) {
            badge.textContent = 'Inactivo';
            badge.className   = 'adm-status conv-estado-badge adm-status--warn';
          } else {
            var s = row.dataset.status || 'ok';
            var map = { danger: ['Vencido','adm-status--danger'], warn: ['Por vencer','adm-status--warn'], ok: ['Activo','adm-status--ok'], info: ['Activo','adm-status--ok'] };
            var e = map[s] || map.ok;
            badge.textContent = e[0];
            badge.className   = 'adm-status conv-estado-badge ' + e[1];
          }
        }
      }
    });
}

var convClaveActiva = '';
function filtrarConvenios(clave){
  convClaveActiva = clave;
  document.querySelectorAll('#conv-pills .adm-career-pill').forEach(b=>{
    b.classList.toggle('active', b.textContent.trim()===(clave||'Todos'));
  });
  aplicarFiltroConv();
}
function aplicarFiltroConv(){
  const q    = document.getElementById('conv-buscar').value.trim().toLowerCase();
  const solo = document.getElementById('conv-vencidos').checked;
  let visibles = 0;
  document.querySelectorAll('#conv-tbody tr[data-carreras]').forEach(tr=>{
    // Sin carreras (data-carreras='') → convenio global, aparece siempre
    const claves = tr.dataset.carreras ? tr.dataset.carreras.split(',') : [];
    const okC = !convClaveActiva || claves.length === 0 || claves.includes(convClaveActiva);
    const okQ = !q || (tr.dataset.search||'').includes(q);
    const okV = !solo || tr.dataset.vence==='1';
    const show = okC && okQ && okV;
    tr.style.display = show ? '' : 'none';
    if(show) visibles++;
  });
  let emptyRow = document.getElementById('conv-empty-row');
  if(visibles === 0){
    if(!emptyRow){
      emptyRow = document.createElement('tr');
      emptyRow.id = 'conv-empty-row';
      emptyRow.innerHTML = '<td colspan="8" class="adm-table-empty">No hay convenios registrados para esta selección.</td>';
      document.getElementById('conv-tbody').appendChild(emptyRow);
    }
    emptyRow.style.display = '';
  } else if(emptyRow){
    emptyRow.style.display = 'none';
  }
}

function previewLogoConv(input){
  if(!input.files || !input.files[0]) return;
  const url = URL.createObjectURL(input.files[0]);
  document.getElementById('conv-logo-img').src = url;
  document.getElementById('conv-logo-preview').style.display = '';
  document.getElementById('conv-logo').value = '';
}
function quitarLogoConv(){
  document.getElementById('conv-logo-file').value = '';
  document.getElementById('conv-logo-img').src = '';
  document.getElementById('conv-logo-preview').style.display = 'none';
  document.getElementById('conv-logo').value = '';
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
  document.getElementById('conv-logo-file').value = '';
  if(cv.logo){
    document.getElementById('conv-logo-img').src = cv.logo;
    document.getElementById('conv-logo-preview').style.display = '';
  } else {
    document.getElementById('conv-logo-img').src = '';
    document.getElementById('conv-logo-preview').style.display = 'none';
  }
  const tipo = document.getElementById('conv-tipo');
  for(let o of tipo.options){ if(o.value===cv.tipo_convenio){ o.selected=true; break; } }
  const sec = document.getElementById('conv-sector');
  for(let o of sec.options){ if(o.value===cv.sector){ o.selected=true; break; } }
  // Marcar carreras: desmarcar todas primero, luego marcar las del convenio
  var ids = (cv.carreras_ids_str || '').split(',').filter(Boolean);
  document.querySelectorAll('.conv-carrera-chk').forEach(function(cb){
    cb.checked = ids.includes(cb.value);
  });
  document.getElementById('form-conv-titulo').textContent='Editar: '+cv.nombre;
  document.getElementById('form-conv').scrollIntoView({behavior:'smooth'});
}
function resetFormConv(){
  document.getElementById('conv-accion').value='convenio_agregar';
  document.getElementById('conv-id').value='';
  document.getElementById('form-conv').reset();
  document.querySelectorAll('.conv-carrera-chk').forEach(function(cb){ cb.checked=false; });
  document.getElementById('form-conv-titulo').textContent='Agregar convenio';
  quitarLogoConv();
}

function procesarSug(id, tipo, rowId, csrf){
  const accion = tipo==='aceptar'?'sugerencia_aceptar':'sugerencia_rechazar';
  if(!confirm(tipo==='aceptar'?'¿Aceptar esta sugerencia?':'¿Rechazar esta sugerencia?')) return;
  adminFetch('convenios',{_csrf: csrf, accion, id})
    .then(r=>{
      if(!r.ok) return;
      document.getElementById(rowId)?.remove();
      if(tipo==='aceptar'){
        // Cambiar al tab de convenios
        showTab('conv','convenios');
        // Pre-llenar el formulario con los datos de la sugerencia
        resetFormConv();
        document.getElementById('conv-nombre').value   = r.nombre   || '';
        document.getElementById('conv-correo').value   = r.correo   || '';
        document.getElementById('conv-contacto').value = r.contacto || '';
        // Scroll al formulario
        document.getElementById('form-conv').scrollIntoView({behavior:'smooth', block:'start'});
        // Resaltar el formulario brevemente para que el admin lo note
        const card = document.getElementById('form-conv').closest('.adm-form-card');
        if(card){
          card.style.transition = 'box-shadow .3s';
          card.style.boxShadow  = '0 0 0 3px var(--tsj-blue)';
          setTimeout(()=>{ card.style.boxShadow = ''; }, 2000);
        }
      }
    });
}

setTimeout(function() {
  if (location.hash === '#sugerencias') showTab('conv', 'sugerencias');
  if (location.hash === '#vencimientos') {
    document.getElementById('conv-vencidos').checked = true;
    aplicarFiltroConv();
  }
}, 0);
</script>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
