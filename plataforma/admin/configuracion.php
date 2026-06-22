<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'configuracion';
$adm_title = 'Configuración';

// Cargar valores actuales desde BD
$cfg = [];
$admins        = [];
$admins_ok     = false; // true si la tabla `admins` existe (migración aplicada)
try {
    $db   = getPDO(DB_NAME);
    $rows = $db->query('SELECT clave, valor FROM configuracion')->fetchAll();
    foreach ($rows as $r) $cfg[$r['clave']] = $r['valor'];
    $db_ok = true;

    // Lista de administradores (defensivo: la tabla puede no existir sin migración)
    try {
        $admins    = $db->query('SELECT id, nombre, email, activo, ultimo_acceso FROM admins ORDER BY nombre')->fetchAll();
        $admins_ok = true;
    } catch (\PDOException $eAdm) {
        $admins_ok = false;
    }
} catch (\Throwable $e) {
    $db_ok = false;
}

function cfgVal(string $key, string $default = ''): string {
    global $cfg;
    return htmlspecialchars($cfg[$key] ?? $default);
}

$csrf = csrfToken();
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Configuración General</h1>
    <p class="adm-page-desc">Correos del sistema, redes sociales, footer y datos de contacto del portal.</p>
  </div>
</div>

<div class="adm-tabs">
  <?php foreach (['portal'=>'Información del portal','correos'=>'Correos del sistema','redes'=>'Redes sociales','administradores'=>'Administradores','seguridad'=>'Seguridad y acceso'] as $k=>$l): ?>
    <button class="adm-tab <?= $k==='portal'?'active':'' ?>"
            data-tab-group="cfg" data-tab="<?= $k ?>" onclick="showTab('cfg','<?= $k ?>')">
      <?= $l ?>
    </button>
  <?php endforeach; ?>
</div>

