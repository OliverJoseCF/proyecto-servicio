<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'inicio';
$adm_title = 'Inicio del Portal';

try {
    $db       = getPDO(DB_NAME);
    $avisos   = $db->query('SELECT * FROM avisos ORDER BY orden, fecha DESC')->fetchAll();
    $carrusel = $db->query('SELECT * FROM carrusel_fotos ORDER BY orden')->fetchAll();
    $faqs_g   = $db->query('SELECT * FROM faq WHERE tipo="general" ORDER BY orden')->fetchAll();
    $db_ok    = true;
} catch (\Throwable $e) {
    $avisos = $carrusel = $faqs_g = [];
    $db_ok  = false;
    $db_err = $e->getMessage();
}

$csrf = csrfToken();
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Gestión del Inicio del Portal</h1>
    <p class="adm-page-desc">Administra el carrusel de fotos, avisos importantes y dudas frecuentes generales.</p>
  </div>
</div>

<div class="adm-tabs">
  <?php foreach (['carrusel'=>'Carrusel de fotos','avisos'=>'Avisos importantes','faq_general'=>'Dudas frecuentes'] as $k=>$l): ?>
  <button class="adm-tab <?= $k==='carrusel'?'active':'' ?>"
          data-tab-group="ini" data-tab="<?= $k ?>" onclick="showTab('ini','<?= $k ?>')">
    <?= $l ?>
  </button>
  <?php endforeach; ?>
</div>

