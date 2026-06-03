<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'biblioteca';
$adm_title = 'Biblioteca';
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Gestión de Biblioteca</h1>
    <p class="adm-page-desc">Catálogo de libros, préstamos activos y solicitudes de estudiantes.</p>
  </div>
</div>
<div class="adm-pending">
  <span class="material-symbols-rounded">construction</span>
  Los datos se cargarán desde la base de datos al integrarla. La interfaz está lista.
</div>

<div class="adm-tabs">
  <?php foreach (['catalogo'=>'Catálogo','prestamos'=>'Préstamos activos','solicitudes'=>'Solicitudes pendientes'] as $k=>$l): ?>
    <button class="adm-tab <?= $k==='catalogo'?'active':'' ?>"
            data-tab-group="bib" data-tab="<?= $k ?>" onclick="showTab('bib','<?= $k ?>')">
      <?= $l ?>
    </button>
  <?php endforeach; ?>
</div>

<!-- ══ Catálogo ══════════════════════════════════════════════════ -->
<div class="adm-tab-panel active" data-tab-group="bib" data-tab="catalogo">
  <div class="adm-toolbar">
    <div class="adm-search">
      <span class="material-symbols-rounded">search</span>
      <input type="text" placeholder="Buscar por título, autor o código…">
    </div>
    <div class="adm-toolbar-actions">
      <select style="padding:9px 12px;border:1.5px solid var(--tsj-gray-200);border-radius:var(--tsj-radius);font-family:var(--tsj-font);font-size:13px">
        <option>Todos los estados</option><option>Disponible</option><option>Ocupado</option>
      </select>
      <button class="adm-btn adm-btn--primary pending-db" data-toast="Agregar libro — pendiente de BD">
        <span class="material-symbols-rounded">add</span> Agregar libro
      </button>
    </div>
  </div>
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Código</th><th>Título</th><th>Autor</th><th>Editorial</th><th>Ejemplares</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php
        $libros = [
          ['BIB-001','Fundamentos de Programación','Dennis Ritchie','McGraw-Hill','3','ok'],
          ['BIB-002','Estructura de Datos en C++','Mark Weiss','Pearson','2','ok'],
          ['BIB-003','Bases de Datos Relacionales','Ramez Elmasri','Addison-Wesley','4','warn'],
          ['BIB-004','Redes de Computadoras','Andrew Tanenbaum','Pearson','2','danger'],
          ['BIB-005','Ingeniería de Software','Ian Sommerville','Pearson','5','ok'],
          ['BIB-006','Cálculo','James Stewart','Cengage','6','ok'],
        ];
        $estLabels = ['ok'=>'Disponible','warn'=>'Bajo stock','danger'=>'Sin ejemplares'];
        foreach ($libros as $l): ?>
        <tr>
          <td><code style="font-size:12px;background:var(--tsj-gray-100);padding:2px 6px;border-radius:4px"><?= $l[0] ?></code></td>
          <td style="font-weight:600"><?= htmlspecialchars($l[1]) ?></td>
          <td><?= htmlspecialchars($l[2]) ?></td>
          <td><?= htmlspecialchars($l[3]) ?></td>
          <td style="text-align:center"><?= $l[4] ?></td>
          <td><span class="adm-status adm-status--<?= $l[5] ?>"><?= $estLabels[$l[5]] ?></span></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm pending-db" data-toast="Editar libro — pendiente de BD"><span class="material-symbols-rounded">edit</span></button>
            <button class="adm-btn adm-btn--danger adm-btn--sm pending-db" data-toast="Eliminar libro — pendiente de BD"><span class="material-symbols-rounded">delete</span></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <!-- Formulario agregar -->
  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">library_add</span> Agregar / Editar libro</div>
    <form class="pending-db">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Código / Folio</label><input type="text" placeholder="Ej. BIB-007"></div>
        <div class="adm-field"><label>Título</label><input type="text" placeholder="Título del libro"></div>
        <div class="adm-field"><label>Autor</label><input type="text" placeholder="Nombre del autor"></div>
        <div class="adm-field"><label>Editorial</label><input type="text" placeholder="Editorial"></div>
        <div class="adm-field"><label>Número de ejemplares</label><input type="number" min="0" value="1"></div>
        <div class="adm-field"><label>Categoría</label>
          <select><option>Programación</option><option>Matemáticas</option><option>Ingeniería</option><option>Administración</option><option>Diseño</option><option>Otro</option></select>
        </div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar libro</button>
        <button type="button" class="adm-btn adm-btn--ghost">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ Préstamos activos ════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="bib" data-tab="prestamos">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Estudiante</th><th>No. Control</th><th>Libro</th><th>Fecha préstamo</th><th>Fecha devolución</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php
        $prestamos = [
          ['Ana García',     '21010001','Fundamentos de Programación','15/01/2025','22/01/2025','ok'],
          ['Luis Martínez',  '21010002','Redes de Computadoras',      '10/01/2025','17/01/2025','warn'],
          ['María López',    '21010003','Bases de Datos Relacionales', '20/01/2025','27/01/2025','ok'],
        ];
        foreach ($prestamos as $p): ?>
        <tr>
          <td style="font-weight:600"><?= htmlspecialchars($p[0]) ?></td>
          <td><?= htmlspecialchars($p[1]) ?></td>
          <td><?= htmlspecialchars($p[2]) ?></td>
          <td><?= htmlspecialchars($p[3]) ?></td>
          <td><?= htmlspecialchars($p[4]) ?></td>
          <td><span class="adm-status adm-status--<?= $p[5] ?>"><?= $p[5]==='ok'?'Al corriente':'Vence pronto' ?></span></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm pending-db" data-toast="Marcar devuelto — pendiente de BD"><span class="material-symbols-rounded">check_circle</span> Devuelto</button>
          </td>
        </tr>
        <?php endforeach; ?>
        <tr><td colspan="7" class="adm-table-empty">Los préstamos activos se cargarán desde la base de datos.</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- ══ Solicitudes pendientes ══════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="bib" data-tab="solicitudes">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Estudiante</th><th>No. Control</th><th>Libro solicitado</th><th>Fecha solicitud</th><th>Tipo</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php
        $sols = [
          ['Pedro Ramírez','21020001','Ingeniería de Software','20/01/2025','Préstamo'],
          ['Sofía Cruz',   '21020002','Cálculo',               '21/01/2025','Consulta en sala'],
        ];
        foreach ($sols as $s): ?>
        <tr>
          <td style="font-weight:600"><?= htmlspecialchars($s[0]) ?></td>
          <td><?= htmlspecialchars($s[1]) ?></td>
          <td><?= htmlspecialchars($s[2]) ?></td>
          <td><?= htmlspecialchars($s[3]) ?></td>
          <td><span class="adm-status adm-status--info"><?= htmlspecialchars($s[4]) ?></span></td>
          <td class="actions">
            <button class="adm-btn adm-btn--primary adm-btn--sm pending-db" data-toast="Aprobar solicitud — pendiente de BD"><span class="material-symbols-rounded">check</span> Aprobar</button>
            <button class="adm-btn adm-btn--danger adm-btn--sm pending-db" data-toast="Rechazar solicitud — pendiente de BD"><span class="material-symbols-rounded">close</span> Rechazar</button>
          </td>
        </tr>
        <?php endforeach; ?>
        <tr><td colspan="6" class="adm-table-empty">Las solicitudes se cargarán desde la base de datos.</td></tr>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
