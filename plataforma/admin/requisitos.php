<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'requisitos';
$adm_title = 'Servicio social y residencias';

try {
    $db = getPDO(DB_NAME);

    $tipos = ['residencia','servicio_social'];
    $data  = [];
    foreach ($tipos as $t) {
        $data[$t]['requisitos'] = $db->prepare('SELECT * FROM requisitos_items WHERE tipo=? ORDER BY orden');
        $data[$t]['requisitos']->execute([$t]);
        $data[$t]['requisitos'] = $data[$t]['requisitos']->fetchAll();

        $data[$t]['fases'] = $db->prepare('SELECT * FROM timeline_fases WHERE tipo=? ORDER BY orden');
        $data[$t]['fases']->execute([$t]);
        $data[$t]['fases'] = $data[$t]['fases']->fetchAll();

        $data[$t]['documentos'] = $db->prepare('SELECT * FROM documentos_descargables WHERE tipo=? ORDER BY orden');
        $data[$t]['documentos']->execute([$t]);
        $data[$t]['documentos'] = $data[$t]['documentos']->fetchAll();

        $data[$t]['faq'] = $db->prepare('SELECT * FROM faq WHERE tipo=? ORDER BY orden');
        $data[$t]['faq']->execute([$t]);
        $data[$t]['faq'] = $data[$t]['faq']->fetchAll();
    }
    $db_ok = true;
} catch (\Throwable $e) {
    $data  = ['residencia'=>['requisitos'=>[],'fases'=>[],'documentos'=>[],'faq'=>[]],'servicio_social'=>['requisitos'=>[],'fases'=>[],'documentos'=>[],'faq'=>[]]];
    $db_ok = false;
}

$csrf = csrfToken();
require_once __DIR__ . '/_layout.php';

