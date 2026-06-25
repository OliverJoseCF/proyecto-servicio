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
    <h1 class="adm-page-title">Configuración</h1>
    <p class="adm-page-desc">Datos del portal, correos de contacto, redes sociales, cuentas de administrador y seguridad.</p>
  </div>
</div>

<div class="adm-pending" style="margin-bottom:18px">
  <span class="material-symbols-rounded">info</span>
  <span>
    Usa las pestañas para encontrar lo que buscas:
    <strong>Datos del portal</strong> (nombre, dirección, footer, mapa) ·
    <strong>Correos de contacto</strong> (los que aparecen en el footer) ·
    <strong>Redes sociales</strong> ·
    <strong>Administradores</strong> (quién puede entrar al panel) ·
    <strong>Seguridad y acceso</strong> (contraseña de la cuenta maestra).
  </span>
</div>

<div class="adm-tabs">
  <?php foreach (['portal'=>'Datos del portal','correos'=>'Correos de contacto','redes'=>'Redes sociales','administradores'=>'Administradores','seguridad'=>'Seguridad y acceso'] as $k=>$l): ?>
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
        <div class="adm-field"><label>Horario de atención</label><input type="text" name="horario_atencion" value="<?= cfgVal('horario_atencion','Lun – Vie: 8:00 AM – 8:00 PM') ?>"></div>
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
        El sitio web oficial se configura en la pestaña <strong>Datos del portal</strong>.
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
    <span><strong>Falta la tabla de administradores.</strong> Vuelve a importar <code>kiosko_tsj.sql</code>
    en phpMyAdmin (recrea la base con todas las tablas, incluida <code>admins</code>) para habilitar
    las cuentas de administrador.</span>
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
          <input type="password" name="password" id="admin-password" placeholder="Mínimo 8 caracteres" minlength="8">
          <span class="adm-field-help" id="admin-pass-help">Mínimo 8 caracteres.</span>
          <div id="admin-pass-requisitos" style="display:none;margin-top:8px;display:flex;flex-direction:column;gap:4px;font-size:12.5px">
            <span id="adm-req-len"  class="adm-req"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle">radio_button_unchecked</span> Mínimo 8 caracteres</span>
            <span id="adm-req-may"  class="adm-req"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle">radio_button_unchecked</span> Al menos una mayúscula (A–Z)</span>
            <span id="adm-req-num"  class="adm-req"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle">radio_button_unchecked</span> Al menos un número (0–9)</span>
            <span id="adm-req-esp"  class="adm-req"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle">radio_button_unchecked</span> Al menos un símbolo (!@#$…)</span>
          </div>
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
    document.getElementById('admin-pass-help').textContent   = 'Mínimo 8 caracteres.';
    document.getElementById('admin-cancelar').style.display  = 'none';
    actualizarRequisitosAdmin('');
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
    actualizarRequisitosAdmin('');
    document.getElementById('admin-cancelar').style.display  = '';
    document.getElementById('admin-form').scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function toggleAdmin(id) {
    var csrf = document.querySelector('#admin-form input[name="_csrf"]').value;
    adminFetch('admins', { _csrf: csrf, accion: 'admin_toggle', id: id }).then(function(j){
      if (j.ok) setTimeout(function(){ location.reload(); }, 700);
    });
  }

  var ADM_REQ_CSS = 'color:var(--tsj-gray-400)';
  var ADM_OK_CSS  = 'color:#16a34a;font-weight:600';

  function actualizarRequisitosAdmin(v) {
    var box = document.getElementById('admin-pass-requisitos');
    if (!box) return;
    box.style.display = v.length > 0 ? 'flex' : 'none';
    function set(id, ok) {
      var el = document.getElementById(id);
      if (!el) return;
      el.style.cssText = ok ? ADM_OK_CSS : ADM_REQ_CSS;
      el.querySelector('.material-symbols-rounded').textContent = ok ? 'check_circle' : 'radio_button_unchecked';
    }
    set('adm-req-len', v.length >= 8);
    set('adm-req-may', /[A-Z]/.test(v));
    set('adm-req-num', /[0-9]/.test(v));
    set('adm-req-esp', /[^A-Za-z0-9]/.test(v));
  }

  document.getElementById('admin-password').addEventListener('input', function () {
    actualizarRequisitosAdmin(this.value);
  });

  function eliminarAdmin(id) {
    if (!confirm('¿Eliminar esta cuenta de administrador? No se puede deshacer.')) return;
    var csrf = document.querySelector('#admin-form input[name="_csrf"]').value;
    adminFetch('admins', { _csrf: csrf, accion: 'admin_eliminar', id: id }).then(function(j){
      if (j.ok) {
        var row = document.getElementById('admin-row-' + id);
        if (row) row.remove();
        var tbody = document.getElementById('admins-tbody');
        if (tbody && !tbody.querySelector('tr[id^="admin-row-"]')) {
          var empty = document.getElementById('admins-empty');
          if (!empty) {
            empty = document.createElement('tr');
            empty.id = 'admins-empty';
            empty.innerHTML = '<td colspan="5" class="adm-table-empty">Aún no hay cuentas. Crea la primera abajo.</td>';
            tbody.appendChild(empty);
          }
          empty.style.display = '';
        }
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

    <div class="adm-pending" style="background:#fefce8;border-color:#fde68a;color:#78350f;margin-bottom:16px">
      <span class="material-symbols-rounded">help</span>
      <span>
        <strong>¿Olvidaste el correo o la contraseña?</strong> No hay recuperación por correo — la cuenta maestra
        vive en el archivo <code>shared/config.local.php</code> del servidor. Para recuperarla:
        <ol style="margin:8px 0 0 16px;line-height:1.8">
          <li>Accede al servidor por <strong>FTP, cPanel o SSH</strong>.</li>
          <li>Abre <code>shared/config.local.php</code> y edita <code>GLOBAL_ADMIN_EMAIL</code> con el correo que quieras usar.</li>
          <li>Para generar un hash nuevo crea un archivo <code>hash.php</code> temporal en el servidor con:<br>
              <code style="display:inline-block;background:#fef9c3;padding:2px 8px;border-radius:4px;margin-top:4px">
                &lt;?php echo password_hash('TuNuevaContraseña1!', PASSWORD_BCRYPT, ['cost' =&gt; 12]);
              </code>
          </li>
          <li>Ábrelo en el navegador, copia el resultado y pégalo en <code>GLOBAL_ADMIN_HASH</code>.</li>
          <li><strong>Borra <code>hash.php</code> del servidor inmediatamente.</strong></li>
        </ol>
      </span>
    </div>
    <form data-proc="configuracion" data-accion="cambiar_password" id="form-cambiar-pass">
      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="cambiar_password">
      <div class="adm-form-grid cols-2">
        <div class="adm-field">
          <label>Correo del administrador</label>
          <input type="email" value="<?= defined('GLOBAL_ADMIN_EMAIL') ? htmlspecialchars(GLOBAL_ADMIN_EMAIL) : '' ?>" disabled>
          <span class="adm-field-help">Definido en config.local.php → GLOBAL_ADMIN_EMAIL</span>
        </div>
        <div class="adm-field"></div>
        <div class="adm-field">
          <label>Nueva contraseña <span style="color:var(--tsj-pink)">*</span></label>
          <input type="password" name="nueva_password" id="cfg-nueva-pass" placeholder="Mínimo 8 caracteres" minlength="8" required>
          <div id="cfg-pass-requisitos" style="display:none;margin-top:8px;flex-direction:column;gap:4px;font-size:12.5px">
            <span id="cfg-req-len" class="adm-req"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle">radio_button_unchecked</span> Mínimo 8 caracteres</span>
            <span id="cfg-req-may" class="adm-req"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle">radio_button_unchecked</span> Al menos una mayúscula (A–Z)</span>
            <span id="cfg-req-num" class="adm-req"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle">radio_button_unchecked</span> Al menos un número (0–9)</span>
            <span id="cfg-req-esp" class="adm-req"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle">radio_button_unchecked</span> Al menos un símbolo (!@#$…)</span>
          </div>
        </div>
        <div class="adm-field">
          <label>Confirmar contraseña <span style="color:var(--tsj-pink)">*</span></label>
          <input type="password" name="confirma_password" id="cfg-confirma-pass" placeholder="Repite la contraseña" required>
          <span class="adm-field-help" id="cfg-pass-match" style="color:var(--tsj-danger);display:none">Las contraseñas no coinciden.</span>
        </div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--warning"><span class="material-symbols-rounded">lock_reset</span> Cambiar contraseña</button>
      </div>
    </form>
    <script>
    (function () {
      var nueva    = document.getElementById('cfg-nueva-pass');
      var confirma = document.getElementById('cfg-confirma-pass');
      var aviso    = document.getElementById('cfg-pass-match');
      var form     = document.getElementById('form-cambiar-pass');
      var box      = document.getElementById('cfg-pass-requisitos');

      var OK_CSS  = 'color:#16a34a;font-weight:600';
      var REQ_CSS = 'color:var(--tsj-gray-400)';

      function setReq(id, ok) {
        var el = document.getElementById(id);
        if (!el) return;
        el.style.cssText = ok ? OK_CSS : REQ_CSS;
        el.querySelector('.material-symbols-rounded').textContent = ok ? 'check_circle' : 'radio_button_unchecked';
      }

      function validarNueva() {
        var v = nueva.value;
        box.style.display = v.length > 0 ? 'flex' : 'none';
        setReq('cfg-req-len', v.length >= 8);
        setReq('cfg-req-may', /[A-Z]/.test(v));
        setReq('cfg-req-num', /[0-9]/.test(v));
        setReq('cfg-req-esp', /[^A-Za-z0-9]/.test(v));
        validarConfirma();
      }

      function validarConfirma() {
        var mismatch = confirma.value !== '' && nueva.value !== confirma.value;
        aviso.style.display = mismatch ? '' : 'none';
        confirma.setCustomValidity(mismatch ? 'Las contraseñas no coinciden' : '');
      }

      nueva.addEventListener('input', validarNueva);
      confirma.addEventListener('input', validarConfirma);

      form.addEventListener('submit', function (e) {
        var v = nueva.value;
        var falla = v.length < 8 || !/[A-Z]/.test(v) || !/[0-9]/.test(v) || !/[^A-Za-z0-9]/.test(v);
        if (falla || nueva.value !== confirma.value) {
          e.preventDefault();
          e.stopImmediatePropagation();
          if (falla) { box.style.display = 'flex'; validarNueva(); nueva.focus(); }
          else { aviso.style.display = ''; confirma.focus(); }
        }
      }, true);
    })();
    </script>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