<!-- ══ Carrusel ══════════════════════════════════════════════════ -->
<div class="adm-tab-panel active" data-tab-group="ini" data-tab="carrusel">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Vista previa</th><th>Título</th><th>Subtítulo</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($carrusel)): ?>
        <tr><td colspan="5" class="adm-table-empty">Sin imágenes en el carrusel.</td></tr>
        <?php endif; ?>
        <?php foreach ($carrusel as $s): ?>
        <tr id="car-<?= $s['id'] ?>" <?= !$s['activo'] ? 'style="opacity:.5"' : '' ?>>
          <td>
            <img src="<?= htmlspecialchars($s['url']) ?>" alt=""
                 style="width:80px;height:45px;object-fit:cover;border-radius:6px;border:1px solid #e8eaf2"
                 onerror="this.style.display='none'">
          </td>
          <td style="font-weight:600"><?= htmlspecialchars($s['titulo'] ?? '—') ?></td>
          <td style="font-size:12.5px;color:var(--tsj-gray-600)"><?= htmlspecialchars($s['subtitulo'] ?? '—') ?></td>
          <td>
            <?php if ($s['activo']): ?>
              <span class="adm-status adm-status--ok">Visible</span>
            <?php else: ?>
              <span class="adm-status adm-status--warn">Oculta</span>
            <?php endif; ?>
          </td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm" title="<?= $s['activo'] ? 'Ocultar' : 'Mostrar' ?>"
                    onclick="toggleActivo('inicio','carrusel_toggle',<?= $s['id'] ?>,this)">
              <span class="material-symbols-rounded"><?= $s['activo'] ? 'visibility_off' : 'visibility' ?></span>
            </button>
            <button class="adm-btn adm-btn--ghost adm-btn--sm" onclick="abrirEditarCar(<?= htmlspecialchars(json_encode($s)) ?>)">
              <span class="material-symbols-rounded">edit</span>
            </button>
            <button class="adm-btn adm-btn--danger adm-btn--sm" onclick="confirmarEliminar('inicio','carrusel_eliminar',<?= $s['id'] ?>,'car-<?= $s['id'] ?>')">
              <span class="material-symbols-rounded">delete</span>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">add_photo_alternate</span> <span id="form-car-titulo">Agregar imagen al carrusel</span></div>
    <form data-proc="inicio" data-reload id="form-car">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="carrusel_agregar" id="car-accion">
      <input type="hidden" name="id" id="car-id">
      <div class="adm-form-grid cols-2">
        <div class="adm-field" style="grid-column:1/-1">
          <label>URL de la imagen <span style="color:var(--tsj-pink)">*</span></label>
          <input type="url" name="url" id="car-url" placeholder="https://… o ruta relativa" required>
          <span class="adm-field-help">Puede ser una URL externa o una ruta dentro del servidor.</span>
        </div>
        <div class="adm-field"><label>Título (opcional)</label><input type="text" name="titulo" id="car-titulo-inp" placeholder="Ej. Semestre 2025-A"></div>
        <div class="adm-field"><label>Subtítulo (opcional)</label><input type="text" name="subtitulo" id="car-subtitulo"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
        <button type="button" class="adm-btn adm-btn--ghost" onclick="resetFormCar()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ Avisos ════════════════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="ini" data-tab="avisos">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Fecha</th><th>Título</th><th>Descripción</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($avisos)): ?>
        <tr><td colspan="5" class="adm-table-empty">Sin avisos registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($avisos as $av): ?>
        <tr id="av-<?= $av['id'] ?>" <?= !$av['activo'] ? 'style="opacity:.5"' : '' ?>>
          <td><span class="adm-status adm-status--info"><?= htmlspecialchars($av['fecha']) ?></span></td>
          <td style="font-weight:600"><?= htmlspecialchars($av['titulo']) ?></td>
          <td style="font-size:12.5px;color:var(--tsj-gray-600)"><?= htmlspecialchars(substr($av['descripcion'] ?? '', 0, 80)) ?><?= strlen($av['descripcion'] ?? '') > 80 ? '…' : '' ?></td>
          <td>
            <?php if ($av['activo']): ?>
              <span class="adm-status adm-status--ok">Visible</span>
            <?php else: ?>
              <span class="adm-status adm-status--warn">Oculto</span>
            <?php endif; ?>
          </td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm" title="<?= $av['activo'] ? 'Ocultar' : 'Mostrar' ?>"
                    onclick="toggleActivo('inicio','aviso_toggle',<?= $av['id'] ?>,this)">
              <span class="material-symbols-rounded"><?= $av['activo'] ? 'visibility_off' : 'visibility' ?></span>
            </button>
            <button class="adm-btn adm-btn--ghost adm-btn--sm" onclick="abrirEditarAv(<?= htmlspecialchars(json_encode($av)) ?>)">
              <span class="material-symbols-rounded">edit</span>
            </button>
            <button class="adm-btn adm-btn--danger adm-btn--sm" onclick="confirmarEliminar('inicio','aviso_eliminar',<?= $av['id'] ?>,'av-<?= $av['id'] ?>')">
              <span class="material-symbols-rounded">delete</span>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">campaign</span> <span id="form-av-titulo">Agregar aviso</span></div>
    <form data-proc="inicio" data-reload id="form-av">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="aviso_agregar" id="av-accion">
      <input type="hidden" name="id" id="av-id">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Título <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="titulo" id="av-titulo" required></div>
        <div class="adm-field"><label>Fecha</label><input type="date" name="fecha" id="av-fecha" value="<?= date('Y-m-d') ?>"></div>
        <div class="adm-field" style="grid-column:1/-1"><label>Descripción</label><textarea name="descripcion" id="av-desc" rows="3"></textarea></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar aviso</button>
        <button type="button" class="adm-btn adm-btn--ghost" onclick="resetFormAv()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ FAQ General ═══════════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="ini" data-tab="faq_general">
  <div class="adm-section-header" style="margin-bottom:16px">
    <h3 class="adm-section-title"><span class="material-symbols-rounded">help</span> Dudas frecuentes generales</h3>
    <button type="button" class="adm-btn adm-btn--primary adm-btn--sm" onclick="addFaqG()">
      <span class="material-symbols-rounded">add</span> Agregar pregunta
    </button>
  </div>
  <form id="form-faq-g">
    <input type="hidden" name="_csrf" value="<?= $csrf ?>">
    <input type="hidden" name="accion" value="faq_general_guardar">
    <div id="faqs-general">
      <?php foreach ($faqs_g as $i => $q): ?>
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
      <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar dudas frecuentes</button>
    </div>
  </form>