<!-- ══ Información del portal ════════════════════════════════ -->
<div class="adm-tab-panel active" data-tab-group="cfg" data-tab="portal">
  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">info</span> Datos generales del portal</div>
    <form data-proc="configuracion" data-accion="guardar_config">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="guardar_config">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Nombre de la institución</label><input type="text" name="nombre_institucion" value="<?= cfgVal('nombre_institucion','Tecnológico Superior de Jalisco') ?>" required></div>
        <div class="adm-field"><label>Campus</label><input type="text" name="campus" value="<?= cfgVal('campus','Campus Chapala') ?>"></div>
        <div class="adm-field" style="grid-column:1/-1"><label>Descripción meta del portal</label><textarea name="descripcion_portal"><?= cfgVal('descripcion_portal','Portal de servicios estudiantiles del Tecnológico Superior de Jalisco — Chapala.') ?></textarea></div>
        <div class="adm-field">
          <label>URL base de la plataforma</label>
          <input type="text" value="<?= htmlspecialchars(defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') ?>" disabled style="background:var(--tsj-gray-100);color:var(--tsj-gray-500);cursor:not-allowed">
          <span class="adm-field-help">Definida en <code>shared/config.local.php</code> → constante <code>PLATAFORMA_URL</code>. No se puede cambiar desde aquí.</span>
        </div>
        <div class="adm-field"><label>Eslogan (footer)</label><input type="text" name="eslogan" value="<?= cfgVal('eslogan','Innovar para transformar a México') ?>"></div>
        <div class="adm-field" style="grid-column:1/-1">
          <label>Sitio web oficial</label>
          <input type="url" name="sitio_oficial_url" value="<?= cfgVal('sitio_oficial_url','https://www.tecmm.edu.mx') ?>" placeholder="https://www.tecmm.edu.mx">
          <span class="adm-field-help">Aparece como enlace en la barra de copyright del footer.</span>
        </div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
      </div>
    </form>
  </div>

  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">location_on</span> Dirección y contacto (footer)</div>
    <form data-proc="configuracion" data-accion="guardar_config">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="guardar_config">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Dirección</label><input type="text" name="direccion" value="<?= cfgVal('direccion','Carretera Chapala-Jocotepec km 7.5, Ajijic, Chapala, Jalisco') ?>"></div>
        <div class="adm-field"><label>Correo general de contacto</label><input type="email" name="correo_general" value="<?= cfgVal('correo_general','campus.chapala@tsj.edu.mx') ?>"></div>
        <div class="adm-field"><label>Teléfono</label><input type="tel" name="telefono" value="<?= cfgVal('telefono','376-766-0000') ?>" inputmode="tel" maxlength="25" pattern="[0-9+\-\s()]{7,25}" title="Solo números, +, -, espacios y paréntesis (entre 7 y 25 caracteres)"></div>
        <div class="adm-field"><label>Horario de atención</label><input type="text" name="horario_atencion" value="<?= cfgVal('horario_atencion','Lun – Vie: 8:00 – 20:00 h') ?>"></div>
        <div class="adm-field" style="grid-column:1/-1"><label>Enlace Google Maps (botón)</label><input type="url" name="maps_link_url" value="<?= cfgVal('maps_link_url') ?>"></div>
        <div class="adm-field" style="grid-column:1/-1"><label>Embed Google Maps (iframe src)</label><textarea name="maps_embed_url" style="min-height:60px"><?= cfgVal('maps_embed_url') ?></textarea><span class="adm-field-help">Ve a Google Maps → Compartir → Insertar mapa → copia la URL del src del iframe.</span></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ Correos del sistema ════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="cfg" data-tab="correos">
  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">mail</span> Correos institucionales</div>
    <div class="adm-pending" style="margin-bottom:16px">
      <span class="material-symbols-rounded">info</span>
      <span>Estos correos aparecen en la sección <strong>Contacto por área</strong> del footer del portal (solo los que tengan valor). Déjalos en blanco para ocultarlos.</span>
    </div>
    <form data-proc="configuracion" data-accion="guardar_correos">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="guardar_correos">
      <div class="adm-form-grid cols-2">
        <?php
        $correos = [
          ['correo_general',     'Correo general de contacto',    'campus.chapala@tsj.edu.mx',                      'Footer → Contacto y enlace mailto'],
          ['correo_biblioteca',  'Biblioteca',                     'biblioteca@chapala.tecmm.edu.mx',                'Footer → Contacto por área'],
          ['correo_vinculacion', 'Vinculación / Convenios',        'vinculacion@chapala.tecmm.edu.mx',               'Footer → Contacto por área'],
          ['correo_facturacion', 'Facturación / Finanzas',         'facturacion@chapala.tecmm.edu.mx',               'Footer → Contacto por área'],
          ['correo_escolares',   'Control escolar',                'escolares@chapala.tecmm.edu.mx',                 'Footer → Contacto por área'],
          ['correo_direccion',   'Dirección',                      'IlianaJanettHernandezPartida@chapala.tecmm.edu.mx','Footer → Contacto por área'],
          ['correo_servicios',   'Servicios generales',            'servicios@chapala.tecmm.edu.mx',                 'Footer → Contacto por área'],
        ];
        foreach ($correos as $c): ?>
        <div class="adm-field">
          <label><?= htmlspecialchars($c[1]) ?></label>
          <input type="email" name="<?= $c[0] ?>" value="<?= cfgVal($c[0], $c[2]) ?>">
          <span class="adm-field-help"><?= htmlspecialchars($c[3]) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar correos</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ Redes sociales ════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="cfg" data-tab="redes">
  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">share</span> Redes sociales del campus</div>
    <form data-proc="configuracion" data-accion="guardar_redes">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="guardar_redes">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Facebook</label><input type="url" name="facebook_url" value="<?= cfgVal('facebook_url','https://www.facebook.com/TecSJ/') ?>"></div>
        <div class="adm-field"><label>YouTube</label><input type="url" name="youtube_url" value="<?= cfgVal('youtube_url','https://www.youtube.com/@TecSuperiorJalisco') ?>"></div>
        <div class="adm-field"><label>Instagram</label><input type="url" name="instagram_url" value="<?= cfgVal('instagram_url') ?>" placeholder="https://instagram.com/…"></div>
        <div class="adm-field"><label>Twitter / X</label><input type="url" name="twitter_url" value="<?= cfgVal('twitter_url') ?>" placeholder="https://twitter.com/…"></div>
        <div class="adm-field"><label>LinkedIn</label><input type="url" name="linkedin_url" value="<?= cfgVal('linkedin_url') ?>" placeholder="https://linkedin.com/…"></div>
      </div>
      <p style="font-size:12px;color:var(--tsj-gray-400);margin:4px 0 16px">
        <span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle">info</span>
        Deja vacío cualquier red que la institución no use — no aparecerá en el footer.
        El sitio web oficial se configura en la pestaña <strong>Información del portal</strong>.
      </p>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar redes</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ Administradores ═══════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="cfg" data-tab="administradores">

  <?php if (!$admins_ok): ?>
  <div class="adm-pending" style="background:#fef2f2;border-color:#fca5a5;color:#991b1b;margin-bottom:18px">
    <span class="material-symbols-rounded">warning</span>
    <span><strong>Falta aplicar la migración.</strong> Ejecuta <code>migracion_admins.sql</code> sobre la base de datos
    <code>kiosko_tsj</code> (phpMyAdmin → SQL) para habilitar las cuentas de administrador.</span>
  </div>
  <?php else: ?>

  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">group</span> Cuentas de administrador</div>
    <div class="adm-pending" style="margin-bottom:16px">
      <span class="material-symbols-rounded">info</span>
      <span>Cada persona que administre el portal debe tener su propia cuenta — así la bitácora registra
      quién hace cada cambio. La <strong>cuenta maestra</strong> (definida en <code>config.local.php</code>)
      siempre funciona como respaldo y se gestiona en la pestaña <strong>Seguridad y acceso</strong>.</span>
    </div>

    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead><tr><th>Nombre</th><th>Correo</th><th>Estado</th><th>Último acceso</th><th style="text-align:right">Acciones</th></tr></thead>
        <tbody id="admins-tbody">
          <?php if (empty($admins)): ?>
          <tr id="admins-empty"><td colspan="5" class="adm-table-empty">Aún no hay cuentas. Crea la primera abajo.</td></tr>
          <?php else: foreach ($admins as $a):
            $esYo = ((int)$a['id'] === adminActualId()); ?>
          <tr id="admin-row-<?= (int)$a['id'] ?>"
              data-id="<?= (int)$a['id'] ?>"
              data-nombre="<?= htmlspecialchars($a['nombre'], ENT_QUOTES) ?>"
              data-email="<?= htmlspecialchars($a['email'], ENT_QUOTES) ?>">
            <td style="font-weight:600">
              <?= htmlspecialchars($a['nombre']) ?>
              <?php if ($esYo): ?><span class="adm-status adm-status--info" style="margin-left:6px">tú</span><?php endif; ?>
            </td>
            <td><?= htmlspecialchars($a['email']) ?></td>
            <td>
              <span class="adm-status <?= $a['activo'] ? 'adm-status--ok' : 'adm-status--danger' ?>">
                <?= $a['activo'] ? 'Activo' : 'Inactivo' ?>
              </span>
            </td>
            <td style="font-size:12.5px;color:var(--tsj-gray-500)"><?= $a['ultimo_acceso'] ? htmlspecialchars($a['ultimo_acceso']) : 'Nunca' ?></td>
            <td style="text-align:right;white-space:nowrap">
              <button type="button" class="adm-btn adm-btn--ghost adm-btn--sm"
                      onclick="editarAdmin(<?= (int)$a['id'] ?>)" title="Editar">
                <span class="material-symbols-rounded">edit</span>
              </button>
              <?php if (!$esYo): ?>
              <button type="button" class="adm-btn adm-btn--ghost adm-btn--sm"
                      onclick="toggleAdmin(<?= (int)$a['id'] ?>)" title="<?= $a['activo'] ? 'Desactivar' : 'Activar' ?>">
                <span class="material-symbols-rounded"><?= $a['activo'] ? 'toggle_on' : 'toggle_off' ?></span>
              </button>
              <button type="button" class="adm-btn adm-btn--ghost adm-btn--sm" style="color:var(--tsj-danger)"
                      onclick="eliminarAdmin(<?= (int)$a['id'] ?>)" title="Eliminar">
                <span class="material-symbols-rounded">delete</span>
              </button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="adm-form-card">
    <div class="adm-form-title">
      <span class="material-symbols-rounded" id="admin-form-icon">person_add</span>
      <span id="admin-form-titulo">Nuevo administrador</span>
    </div>
    <form data-proc="admins" data-reload id="admin-form">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="admin_agregar" id="admin-accion">
      <input type="hidden" name="id" value="" id="admin-id">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Nombre completo <span style="color:var(--tsj-pink)">*</span></label>
          <input type="text" name="nombre" id="admin-nombre" maxlength="150" required></div>
        <div class="adm-field"><label>Correo electrónico <span style="color:var(--tsj-pink)">*</span></label>
          <input type="email" name="email" id="admin-email" maxlength="254" required></div>
        <div class="adm-field" style="grid-column:1/-1">
          <label>Contraseña <span style="color:var(--tsj-pink)" id="admin-pass-req">*</span></label>
          <input type="password" name="password" id="admin-password" placeholder="Mínimo 12 caracteres" minlength="12">
          <span class="adm-field-help" id="admin-pass-help">Mínimo 12 caracteres.</span>
        </div>
      </div>
      <div class="adm-form-actions">
        <button type="button" class="adm-btn adm-btn--ghost" id="admin-cancelar" onclick="resetFormAdmin()" style="display:none">Cancelar</button>
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
      </div>
    </form>
  </div>

  <script>
  function resetFormAdmin() {
    document.getElementById('admin-accion').value   = 'admin_agregar';
    document.getElementById('admin-id').value        = '';
    document.getElementById('admin-nombre').value    = '';
    document.getElementById('admin-email').value     = '';
    document.getElementById('admin-password').value  = '';
    document.getElementById('admin-form-titulo').textContent = 'Nuevo administrador';
    document.getElementById('admin-form-icon').textContent   = 'person_add';
    document.getElementById('admin-password').required = true;
    document.getElementById('admin-pass-req').style.display  = '';
    document.getElementById('admin-pass-help').textContent   = 'Mínimo 12 caracteres.';
    document.getElementById('admin-cancelar').style.display  = 'none';
  }

  function editarAdmin(id) {
    var row = document.getElementById('admin-row-' + id);
    if (!row) return;
    document.getElementById('admin-accion').value  = 'admin_editar';
    document.getElementById('admin-id').value       = id;
    document.getElementById('admin-nombre').value   = row.dataset.nombre;
    document.getElementById('admin-email').value    = row.dataset.email;
    document.getElementById('admin-password').value = '';
    document.getElementById('admin-form-titulo').textContent = 'Editar administrador';
    document.getElementById('admin-form-icon').textContent   = 'manage_accounts';
    document.getElementById('admin-password').required = false;
    document.getElementById('admin-pass-req').style.display  = 'none';
    document.getElementById('admin-pass-help').textContent   = 'Déjala en blanco para conservar la contraseña actual.';
    document.getElementById('admin-cancelar').style.display  = '';
    document.getElementById('admin-form').scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function toggleAdmin(id) {
    var csrf = document.querySelector('#admin-form input[name="_csrf"]').value;
    adminFetch('admins', { _csrf: csrf, accion: 'admin_toggle', id: id }).then(function(j){
      if (j.ok) setTimeout(function(){ location.reload(); }, 700);
    });
  }

  function eliminarAdmin(id) {
    if (!confirm('¿Eliminar esta cuenta de administrador? No se puede deshacer.')) return;
    var csrf = document.querySelector('#admin-form input[name="_csrf"]').value;
    adminFetch('admins', { _csrf: csrf, accion: 'admin_eliminar', id: id }).then(function(j){
      if (j.ok) {
        var row = document.getElementById('admin-row-' + id);
        if (row) row.remove();
      }
    });
  }
  </script>

  <?php endif; ?>
</div>

<!-- ══ Seguridad y acceso ════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="cfg" data-tab="seguridad">
  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">lock</span> Contraseña de la cuenta maestra</div>
    <div class="adm-pending" style="margin-bottom:16px">
      <span class="material-symbols-rounded">info</span>
      <span>Esta es la cuenta de <strong>respaldo</strong> que siempre funciona, incluso si la base de datos falla.
      El nuevo hash se escribe automáticamente en <strong>shared/config.local.php</strong>.
      Las cuentas del día a día se gestionan en la pestaña <strong>Administradores</strong>.</span>
    </div>
    <form data-proc="configuracion" data-accion="cambiar_password">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="cambiar_password">
      <div class="adm-form-grid cols-2">
        <div class="adm-field">
          <label>Correo del administrador</label>
          <input type="email" value="<?= defined('GLOBAL_ADMIN_EMAIL') ? htmlspecialchars(GLOBAL_ADMIN_EMAIL) : '' ?>" disabled>
          <span class="adm-field-help">Definido en config.local.php → GLOBAL_ADMIN_EMAIL</span>
        </div>
        <div class="adm-field"></div>
        <div class="adm-field"><label>Nueva contraseña <span style="color:var(--tsj-pink)">*</span></label><input type="password" name="nueva_password" placeholder="Mínimo 12 caracteres" minlength="12" required></div>
        <div class="adm-field"><label>Confirmar contraseña <span style="color:var(--tsj-pink)">*</span></label><input type="password" name="confirma_password" placeholder="Repite la contraseña" required></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--warning"><span class="material-symbols-rounded">lock_reset</span> Cambiar contraseña</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
