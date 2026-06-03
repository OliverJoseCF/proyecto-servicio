<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'visitantes';
$adm_title = 'Visitantes';
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Gestión de Visitantes</h1>
    <p class="adm-page-desc">Directorio, docentes, coordinadores, planes de estudio y contenido de servicios.</p>
  </div>
</div>
<div class="adm-pending">
  <span class="material-symbols-rounded">construction</span>
  Los cambios se guardarán al conectar la base de datos. Actualmente los datos son de referencia.
</div>

<!-- Tabs -->
<div class="adm-tabs">
  <?php
  $tabs = [
    'directorio' => 'Directorio',
    'docentes'   => 'Docentes',
    'coord'      => 'Coordinadores',
    'materias'   => 'Planes de Estudio',
    'secretarias'=> 'Secretarías',
    'servicios'  => 'Servicios (Nuevo Ingreso / Reinscripción)',
    'ubicacion'  => 'Ubicación',
  ];
  foreach ($tabs as $key => $label): ?>
    <button class="adm-tab <?= $key === 'directorio' ? 'active' : '' ?>"
            data-tab-group="vis" data-tab="<?= $key ?>"
            onclick="showTab('vis','<?= $key ?>')">
      <?= $label ?>
    </button>
  <?php endforeach; ?>
</div>

