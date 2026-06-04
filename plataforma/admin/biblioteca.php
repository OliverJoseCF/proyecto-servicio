<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'biblioteca';
$adm_title = 'Biblioteca';

try {
    $db = getPDO(DB_NAME);
    $libros      = $db->query('SELECT * FROM libros ORDER BY codigo')->fetchAll();
    $prestamos   = $db->query('SELECT p.*,l.nombre libro_nombre FROM prestamos p JOIN libros l ON p.libro_id=l.id WHERE p.devuelto=0 ORDER BY p.fecha_devolucion')->fetchAll();
    $solicitudes = $db->query('SELECT s.*,l.nombre libro_nombre FROM solicitudes_biblioteca s JOIN libros l ON s.libro_id=l.id WHERE s.estado="pendiente" ORDER BY s.created_at DESC')->fetchAll();
    $db_ok = true;
} catch (\Throwable $e) {
    $libros = $prestamos = $solicitudes = [];
    $db_ok  = false;
}

$csrf = csrfToken();
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Gestión de Biblioteca</h1>
    <p class="adm-page-desc">Catálogo de libros, préstamos activos y solicitudes de estudiantes.</p>
  </div>
</div>

<div class="adm-tabs">
  <button class="adm-tab active" data-tab-group="bib" data-tab="catalogo" onclick="showTab('bib','catalogo')">
    Catálogo
  </button>
  <button class="adm-tab" data-tab-group="bib" data-tab="prestamos" onclick="showTab('bib','prestamos')">
    Préstamos activos <?php if (count($prestamos)): ?><span style="background:var(--tsj-blue);color:#fff;font-size:11px;padding:1px 7px;border-radius:99px;margin-left:4px"><?= count($prestamos) ?></span><?php endif; ?>
  </button>
  <button class="adm-tab" data-tab-group="bib" data-tab="solicitudes" onclick="showTab('bib','solicitudes')" id="tab-solicitudes">
    Solicitudes pendientes <?php if (count($solicitudes)): ?><span style="background:#f59e0b;color:#fff;font-size:11px;padding:1px 7px;border-radius:99px;margin-left:4px"><?= count($solicitudes) ?></span><?php endif; ?>
  </button>
</div>

