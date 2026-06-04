<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'visitantes';
$adm_title = 'Visitantes';

try {
    $db = getPDO(DB_NAME);

    $directorio   = $db->query('SELECT * FROM directorio ORDER BY orden,nombre')->fetchAll();
    $carreras     = $db->query('SELECT * FROM carreras ORDER BY orden')->fetchAll();
    $docentes     = $db->query('SELECT d.*,c.clave carrera_clave,c.nombre carrera_nombre FROM docentes d LEFT JOIN carreras c ON d.carrera_id=c.id ORDER BY d.orden,d.nombre')->fetchAll();
    $coordinadores= $db->query('SELECT co.*,c.nombre carrera_nombre FROM coordinadores co JOIN carreras c ON co.carrera_id=c.id ORDER BY c.orden')->fetchAll();
    $secretarias  = $db->query('SELECT * FROM secretarias ORDER BY orden,nombre')->fetchAll();
    $ni           = $db->query('SELECT * FROM nuevo_ingreso_config LIMIT 1')->fetch();

    $materias_por_carrera = [];
    $mats = $db->query('SELECT m.*,c.clave carrera_clave FROM materias m JOIN carreras c ON m.carrera_id=c.id ORDER BY m.orden')->fetchAll();
    foreach ($mats as $m) $materias_por_carrera[$m['carrera_clave']][] = $m;

    $db_ok = true;
} catch (\Throwable $e) {
    $directorio = $carreras = $docentes = $coordinadores = $secretarias = $mats = [];
    $ni = null;
    $materias_por_carrera = [];
    $db_ok = false;
}

$csrf      = csrfToken();
$base_img  = PLATAFORMA_URL . '/modulos/visitantes/imagenes/';

require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Gestión de Visitantes</h1>
    <p class="adm-page-desc">Directorio, docentes, coordinadores, planes de estudio y contenido de servicios.</p>
  </div>
</div>

<div class="adm-tabs">
  <?php
  $tabs = ['directorio'=>'Directorio','docentes'=>'Docentes','coord'=>'Coordinadores',
           'materias'=>'Planes de Estudio','secretarias'=>'Secretarías','servicios'=>'Nuevo Ingreso'];
  foreach ($tabs as $key => $label): ?>
    <button class="adm-tab <?= $key==='directorio'?'active':'' ?>"
            data-tab-group="vis" data-tab="<?= $key ?>" onclick="showTab('vis','<?= $key ?>')">
      <?= $label ?>
    </button>
  <?php endforeach; ?>
</div>

