<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'visitantes';
$adm_title = 'Directorio y carreras';

try {
    $db = getPDO(DB_NAME);

    $directorio   = $db->query('SELECT * FROM directorio ORDER BY orden,nombre LIMIT 500')->fetchAll();
    $carreras     = $db->query('SELECT * FROM carreras ORDER BY orden LIMIT 20')->fetchAll();

    // Descripciones públicas de cada carrera (clave desc_<CLAVE> en configuracion).
    // Se adjuntan a cada carrera para que el formulario de edición las precargue
    // y no las borre al guardar.
    $descCarreras = [];
    foreach ($db->query("SELECT clave, valor FROM configuracion WHERE clave LIKE 'desc\\_%'")->fetchAll() as $row) {
        $descCarreras[substr($row['clave'], 5)] = $row['valor'];
    }
    foreach ($carreras as &$_c) {
        $_c['descripcion'] = $descCarreras[$_c['clave']] ?? '';
    }
    unset($_c);

    // Atributos de egreso por carrera (texto concatenado para el formulario de edición)
    $atributosPorCarrera = [];
    try {
        foreach ($db->query('SELECT carrera_id, texto FROM atributos_egreso WHERE activo=1 ORDER BY orden')->fetchAll() as $a) {
            $atributosPorCarrera[$a['carrera_id']][] = $a['texto'];
        }
    } catch (\Throwable $_eA) { /* tabla nueva — ignorar en BD antigua */ }
    foreach ($carreras as &$_c) {
        $_c['atributos_egreso_texto'] = implode("\n", $atributosPorCarrera[$_c['id']] ?? []);
    }
    unset($_c);

    $docentes     = $db->query('SELECT d.* FROM docentes d ORDER BY d.orden,d.nombre LIMIT 500')->fetchAll();
    // Carreras por docente (pivot)
    $docCarreras = [];
    foreach ($db->query('SELECT dc.docente_id, c.id, c.clave, c.nombre FROM docente_carrera dc JOIN carreras c ON c.id=dc.carrera_id ORDER BY c.orden')->fetchAll() as $r) {
        $docCarreras[$r['docente_id']][] = $r;
    }
    $coordinadores= $db->query('SELECT co.*,c.nombre carrera_nombre FROM coordinadores co JOIN carreras c ON co.carrera_id=c.id ORDER BY c.orden LIMIT 50')->fetchAll();
    $secretarias  = $db->query('SELECT * FROM secretarias ORDER BY orden,nombre LIMIT 50')->fetchAll();
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
    <h1 class="adm-page-title">Directorio y carreras</h1>
    <p class="adm-page-desc">Directorio de personal, docentes, coordinadores, carreras, planes de estudio, secretarías y datos de Nuevo Ingreso del portal.</p>
  </div>
</div>

<div class="adm-pending" style="margin-bottom:18px">
  <span class="material-symbols-rounded">info</span>
  <span>
    ¿No encuentras algo? Cada pestaña gestiona una parte:
    <strong>Directorio</strong> (personal y áreas) ·
    <strong>Docentes</strong> ·
    <strong>Coordinadores</strong> ·
    <strong>Oferta académica</strong> (crear/editar carreras: color, ícono, retícula y textos) ·
    <strong>Planes de estudio</strong> (materias de cada carrera) ·
    <strong>Secretarías</strong> ·
    <strong>Nuevo ingreso</strong> (examen y requisitos de admisión).
  </span>
</div>

<div class="adm-tabs">
  <?php
  $tabs = ['directorio'=>'Directorio','docentes'=>'Docentes','coord'=>'Coordinadores',
           'oferta'=>'Oferta académica (carreras)','materias'=>'Planes de estudio (materias)','secretarias'=>'Secretarías','servicios'=>'Nuevo ingreso'];
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
      <thead><tr><th>Foto</th><th>Nombre</th><th>Puesto / Área</th><th>Ubicación</th><th>Correo</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($directorio)): ?>
        <tr><td colspan="7" class="adm-table-empty">Sin personas registradas.</td></tr>
        <?php endif; ?>
        <?php foreach ($directorio as $p): ?>
        <tr id="dir-<?= $p['id'] ?>" <?= !$p['activo'] ? 'style="opacity:.5"' : '' ?>>
          <td class="col-photo">
            <img src="<?= $p['foto'] ? $base_img.htmlspecialchars($p['foto']) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='38' height='38'%3E%3Crect width='38' height='38' fill='%23e5e7eb'/%3E%3C/svg%3E" ?>"
                 style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid var(--tsj-blue-100)" alt="">
          </td>
          <td style="font-weight:600;color:var(--tsj-blue)"><?= htmlspecialchars($p['nombre']) ?></td>
          <td><?= htmlspecialchars($p['puesto'] ?? '—') ?></td>
          <td style="font-size:12.5px;color:var(--tsj-gray-600)">
            <?= htmlspecialchars($p['ubicacion_fisica'] ?? '—') ?>
            <?php if (!empty($p['extension']) && $p['extension'] !== 'S/N'): ?>
              <span style="display:block;color:var(--tsj-gray-400);font-size:11.5px">Ext. <?= htmlspecialchars($p['extension']) ?></span>
            <?php endif; ?>
          </td>
          <td><a href="mailto:<?= htmlspecialchars($p['correo'] ?? '') ?>" style="color:var(--tsj-blue);font-size:12.5px"><?= htmlspecialchars($p['correo'] ?? '—') ?></a></td>
          <td>
            <?php if ($p['activo']): ?>
              <span class="adm-status adm-status--ok">Visible</span>
            <?php else: ?>
              <span class="adm-status adm-status--warn">Oculto</span>
            <?php endif; ?>
          </td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm" title="<?= $p['activo'] ? 'Ocultar' : 'Mostrar' ?>"
                    onclick="toggleActivo('visitantes','directorio_toggle',<?= $p['id'] ?>,this)">
              <span class="material-symbols-rounded"><?= $p['activo'] ? 'visibility_off' : 'visibility' ?></span>
            </button>
            <button class="adm-btn adm-btn--ghost adm-btn--sm"
                    onclick="abrirEditar('dir',<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)">
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
    <form data-proc="visitantes" data-reload id="form-dir" enctype="multipart/form-data">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="directorio_agregar" id="dir-accion">
      <input type="hidden" name="id" id="dir-id">
      <input type="hidden" name="foto" id="dir-foto-actual">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Nombre completo <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="nombre" id="dir-nombre" required></div>
        <div class="adm-field"><label>Puesto / Área</label><input type="text" name="puesto" id="dir-puesto"></div>
        <div class="adm-field"><label>Correo electrónico</label><input type="email" name="correo" id="dir-correo"></div>
        <div class="adm-field"><label>Teléfono</label><input type="text" name="telefono" id="dir-telefono" placeholder="S/N" inputmode="tel" maxlength="25" pattern="[0-9+\-\s()]{7,25}|S\/N" title="Número de teléfono (ej. 376-766-0000) o escribe S/N si no aplica"></div>
        <div class="adm-field"><label>Extensión</label><input type="text" name="extension" id="dir-extension" placeholder="Ej. Ext. 101"></div>
        <div class="adm-field"><label>Ubicación física</label><input type="text" name="ubicacion_fisica" id="dir-ubicacion" placeholder="Ej. Módulo A, Planta Baja"></div>
        <div class="adm-field" style="grid-column:1/-1">
          <label>Foto de perfil</label>
          <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
            <img id="dir-foto-preview"
                 src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='72' height='72'%3E%3Crect width='72' height='72' fill='%23e5e7eb' rx='36'/%3E%3C/svg%3E"
                 style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid var(--tsj-gray-200);flex-shrink:0" alt="">
            <div style="flex:1;min-width:200px">
              <input type="file" name="foto_archivo" id="dir-foto-file"
                     accept="image/jpeg,image/png,image/webp"
                     onchange="previsualizarFoto(this,'dir-foto-preview')"
                     style="display:block;margin-bottom:6px">
              <span class="adm-field-help">JPG, PNG o WEBP · máx. 3 MB. Si no cambias la foto, la actual se conserva.</span>
            </div>
          </div>
        </div>
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
    <button class="adm-career-pill active" data-clave="" onclick="filtrarDocentes('')">Todos</button>
    <?php foreach ($carreras as $c): ?>
    <button class="adm-career-pill" data-clave="<?= htmlspecialchars($c['clave']) ?>"
            onclick="filtrarDocentes('<?= $c['clave'] ?>')">
      <?= htmlspecialchars($c['clave']) ?>
    </button>
    <?php endforeach; ?>
  </div>
  <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <input type="text" id="doc-buscar" placeholder="Buscar docente por nombre o correo…" oninput="aplicarFiltroDoc()"
           style="flex:1;min-width:220px;max-width:420px;padding:9px 12px;border:1.5px solid var(--tsj-gray-200);border-radius:8px;font-size:13px;font-family:inherit">
  </div>
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Nombre</th><th>Foto</th><th>Correo</th><th>Carrera</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody id="doc-tbody">
        <?php if (empty($docentes)): ?>
        <tr><td colspan="6" class="adm-table-empty">Sin docentes registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($docentes as $d):
          $dCarreras   = $docCarreras[$d['id']] ?? [];
          $dClaves     = array_column($dCarreras, 'clave');
          $dCarreraIds = array_column($dCarreras, 'id');
          $dActivo     = (int)($d['activo'] ?? 1);
        ?>
        <tr id="doc-<?= $d['id'] ?>" data-carrera="<?= htmlspecialchars(implode(' ', $dClaves)) ?>"
            data-search="<?= htmlspecialchars(mb_strtolower($d['nombre'] . ' ' . ($d['correo'] ?? ''))) ?>"
            <?= !$dActivo ? 'style="opacity:.5"' : '' ?>>
          <td style="font-weight:600"><?= htmlspecialchars($d['nombre']) ?></td>
          <td class="col-photo">
            <img src="<?= $d['foto'] ? $base_img.htmlspecialchars($d['foto']) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='38' height='38'%3E%3Crect width='38' height='38' fill='%23e5e7eb'/%3E%3C/svg%3E" ?>"
                 style="width:38px;height:38px;border-radius:50%;object-fit:cover" alt="">
          </td>
          <td><?= $d['correo'] ? '<a href="mailto:'.htmlspecialchars($d['correo']).'" style="color:var(--tsj-blue)">'.htmlspecialchars($d['correo']).'</a>' : '<span style="color:var(--tsj-gray-400)">—</span>' ?></td>
          <td>
            <?php if ($dCarreras): foreach ($dCarreras as $dc): ?>
              <span class="adm-status adm-status--info" style="margin:1px 2px;display:inline-block"><?= htmlspecialchars($dc['clave']) ?></span>
            <?php endforeach; else: ?>
              <span style="color:var(--tsj-gray-400)">—</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($dActivo): ?>
              <span class="adm-status adm-status--ok activo-badge">Activo</span>
            <?php else: ?>
              <span class="adm-status adm-status--warn activo-badge">Inactivo</span>
            <?php endif; ?>
          </td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm" title="<?= $dActivo ? 'Desactivar' : 'Activar' ?>"
                    onclick="toggleActivoDoc('visitantes','docente_toggle',<?= $d['id'] ?>,this)">
              <span class="material-symbols-rounded"><?= $dActivo ? 'person_off' : 'how_to_reg' ?></span>
            </button>
            <button class="adm-btn adm-btn--ghost adm-btn--sm"
                    onclick="abrirEditarDoc(<?= htmlspecialchars(json_encode(['id'=>$d['id'],'nombre'=>$d['nombre'],'correo'=>$d['correo'],'foto'=>$d['foto'],'carrera_ids'=>$dCarreraIds]), ENT_QUOTES, 'UTF-8') ?>)">
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
    <form data-proc="visitantes" data-reload id="form-doc" enctype="multipart/form-data">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="docente_agregar" id="doc-accion">
      <input type="hidden" name="id" id="doc-id">
      <input type="hidden" name="foto" id="doc-foto-actual">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Nombre completo <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="nombre" id="doc-nombre" required></div>
        <div class="adm-field"><label>Correo electrónico</label><input type="email" name="correo" id="doc-correo"></div>
        <div class="adm-field" style="grid-column:1/-1">
          <label>Carreras en las que imparte clase <span style="font-weight:400;color:var(--tsj-gray-500)">(selecciona una o varias)</span></label>
          <div id="doc-carreras-checks" style="display:flex;flex-wrap:wrap;gap:10px 20px;margin-top:6px">
            <?php foreach ($carreras as $c): ?>
            <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:13px;font-weight:500;color:var(--tsj-blue-dark)">
              <input type="checkbox" name="carrera_ids[]" value="<?= $c['id'] ?>"
                     style="width:15px;height:15px;accent-color:var(--tsj-blue);cursor:pointer">
              <span class="adm-status adm-status--info" style="font-size:11px;pointer-events:none"><?= htmlspecialchars($c['clave']) ?></span>
              <?= htmlspecialchars($c['nombre']) ?>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="adm-field" style="grid-column:1/-1">
          <label>Foto de perfil</label>
          <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
            <img id="doc-foto-preview"
                 src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='72' height='72'%3E%3Crect width='72' height='72' fill='%23e5e7eb' rx='36'/%3E%3C/svg%3E"
                 style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid var(--tsj-gray-200);flex-shrink:0" alt="">
            <div style="flex:1;min-width:200px">
              <input type="file" name="foto_archivo" id="doc-foto-file"
                     accept="image/jpeg,image/png,image/webp"
                     onchange="previsualizarFoto(this,'doc-foto-preview')"
                     style="display:block;margin-bottom:6px">
              <span class="adm-field-help">JPG, PNG o WEBP · máx. 3 MB. Si no cambias la foto, la actual se conserva.</span>
            </div>
          </div>
        </div>
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
        <?php if (empty($coordinadores)): ?>
        <tr><td colspan="4" class="adm-table-empty">Sin coordinadores registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($coordinadores as $c): ?>
        <tr id="coord-<?= $c['id'] ?>">
          <td style="font-weight:600;color:var(--tsj-blue)"><?= htmlspecialchars($c['carrera_nombre']) ?></td>
          <td><?= htmlspecialchars($c['nombre']) ?></td>
          <td><a href="mailto:<?= htmlspecialchars($c['correo'] ?? '') ?>" style="color:var(--tsj-blue)"><?= htmlspecialchars($c['correo'] ?? '') ?></a></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm"
                    onclick="abrirEditarCoord(<?= htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8') ?>)">
              <span class="material-symbols-rounded">edit</span>
            </button>
            <button class="adm-btn adm-btn--danger adm-btn--sm"
                    onclick="confirmarEliminar('visitantes','coord_eliminar',<?= $c['id'] ?>,'coord-<?= $c['id'] ?>')">
              <span class="material-symbols-rounded">delete</span>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div style="margin-top:14px">
    <button type="button" class="adm-btn adm-btn--primary adm-btn--sm" onclick="abrirAgregarCoord()">
      <span class="material-symbols-rounded">person_add</span> Agregar coordinador
    </button>
  </div>
  <div class="adm-form-card" style="margin-top:20px; display:none" id="form-coord-wrap">
    <div class="adm-form-title"><span class="material-symbols-rounded">manage_accounts</span> <span id="form-coord-titulo">Editar coordinador</span></div>
    <form data-proc="visitantes" data-reload id="form-coord">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="coord_editar" id="coord-accion">
      <input type="hidden" name="id" id="coord-id">
      <div class="adm-form-grid cols-3">
        <div class="adm-field" id="coord-carrera-field" style="display:none"><label>Carrera <span style="color:var(--tsj-pink)">*</span></label>
          <select name="carrera_id" id="coord-carrera">
            <?php foreach ($carreras as $ca): ?>
            <option value="<?= $ca['id'] ?>"><?= htmlspecialchars($ca['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
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
  <div class="adm-career-pills" style="margin-bottom:16px">
    <?php foreach ($carreras as $i => $c): ?>
    <button class="adm-career-pill <?= $i===0?'active':'' ?>"
            onclick="filtrarMaterias('<?= $c['clave'] ?>')">
      <?= htmlspecialchars($c['clave']) ?>
    </button>
    <?php endforeach; ?>
  </div>
  <?php foreach ($carreras as $i => $c): ?>
  <div class="adm-section" data-carrera-sec="<?= $c['clave'] ?>" style="<?= $i===0?'':'display:none;' ?>margin-bottom:20px">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">list_alt</span> Materias — <?= htmlspecialchars($c['nombre']) ?></h3>
    </div>
    <div class="adm-section-body">
      <form class="form-materias-real" data-carrera="<?= $c['id'] ?>">
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
</div>

<!-- ══ TAB: Oferta Académica ═══════════════════════════════════ -->
<!-- La descripción pública de cada carrera (desc_<CLAVE> en configuracion) se edita
     desde el formulario de carrera más abajo (campo "Descripción breve"). -->
<div class="adm-tab-panel" data-tab-group="vis" data-tab="oferta">

  <!-- ── Gestión de carreras ── -->
  <div class="adm-section" style="margin-bottom:24px">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">school</span> Carreras registradas</h3>
      <a href="<?= PLATAFORMA_URL ?>/modulos/visitantes/ofertaAcademica.php" target="_blank" class="adm-btn adm-btn--ghost adm-btn--sm">
        <span class="material-symbols-rounded">open_in_new</span> Ver página pública
      </a>
    </div>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead><tr><th>Color</th><th>Clave</th><th>Nombre de la carrera</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody id="carreras-tbody">
          <?php if (empty($carreras)): ?>
          <tr><td colspan="5" class="adm-table-empty">Sin carreras registradas.</td></tr>
          <?php endif; ?>
          <?php foreach ($carreras as $c):
            $color = htmlspecialchars($c['color'] ?? '#32129a');
          ?>
          <tr id="car-<?= $c['id'] ?>" <?= !$c['activo'] ? 'style="opacity:.5"' : '' ?>>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <span style="display:inline-block;width:24px;height:24px;border-radius:6px;background:<?= $color ?>;flex-shrink:0;border:1px solid rgba(0,0,0,.1)"></span>
                <code style="font-size:11px;color:var(--tsj-gray-600)"><?= $color ?></code>
              </div>
            </td>
            <td><code style="background:<?= $color ?>22;color:<?= $color ?>;padding:2px 8px;border-radius:4px;font-size:12px;font-weight:700"><?= htmlspecialchars($c['clave']) ?></code></td>
            <td style="font-weight:600"><?= htmlspecialchars($c['nombre']) ?></td>
            <td>
              <?php if ($c['activo']): ?>
                <span class="adm-status adm-status--ok">Activa</span>
              <?php else: ?>
                <span class="adm-status adm-status--warn">Inactiva</span>
              <?php endif; ?>
            </td>
            <td class="actions">
              <button class="adm-btn adm-btn--ghost adm-btn--sm" title="<?= $c['activo'] ? 'Desactivar' : 'Activar' ?>"
                      onclick="toggleActivo('visitantes','carrera_toggle',<?= $c['id'] ?>,this)">
                <span class="material-symbols-rounded"><?= $c['activo'] ? 'visibility_off' : 'visibility' ?></span>
              </button>
              <button class="adm-btn adm-btn--ghost adm-btn--sm"
                      onclick="abrirEditarCarrera(<?= htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8') ?>)">
                <span class="material-symbols-rounded">edit</span>
              </button>
              <button class="adm-btn adm-btn--danger adm-btn--sm"
                      onclick="confirmarEliminar('visitantes','carrera_eliminar',<?= $c['id'] ?>,'car-<?= $c['id'] ?>')">
                <span class="material-symbols-rounded">delete</span>
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Formulario agregar/editar carrera -->
    <div class="adm-form-card" style="margin-top:20px">
      <div class="adm-form-title"><span class="material-symbols-rounded">add_circle</span> <span id="form-car-titulo">Agregar carrera</span></div>
      <form id="form-carrera" enctype="multipart/form-data">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <input type="hidden" name="accion" value="carrera_agregar" id="car-accion">
        <input type="hidden" name="id" id="car-id">
        <!-- ── Subsección: Datos básicos ── -->
        <div class="adm-subform-head">
          <span class="material-symbols-rounded">badge</span> Datos básicos
        </div>
        <p class="adm-subform-desc">Identidad de la carrera: clave, nombre, color e ícono. Es lo mínimo para crearla.</p>
        <div class="adm-form-grid cols-3">
          <div class="adm-field">
            <label>Clave <span style="color:var(--tsj-pink)">*</span></label>
            <input type="text" name="clave" id="car-clave" placeholder="Ej. ISC, IM, LG" maxlength="10" required>
            <span class="adm-field-help">Siglas cortas únicas. No se puede cambiar después.</span>
          </div>
          <div class="adm-field" style="grid-column:span 2">
            <label>Nombre completo de la carrera <span style="color:var(--tsj-pink)">*</span></label>
            <input type="text" name="nombre" id="car-nombre" placeholder="Ej. Ingeniería en Sistemas Computacionales" required>
          </div>
          <div class="adm-field">
            <label>Color de la carrera <span style="color:var(--tsj-pink)">*</span></label>
            <div style="display:flex;align-items:center;gap:10px">
              <input type="color" name="color" id="car-color" value="#32129a"
                     style="width:48px;height:38px;border:1.5px solid var(--tsj-gray-200);border-radius:8px;cursor:pointer;padding:2px">
              <input type="text" id="car-color-hex" value="#32129a" maxlength="7"
                     style="flex:1;font-family:monospace;font-size:13px"
                     placeholder="#rrggbb"
                     oninput="sincColorHex(this)">
            </div>
            <span class="adm-field-help">Se aplica en tarjetas, planes de estudio y convenios.</span>
          </div>
          <div class="adm-field" style="grid-column:span 2">
            <label>Descripción breve (aparece en la tarjeta pública)</label>
            <textarea name="descripcion" id="car-descripcion" rows="2" placeholder="Breve descripción de la carrera para la página de Oferta Académica"></textarea>
          </div>
          <div class="adm-field" style="grid-column:1/-1">
            <label>Ícono de la carrera</label>
            <input type="hidden" name="icono" id="car-icono" value="school">
            <div style="display:flex;align-items:center;gap:14px">
              <div id="car-icono-preview" style="width:44px;height:44px;border-radius:12px;background:#32129a;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <span class="material-symbols-rounded" id="car-icono-preview-symbol" style="font-size:24px;color:#fff">school</span>
              </div>
              <div style="flex:1">
                <div style="font-weight:600;font-size:13px;color:#1a0960" id="car-icono-nombre-label">school</div>
                <button type="button" onclick="toggleIconoGrid()"
                        style="margin-top:4px;display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:var(--tsj-blue);background:none;border:none;cursor:pointer;padding:0">
                  <span class="material-symbols-rounded" style="font-size:15px" id="car-icono-toggle-icon">expand_more</span>
                  <span id="car-icono-toggle-label">Cambiar ícono</span>
                </button>
              </div>
            </div>
            <div id="car-icono-grid" style="display:none;margin-top:10px;grid-template-columns:repeat(auto-fill,minmax(68px,1fr));gap:8px;max-height:260px;overflow-y:auto;padding:4px 2px">
              <?php
              $iconosDisponibles = [
                'school','computer','precision_manufacturing','settings_suggest','animation',
                'business_center','restaurant','biotech','architecture','calculate',
                'science','psychology','palette','brush','music_note',
                'health_and_safety','local_hospital','engineering','construction','eco',
                'agriculture','water_drop','electric_bolt','solar_power','factory',
                'inventory_2','local_shipping','flight','directions_car','account_balance',
                'gavel','policy','security','manage_accounts','support_agent',
                'analytics','bar_chart','trending_up','currency_exchange','payments',
                'code','terminal','cloud','storage','lan',
                'wifi','smartphone','camera','videocam','headphones',
                'sports_soccer','fitness_center','hiking','travel_explore','public',
              ];
              foreach ($iconosDisponibles as $ic): ?>
              <button type="button" class="car-icono-btn"
                      data-icono="<?= $ic ?>"
                      onclick="seleccionarIcono('<?= $ic ?>',true)"
                      title="<?= $ic ?>"
                      style="display:flex;flex-direction:column;align-items:center;gap:4px;padding:8px 4px;border:1.5px solid #e8eaf2;border-radius:10px;background:#fff;cursor:pointer;transition:all .15s;font-size:10px;color:#6b7280">
                <span class="material-symbols-rounded" style="font-size:24px;color:#4a5170"><?= $ic ?></span>
                <span style="font-size:9.5px;line-height:1.2;text-align:center;word-break:break-all"><?= $ic ?></span>
              </button>
              <?php endforeach; ?>
            </div>
          </div>
        </div><!-- /Datos básicos -->

        <!-- ── Subsección: Textos académicos ── -->
        <div class="adm-subform-head adm-subform-head--sep">
          <span class="material-symbols-rounded">menu_book</span> Textos académicos
        </div>
        <p class="adm-subform-desc">Se muestran en la página pública de la carrera (Oferta académica). Puedes dejarlos en blanco y completarlos después.</p>
        <div class="adm-form-grid cols-1">
          <div class="adm-field">
            <label>Objetivo General</label>
            <textarea name="objetivo_general" id="car-objetivo" rows="4" placeholder="Describe el objetivo general de la carrera..."></textarea>
          </div>
          <div class="adm-field">
            <label>Perfil Profesional</label>
            <textarea name="perfil_profesional" id="car-perfil" rows="4" placeholder="Describe el perfil del profesional egresado..."></textarea>
          </div>
          <div class="adm-field">
            <label>Objetivos Educacionales</label>
            <textarea name="objetivos_educacionales" id="car-objetivos-edu" rows="4" placeholder="Lista los objetivos educacionales..."></textarea>
          </div>
          <div class="adm-field">
            <label>Atributos de Egreso</label>
            <span class="adm-field-help">Un atributo por línea. Se muestran como lista numerada en la página pública.</span>
            <textarea name="atributos_egreso" id="car-atributos" rows="5" placeholder="Capacidad de análisis y diseño de sistemas..."></textarea>
          </div>
        </div>

        <!-- ── Subsección: Archivos y multimedia ── -->
        <div class="adm-subform-head adm-subform-head--sep">
          <span class="material-symbols-rounded">attach_file</span> Archivos y multimedia
        </div>
        <p class="adm-subform-desc">Retícula en PDF e imagen de portada (esta última aparece en las tarjetas de Convenios).</p>
        <div class="adm-form-grid cols-1">
          <div class="adm-field" style="grid-column:1/-1">
            <label>Retícula (mapa curricular)</label>
            <div style="display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap">
              <div style="flex:1;min-width:200px">
                <input type="file" name="reticula_archivo" id="car-reticula-file"
                       accept=".pdf,application/pdf">
                <span class="adm-field-help">PDF — máx. 10 MB. Sube un archivo nuevo para reemplazar el actual.</span>
              </div>
              <div id="car-reticula-actual-wrap" style="display:none;flex-shrink:0;align-items:center;gap:8px">
                <span class="material-symbols-rounded" style="font-size:22px;color:#6b7280">picture_as_pdf</span>
                <a id="car-reticula-actual-link" href="#" target="_blank" rel="noopener"
                   style="font-size:12.5px;color:var(--tsj-blue);font-weight:600;text-decoration:none">
                  Ver retícula actual
                </a>
              </div>
            </div>
          </div>
          <div class="adm-field" style="grid-column:1/-1">
            <label>Imagen de portada <span style="color:var(--tsj-gray-500);font-weight:400;font-size:12px">(aparece en la card de Convenios)</span></label>
            <div style="display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap">
              <div style="flex:1;min-width:200px">
                <input type="file" name="imagen_portada" id="car-imagen-file"
                       accept="image/*,.webp"
                       onchange="previsualizarPortada(this)">
                <span class="adm-field-help">JPG, PNG, WEBP — máx. 5 MB. Sube una imagen nueva para reemplazar la actual.</span>
              </div>
              <div id="car-imagen-preview-wrap" style="display:none;flex-shrink:0">
                <img id="car-imagen-preview" src="" alt="Vista previa"
                     style="width:140px;height:80px;object-fit:cover;border-radius:8px;border:1.5px solid var(--tsj-gray-200)">
                <div style="font-size:11px;color:var(--tsj-gray-400);text-align:center;margin-top:4px">Vista previa</div>
              </div>
            </div>
            <div style="margin-top:8px">
              <label style="font-size:12px;color:var(--tsj-gray-500)">O pegar URL externa:</label>
              <input type="url" name="imagen_url" id="car-imagen-url"
                     placeholder="https://… (si no subes archivo)"
                     style="margin-top:4px">
            </div>
          </div>
        </div>
        <div class="adm-form-actions">
          <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar carrera</button>
          <button type="button" class="adm-btn adm-btn--ghost" onclick="resetFormCarrera()">Cancelar</button>
        </div>
      </form>
    </div>
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
                    onclick="abrirEditarSec(<?= htmlspecialchars(json_encode($s), ENT_QUOTES, 'UTF-8') ?>)">
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
    <form data-proc="visitantes" data-reload id="form-sec">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="secretaria_agregar" id="sec-accion">
      <input type="hidden" name="id" id="sec-id">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Nombre completo <span style="color:var(--tsj-pink)">*</span></label><input type="text" name="nombre" id="sec-nombre" required></div>
        <div class="adm-field"><label>Rol</label><input type="text" name="rol" id="sec-rol"></div>
        <div class="adm-field"><label>Correo</label><input type="email" name="correo" id="sec-correo"></div>
        <div class="adm-field"><label>Teléfono</label><input type="tel" name="telefono" id="sec-telefono" inputmode="tel" maxlength="25" pattern="[0-9+\-\s()]{7,25}" title="Solo números, +, -, espacios y paréntesis (entre 7 y 25 caracteres)"></div>
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
// ── Filtro teléfono directorio: solo dígitos/+/-/espacios/() o "S/N" ──
(function () {
  var campo = document.getElementById('dir-telefono');
  if (!campo) return;
  campo.addEventListener('input', function () {
    var v = this.value;
    var pos = this.selectionStart;
    // Permitir mientras se está escribiendo "S/N" (parcial)
    if (/^[Ss](\/([Nn])?)?$/.test(v)) return;
    // Permitir número de teléfono (solo caracteres válidos)
    var limpio = v.replace(/[^0-9+\-\s()]/g, '');
    if (limpio !== v) {
      this.value = limpio;
      try { this.setSelectionRange(pos - 1, pos - 1); } catch (e) {}
    }
  });
})();

// ── Preview imagen de portada ────────────────────────────────────
function previsualizarPortada(input) {
  var wrap = document.getElementById('car-imagen-preview-wrap');
  var img  = document.getElementById('car-imagen-preview');
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      img.src = e.target.result;
      wrap.style.display = '';
    };
    reader.readAsDataURL(input.files[0]);
  } else {
    wrap.style.display = 'none';
    img.src = '';
  }
}

