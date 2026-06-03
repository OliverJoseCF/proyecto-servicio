<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'horarios';
$adm_title = 'Buscar Maestro';
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Gestión de Maestros y Horarios</h1>
    <p class="adm-page-desc">Agrega, edita o elimina maestros, sus correos y archivos de horario.</p>
  </div>
</div>
<div class="adm-pending">
  <span class="material-symbols-rounded">construction</span>
  Los maestros reales se cargarán desde la base de datos. La interfaz está lista.
</div>

<div class="adm-tabs">
  <?php foreach (['maestros'=>'Maestros','horarios_archivos'=>'Archivos de horario','carreras'=>'Carreras'] as $k=>$l): ?>
    <button class="adm-tab <?= $k==='maestros'?'active':'' ?>"
            data-tab-group="hor" data-tab="<?= $k ?>" onclick="showTab('hor','<?= $k ?>')">
      <?= $l ?>
    </button>
  <?php endforeach; ?>
</div>

<!-- ══ Maestros ══════════════════════════════════════════════ -->
<div class="adm-tab-panel active" data-tab-group="hor" data-tab="maestros">
  <div class="adm-toolbar">
    <div class="adm-search">
      <span class="material-symbols-rounded">search</span>
      <input type="text" placeholder="Buscar por nombre o carrera…">
    </div>
    <div class="adm-toolbar-actions">
      <select style="padding:9px 12px;border:1.5px solid var(--tsj-gray-200);border-radius:var(--tsj-radius);font-family:var(--tsj-font);font-size:13px">
        <option>Todas las carreras</option>
        <option>Sistemas</option><option>Industrial</option><option>Mecatrónica</option>
        <option>Animación</option><option>Gestión</option><option>Gastronomía</option>
      </select>
      <button class="adm-btn adm-btn--primary pending-db" data-toast="Agregar maestro — pendiente de BD">
        <span class="material-symbols-rounded">person_add</span> Agregar maestro
      </button>
    </div>
  </div>
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Foto</th><th>Nombre</th><th>Apellido</th><th>Carrera</th><th>Semestre</th><th>Correo</th><th>Horario</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php
        $maestros = [
          ['miguel.png','Miguel Ángel','Delgado López',   'Sistemas','3er','miguel.delgado@chapala.tecmm.edu.mx',    'horario_delgado.pdf'],
          [null,        'Alberto',     'Chavolla',         'Industrial','4to','alberto.chavolla@chapala.tecmm.edu.mx', 'horario_chavolla.pdf'],
          ['julio.png', 'Julio César', 'Chávez Novoa',    'Sistemas','5to','julio.chavez@chapala.tecmm.edu.mx',       'horario_chavez.pdf'],
          [null,        'Francisco',   'González Siordia','Mecatrónica','2do','fgonzalez@chapala.tecmm.edu.mx',       null],
          ['jorge.png', 'José Jorge',  'Hernández Ochoa', 'Sistemas','6to','jorge.hernandez@chapala.tecmm.edu.mx',    'horario_hernandez.pdf'],
        ];
        $base_img = PLATAFORMA_URL . '/modulos/visitantes/imagenes/';
        foreach ($maestros as $m): ?>
        <tr>
          <td class="col-photo">
            <img src="<?= $m[0] ? $base_img . htmlspecialchars($m[0]) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='38' height='38'%3E%3Crect width='38' height='38' fill='%23e5e7eb'/%3E%3C/svg%3E" ?>"
                 alt="" style="width:38px;height:38px;border-radius:50%;object-fit:cover">
          </td>
          <td style="font-weight:600"><?= htmlspecialchars($m[1]) ?></td>
          <td><?= htmlspecialchars($m[2]) ?></td>
          <td><span class="adm-status adm-status--info"><?= htmlspecialchars($m[3]) ?></span></td>
          <td><?= htmlspecialchars($m[4]) ?></td>
          <td><a href="mailto:<?= htmlspecialchars($m[5]) ?>" style="color:var(--tsj-blue);font-size:12.5px"><?= htmlspecialchars($m[5]) ?></a></td>
          <td>
            <?php if ($m[6]): ?>
              <span class="adm-status adm-status--ok"><span class="material-symbols-rounded" style="font-size:14px">attach_file</span> <?= htmlspecialchars($m[6]) ?></span>
            <?php else: ?>
              <span class="adm-status adm-status--warn">Sin archivo</span>
            <?php endif; ?>
          </td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm pending-db" data-toast="Editar maestro — pendiente de BD"><span class="material-symbols-rounded">edit</span></button>
            <button class="adm-btn adm-btn--danger adm-btn--sm pending-db" data-toast="Eliminar maestro — pendiente de BD"><span class="material-symbols-rounded">delete</span></button>
          </td>
        </tr>
        <?php endforeach; ?>
        <tr><td colspan="8" class="adm-table-empty">Los maestros reales se cargarán desde la base de datos.</td></tr>
      </tbody>
    </table>
  </div>

  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">school</span> Agregar / Editar maestro</div>
    <form class="pending-db">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Nombre(s)</label><input type="text" placeholder="Nombre del maestro"></div>
        <div class="adm-field"><label>Apellido(s)</label><input type="text" placeholder="Apellidos"></div>
        <div class="adm-field"><label>Correo electrónico</label><input type="email" placeholder="correo@chapala.tecmm.edu.mx"></div>
        <div class="adm-field"><label>Carrera</label>
          <select><option>Sistemas Computacionales</option><option>Industrial</option><option>Mecatrónica</option><option>Animación</option><option>Gestión</option><option>Gastronomía</option></select>
        </div>
        <div class="adm-field"><label>Semestre</label>
          <select><option>1er</option><option>2do</option><option>3er</option><option>4to</option><option>5to</option><option>6to</option><option>7mo</option><option>8vo</option></select>
        </div>
        <div class="adm-field"><label>Foto (URL)</label><input type="text" placeholder="URL de la foto del maestro"></div>
        <div class="adm-field"><label>Archivo de horario (PDF/imagen)</label><input type="file" accept=".pdf,.jpg,.png"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar maestro</button>
        <button type="button" class="adm-btn adm-btn--ghost">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ Archivos de horario ════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="hor" data-tab="horarios_archivos">
  <div class="adm-section">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">folder_open</span> Archivos de horario subidos</h3>
      <button class="adm-btn adm-btn--primary adm-btn--sm pending-db" data-toast="Subir archivo — pendiente de BD">
        <span class="material-symbols-rounded">upload</span> Subir archivo
      </button>
    </div>
    <div class="adm-section-body">
      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead><tr><th>Archivo</th><th>Maestro asociado</th><th>Tipo</th><th>Fecha subida</th><th>Acciones</th></tr></thead>
          <tbody>
            <?php
            $archivos = [
              ['horario_delgado.pdf',   'Miguel Ángel Delgado',  'PDF','15/01/2025'],
              ['horario_chavez.pdf',    'Julio César Chávez',     'PDF','12/01/2025'],
              ['horario_hernandez.pdf', 'José Jorge Hernández',   'PDF','10/01/2025'],
            ];
            foreach ($archivos as $a): ?>
            <tr>
              <td><span class="material-symbols-rounded" style="color:var(--tsj-pink);vertical-align:middle">picture_as_pdf</span> <?= htmlspecialchars($a[0]) ?></td>
              <td><?= htmlspecialchars($a[1]) ?></td>
              <td><span class="adm-status adm-status--info"><?= $a[2] ?></span></td>
              <td><?= $a[3] ?></td>
              <td class="actions">
                <button class="adm-btn adm-btn--ghost adm-btn--sm pending-db" data-toast="Reemplazar archivo — pendiente de BD"><span class="material-symbols-rounded">upload</span></button>
                <button class="adm-btn adm-btn--danger adm-btn--sm pending-db" data-toast="Eliminar archivo — pendiente de BD"><span class="material-symbols-rounded">delete</span></button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- ══ Carreras ════════════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="hor" data-tab="carreras">
  <div class="adm-section">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">school</span> Carreras registradas</h3>
      <button class="adm-btn adm-btn--primary adm-btn--sm pending-db" data-toast="Agregar carrera — pendiente de BD">
        <span class="material-symbols-rounded">add</span> Agregar carrera
      </button>
    </div>
    <div class="adm-section-body">
      <div class="adm-list-editor">
        <?php foreach (['Ingeniería en Sistemas Computacionales','Ingeniería Industrial','Ingeniería Mecatrónica','Ingeniería en Animación Digital y Efectos Visuales','Ingeniería en Gestión Empresarial','Gastronomía'] as $car): ?>
        <div class="adm-list-item">
          <span class="adm-list-item-drag material-symbols-rounded">drag_indicator</span>
          <input type="text" value="<?= htmlspecialchars($car) ?>">
          <button class="adm-btn adm-btn--danger adm-btn--sm pending-db"><span class="material-symbols-rounded">delete</span></button>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="adm-form-actions" style="margin-top:14px">
        <button class="adm-btn adm-btn--primary pending-db"><span class="material-symbols-rounded">save</span> Guardar cambios</button>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