<!-- ══ TAB: Directorio ══════════════════════════════════════════ -->
<div class="adm-tab-panel active" data-tab-group="vis" data-tab="directorio">
  <div class="adm-toolbar">
    <div class="adm-search">
      <span class="material-symbols-rounded">search</span>
      <input type="text" placeholder="Buscar por nombre o correo…">
    </div>
    <div class="adm-toolbar-actions">
      <button class="adm-btn adm-btn--primary pending-db" data-toast="Agregar persona — pendiente de BD">
        <span class="material-symbols-rounded">person_add</span> Agregar persona
      </button>
    </div>
  </div>
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr>
        <th>Foto</th><th>Nombre</th><th>Puesto / Área</th><th>Correo</th><th>Teléfono</th><th>Acciones</th>
      </tr></thead>
      <tbody>
        <?php
        $directorio = [
          ['miguel.png', 'Miguel Ángel Delgado López',       'Sistemas Computacionales',  'miguel.delgado@chapala.tecmm.edu.mx',         'S/N'],
          ['julio.png',  'Julio César Chávez Novoa',          'Sistemas Computacionales',  'julio.chavez@chapala.tecmm.edu.mx',           'S/N'],
          ['carmen.png', 'Carmen Leticia Salcedo Quevedo',    'Sistemas Computacionales',  'carmen.salcedo@chapala.tecmm.edu.mx',         'S/N'],
          ['jorge.png',  'José Jorge Hernández Ochoa',        'Sistemas Computacionales',  'jorge.hernandez@chapala.tecmm.edu.mx',        'S/N'],
          [null,         'Francisco Javier González Siordia', 'Sistemas Computacionales',  'francisco.gonzales@chapala.tecmm.edu.mx',     'S/N'],
          ['gamas.png',  'José Guadalupe Gamas Gamas',        'Sistemas Computacionales',  'jose.gamas@chapala.tecmm.edu.mx',             'S/N'],
        ];
        $base_img = PLATAFORMA_URL . '/modulos/visitantes/imagenes/';
        foreach ($directorio as $p):
        ?>
        <tr>
          <td class="col-photo">
            <img src="<?= $p[0] ? $base_img . htmlspecialchars($p[0]) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='38' height='38'%3E%3Crect width='38' height='38' fill='%23e5e7eb'/%3E%3C/svg%3E" ?>"
                 alt="<?= htmlspecialchars($p[1]) ?>"
                 style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid var(--tsj-blue-100)">
          </td>
          <td style="font-weight:600;color:var(--tsj-blue)"><?= htmlspecialchars($p[1]) ?></td>
          <td><?= htmlspecialchars($p[2]) ?></td>
          <td><a href="mailto:<?= htmlspecialchars($p[3]) ?>" style="color:var(--tsj-blue)"><?= htmlspecialchars($p[3]) ?></a></td>
          <td><?= htmlspecialchars($p[4]) ?></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm pending-db" data-toast="Editar persona — pendiente de BD">
              <span class="material-symbols-rounded">edit</span>
            </button>
            <button class="adm-btn adm-btn--danger adm-btn--sm pending-db" data-toast="Eliminar persona — pendiente de BD">
              <span class="material-symbols-rounded">delete</span>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <!-- Formulario agregar (preview) -->
  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title">
      <span class="material-symbols-rounded">person_add</span> Agregar nueva persona al directorio
    </div>
    <form class="pending-db">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Nombre completo</label><input type="text" placeholder="Ej. María García López"></div>
        <div class="adm-field"><label>Puesto / Área</label><input type="text" placeholder="Ej. Sistemas Computacionales"></div>
        <div class="adm-field"><label>Correo electrónico</label><input type="email" placeholder="nombre@chapala.tecmm.edu.mx"></div>
        <div class="adm-field"><label>Teléfono</label><input type="tel" placeholder="Ej. 331-234-5678"></div>
        <div class="adm-field"><label>Foto (URL o subir)</label><input type="text" placeholder="URL de la imagen"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary">
          <span class="material-symbols-rounded">save</span> Guardar persona
        </button>
        <button type="button" class="adm-btn adm-btn--ghost">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ TAB: Docentes ════════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="vis" data-tab="docentes">
  <div class="adm-career-pills">
    <?php foreach (['ISC' => 'Sistemas', 'II' => 'Industrial', 'IM' => 'Mecatrónica', 'IADEV' => 'Animación', 'IGE' => 'Gestión', 'LG' => 'Gastronomía'] as $k => $v): ?>
      <button class="adm-career-pill <?= $k === 'ISC' ? 'active' : '' ?>"
              onclick="document.querySelectorAll('.adm-career-pill').forEach(e=>e.classList.remove('active'));this.classList.add('active')">
        <?= $v ?>
      </button>
    <?php endforeach; ?>
  </div>
  <div class="adm-toolbar">
    <div class="adm-search">
      <span class="material-symbols-rounded">search</span>
      <input type="text" placeholder="Buscar docente…">
    </div>
    <button class="adm-btn adm-btn--primary pending-db" data-toast="Agregar docente — pendiente de BD">
      <span class="material-symbols-rounded">person_add</span> Agregar docente
    </button>
  </div>
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Nombre</th><th>Foto</th><th>Correo</th><th>Carrera</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php
        $docentes = [
          ['Miguel Ángel Delgado López', 'miguel.png', 'miguel.delgado@chapala.tecmm.edu.mx', 'ISC'],
          ['Alberto Chavolla',           null,         '—',                                     'ISC'],
          ['Francisco Javier González',  null,         'francisco.gonzales@chapala.tecmm.edu.mx','ISC'],
          ['Julio César Chávez Novoa',   'julio.png',  'julio.chavez@chapala.tecmm.edu.mx',    'ISC'],
          ['Edgar Martínez',             null,         '—',                                     'ISC'],
          ['José Jorge Hernández Ochoa', 'jorge.png',  'jorge.hernandez@chapala.tecmm.edu.mx', 'ISC'],
          ['Carmen Leticia Salcedo',     'carmen.png', 'carmen.salcedo@chapala.tecmm.edu.mx',  'ISC'],
          ['José Guadalupe Gamas',       'gamas.png',  'jose.gamas@chapala.tecmm.edu.mx',      'ISC'],
        ];
        foreach ($docentes as $d):
        ?>
        <tr>
          <td style="font-weight:600"><?= htmlspecialchars($d[0]) ?></td>
          <td class="col-photo">
            <img src="<?= $d[1] ? $base_img . htmlspecialchars($d[1]) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='38' height='38'%3E%3Crect width='38' height='38' fill='%23e5e7eb'/%3E%3C/svg%3E" ?>"
                 alt="" style="width:38px;height:38px;border-radius:50%;object-fit:cover">
          </td>
          <td><?= $d[2] !== '—' ? '<a href="mailto:' . htmlspecialchars($d[2]) . '" style="color:var(--tsj-blue)">' . htmlspecialchars($d[2]) . '</a>' : '<span style="color:var(--tsj-gray-400)">—</span>' ?></td>
          <td><span class="adm-status adm-status--info"><?= htmlspecialchars($d[3]) ?></span></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm pending-db" data-toast="Editar docente — pendiente de BD"><span class="material-symbols-rounded">edit</span></button>
            <button class="adm-btn adm-btn--danger adm-btn--sm pending-db" data-toast="Eliminar docente — pendiente de BD"><span class="material-symbols-rounded">delete</span></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">school</span> Agregar / Editar docente</div>
    <form class="pending-db">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Nombre completo</label><input type="text" placeholder="Nombre del docente"></div>
        <div class="adm-field"><label>Correo electrónico</label><input type="email" placeholder="correo@chapala.tecmm.edu.mx"></div>
        <div class="adm-field"><label>Carrera</label>
          <select><option>Sistemas Computacionales</option><option>Industrial</option><option>Mecatrónica</option><option>Animación</option><option>Gestión</option><option>Gastronomía</option></select>
        </div>
        <div class="adm-field"><label>Foto (URL)</label><input type="text" placeholder="URL de la foto"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
        <button type="button" class="adm-btn adm-btn--ghost">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ TAB: Coordinadores ════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="vis" data-tab="coord">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Carrera</th><th>Nombre del Coordinador</th><th>Correo</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php
        $coords = [
          ['Ing. en Sistemas Computacionales',            'Claudio Castillo',        'claudio.castillo@chapala.tecmm.edu.mx'],
          ['Ing. en Gestión Empresarial',                  'Pablo Rojas',             'pablo.rojas@chapala.tecmm.edu.mx'],
          ['Ing. Mecatrónica',                             'Iván (coordinador)',       'ivan@chapala.tecmm.edu.mx'],
          ['Ing. en Animación Digital',                    'Coordinador Animación',   'coord.animacion@chapala.tecmm.edu.mx'],
          ['Ing. Industrial',                              'Leonardo',                'leonardo@chapala.tecmm.edu.mx'],
          ['Gastronomía',                                  'Coordinador Gastronomía', 'coord.gastronomia@chapala.tecmm.edu.mx'],
        ];
        foreach ($coords as $c): ?>
        <tr>
          <td style="font-weight:600;color:var(--tsj-blue)"><?= htmlspecialchars($c[0]) ?></td>
          <td><?= htmlspecialchars($c[1]) ?></td>
          <td><a href="mailto:<?= htmlspecialchars($c[2]) ?>" style="color:var(--tsj-blue)"><?= htmlspecialchars($c[2]) ?></a></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm pending-db" data-toast="Editar coordinador — pendiente de BD"><span class="material-symbols-rounded">edit</span></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ══ TAB: Planes de Estudio ══════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="vis" data-tab="materias">
  <div class="adm-career-pills" style="margin-bottom:20px">
    <?php foreach (['ISC' => 'Sistemas', 'II' => 'Industrial', 'IM' => 'Mecatrónica', 'IADEV' => 'Animación', 'IGE' => 'Gestión', 'LG' => 'Gastronomía'] as $k => $v): ?>
      <button class="adm-career-pill <?= $k === 'ISC' ? 'active' : '' ?>"
              onclick="document.querySelectorAll('[data-career-pills]>.adm-career-pill').forEach(e=>e.classList.remove('active'));this.classList.add('active')"><?= $v ?></button>
    <?php endforeach; ?>
  </div>
  <div class="adm-section">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">list_alt</span> Materias — Ing. en Sistemas Computacionales</h3>
      <button class="adm-btn adm-btn--primary adm-btn--sm pending-db" data-toast="Agregar materia — pendiente de BD">
        <span class="material-symbols-rounded">add</span> Agregar materia
      </button>
    </div>
    <div class="adm-section-body">
      <div class="adm-list-editor">
        <?php
        $materias = ['Fundamentos de Programación','Estructuras de Datos','Bases de Datos',
                     'Redes de Computadoras','Sistemas Operativos','Ingeniería de Software',
                     'Análisis de Sistemas','Arquitectura de Computadoras','Desarrollo Web',
                     'Programación Orientada a Objetos'];
        foreach ($materias as $m): ?>
        <div class="adm-list-item">
          <span class="adm-list-item-drag material-symbols-rounded">drag_indicator</span>
          <input type="text" value="<?= htmlspecialchars($m) ?>">
          <button class="adm-btn adm-btn--danger adm-btn--sm pending-db" data-toast="Eliminar materia — pendiente de BD">
            <span class="material-symbols-rounded">delete</span>
          </button>
        </div>
        <?php endforeach; ?>
        <button class="adm-list-add pending-db" data-toast="Agregar materia — pendiente de BD">
          <span class="material-symbols-rounded">add</span> Agregar materia
        </button>
      </div>
      <div class="adm-form-actions" style="margin-top:16px">
        <button class="adm-btn adm-btn--primary pending-db"><span class="material-symbols-rounded">save</span> Guardar cambios</button>
      </div>
    </div>
  </div>