// ── Sincronización color picker ↔ input hex ─────────────────────
function sincColorHex(hexInput) {
  var val = hexInput.value;
  if (/^#[0-9a-fA-F]{6}$/.test(val)) {
    document.getElementById('car-color').value = val;
  }
}
document.addEventListener('DOMContentLoaded', function () {
  var picker = document.getElementById('car-color');
  if (picker) {
    picker.addEventListener('input', function () {
      document.getElementById('car-color-hex').value = picker.value;
    });
  }
});

// ── Selector de ícono de carrera ────────────────────────────────
function toggleIconoGrid() {
  var grid  = document.getElementById('car-icono-grid');
  var icon  = document.getElementById('car-icono-toggle-icon');
  var label = document.getElementById('car-icono-toggle-label');
  var open  = grid.style.display === 'grid';
  grid.style.display  = open ? 'none' : 'grid';
  icon.textContent    = open ? 'expand_more' : 'expand_less';
  label.textContent   = open ? 'Cambiar ícono' : 'Cerrar';
}
function seleccionarIcono(icono, cerrarGrid) {
  document.getElementById('car-icono').value = icono;
  document.getElementById('car-icono-preview-symbol').textContent = icono;
  document.getElementById('car-icono-nombre-label').textContent   = icono;
  var color = document.getElementById('car-color').value || '#32129a';
  document.getElementById('car-icono-preview').style.background   = color;
  document.querySelectorAll('.car-icono-btn').forEach(function(btn) {
    var sel = btn.dataset.icono === icono;
    btn.style.borderColor  = sel ? color : '#e8eaf2';
    btn.style.background   = sel ? color + '18' : '#fff';
    btn.querySelector('.material-symbols-rounded').style.color = sel ? color : '#4a5170';
  });
  if (cerrarGrid) {
    var grid = document.getElementById('car-icono-grid');
    grid.style.display = 'none';
    document.getElementById('car-icono-toggle-icon').textContent  = 'expand_more';
    document.getElementById('car-icono-toggle-label').textContent = 'Cambiar ícono';
  }
}
// Sincronizar color del preview al cambiar el color de la carrera
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('car-color').addEventListener('input', function() {
    var ic = document.getElementById('car-icono').value;
    seleccionarIcono(ic || 'school');
  });
  seleccionarIcono('school');
});

