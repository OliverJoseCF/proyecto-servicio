<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'biblioteca';
$adm_title = 'Biblioteca';

// Búsqueda y paginación del catálogo (server-side)
$q          = mb_substr(trim($_GET['q'] ?? ''), 0, 100);
$pag        = max(1, (int)($_GET['pag'] ?? 1));
$porPagina  = 50;
$totalLibros = 0;

try {
    $db = getPDO(DB_NAME);

    if ($q !== '') {
        $like = '%' . $q . '%';
        $stmt = $db->prepare('SELECT COUNT(*) FROM libros WHERE activo=1 AND (codigo LIKE ? OR nombre LIKE ? OR autor LIKE ?)');
        $stmt->execute([$like, $like, $like]);
        $totalLibros = (int)$stmt->fetchColumn();
    } else {
        $totalLibros = (int)$db->query('SELECT COUNT(*) FROM libros WHERE activo=1')->fetchColumn();
    }
    $totalPags = max(1, (int)ceil($totalLibros / $porPagina));
    $pag       = min($pag, $totalPags);
    $offset    = ($pag - 1) * $porPagina;

    if ($q !== '') {
        $stmt = $db->prepare("SELECT id, codigo, nombre, autor, editorial, categoria, ejemplares FROM libros WHERE activo=1 AND (codigo LIKE ? OR nombre LIKE ? OR autor LIKE ?) ORDER BY codigo LIMIT $porPagina OFFSET $offset");
        $stmt->execute([$like, $like, $like]);
        $libros = $stmt->fetchAll();
    } else {
        $libros = $db->query("SELECT id, codigo, nombre, autor, editorial, categoria, ejemplares FROM libros WHERE activo=1 ORDER BY codigo LIMIT $porPagina OFFSET $offset")->fetchAll();
    }

    $prestamos   = $db->query('SELECT p.*,l.nombre libro_nombre FROM prestamos p JOIN libros l ON p.libro_id=l.id WHERE p.devuelto=0 ORDER BY p.fecha_devolucion LIMIT 500')->fetchAll();
    $solicitudes = $db->query('SELECT s.*,l.nombre libro_nombre FROM solicitudes_biblioteca s JOIN libros l ON s.libro_id=l.id WHERE s.estado="pendiente" ORDER BY s.created_at DESC LIMIT 500')->fetchAll();
    $db_ok = true;
} catch (\Throwable $e) {
    $libros = $prestamos = $solicitudes = [];
    $totalPags = 1;
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

<div class="adm-pending" style="margin-bottom:18px">
  <span class="material-symbols-rounded">info</span>
  <span>
    Usa las pestañas según lo que necesites:
    <strong>Catálogo</strong> (agregar y editar libros) ·
    <strong>Préstamos activos</strong> (libros prestados sin devolver) ·
    <strong>Solicitudes pendientes</strong> (peticiones de estudiantes por aprobar). Las pestañas con número tienen asuntos esperando.
  </span>
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

  <form method="GET" style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar por código, título o autor…"
           style="flex:1;min-width:220px;max-width:420px;padding:9px 12px;border:1.5px solid var(--tsj-gray-200);border-radius:8px;font-size:13px;font-family:inherit">
    <button type="submit" class="adm-btn adm-btn--primary adm-btn--sm"><span class="material-symbols-rounded">search</span> Buscar</button>
    <?php if ($q !== ''): ?>
      <a href="biblioteca.php" class="adm-btn adm-btn--ghost adm-btn--sm">Limpiar</a>
    <?php endif; ?>
    <span style="margin-left:auto;font-size:12px;color:var(--tsj-gray-500)"><?= $totalLibros ?> libro<?= $totalLibros === 1 ? '' : 's' ?><?= $q !== '' ? ' encontrados' : ' en catálogo' ?></span>
  </form>

  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Código</th><th>Título</th><th>Autor</th><th>Editorial</th><th>Categoría</th><th>Ejemplares</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($libros)): ?>
        <tr><td colspan="7" class="adm-table-empty"><?= $q !== '' ? 'Sin resultados para la búsqueda.' : 'Sin libros en catálogo.' ?></td></tr>
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
                    onclick="abrirEditarLibro(<?= htmlspecialchars(json_encode($l), ENT_QUOTES, 'UTF-8') ?>)">
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

  <?php if ($totalPags > 1): ?>
  <div style="display:flex;gap:8px;align-items:center;justify-content:center;margin-top:14px">
    <?php $qs = $q !== '' ? '&q=' . urlencode($q) : ''; ?>
    <?php if ($pag > 1): ?>
      <a href="?pag=<?= $pag - 1 ?><?= $qs ?>" class="adm-btn adm-btn--ghost adm-btn--sm"><span class="material-symbols-rounded">chevron_left</span> Anterior</a>
    <?php endif; ?>
    <span style="font-size:13px;color:var(--tsj-gray-600)">Página <?= $pag ?> de <?= $totalPags ?></span>
    <?php if ($pag < $totalPags): ?>
      <a href="?pag=<?= $pag + 1 ?><?= $qs ?>" class="adm-btn adm-btn--ghost adm-btn--sm">Siguiente <span class="material-symbols-rounded">chevron_right</span></a>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">library_add</span> <span id="form-lib-titulo">Agregar libro</span></div>
    <form data-proc="biblioteca" data-reload id="form-lib">
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

  <div style="display:flex;gap:12px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <input type="text" id="prest-filtro" placeholder="Filtrar por estudiante, control o libro…" oninput="filtrarPrestamos()"
           style="flex:1;min-width:220px;max-width:420px;padding:9px 12px;border:1.5px solid var(--tsj-gray-200);border-radius:8px;font-size:13px;font-family:inherit">
    <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--tsj-gray-600);cursor:pointer">
      <input type="checkbox" id="prest-atrasados" onchange="filtrarPrestamos()"> Solo atrasados
    </label>
    <a href="procesos/export.php?tipo=prestamos" class="adm-btn adm-btn--ghost adm-btn--sm" style="margin-left:auto">
      <span class="material-symbols-rounded">download</span> Exportar CSV
    </a>
  </div>

  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Estudiante</th><th>No. Control</th><th>Libro</th><th>Tipo</th><th>Fecha préstamo</th><th>Fecha devolución</th><th>Acciones</th></tr></thead>
      <tbody id="prest-tbody">
        <?php if (empty($prestamos)): ?>
        <tr><td colspan="7" class="adm-table-empty">Sin préstamos activos.</td></tr>
        <?php endif; ?>
        <?php foreach ($prestamos as $p):
          $vence = new DateTime($p['fecha_devolucion'] ?? 'tomorrow');
          $hoy   = new DateTime();
          $diff  = $hoy->diff($vence)->days;
          $status= $hoy > $vence ? 'danger' : ($diff <= 2 ? 'warn' : 'ok');
        ?>
        <tr id="prest-<?= $p['id'] ?>"
            data-search="<?= htmlspecialchars(mb_strtolower($p['estudiante_nombre'] . ' ' . $p['estudiante_control'] . ' ' . $p['libro_nombre'])) ?>"
            data-atrasado="<?= $status === 'danger' ? '1' : '0' ?>">
          <td style="font-weight:600"><?= htmlspecialchars($p['estudiante_nombre']) ?></td>
          <td><?= htmlspecialchars($p['estudiante_control']) ?></td>
          <td><?= htmlspecialchars($p['libro_nombre']) ?></td>
          <td><span class="adm-status adm-status--info"><?= $p['tipo']==='prestamo'?'Préstamo':'Consulta sala' ?></span></td>
          <td><?= $p['fecha_prestamo'] ? date('d/m/Y', strtotime($p['fecha_prestamo'])) : '—' ?></td>
          <td><span class="adm-status adm-status--<?= $status ?>"><?= $p['fecha_devolucion'] ? date('d/m/Y', strtotime($p['fecha_devolucion'])) : '—' ?></span></td>
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

  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">assignment_add</span> Registrar préstamo en mostrador</div>
    <p style="font-size:12px;color:var(--tsj-gray-500);margin:-6px 0 14px">Para préstamos directos sin pasar por una solicitud del kiosko. El libro se identifica por su código/folio.</p>
    <form data-proc="biblioteca" data-reload>
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="prestamo_registrar">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Código del libro <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="codigo" placeholder="Ej. BIB-011" required></div>
        <div class="adm-field"><label>Nombre del estudiante <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="estudiante_nombre" required></div>
        <div class="adm-field"><label>No. de control</label><input type="text" name="estudiante_control" maxlength="15"></div>
        <div class="adm-field"><label>Carrera</label><input type="text" name="carrera"></div>
        <div class="adm-field"><label>Tipo</label>
          <select name="tipo"><option value="prestamo">Préstamo</option><option value="consulta_sala">Consulta en sala</option></select>
        </div>
        <div class="adm-field"><label>Días de préstamo</label><input type="number" name="dias" min="1" max="60" value="7"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Registrar préstamo</button>
      </div>
    </form>
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
          <td><?= $s['estudiante_control'] !== '' ? htmlspecialchars($s['estudiante_control']) : '<span style="color:var(--tsj-gray-400);font-size:12px">—</span>' ?></td>
          <td><?= htmlspecialchars($s['libro_nombre']) ?></td>
          <td><span class="adm-status adm-status--info"><?= $s['tipo']==='prestamo'?'Préstamo':'Consulta sala' ?></span></td>
          <td><?= $s['created_at'] ? date('d/m/Y', strtotime($s['created_at'])) : '—' ?></td>
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
  adminFetch('biblioteca',{_csrf: csrf, accion:'prestamo_devuelto', id})
    .then(r=>{ if(r.ok) document.getElementById(rowId)?.remove(); });
}
function procesarSol(id, tipo, rowId, csrf){
  const accion = tipo==='aprobar'?'solicitud_aprobar':'solicitud_rechazar';
  const msg    = tipo==='aprobar'?'¿Aprobar esta solicitud?':'¿Rechazar esta solicitud?';
  if(!confirm(msg)) return;
  adminFetch('biblioteca',{_csrf: csrf, accion, id})
    .then(r=>{ if(r.ok) document.getElementById(rowId)?.remove(); });
}
function filtrarPrestamos(){
  const q    = document.getElementById('prest-filtro').value.trim().toLowerCase();
  const solo = document.getElementById('prest-atrasados').checked;
  document.querySelectorAll('#prest-tbody tr[data-search]').forEach(tr=>{
    const okQ = !q || tr.dataset.search.includes(q);
    const okA = !solo || tr.dataset.atrasado === '1';
    tr.style.display = (okQ && okA) ? '' : 'none';
  });
}
// Activar tab si viene desde dashboard con hash (setTimeout difiere la ejecución
// hasta que _layout_end.php haya definido showTab)
setTimeout(function() {
  if (['#solicitudes','#prestamos'].includes(location.hash))
    showTab('bib', location.hash.slice(1));
}, 0);
</script>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