// Helper para renderizar un tab de tipo
function renderTipoTab(string $tipo, array $d, string $csrf): void {
    $label = $tipo === 'residencia' ? 'Residencia Profesional' : 'Servicio Social';
    $active = $tipo === 'residencia' ? 'active' : '';
?>
<div class="adm-tab-panel <?= $active ?>" data-tab-group="req" data-tab="<?= $tipo ?>">

  <!-- Checklist de requisitos -->
  <div class="adm-section" style="margin-bottom:20px">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">checklist</span> Checklist de requisitos</h3>
    </div>
    <div class="adm-section-body">
      <form data-proc="requisitos" data-accion="requisitos_guardar">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <input type="hidden" name="accion" value="requisitos_guardar">
        <input type="hidden" name="tipo" value="<?= $tipo ?>">
        <div class="adm-list-editor" id="req-list-<?= $tipo ?>">
          <?php foreach ($d['requisitos'] as $item): ?>
          <div class="adm-list-item">
            <span class="adm-list-item-drag material-symbols-rounded">drag_indicator</span>
            <input type="text" name="items[]" value="<?= htmlspecialchars($item['texto']) ?>">
            <button type="button" class="adm-btn adm-btn--danger adm-btn--sm" onclick="this.closest('.adm-list-item').remove()">
              <span class="material-symbols-rounded">delete</span>
            </button>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="adm-list-add" onclick="addItem('req-list-<?= $tipo ?>','items[]')">
          <span class="material-symbols-rounded">add</span> Agregar requisito
        </button>
        <div class="adm-form-actions" style="margin-top:14px">
          <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar requisitos</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Timeline de fases -->
  <div class="adm-section" style="margin-bottom:20px">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">timeline</span> Fases del proceso</h3>
      <button type="button" class="adm-btn adm-btn--primary adm-btn--sm" onclick="addFase('fases-<?= $tipo ?>')">
        <span class="material-symbols-rounded">add</span> Agregar fase
      </button>
    </div>
    <div class="adm-section-body">
      <form class="form-multipart" data-accion-mp="timeline_guardar">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <input type="hidden" name="accion" value="timeline_guardar">
        <input type="hidden" name="tipo" value="<?= $tipo ?>">
        <div id="fases-<?= $tipo ?>">
          <?php foreach ($d['fases'] as $i => $f): ?>
          <div class="adm-form-card" style="margin-bottom:12px;padding:16px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
              <span style="background:var(--tsj-blue);color:#fff;width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0"><?= $i+1 ?></span>
              <strong style="color:var(--tsj-blue)"><?= htmlspecialchars($f['titulo']) ?></strong>
              <button type="button" class="adm-btn adm-btn--danger adm-btn--sm" style="margin-left:auto" onclick="this.closest('.adm-form-card').remove()">
                <span class="material-symbols-rounded">delete</span>
              </button>
            </div>
            <div class="adm-form-grid cols-3">
              <div class="adm-field"><label>Título</label><input type="text" name="fases[<?= $i ?>][titulo]" value="<?= htmlspecialchars($f['titulo']) ?>" required></div>
              <div class="adm-field"><label>Descripción</label><input type="text" name="fases[<?= $i ?>][descripcion]" value="<?= htmlspecialchars($f['descripcion'] ?? '') ?>"></div>
              <div class="adm-field"><label>Tiempo / Referencia</label><input type="text" name="fases[<?= $i ?>][tiempo]" value="<?= htmlspecialchars($f['tiempo_referencia'] ?? '') ?>"></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="adm-form-actions">
          <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar fases</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Documentos descargables -->
  <div class="adm-section" style="margin-bottom:20px">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">download</span> Documentos descargables</h3>
    </div>
    <div class="adm-section-body">
      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead><tr><th>Nombre</th><th>URL / Enlace</th><th>Tipo</th><th>Acciones</th></tr></thead>
          <tbody id="docs-<?= $tipo ?>">
            <?php foreach ($d['documentos'] as $doc): ?>
            <tr id="doc-<?= $doc['id'] ?>">
              <td style="font-weight:600"><?= htmlspecialchars($doc['nombre']) ?></td>
              <td><a href="<?= htmlspecialchars($doc['url']) ?>" target="_blank" style="color:var(--tsj-blue);font-size:12.5px"><?= htmlspecialchars(substr($doc['url'],0,50)) ?>…</a></td>
              <td><span class="adm-status adm-status--info"><?= htmlspecialchars($doc['tipo_archivo']) ?></span></td>
              <td class="actions">
                <button class="adm-btn adm-btn--ghost adm-btn--sm" onclick="abrirEditarDoc(<?= htmlspecialchars(json_encode($doc)) ?>,'<?= $tipo ?>')">
                  <span class="material-symbols-rounded">edit</span>
                </button>
                <button class="adm-btn adm-btn--danger adm-btn--sm" onclick="confirmarEliminar('requisitos','doc_eliminar',<?= $doc['id'] ?>,'doc-<?= $doc['id'] ?>')">
                  <span class="material-symbols-rounded">delete</span>
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="adm-form-card" style="margin-top:14px" id="form-doc-wrap-<?= $tipo ?>">
        <form data-proc="requisitos" data-reload class="form-doc" id="form-doc-<?= $tipo ?>">
          <input type="hidden" name="_csrf" value="<?= $csrf ?>">
          <input type="hidden" name="accion" value="doc_agregar" class="doc-accion">
          <input type="hidden" name="tipo" value="<?= $tipo ?>">
          <input type="hidden" name="id" class="doc-id">
          <div class="adm-form-grid cols-3">
            <div class="adm-field"><label>Nombre del documento</label><input type="text" name="nombre" class="doc-nombre" required></div>
            <div class="adm-field"><label>URL del archivo</label><input type="url" name="url" class="doc-url" required></div>
            <div class="adm-field"><label>Tipo</label>
              <select name="tipo_archivo" class="doc-tipo">
                <option value="Google Drive">Google Drive</option>
                <option value="PDF">PDF local</option>
                <option value="Otro">Otro</option>
              </select>
            </div>
          </div>
          <div class="adm-form-actions">
            <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar documento</button>
            <button type="button" class="adm-btn adm-btn--ghost" onclick="resetFormDoc('<?= $tipo ?>')">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- FAQ -->
  <div class="adm-section">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">help</span> Preguntas frecuentes (FAQ)</h3>
      <button type="button" class="adm-btn adm-btn--primary adm-btn--sm" onclick="addFaq('faqs-<?= $tipo ?>')">
        <span class="material-symbols-rounded">add</span> Agregar pregunta
      </button>
    </div>
    <div class="adm-section-body">
      <form class="form-multipart" data-accion-mp="faq_guardar">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <input type="hidden" name="accion" value="faq_guardar">
        <input type="hidden" name="tipo" value="<?= $tipo ?>">
        <div id="faqs-<?= $tipo ?>">
          <?php foreach ($d['faq'] as $i => $q): ?>
          <div class="adm-form-card" style="margin-bottom:12px;padding:16px">
            <div class="adm-form-grid cols-1">
              <div class="adm-field"><label>Pregunta <?= $i+1 ?></label><input type="text" name="faqs[<?= $i ?>][pregunta]" value="<?= htmlspecialchars($q['pregunta']) ?>" required></div>
              <div class="adm-field"><label>Respuesta</label><textarea name="faqs[<?= $i ?>][respuesta]"><?= htmlspecialchars($q['respuesta']) ?></textarea></div>
            </div>
            <div style="margin-top:8px;text-align:right">
              <button type="button" class="adm-btn adm-btn--danger adm-btn--sm" onclick="this.closest('.adm-form-card').remove()">
                <span class="material-symbols-rounded">delete</span> Eliminar
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="adm-form-actions">
          <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar FAQ</button>
        </div>
      </form>
    </div>
  </div>

</div>
<?php } ?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Servicio social y residencias</h1>
    <p class="adm-page-desc">Requisitos, documentos descargables, fases del proceso (timeline) y preguntas frecuentes.</p>
  </div>