</div>

<!-- ══ TAB: Secretarías ════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="vis" data-tab="secretarias">
  <div class="adm-toolbar">
    <div></div>
    <button class="adm-btn adm-btn--primary pending-db" data-toast="Agregar secretaria — pendiente de BD">
      <span class="material-symbols-rounded">person_add</span> Agregar
    </button>
  </div>
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Nombre</th><th>Rol</th><th>Correo</th><th>Teléfono</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php
        $secs = [
          ['Laura Martínez',    'Secretaria Administrativa',  'laura.martinez@chapala.tecmm.edu.mx',    '331-234-5678'],
          ['María López',       'Recepcionista',              'maria.lopez@chapala.tecmm.edu.mx',       '331-456-7890'],
          ['Patricia Gómez',    'Secretaria de Dirección',    'patricia.gomez@chapala.tecmm.edu.mx',    '332-567-8901'],
          ['Ana Rivera',        'Asistente Académica',        'ana.rivera@chapala.tecmm.edu.mx',        '333-678-9012'],
          ['Gabriela Torres',   'Secretaria Control Escolar', 'gabriela.torres@chapala.tecmm.edu.mx',   '334-789-0123'],
        ];
        foreach ($secs as $s): ?>
        <tr>
          <td style="font-weight:600"><?= htmlspecialchars($s[0]) ?></td>
          <td><?= htmlspecialchars($s[1]) ?></td>
          <td><a href="mailto:<?= htmlspecialchars($s[2]) ?>" style="color:var(--tsj-blue)"><?= htmlspecialchars($s[2]) ?></a></td>
          <td><?= htmlspecialchars($s[3]) ?></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm pending-db" data-toast="Editar — pendiente de BD"><span class="material-symbols-rounded">edit</span></button>
            <button class="adm-btn adm-btn--danger adm-btn--sm pending-db" data-toast="Eliminar — pendiente de BD"><span class="material-symbols-rounded">delete</span></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ══ TAB: Servicios ══════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="vis" data-tab="servicios">
  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">how_to_reg</span> Nuevo Ingreso — Requisitos</div>
    <form class="pending-db">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Fecha del examen de admisión (día del mes)</label><input type="number" value="20" min="1" max="31"></div>
        <div class="adm-field"><label>Hora del examen</label><input type="time" value="08:00"></div>
      </div>
      <div class="adm-field" style="margin-bottom:16px"><label>Requisitos de admisión (uno por línea)</label>
        <textarea>Copia de la identificación oficial.