// ── Carreras ────────────────────────────────────────────────────
function abrirEditarCarrera(c) {
  document.getElementById('car-accion').value       = 'carrera_editar';
  document.getElementById('car-id').value           = c.id;
  document.getElementById('car-clave').value        = c.clave || '';
  document.getElementById('car-clave').readOnly     = true;
  document.getElementById('car-nombre').value       = c.nombre || '';
  var color = c.color || '#32129a';
  document.getElementById('car-color').value        = color;
  document.getElementById('car-color-hex').value    = color;
  document.getElementById('car-descripcion').value     = c.descripcion || '';
  document.getElementById('car-objetivo').value         = c.objetivo_general || '';
  document.getElementById('car-perfil').value           = c.perfil_profesional || '';
  document.getElementById('car-objetivos-edu').value    = c.objetivos_educacionales || '';
  document.getElementById('car-atributos').value        = c.atributos_egreso_texto || '';
  document.getElementById('car-imagen-url').value       = c.imagen_url || '';
  seleccionarIcono(c.icono || 'school');
  // Retícula actual
  var retWrap = document.getElementById('car-reticula-actual-wrap');
  var retLink = document.getElementById('car-reticula-actual-link');
  if (c.reticula_url) {
    retLink.href = c.reticula_url;
    retWrap.style.display = 'flex';
  } else {
    retWrap.style.display = 'none';
  }
  // Mostrar imagen actual como preview
  var wrap = document.getElementById('car-imagen-preview-wrap');
  var prev = document.getElementById('car-imagen-preview');
  if (c.imagen_url) { prev.src = c.imagen_url; wrap.style.display = ''; }
  else { wrap.style.display = 'none'; prev.src = ''; }
  document.getElementById('form-car-titulo').textContent = 'Editar: ' + c.nombre;
  document.getElementById('form-carrera').scrollIntoView({ behavior: 'smooth' });
}
function resetFormCarrera() {
  document.getElementById('car-accion').value       = 'carrera_agregar';
  document.getElementById('car-id').value           = '';
  document.getElementById('car-clave').readOnly     = false;
  document.getElementById('form-carrera').reset();
  document.getElementById('car-color').value        = '#32129a';
  document.getElementById('car-color-hex').value    = '#32129a';
  document.getElementById('car-objetivo').value     = '';
  document.getElementById('car-perfil').value       = '';
  document.getElementById('car-objetivos-edu').value = '';
  document.getElementById('car-atributos').value    = '';
  seleccionarIcono('school');
  document.getElementById('car-reticula-actual-wrap').style.display = 'none';
  document.getElementById('car-imagen-url').value   = '';
  document.getElementById('car-imagen-preview-wrap').style.display = 'none';
  document.getElementById('car-imagen-preview').src = '';
  document.getElementById('form-car-titulo').textContent = 'Agregar carrera';
}