<!-- ══ TAB: Directorio ══════════════════════════════════════════ -->
<div class="adm-tab-panel active" data-tab-group="vis" data-tab="directorio">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Foto</th><th>Nombre</th><th>Puesto / Área</th><th>Correo</th><th>Teléfono</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($directorio)): ?>
        <tr><td colspan="6" class="adm-table-empty">Sin personas registradas.</td></tr>
        <?php endif; ?>
        <?php foreach ($directorio as $p): ?>
        <tr id="dir-<?= $p['id'] ?>">
          <td class="col-photo">
            <img src="<?= $p['foto'] ? $base_img.htmlspecialchars($p['foto']) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='38' height='38'%3E%3Crect width='38' height='38' fill='%23e5e7eb'/%3E%3C/svg%3E" ?>"
                 style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid var(--tsj-blue-100)" alt="">
          </td>
          <td style="font-weight:600;color:var(--tsj-blue)"><?= htmlspecialchars($p['nombre']) ?></td>
          <td><?= htmlspecialchars($p['puesto'] ?? '') ?></td>
          <td><a href="mailto:<?= htmlspecialchars($p['correo'] ?? '') ?>" style="color:var(--tsj-blue)"><?= htmlspecialchars($p['correo'] ?? '') ?></a></td>
          <td><?= htmlspecialchars($p['telefono'] ?? 'S/N') ?></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm"
                    onclick="abrirEditar('dir',<?= htmlspecialchars(json_encode($p)) ?>)">
              <span class="material-symbols-rounded">edit</span>
            </button>
            <button class="adm-btn adm-btn--danger adm-btn--sm"
                    onclick="confirmarEliminar('visitantes','directorio_eliminar',<?= $p['id'] ?>,'dir-<?= $p['id'] ?>')">
              <span class="material-symbols-rounded">delete</span>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Formulario agregar/editar directorio -->
  <div class="adm-form-card" style="margin-top:20px" id="form-dir-wrap">
    <div class="adm-form-title"><span class="material-symbols-rounded">person_add</span> <span id="form-dir-titulo">Agregar persona al directorio</span></div>
    <form data-proc="visitantes" data-accion="directorio_agregar" id="form-dir">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="directorio_agregar" id="dir-accion">
      <input type="hidden" name="id" id="dir-id">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Nombre completo <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="nombre" id="dir-nombre" required></div>
        <div class="adm-field"><label>Puesto / Área</label><input type="text" name="puesto" id="dir-puesto"></div>
        <div class="adm-field"><label>Correo electrónico</label><input type="email" name="correo" id="dir-correo"></div>
        <div class="adm-field"><label>Teléfono</label><input type="tel" name="telefono" id="dir-telefono" placeholder="S/N"></div>
        <div class="adm-field"><label>Extensión</label><input type="text" name="extension" id="dir-extension" placeholder="Ej. Ext. 101"></div>
        <div class="adm-field"><label>Ubicación física</label><input type="text" name="ubicacion_fisica" id="dir-ubicacion" placeholder="Ej. Módulo A, Planta Baja"></div>
        <div class="adm-field"><label>Foto (nombre de archivo)</label><input type="text" name="foto" id="dir-foto" placeholder="ej. miguel.png"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
        <button type="button" class="adm-btn adm-btn--ghost" onclick="resetFormDir()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ TAB: Docentes ════════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="vis" data-tab="docentes">
  <div class="adm-career-pills" id="doc-pills">
    <?php foreach ($carreras as $c): ?>
    <button class="adm-career-pill <?= $c['clave']==='ISC'?'active':'' ?>"
            onclick="filtrarDocentes('<?= $c['clave'] ?>')">
      <?= htmlspecialchars($c['clave']) ?>
    </button>
    <?php endforeach; ?>
  </div>
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Nombre</th><th>Foto</th><th>Correo</th><th>Carrera</th><th>Acciones</th></tr></thead>
      <tbody id="doc-tbody">
        <?php if (empty($docentes)): ?>
        <tr><td colspan="5" class="adm-table-empty">Sin docentes registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($docentes as $d): ?>
        <tr id="doc-<?= $d['id'] ?>" data-carrera="<?= htmlspecialchars($d['carrera_clave'] ?? '') ?>">
          <td style="font-weight:600"><?= htmlspecialchars($d['nombre']) ?></td>
          <td class="col-photo">
            <img src="<?= $d['foto'] ? $base_img.htmlspecialchars($d['foto']) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='38' height='38'%3E%3Crect width='38' height='38' fill='%23e5e7eb'/%3E%3C/svg%3E" ?>"
                 style="width:38px;height:38px;border-radius:50%;object-fit:cover" alt="">
          </td>
          <td><?= $d['correo'] ? '<a href="mailto:'.htmlspecialchars($d['correo']).'" style="color:var(--tsj-blue)">'.htmlspecialchars($d['correo']).'</a>' : '<span style="color:var(--tsj-gray-400)">—</span>' ?></td>
          <td><span class="adm-status adm-status--info"><?= htmlspecialchars($d['carrera_clave'] ?? '—') ?></span></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm"
                    onclick="abrirEditarDoc(<?= htmlspecialchars(json_encode($d)) ?>)">
              <span class="material-symbols-rounded">edit</span>
            </button>
            <button class="adm-btn adm-btn--danger adm-btn--sm"
                    onclick="confirmarEliminar('visitantes','docente_eliminar',<?= $d['id'] ?>,'doc-<?= $d['id'] ?>')">
              <span class="material-symbols-rounded">delete</span>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">school</span> <span id="form-doc-titulo">Agregar docente</span></div>
    <form data-proc="visitantes" data-accion="docente_agregar" id="form-doc">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="docente_agregar" id="doc-accion">
      <input type="hidden" name="id" id="doc-id">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Nombre completo <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="nombre" id="doc-nombre" required></div>
        <div class="adm-field"><label>Correo electrónico</label><input type="email" name="correo" id="doc-correo"></div>
        <div class="adm-field"><label>Carrera</label>
          <select name="carrera_id" id="doc-carrera">
            <option value="">Sin asignar</option>
            <?php foreach ($carreras as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="adm-field"><label>Foto (nombre de archivo)</label><input type="text" name="foto" id="doc-foto" placeholder="ej. miguel.png"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
        <button type="button" class="adm-btn adm-btn--ghost" onclick="resetFormDoc()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ TAB: Coordinadores ══════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="vis" data-tab="coord">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Carrera</th><th>Nombre del Coordinador</th><th>Correo</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php foreach ($coordinadores as $c): ?>
        <tr id="coord-<?= $c['id'] ?>">
          <td style="font-weight:600;color:var(--tsj-blue)"><?= htmlspecialchars($c['carrera_nombre']) ?></td>
          <td><?= htmlspecialchars($c['nombre']) ?></td>
          <td><a href="mailto:<?= htmlspecialchars($c['correo'] ?? '') ?>" style="color:var(--tsj-blue)"><?= htmlspecialchars($c['correo'] ?? '') ?></a></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm"
                    onclick="abrirEditarCoord(<?= htmlspecialchars(json_encode($c)) ?>)">
              <span class="material-symbols-rounded">edit</span>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="adm-form-card" style="margin-top:20px" id="form-coord-wrap" style="display:none">
    <div class="adm-form-title"><span class="material-symbols-rounded">manage_accounts</span> Editar coordinador</div>
    <form data-proc="visitantes" data-accion="coord_editar" id="form-coord">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="coord_editar">
      <input type="hidden" name="id" id="coord-id">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Nombre</label><input type="text" name="nombre" id="coord-nombre" required></div>
        <div class="adm-field"><label>Correo</label><input type="email" name="correo" id="coord-correo"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
        <button type="button" class="adm-btn adm-btn--ghost" onclick="document.getElementById('form-coord-wrap').style.display='none'">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ TAB: Planes de Estudio ══════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="vis" data-tab="materias">
  <?php foreach ($carreras as $c): ?>
  <div class="adm-section" data-carrera-sec="<?= $c['clave'] ?>" style="<?= $c['clave']==='ISC'?'':'display:none' ?>margin-bottom:20px">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">list_alt</span> Materias — <?= htmlspecialchars($c['nombre']) ?></h3>
    </div>
    <div class="adm-section-body">
      <form data-proc="visitantes" data-accion="materias_guardar" class="form-materias">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <input type="hidden" name="accion" value="materias_guardar">
        <input type="hidden" name="carrera_id" value="<?= $c['id'] ?>">
        <div class="adm-list-editor" id="mat-list-<?= $c['id'] ?>">
          <?php foreach ($materias_por_carrera[$c['clave']] ?? [] as $m): ?>
          <div class="adm-list-item">
            <span class="adm-list-item-drag material-symbols-rounded">drag_indicator</span>
            <input type="text" name="materias[]" value="<?= htmlspecialchars($m['nombre']) ?>">
            <button type="button" class="adm-btn adm-btn--danger adm-btn--sm" onclick="this.closest('.adm-list-item').remove()">
              <span class="material-symbols-rounded">delete</span>
            </button>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="adm-list-add" onclick="addMateria(<?= $c['id'] ?>)">
          <span class="material-symbols-rounded">add</span> Agregar materia
        </button>
        <div class="adm-form-actions" style="margin-top:14px">
          <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar materias</button>
        </div>
      </form>
    </div>
  </div>
  <?php endforeach; ?>
  <div class="adm-career-pills" style="margin-bottom:16px">
    <?php foreach ($carreras as $c): ?>
    <button class="adm-career-pill <?= $c['clave']==='ISC'?'active':'' ?>"
            onclick="filtrarMaterias('<?= $c['clave'] ?>')">
      <?= htmlspecialchars($c['clave']) ?>
    </button>
    <?php endforeach; ?>
  </div>
</div>

<!-- ══ TAB: Secretarías ════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="vis" data-tab="secretarias">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Nombre</th><th>Rol</th><th>Correo</th><th>Teléfono</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($secretarias)): ?>
        <tr><td colspan="5" class="adm-table-empty">Sin secretarias registradas.</td></tr>
        <?php endif; ?>
        <?php foreach ($secretarias as $s): ?>
        <tr id="sec-<?= $s['id'] ?>">
          <td style="font-weight:600"><?= htmlspecialchars($s['nombre']) ?></td>
          <td><?= htmlspecialchars($s['rol'] ?? '') ?></td>
          <td><a href="mailto:<?= htmlspecialchars($s['correo'] ?? '') ?>" style="color:var(--tsj-blue)"><?= htmlspecialchars($s['correo'] ?? '') ?></a></td>
          <td><?= htmlspecialchars($s['telefono'] ?? '') ?></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm"
                    onclick="abrirEditarSec(<?= htmlspecialchars(json_encode($s)) ?>)">
              <span class="material-symbols-rounded">edit</span>
            </button>
            <button class="adm-btn adm-btn--danger adm-btn--sm"
                    onclick="confirmarEliminar('visitantes','secretaria_eliminar',<?= $s['id'] ?>,'sec-<?= $s['id'] ?>')">
              <span class="material-symbols-rounded">delete</span>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">person_add</span> <span id="form-sec-titulo">Agregar secretaria</span></div>
    <form data-proc="visitantes" data-accion="secretaria_agregar" id="form-sec">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="secretaria_agregar" id="sec-accion">
      <input type="hidden" name="id" id="sec-id">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Nombre completo <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="nombre" id="sec-nombre" required></div>
        <div class="adm-field"><label>Rol</label><input type="text" name="rol" id="sec-rol"></div>
        <div class="adm-field"><label>Correo</label><input type="email" name="correo" id="sec-correo"></div>
        <div class="adm-field"><label>Teléfono</label><input type="tel" name="telefono" id="sec-telefono"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
        <button type="button" class="adm-btn adm-btn--ghost" onclick="resetFormSec()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ TAB: Nuevo Ingreso ════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="vis" data-tab="servicios">
  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">how_to_reg</span> Nuevo Ingreso — Configuración</div>
    <form data-proc="visitantes" data-accion="nuevo_ingreso_guardar">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="nuevo_ingreso_guardar">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Día del examen (del mes)</label><input type="number" name="dia_examen" value="<?= (int)($ni['dia_examen'] ?? 20) ?>" min="1" max="31" required></div>
        <div class="adm-field"><label>Hora del examen</label><input type="time" name="hora_examen" value="<?= htmlspecialchars($ni['hora_examen'] ?? '08:00') ?>"></div>
        <div class="adm-field" style="grid-column:1/-1"><label>Lugar del examen</label><input type="text" name="lugar_examen" value="<?= htmlspecialchars($ni['lugar_examen'] ?? 'Tecnológico Superior de Jalisco, Campus Chapala') ?>"></div>
      </div>
      <div class="adm-field" style="margin-bottom:16px">
        <label>Requisitos de admisión (uno por línea)</label>
        <textarea name="requisitos" style="min-height:140px"><?php
          $reqs = $ni['requisitos'] ?? '[]';
          $arr  = json_decode($reqs, true) ?: [];
          echo htmlspecialchars(implode("\n", $arr));
        ?></textarea>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
      </div>
    </form>
  </div>