Certificado de estudios anteriores (original y copia).
Comprobante de domicilio.
Fotografías tamaño infantil (4 piezas).
Formulario de inscripción llenado.</textarea>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
      </div>
    </form>
  </div>
  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">school</span> Re-inscripción — Carreras disponibles</div>
    <p style="font-size:13px;color:var(--tsj-gray-600);margin:0 0 14px">Carreras que aparecen en el selector del comprobante de re-inscripción.</p>
    <div class="adm-list-editor">
      <?php
      $carreras = ['Ingeniería Mecatrónica','Ingeniería en Sistemas Computacionales',
                   'Ingeniería Industrial','Ingeniería en Animación Digital y Efectos Visuales',
                   'Gastronomía','Ingeniería en Gestión Empresarial'];
      foreach ($carreras as $car): ?>
      <div class="adm-list-item">
        <span class="adm-list-item-drag material-symbols-rounded">drag_indicator</span>
        <input type="text" value="<?= htmlspecialchars($car) ?>">
        <button class="adm-btn adm-btn--danger adm-btn--sm pending-db"><span class="material-symbols-rounded">delete</span></button>
      </div>
      <?php endforeach; ?>
      <button class="adm-list-add pending-db"><span class="material-symbols-rounded">add</span> Agregar carrera</button>
    </div>
    <div class="adm-form-actions" style="margin-top:14px">
      <button class="adm-btn adm-btn--primary pending-db"><span class="material-symbols-rounded">save</span> Guardar cambios</button>
    </div>
  </div>
</div>

<!-- ══ TAB: Ubicación ══════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="vis" data-tab="ubicacion">
  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">location_on</span> Ubicación del Campus</div>
    <form class="pending-db">
      <div class="adm-form-grid cols-1">
        <div class="adm-field">
          <label>Enlace de Google Maps (URL completa)</label>
          <input type="url" value="https://maps.app.goo.gl/w3rApmQrocT3j5V88">
        </div>
        <div class="adm-field">
          <label>URL del embed de Google Maps (iframe src)</label>
          <textarea style="min-height:70px">https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3733.5!2d-103.1901!3d20.2985…</textarea>
          <span class="adm-field-help">Ve a Google Maps → Compartir → Insertar mapa → copia la URL del src del iframe.</span>
        </div>
        <div class="adm-field">
          <label>Dirección textual</label>
          <input type="text" value="Carretera Chapala-Jocotepec km 7.5, Ajijic, Chapala, Jalisco">
        </div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