// ── Materias: handler propio para array materias[] (evita aplanamiento) ──
(function () {
  var base = document.querySelector('meta[name="plataforma-url"]')?.content || '/plataforma';
  document.querySelectorAll('.form-materias-real').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var btn = form.querySelector('[type="submit"]');
      if (btn) btn.disabled = true;
      fetch(base + '/admin/procesos/visitantes.php', { method: 'POST', body: new FormData(form) })
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

// ── Carrera: handler propio (tiene file upload) ──────────────────
(function () {
  var base = document.querySelector('meta[name="plataforma-url"]')?.content || '/plataforma';
  var form = document.getElementById('form-carrera');
  if (!form) return;
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var btn = form.querySelector('[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Guardando…'; }
    fetch(base + '/admin/procesos/visitantes.php', { method: 'POST', body: new FormData(form) })
      .then(function (r) { return r.json(); })
      .then(function (json) {
        showToast(json.msg, json.ok ? 'ok' : 'error');
        if (json.ok) setTimeout(function () {
          // Guardar tab activo antes de recargar
          var activeTab = document.querySelector('.adm-tab[data-tab-group="vis"].active');
          if (activeTab) try { sessionStorage.setItem('adm_tab_vis', activeTab.dataset.tab); } catch(e) {}
          location.reload();
        }, 900);
      })
      .catch(function (err) { showToast('Error: ' + err.message, 'error'); })
      .finally(function () {
        if (btn) { btn.disabled = false; btn.textContent = 'Guardar carrera'; }
      });
  });
})();