</div>

<script>
// ── Filtros de carrera ──────────────────────────────────────────
function filtrarDocentes(clave){
  document.querySelectorAll('#doc-pills .adm-career-pill').forEach(b=>{
    b.classList.toggle('active', b.textContent.trim()===clave);
  });
  document.querySelectorAll('#doc-tbody tr[data-carrera]').forEach(tr=>{
    tr.style.display = (tr.dataset.carrera===clave || clave==='') ? '' : 'none';
  });
}
function filtrarMaterias(clave){
  document.querySelectorAll('[data-carrera-sec]').forEach(s=>{
    s.style.display = s.dataset.carrerasSec===clave ? '' : 'none';
  });
  // Corregir atributo typo
  document.querySelectorAll('[data-carrera-sec]').forEach(s=>{
    s.style.display = s.getAttribute('data-carrera-sec')===clave ? '' : 'none';
  });
  document.querySelectorAll('.adm-career-pill').forEach(b=>{
    if(b.closest('[data-tab-group="vis"][data-tab="materias"]')===null) return;
    b.classList.toggle('active', b.textContent.trim()===clave);
  });
}
document.addEventListener('DOMContentLoaded',()=>filtrarDocentes('ISC'));

// ── Materia agregar fila ────────────────────────────────────────
function addMateria(carreraId){
  const list = document.getElementById('mat-list-'+carreraId);
  const div  = document.createElement('div');
  div.className = 'adm-list-item';
  div.innerHTML = `<span class="adm-list-item-drag material-symbols-rounded">drag_indicator</span>
    <input type="text" name="materias[]" placeholder="Nueva materia">
    <button type="button" class="adm-btn adm-btn--danger adm-btn--sm" onclick="this.closest('.adm-list-item').remove()">
      <span class="material-symbols-rounded">delete</span></button>`;
  list.appendChild(div);
  div.querySelector('input').focus();
}