</div>

<script>
// ── Carrusel ────────────────────────────────────────────────────
function abrirEditarCar(s){
  document.getElementById('car-accion').value      = 'carrusel_editar';
  document.getElementById('car-id').value          = s.id;
  document.getElementById('car-url').value         = s.url||'';
  document.getElementById('car-titulo-inp').value  = s.titulo||'';
  document.getElementById('car-subtitulo').value   = s.subtitulo||'';
  document.getElementById('form-car-titulo').textContent = 'Editar imagen';
  document.getElementById('form-car').scrollIntoView({behavior:'smooth'});
}
function resetFormCar(){
  document.getElementById('car-accion').value='carrusel_agregar';
  document.getElementById('car-id').value='';
  document.getElementById('form-car').reset();
  document.getElementById('form-car-titulo').textContent='Agregar imagen al carrusel';
}

// ── Avisos ──────────────────────────────────────────────────────
function abrirEditarAv(a){
  document.getElementById('av-accion').value = 'aviso_editar';
  document.getElementById('av-id').value     = a.id;
  document.getElementById('av-titulo').value = a.titulo||'';
  document.getElementById('av-fecha').value  = a.fecha||'';
  document.getElementById('av-desc').value   = a.descripcion||'';
  document.getElementById('form-av-titulo').textContent = 'Editar aviso';
  document.getElementById('form-av').scrollIntoView({behavior:'smooth'});
}
function resetFormAv(){
  document.getElementById('av-accion').value='aviso_agregar';
  document.getElementById('av-id').value='';
  document.getElementById('form-av').reset();
  document.getElementById('av-fecha').value='<?= date('Y-m-d') ?>';
  document.getElementById('form-av-titulo').textContent='Agregar aviso';
}

// ── Toggle visibilidad (carrusel / avisos) ───────────────────────
function toggleActivo(modulo, accion, id, btn) {
  var csrfEl = document.querySelector('input[name="_csrf"]');
  var csrf   = csrfEl ? csrfEl.value : '';
  adminFetch(modulo, { _csrf: csrf, accion: accion, id: id })
    .then(function (json) {
      if (json.ok) {
        var row  = btn.closest('tr');
        var icon = btn.querySelector('.material-symbols-rounded');
        var badge = row.querySelector('.adm-status');
        var activo = json.activo;
        row.style.opacity = activo ? '' : '0.5';
        icon.textContent  = activo ? 'visibility_off' : 'visibility';
        btn.title         = activo ? 'Ocultar' : 'Mostrar';
        if (badge) {
          badge.textContent  = activo ? 'Visible' : 'Oculto/a';
          badge.className    = 'adm-status ' + (activo ? 'adm-status--ok' : 'adm-status--warn');
        }
      }
    });
}

// ── FAQ General ─────────────────────────────────────────────────
(function () {
  var formFaq = document.getElementById('form-faq-g');
  if (!formFaq) return;
  formFaq.addEventListener('submit', function (e) {
    e.preventDefault();
    var base = (document.querySelector('meta[name="plataforma-url"]')?.content || '/plataforma');
    var btn  = formFaq.querySelector('[type="submit"]');
    if (btn) btn.disabled = true;
    fetch(base + '/admin/procesos/inicio.php', { method: 'POST', body: new FormData(formFaq) })
      .then(function (r) { return r.json(); })
      .then(function (json) {
        showToast(json.msg, json.ok ? 'ok' : 'error');
        if (json.ok) setTimeout(function () { location.reload(); }, 900);
      })
      .catch(function (err) { showToast('Error: ' + err.message, 'error'); })
      .finally(function () { if (btn) btn.disabled = false; });
  });
})();

var faqGCount = 100;
function addFaqG(){
  var c   = document.getElementById('faqs-general');
  var idx = faqGCount++;
  var div = document.createElement('div');
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
</script>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