// ── Toggle visibilidad ──────────────────────────────────────────
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
        btn.title         = activo ? 'Ocultar' : 'Mostrar';
        if (badge) {
          badge.textContent = activo ? 'Visible' : 'Oculto';
          badge.className   = 'adm-status ' + (activo ? 'adm-status--ok' : 'adm-status--warn');
        }
      }
    });
}

// ── Filtros de carrera ──────────────────────────────────────────
var docClaveActiva = '';
function filtrarDocentes(clave){
  docClaveActiva = clave;
  document.querySelectorAll('#doc-pills .adm-career-pill').forEach(b=>{
    b.classList.toggle('active', (b.dataset.clave ?? b.textContent.trim()) === clave);
  });
  aplicarFiltroDoc();
}
function aplicarFiltroDoc(){
  const q = document.getElementById('doc-buscar').value.trim().toLowerCase();
  document.querySelectorAll('#doc-tbody tr[data-carrera]').forEach(tr=>{
    const claves = (tr.dataset.carrera||'').split(' ').filter(Boolean);
    const okC = !docClaveActiva || claves.includes(docClaveActiva);
    const okQ = !q || (tr.dataset.search||'').includes(q);
    tr.style.display = (okC && okQ) ? '' : 'none';
  });
}
function toggleActivoDoc(modulo, accion, id, btn) {
  var csrfEl = document.querySelector('input[name="_csrf"]');
  var csrf   = csrfEl ? csrfEl.value : '';
  adminFetch(modulo, { _csrf: csrf, accion: accion, id: id })
    .then(function (json) {
      if (json.ok) {
        var row   = btn.closest('tr');
        var icon  = btn.querySelector('.material-symbols-rounded');
        var badge = row.querySelector('.activo-badge');
        var activo = json.activo;
        row.style.opacity = activo ? '' : '0.5';
        icon.textContent  = activo ? 'person_off' : 'how_to_reg';
        btn.title         = activo ? 'Desactivar' : 'Activar';
        if (badge) {
          badge.textContent = activo ? 'Activo' : 'Inactivo';
          badge.className   = 'adm-status activo-badge ' + (activo ? 'adm-status--ok' : 'adm-status--warn');
        }
      }
    });
}
function filtrarMaterias(clave){
  document.querySelectorAll('[data-carrera-sec]').forEach(s=>{
    s.style.display = s.getAttribute('data-carrera-sec')===clave ? '' : 'none';
  });
  document.querySelectorAll('[data-tab="materias"] .adm-career-pill').forEach(b=>{
    b.classList.toggle('active', b.textContent.trim()===clave);
  });
}
document.addEventListener('DOMContentLoaded', function() {
  filtrarDocentes('');
});

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