// ── Directorio ─────────────────────────────────────────────────
function abrirEditar(tipo, p){
  document.getElementById('dir-accion').value = 'directorio_editar';
  document.getElementById('dir-id').value     = p.id;
  document.getElementById('dir-nombre').value  = p.nombre||'';
  document.getElementById('dir-puesto').value  = p.puesto||'';
  document.getElementById('dir-correo').value  = p.correo||'';
  document.getElementById('dir-telefono').value  = p.telefono||'';
  document.getElementById('dir-extension').value = p.extension||'';
  document.getElementById('dir-ubicacion').value  = p.ubicacion_fisica||'';
  document.getElementById('dir-foto').value       = p.foto||'';
  document.getElementById('form-dir-titulo').textContent = 'Editar: '+p.nombre;
  document.getElementById('form-dir-wrap').scrollIntoView({behavior:'smooth'});
}
function resetFormDir(){
  document.getElementById('dir-accion').value='directorio_agregar';
  document.getElementById('dir-id').value='';
  document.getElementById('form-dir').reset();
  document.getElementById('form-dir-titulo').textContent='Agregar persona al directorio';
}

// ── Docentes ────────────────────────────────────────────────────
function abrirEditarDoc(d){
  document.getElementById('doc-accion').value  = 'docente_editar';
  document.getElementById('doc-id').value      = d.id;
  document.getElementById('doc-nombre').value  = d.nombre||'';
  document.getElementById('doc-correo').value  = d.correo||'';
  document.getElementById('doc-foto').value    = d.foto||'';
  const sel = document.getElementById('doc-carrera');
  for(let o of sel.options){ if(o.value==d.carrera_id){ o.selected=true; break; } }
  document.getElementById('form-doc-titulo').textContent='Editar: '+d.nombre;
  document.getElementById('form-doc').scrollIntoView({behavior:'smooth'});
}
function resetFormDoc(){
  document.getElementById('doc-accion').value='docente_agregar';
  document.getElementById('doc-id').value='';
  document.getElementById('form-doc').reset();
  document.getElementById('form-doc-titulo').textContent='Agregar docente';
}