<!-- ══ Catálogo ══════════════════════════════════════════════════ -->
<div class="adm-tab-panel active" data-tab-group="bib" data-tab="catalogo">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Código</th><th>Título</th><th>Autor</th><th>Editorial</th><th>Categoría</th><th>Ejemplares</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($libros)): ?>
        <tr><td colspan="7" class="adm-table-empty">Sin libros en catálogo.</td></tr>
        <?php endif; ?>
        <?php foreach ($libros as $l): ?>
        <tr id="lib-<?= $l['id'] ?>">
          <td><code style="font-size:12px;background:var(--tsj-gray-100);padding:2px 6px;border-radius:4px"><?= htmlspecialchars($l['codigo']) ?></code></td>
          <td style="font-weight:600"><?= htmlspecialchars($l['nombre']) ?></td>
          <td><?= htmlspecialchars($l['autor'] ?? '') ?></td>
          <td><?= htmlspecialchars($l['editorial'] ?? '') ?></td>
          <td><?= htmlspecialchars($l['categoria'] ?? '') ?></td>
          <td style="text-align:center"><?= (int)$l['ejemplares'] ?></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm"
                    onclick="abrirEditarLibro(<?= htmlspecialchars(json_encode($l)) ?>)">
              <span class="material-symbols-rounded">edit</span>
            </button>
            <button class="adm-btn adm-btn--danger adm-btn--sm"
                    onclick="confirmarEliminar('biblioteca','libro_eliminar',<?= $l['id'] ?>,'lib-<?= $l['id'] ?>')">
              <span class="material-symbols-rounded">delete</span>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">library_add</span> <span id="form-lib-titulo">Agregar libro</span></div>
    <form data-proc="biblioteca" data-accion="libro_agregar" id="form-lib">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="libro_agregar" id="lib-accion">
      <input type="hidden" name="id" id="lib-id">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Código / Folio <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="codigo" id="lib-codigo" placeholder="Ej. BIB-011" required></div>
        <div class="adm-field"><label>Título <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="nombre" id="lib-nombre" required></div>
        <div class="adm-field"><label>Autor</label><input type="text" name="autor" id="lib-autor"></div>
        <div class="adm-field"><label>Editorial</label><input type="text" name="editorial" id="lib-editorial"></div>
        <div class="adm-field"><label>Número de ejemplares</label><input type="number" name="ejemplares" id="lib-ejemplares" min="0" value="1"></div>
        <div class="adm-field"><label>Categoría</label>
          <select name="categoria" id="lib-categoria">
            <option>Programación</option><option>Matemáticas</option><option>Ingeniería</option>
            <option>Administración</option><option>Finanzas</option><option>Electrónica</option>
            <option>Redes</option><option>Bases de datos</option><option>Diseño</option><option>Otro</option>
          </select>
        </div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar libro</button>
        <button type="button" class="adm-btn adm-btn--ghost" onclick="resetFormLib()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ Préstamos activos ════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="bib" data-tab="prestamos">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Estudiante</th><th>No. Control</th><th>Libro</th><th>Tipo</th><th>Fecha préstamo</th><th>Fecha devolución</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($prestamos)): ?>
        <tr><td colspan="7" class="adm-table-empty">Sin préstamos activos.</td></tr>
        <?php endif; ?>
        <?php foreach ($prestamos as $p):
          $vence = new DateTime($p['fecha_devolucion'] ?? 'tomorrow');
          $hoy   = new DateTime();
          $diff  = $hoy->diff($vence)->days;
          $status= $hoy > $vence ? 'danger' : ($diff <= 2 ? 'warn' : 'ok');
        ?>
        <tr id="prest-<?= $p['id'] ?>">
          <td style="font-weight:600"><?= htmlspecialchars($p['estudiante_nombre']) ?></td>
          <td><?= htmlspecialchars($p['estudiante_control']) ?></td>
          <td><?= htmlspecialchars($p['libro_nombre']) ?></td>
          <td><span class="adm-status adm-status--info"><?= $p['tipo']==='prestamo'?'Préstamo':'Consulta sala' ?></span></td>
          <td><?= htmlspecialchars($p['fecha_prestamo'] ?? '') ?></td>
          <td><span class="adm-status adm-status--<?= $status ?>"><?= htmlspecialchars($p['fecha_devolucion'] ?? '—') ?></span></td>
          <td class="actions">
            <button class="adm-btn adm-btn--primary adm-btn--sm"
                    onclick="marcarDevuelto(<?= $p['id'] ?>,'prest-<?= $p['id'] ?>','<?= $csrf ?>')">
              <span class="material-symbols-rounded">check_circle</span> Devuelto
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ══ Solicitudes pendientes ══════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="bib" data-tab="solicitudes">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Estudiante</th><th>No. Control</th><th>Libro solicitado</th><th>Tipo</th><th>Fecha</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($solicitudes)): ?>
        <tr><td colspan="6" class="adm-table-empty">Sin solicitudes pendientes.</td></tr>
        <?php endif; ?>
        <?php foreach ($solicitudes as $s): ?>
        <tr id="sol-<?= $s['id'] ?>">
          <td style="font-weight:600"><?= htmlspecialchars($s['estudiante_nombre']) ?></td>
          <td><?= htmlspecialchars($s['estudiante_control']) ?></td>
          <td><?= htmlspecialchars($s['libro_nombre']) ?></td>
          <td><span class="adm-status adm-status--info"><?= $s['tipo']==='prestamo'?'Préstamo':'Consulta sala' ?></span></td>
          <td><?= htmlspecialchars(substr($s['created_at'],0,10)) ?></td>
          <td class="actions">
            <button class="adm-btn adm-btn--primary adm-btn--sm"
                    onclick="procesarSol(<?= $s['id'] ?>,'aprobar','sol-<?= $s['id'] ?>','<?= $csrf ?>')">
              <span class="material-symbols-rounded">check</span> Aprobar
            </button>
            <button class="adm-btn adm-btn--danger adm-btn--sm"
                    onclick="procesarSol(<?= $s['id'] ?>,'rechazar','sol-<?= $s['id'] ?>','<?= $csrf ?>')">
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
function abrirEditarLibro(l){
  document.getElementById('lib-accion').value   = 'libro_editar';
  document.getElementById('lib-id').value       = l.id;
  document.getElementById('lib-codigo').value   = l.codigo||'';
  document.getElementById('lib-nombre').value   = l.nombre||'';
  document.getElementById('lib-autor').value    = l.autor||'';
  document.getElementById('lib-editorial').value= l.editorial||'';
  document.getElementById('lib-ejemplares').value= l.ejemplares||1;
  const sel = document.getElementById('lib-categoria');
  for(let o of sel.options){ if(o.value===l.categoria||o.text===l.categoria){ o.selected=true; break; } }
  document.getElementById('form-lib-titulo').textContent='Editar: '+l.nombre;
  document.getElementById('form-lib').scrollIntoView({behavior:'smooth'});
}
function resetFormLib(){
  document.getElementById('lib-accion').value='libro_agregar';
  document.getElementById('lib-id').value='';
  document.getElementById('form-lib').reset();
  document.getElementById('form-lib-titulo').textContent='Agregar libro';
}
function marcarDevuelto(id, rowId, csrf){
  if(!confirm('¿Marcar este préstamo como devuelto?')) return;
  adminFetch('biblioteca',{csrf,accion:'prestamo_devuelto',id})
    .then(r=>{ if(r.ok) document.getElementById(rowId)?.remove(); });
}
function procesarSol(id, tipo, rowId, csrf){
  const accion = tipo==='aprobar'?'solicitud_aprobar':'solicitud_rechazar';
  const msg    = tipo==='aprobar'?'¿Aprobar esta solicitud?':'¿Rechazar esta solicitud?';
  if(!confirm(msg)) return;
  adminFetch('biblioteca',{csrf,accion,id})
    .then(r=>{ if(r.ok) document.getElementById(rowId)?.remove(); });
}
// Activar tab solicitudes si viene desde dashboard
if(location.hash==='#solicitudes') showTab('bib','solicitudes');
</script>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