// ── Vista previa de foto ────────────────────────────────────────
function previsualizarFoto(input, previewId) {
  if (!input.files || !input.files[0]) return;
  var reader = new FileReader();
  reader.onload = function(e) {
    document.getElementById(previewId).src = e.target.result;
  };
  reader.readAsDataURL(input.files[0]);
}

var _baseImg = (document.querySelector('meta[name="plataforma-url"]')?.content || '/plataforma')
               + '/modulos/visitantes/imagenes/';
var _placeholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='72' height='72'%3E%3Crect width='72' height='72' fill='%23e5e7eb' rx='36'/%3E%3C/svg%3E";

// ── Directorio ─────────────────────────────────────────────────
function abrirEditar(tipo, p){
  document.getElementById('dir-accion').value        = 'directorio_editar';
  document.getElementById('dir-id').value            = p.id;
  document.getElementById('dir-nombre').value        = p.nombre||'';
  document.getElementById('dir-puesto').value        = p.puesto||'';
  document.getElementById('dir-correo').value        = p.correo||'';
  document.getElementById('dir-telefono').value      = p.telefono||'';
  document.getElementById('dir-extension').value     = p.extension||'';
  document.getElementById('dir-ubicacion').value     = p.ubicacion_fisica||'';
  document.getElementById('dir-foto-actual').value   = p.foto||'';
  var preview = document.getElementById('dir-foto-preview');
  preview.src = p.foto ? _baseImg + p.foto : _placeholder;
  preview.onerror = function(){ this.src = _placeholder; };
  document.getElementById('form-dir-titulo').textContent = 'Editar: '+p.nombre;
  document.getElementById('form-dir-wrap').scrollIntoView({behavior:'smooth'});
}
function resetFormDir(){
  document.getElementById('dir-accion').value = 'directorio_agregar';
  document.getElementById('dir-id').value     = '';
  document.getElementById('dir-foto-actual').value = '';
  document.getElementById('form-dir').reset();
  document.getElementById('dir-foto-preview').src = _placeholder;
  document.getElementById('form-dir-titulo').textContent = 'Agregar persona al directorio';
}

