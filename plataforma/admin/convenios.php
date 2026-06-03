<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'convenios';
$adm_title = 'Convenios';
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Gestión de Convenios</h1>
    <p class="adm-page-desc">Empresas vinculadas por carrera, contactos y sugerencias de alumnos.</p>
  </div>
</div>
<div class="adm-pending">
  <span class="material-symbols-rounded">construction</span>
  Los convenios reales se cargarán desde la base de datos. La interfaz está lista.
</div>

<div class="adm-tabs">
  <?php foreach (['convenios'=>'Convenios por carrera','sugerencias'=>'Sugerencias de empresas'] as $k=>$l): ?>
    <button class="adm-tab <?= $k==='convenios'?'active':'' ?>"
            data-tab-group="conv" data-tab="<?= $k ?>" onclick="showTab('conv','<?= $k ?>')">
      <?= $l ?>
    </button>
  <?php endforeach; ?>
</div>

<!-- ══ Convenios por carrera ══════════════════════════════════ -->
<div class="adm-tab-panel active" data-tab-group="conv" data-tab="convenios">
  <div class="adm-career-pills" style="margin-bottom:20px">
    <?php foreach (['IADEV'=>'Animación','IM'=>'Mecatrónica','ISC'=>'Sistemas','II'=>'Industrial','LG'=>'Gastronomía','IGE'=>'Gestión'] as $k=>$v): ?>
      <button class="adm-career-pill <?= $k==='IADEV'?'active':'' ?>"
              onclick="document.querySelectorAll('.adm-career-pill').forEach(e=>e.classList.remove('active'));this.classList.add('active')">
        <?= $v ?>
      </button>
    <?php endforeach; ?>
  </div>
  <div class="adm-toolbar">
    <div class="adm-search">
      <span class="material-symbols-rounded">search</span>
      <input type="text" placeholder="Buscar empresa o contacto…">
    </div>
    <button class="adm-btn adm-btn--primary pending-db" data-toast="Agregar convenio — pendiente de BD">
      <span class="material-symbols-rounded">add</span> Agregar convenio
    </button>
  </div>
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Empresa</th><th>Tipo de convenio</th><th>Contacto</th><th>Correo / Tel.</th><th>Vencimiento</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php
        $convs = [
          ['Empresa Tecnológica S.A.',  'Residencia profesional', 'Carlos Mendoza',   'cmendoza@empresa.com',       '31/12/2025','ok'],
          ['Estudio Creativo MX',       'Servicio social',        'Ana Torres',        'ana.torres@estudio.mx',      '30/06/2025','warn'],
          ['Industrias del Bajío',      'Prácticas profesionales','Roberto Sánchez',   'rsanchez@industrias.com',    '15/08/2025','ok'],
        ];
        $est = ['ok'=>'Vigente','warn'=>'Por vencer','danger'=>'Vencido'];
        foreach ($convs as $c): ?>
        <tr>
          <td style="font-weight:600"><?= htmlspecialchars($c[0]) ?></td>
          <td><span class="adm-status adm-status--info"><?= htmlspecialchars($c[1]) ?></span></td>
          <td><?= htmlspecialchars($c[2]) ?></td>
          <td><a href="mailto:<?= htmlspecialchars($c[3]) ?>" style="color:var(--tsj-blue)"><?= htmlspecialchars($c[3]) ?></a></td>
          <td><?= htmlspecialchars($c[4]) ?></td>
          <td><span class="adm-status adm-status--<?= $c[5] ?>"><?= $est[$c[5]] ?></span></td>
          <td class="actions">
            <button class="adm-btn adm-btn--ghost adm-btn--sm pending-db" data-toast="Editar convenio — pendiente de BD"><span class="material-symbols-rounded">edit</span></button>
            <button class="adm-btn adm-btn--danger adm-btn--sm pending-db" data-toast="Eliminar convenio — pendiente de BD"><span class="material-symbols-rounded">delete</span></button>
          </td>
        </tr>
        <?php endforeach; ?>
        <tr><td colspan="7" class="adm-table-empty">Los convenios reales se cargarán desde la base de datos.</td></tr>
      </tbody>
    </table>
  </div>

  <div class="adm-form-card" style="margin-top:20px">
    <div class="adm-form-title"><span class="material-symbols-rounded">handshake</span> Agregar / Editar convenio</div>
    <form class="pending-db">
      <div class="adm-form-grid cols-3">
        <div class="adm-field"><label>Nombre de la empresa</label><input type="text" placeholder="Ej. Empresa S.A. de C.V."></div>
        <div class="adm-field"><label>Tipo de convenio</label>
          <select><option>Residencia profesional</option><option>Servicio social</option><option>Prácticas profesionales</option><option>Otro</option></select>
        </div>
        <div class="adm-field"><label>Carrera</label>
          <select><option>Animación Digital</option><option>Mecatrónica</option><option>Sistemas Computacionales</option><option>Industrial</option><option>Gastronomía</option><option>Gestión Empresarial</option></select>
        </div>
        <div class="adm-field"><label>Nombre del contacto</label><input type="text" placeholder="Nombre del responsable"></div>
        <div class="adm-field"><label>Correo del contacto</label><input type="email" placeholder="contacto@empresa.com"></div>
        <div class="adm-field"><label>Teléfono del contacto</label><input type="tel" placeholder="Ej. 333-000-1111"></div>
        <div class="adm-field"><label>Fecha de vencimiento</label><input type="date"></div>
        <div class="adm-field"><label>Logo (URL)</label><input type="text" placeholder="URL del logo de la empresa"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar convenio</button>
        <button type="button" class="adm-btn adm-btn--ghost">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ Sugerencias de empresas ════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="conv" data-tab="sugerencias">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead><tr><th>Empresa sugerida</th><th>Correo empresa</th><th>Contacto</th><th>Fecha</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php
        $sugs = [
          ['Digital Studio Guadalajara','hola@digitalstudio.mx',  'Sofía Reyes',   '18/01/2025','warn'],
          ['Manufactura del Lago S.A.',  'contacto@mfglago.com',   'Hugo Fuentes',  '19/01/2025','warn'],
        ];
        foreach ($sugs as $s): ?>
        <tr>
          <td style="font-weight:600"><?= htmlspecialchars($s[0]) ?></td>
          <td><a href="mailto:<?= htmlspecialchars($s[1]) ?>" style="color:var(--tsj-blue)"><?= htmlspecialchars($s[1]) ?></a></td>
          <td><?= htmlspecialchars($s[2]) ?></td>
          <td><?= htmlspecialchars($s[3]) ?></td>
          <td><span class="adm-status adm-status--warn">Pendiente</span></td>
          <td class="actions">
            <button class="adm-btn adm-btn--primary adm-btn--sm pending-db" data-toast="Convertir a convenio — pendiente de BD"><span class="material-symbols-rounded">add_task</span> Aceptar</button>
            <button class="adm-btn adm-btn--danger adm-btn--sm pending-db" data-toast="Rechazar sugerencia — pendiente de BD"><span class="material-symbols-rounded">close</span> Rechazar</button>
          </td>
        </tr>
        <?php endforeach; ?>
        <tr><td colspan="6" class="adm-table-empty">Las sugerencias de alumnos se cargarán desde la base de datos.</td></tr>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