// ── Coordinadores ───────────────────────────────────────────────
function abrirEditarCoord(c){
  document.getElementById('coord-id').value    = c.id;
  document.getElementById('coord-nombre').value= c.nombre||'';
  document.getElementById('coord-correo').value= c.correo||'';
  document.getElementById('form-coord-wrap').style.display='';
  document.getElementById('form-coord-wrap').scrollIntoView({behavior:'smooth'});
}

// ── Secretarías ─────────────────────────────────────────────────
function abrirEditarSec(s){
  document.getElementById('sec-accion').value   = 'secretaria_editar';
  document.getElementById('sec-id').value       = s.id;
  document.getElementById('sec-nombre').value   = s.nombre||'';
  document.getElementById('sec-rol').value      = s.rol||'';
  document.getElementById('sec-correo').value   = s.correo||'';
  document.getElementById('sec-telefono').value = s.telefono||'';
  document.getElementById('form-sec-titulo').textContent='Editar: '+s.nombre;
  document.getElementById('form-sec').scrollIntoView({behavior:'smooth'});
}
function resetFormSec(){
  document.getElementById('sec-accion').value='secretaria_agregar';
  document.getElementById('sec-id').value='';
  document.getElementById('form-sec').reset();
  document.getElementById('form-sec-titulo').textContent='Agregar secretaria';
}
</script>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