// ── Docentes ────────────────────────────────────────────────────
function abrirEditarDoc(d){
  document.getElementById('doc-accion').value      = 'docente_editar';
  document.getElementById('doc-id').value          = d.id;
  document.getElementById('doc-nombre').value      = d.nombre||'';
  document.getElementById('doc-correo').value      = d.correo||'';
  document.getElementById('doc-foto-actual').value = d.foto||'';
  var preview = document.getElementById('doc-foto-preview');
  preview.src = d.foto ? _baseImg + d.foto : _placeholder;
  preview.onerror = function(){ this.src = _placeholder; };
  // Marcar checkboxes según carreras actuales del docente
  var ids = d.carrera_ids || [];
  document.querySelectorAll('#doc-carreras-checks input[type="checkbox"]').forEach(function(cb){
    cb.checked = ids.map(String).includes(cb.value);
  });
  document.getElementById('form-doc-titulo').textContent='Editar: '+d.nombre;
  document.getElementById('form-doc').scrollIntoView({behavior:'smooth'});
}
function resetFormDoc(){
  document.getElementById('doc-accion').value      = 'docente_agregar';
  document.getElementById('doc-id').value          = '';
  document.getElementById('doc-foto-actual').value = '';
  document.getElementById('form-doc').reset();
  document.getElementById('doc-foto-preview').src  = _placeholder;
  document.querySelectorAll('#doc-carreras-checks input[type="checkbox"]').forEach(function(cb){ cb.checked = false; });
  document.getElementById('form-doc-titulo').textContent = 'Agregar docente';
}

// ── Coordinadores ───────────────────────────────────────────────
function abrirEditarCoord(c){
  document.getElementById('coord-accion').value = 'coord_editar';
  document.getElementById('coord-id').value    = c.id;
  document.getElementById('coord-nombre').value= c.nombre||'';
  document.getElementById('coord-correo').value= c.correo||'';
  document.getElementById('coord-carrera-field').style.display='none';
  document.getElementById('form-coord-titulo').textContent='Editar coordinador';
  document.getElementById('form-coord-wrap').style.display='';
  document.getElementById('form-coord-wrap').scrollIntoView({behavior:'smooth'});
}
function abrirAgregarCoord(){
  document.getElementById('coord-accion').value = 'coord_agregar';
  document.getElementById('coord-id').value    = '';
  document.getElementById('coord-nombre').value= '';
  document.getElementById('coord-correo').value= '';
  document.getElementById('coord-carrera-field').style.display='';
  document.getElementById('form-coord-titulo').textContent='Agregar coordinador';
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
