<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'configuracion';
$adm_title = 'Configuración';
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Configuración General</h1>
    <p class="adm-page-desc">Correos del sistema, redes sociales, footer y datos de contacto del portal.</p>
  </div>
</div>
<div class="adm-pending">
  <span class="material-symbols-rounded">construction</span>
  Los cambios se aplicarán al integrar la base de datos y actualizar los archivos de configuración.
</div>

<div class="adm-tabs">
  <?php foreach (['portal'=>'Información del portal','correos'=>'Correos del sistema','redes'=>'Redes sociales','seguridad'=>'Seguridad y acceso'] as $k=>$l): ?>
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
    <form class="pending-db">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Nombre de la institución</label><input type="text" value="Tecnológico Superior de Jalisco"></div>
        <div class="adm-field"><label>Campus</label><input type="text" value="Campus Chapala"></div>
        <div class="adm-field adm-form-grid" style="grid-column:1/-1"><label>Descripción meta del portal</label><textarea>Portal de servicios estudiantiles del Tecnológico Superior de Jalisco — Chapala.</textarea></div>
        <div class="adm-field"><label>URL base de la plataforma</label><input type="text" value="/plataforma"></div>
        <div class="adm-field"><label>Eslogan (footer)</label><input type="text" value="Innovar para transformar a México"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
      </div>
    </form>
  </div>

  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">location_on</span> Dirección y contacto (footer)</div>
    <form class="pending-db">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Dirección</label><input type="text" value="Carretera Chapala-Jocotepec km 7.5, Ajijic, Chapala, Jalisco"></div>
        <div class="adm-field"><label>Correo general de contacto</label><input type="email" value="campus.chapala@tsj.edu.mx"></div>
        <div class="adm-field"><label>Teléfono</label><input type="tel" value="376-766-0000"></div>
        <div class="adm-field"><label>Horario de atención</label><input type="text" value="Lun – Vie: 8:00 – 20:00 h"></div>
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
    <form class="pending-db">
      <div class="adm-form-grid cols-2">
        <?php
        $correos = [
          ['Administrador general (login)',  'admin@chapala.tecmm.edu.mx',           'GLOBAL_ADMIN_EMAIL'],
          ['Biblioteca',                     'biblioteca@chapala.tecmm.edu.mx',      'BIBLIOTECA_EMAIL'],
          ['Vinculación / Convenios',        'vinculacion@chapala.tecmm.edu.mx',     'VINCULACION_EMAIL'],
          ['Facturación / Finanzas',         'facturacion@chapala.tecmm.edu.mx',     'FINANZAS_EMAIL'],
          ['Control escolar',                'escolares@chapala.tecmm.edu.mx',       'ESCOLARES_EMAIL'],
          ['Dirección',                      'IlianaJanettHernandezPartida@chapala.tecmm.edu.mx', 'DIRECCION_EMAIL'],
          ['Servicios generales',            'servicios@chapala.tecmm.edu.mx',       'SERVICIOS_EMAIL'],
        ];
        foreach ($correos as $c): ?>
        <div class="adm-field">
          <label><?= htmlspecialchars($c[0]) ?> <code style="font-size:10px;background:var(--tsj-gray-100);padding:1px 5px;border-radius:3px"><?= htmlspecialchars($c[2]) ?></code></label>
          <input type="email" value="<?= htmlspecialchars($c[1]) ?>">
        </div>
        <?php endforeach; ?>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar correos</button>
      </div>
    </form>
  </div>

  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">contacts</span> Correos de coordinadores por carrera</div>
    <form class="pending-db">
      <div class="adm-form-grid cols-2">
        <?php
        $cords = [
          ['Coordinador ISC',       'claudio.castillo@chapala.tecmm.edu.mx'],
          ['Coordinador Gestión',   'pablo.rojas@chapala.tecmm.edu.mx'],
          ['Coordinador Mecatrónica','ivan@chapala.tecmm.edu.mx'],
          ['Coordinador Animación', 'coord.animacion@chapala.tecmm.edu.mx'],
          ['Coordinador Industrial','leonardo@chapala.tecmm.edu.mx'],
          ['Coordinador Gastronomía','coord.gastronomia@chapala.tecmm.edu.mx'],
        ];
        foreach ($cords as $c): ?>
        <div class="adm-field"><label><?= htmlspecialchars($c[0]) ?></label><input type="email" value="<?= htmlspecialchars($c[1]) ?>"></div>
        <?php endforeach; ?>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ Redes sociales ════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="cfg" data-tab="redes">
  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">share</span> Redes sociales del campus</div>
    <form class="pending-db">
      <div class="adm-form-grid cols-2">
        <div class="adm-field">
          <label><span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">thumb_up</span> Facebook</label>
          <input type="url" value="https://www.facebook.com/TecSJ/">
        </div>
        <div class="adm-field">
          <label><span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">smart_display</span> YouTube</label>
          <input type="url" value="https://www.youtube.com/@TecSuperiorJalisco">
        </div>
        <div class="adm-field">
          <label><span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">photo_camera</span> Instagram</label>
          <input type="url" placeholder="https://instagram.com/…">
        </div>
        <div class="adm-field">
          <label><span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">alternate_email</span> Twitter / X</label>
          <input type="url" placeholder="https://twitter.com/…">
        </div>
        <div class="adm-field">
          <label><span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">work</span> LinkedIn</label>
          <input type="url" placeholder="https://linkedin.com/…">
        </div>
        <div class="adm-field">
          <label><span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">language</span> Sitio web oficial</label>
          <input type="url" value="https://www.tecmm.edu.mx">
        </div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar redes</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ Seguridad y acceso ════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="cfg" data-tab="seguridad">
  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">lock</span> Credenciales del administrador global</div>
    <div class="adm-pending" style="margin-bottom:16px">
      <span class="material-symbols-rounded">info</span>
      Para cambiar la contraseña, genera un nuevo hash bcrypt con:<br>
      <code style="font-family:monospace;font-size:12px;display:block;margin-top:6px;background:rgba(0,0,0,.06);padding:8px 12px;border-radius:6px">
        C:\xampp\php\php.exe -r "echo password_hash('nueva_clave', PASSWORD_BCRYPT, ['cost'=>12]);"
      </code>
      Y cópialo en <strong>shared/config.local.php</strong> → <code>GLOBAL_ADMIN_HASH</code>.
    </div>
    <form class="pending-db">
      <div class="adm-form-grid cols-2">
        <div class="adm-field">
          <label>Correo del administrador</label>
          <input type="email" value="admin@chapala.tecmm.edu.mx">
          <span class="adm-field-help">Definido en GLOBAL_ADMIN_EMAIL (config.local.php)</span>
        </div>
        <div class="adm-field">
          <label>Nueva contraseña</label>
          <input type="password" placeholder="Mínimo 12 caracteres">
        </div>
        <div class="adm-field">
          <label>Confirmar nueva contraseña</label>
          <input type="password" placeholder="Repite la contraseña">
        </div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--warning"><span class="material-symbols-rounded">lock_reset</span> Cambiar contraseña</button>
      </div>
    </form>
  </div>

  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">security</span> Configuración de seguridad</div>
    <form class="pending-db">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Máx. intentos de login antes de bloqueo</label><input type="number" value="5" min="1" max="20"></div>
        <div class="adm-field"><label>Tiempo de bloqueo (minutos)</label><input type="number" value="15" min="5"></div>
        <div class="adm-field"><label>Timeout de sesión inactiva (minutos)</label><input type="number" value="60" min="15"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