</div>

<div class="adm-tabs">
  <button class="adm-tab active" data-tab-group="req" data-tab="residencia" onclick="showTab('req','residencia')">Residencia Profesional</button>
  <button class="adm-tab" data-tab-group="req" data-tab="servicio_social" onclick="showTab('req','servicio_social')">Servicio Social</button>
</div>

<?php
renderTipoTab('residencia',    $data['residencia'],    $csrf);
renderTipoTab('servicio_social',$data['servicio_social'],$csrf);
?>

<script>
// Manejador para formularios con arrays anidados (fases/faq) — usa FormData directo
(function () {
  var base = document.querySelector('meta[name="plataforma-url"]')?.content || '/plataforma';
  document.querySelectorAll('.form-multipart').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var btn = form.querySelector('[type="submit"]');
      if (btn) btn.disabled = true;
      fetch(base + '/admin/procesos/requisitos.php', { method: 'POST', body: new FormData(form) })
        .then(function (r) { return r.json(); })
        .then(function (json) {
          showToast(json.msg, json.ok ? 'ok' : 'error');
          if (json.ok) setTimeout(function () { location.reload(); }, 900);
        })
        .catch(function (err) { showToast('Error: ' + err.message, 'error'); })
        .finally(function () { if (btn) btn.disabled = false; });
    });
  });
})();

// Helpers para agregar items dinámicos
function addItem(listId, name){
  const list = document.getElementById(listId);
  const div  = document.createElement('div');
  div.className = 'adm-list-item';
  div.innerHTML = `<span class="adm-list-item-drag material-symbols-rounded">drag_indicator</span>
    <input type="text" name="${name}" placeholder="Nuevo ítem">
    <button type="button" class="adm-btn adm-btn--danger adm-btn--sm" onclick="this.closest('.adm-list-item').remove()">
      <span class="material-symbols-rounded">delete</span></button>`;
  list.appendChild(div);
  div.querySelector('input').focus();
}

let faseCount = 100;
function addFase(containerId){
  const c   = document.getElementById(containerId);
  const idx = faseCount++;
  const div = document.createElement('div');
  div.className='adm-form-card';
  div.style='margin-bottom:12px;padding:16px';
  div.innerHTML=`<div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
    <span style="background:var(--tsj-blue);color:#fff;width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700">+</span>
    <strong style="color:var(--tsj-blue)">Nueva fase</strong>
    <button type="button" class="adm-btn adm-btn--danger adm-btn--sm" style="margin-left:auto" onclick="this.closest('.adm-form-card').remove()">
      <span class="material-symbols-rounded">delete</span></button></div>
    <div class="adm-form-grid cols-3">
      <div class="adm-field"><label>Título</label><input type="text" name="fases[${idx}][titulo]" required></div>
      <div class="adm-field"><label>Descripción</label><input type="text" name="fases[${idx}][descripcion]"></div>
      <div class="adm-field"><label>Tiempo / Referencia</label><input type="text" name="fases[${idx}][tiempo]"></div>
    </div>`;
  c.appendChild(div);
  div.querySelector('input').focus();
}

let faqCount = 100;
function addFaq(containerId){
  const c   = document.getElementById(containerId);
  const idx = faqCount++;
  const div = document.createElement('div');
  div.className='adm-form-card';
  div.style='margin-bottom:12px;padding:16px';
  div.innerHTML=`<div class="adm-form-grid cols-1">
    <div class="adm-field"><label>Pregunta</label><input type="text" name="faqs[${idx}][pregunta]" required></div>
    <div class="adm-field"><label>Respuesta</label><textarea name="faqs[${idx}][respuesta]"></textarea></div></div>
    <div style="margin-top:8px;text-align:right">
      <button type="button" class="adm-btn adm-btn--danger adm-btn--sm" onclick="this.closest('.adm-form-card').remove()">
        <span class="material-symbols-rounded">delete</span> Eliminar</button></div>`;
  c.appendChild(div);
  div.querySelector('input').focus();
}

function abrirEditarDoc(doc, tipo){
  const form = document.getElementById('form-doc-'+tipo);
  form.querySelector('.doc-accion').value = 'doc_editar';
  form.querySelector('.doc-id').value     = doc.id;
  form.querySelector('.doc-nombre').value = doc.nombre||'';
  form.querySelector('.doc-url').value    = doc.url||'';
  const sel = form.querySelector('.doc-tipo');
  for(let o of sel.options){ if(o.value===doc.tipo_archivo){ o.selected=true; break; } }
  form.scrollIntoView({behavior:'smooth'});
}
function resetFormDoc(tipo){
  const form = document.getElementById('form-doc-'+tipo);
  form.querySelector('.doc-accion').value='doc_agregar';
  form.querySelector('.doc-id').value='';
  form.reset();
}
</script>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
