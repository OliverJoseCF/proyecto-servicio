<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
require_once dirname(__DIR__) . '/shared/config.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'configuracion';
$adm_title = 'Configuración';

// Cargar valores actuales desde BD
$cfg = [];
try {
    $db   = getPDO(DB_NAME);
    $rows = $db->query('SELECT clave, valor FROM configuracion')->fetchAll();
    foreach ($rows as $r) $cfg[$r['clave']] = $r['valor'];
    $db_ok = true;
} catch (\Throwable $e) {
    $db_ok = false;
}

function cfgVal(string $key, string $default = ''): string {
    global $cfg;
    return htmlspecialchars($cfg[$key] ?? $default);
}

$csrf = getCsrfToken();
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Configuración General</h1>
    <p class="adm-page-desc">Correos del sistema, redes sociales, footer y datos de contacto del portal.</p>
  </div>
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
    <form data-proc="configuracion" data-accion="guardar_config">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="guardar_config">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Nombre de la institución</label><input type="text" name="nombre_institucion" value="<?= cfgVal('nombre_institucion','Tecnológico Superior de Jalisco') ?>" required></div>
        <div class="adm-field"><label>Campus</label><input type="text" name="campus" value="<?= cfgVal('campus','Campus Chapala') ?>"></div>
        <div class="adm-field" style="grid-column:1/-1"><label>Descripción meta del portal</label><textarea name="descripcion_portal"><?= cfgVal('descripcion_portal','Portal de servicios estudiantiles del Tecnológico Superior de Jalisco — Chapala.') ?></textarea></div>
        <div class="adm-field"><label>URL base de la plataforma</label><input type="text" name="plataforma_url" value="<?= cfgVal('plataforma_url','/plataforma') ?>"></div>
        <div class="adm-field"><label>Eslogan (footer)</label><input type="text" name="eslogan" value="<?= cfgVal('eslogan','Innovar para transformar a México') ?>"></div>
      </div>
      <div class="adm-form-actions">
        <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar</button>
      </div>
    </form>
  </div>

  <div class="adm-form-card">
    <div class="adm-form-title"><span class="material-symbols-rounded">location_on</span> Dirección y contacto (footer)</div>
    <form data-proc="configuracion" data-accion="guardar_config">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="guardar_config">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Dirección</label><input type="text" name="direccion" value="<?= cfgVal('direccion','Carretera Chapala-Jocotepec km 7.5, Ajijic, Chapala, Jalisco') ?>"></div>
        <div class="adm-field"><label>Correo general de contacto</label><input type="email" name="correo_general" value="<?= cfgVal('correo_general','campus.chapala@tsj.edu.mx') ?>"></div>
        <div class="adm-field"><label>Teléfono</label><input type="tel" name="telefono" value="<?= cfgVal('telefono','376-766-0000') ?>"></div>
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
    <form data-proc="configuracion" data-accion="guardar_correos">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="guardar_correos">
      <div class="adm-form-grid cols-2">
        <?php
        $correos = [
          ['correo_general',     'Correo general de contacto',    'campus.chapala@tsj.edu.mx'],
          ['correo_biblioteca',  'Biblioteca',                     'biblioteca@chapala.tecmm.edu.mx'],
          ['correo_vinculacion', 'Vinculación / Convenios',        'vinculacion@chapala.tecmm.edu.mx'],
          ['correo_facturacion', 'Facturación / Finanzas',         'facturacion@chapala.tecmm.edu.mx'],
          ['correo_escolares',   'Control escolar',                'escolares@chapala.tecmm.edu.mx'],
          ['correo_direccion',   'Dirección',                      'IlianaJanettHernandezPartida@chapala.tecmm.edu.mx'],
          ['correo_servicios',   'Servicios generales',            'servicios@chapala.tecmm.edu.mx'],
        ];
        foreach ($correos as $c): ?>
        <div class="adm-field">
          <label><?= htmlspecialchars($c[1]) ?></label>
          <input type="email" name="<?= $c[0] ?>" value="<?= cfgVal($c[0], $c[2]) ?>">
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
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="accion" value="guardar_redes">
      <div class="adm-form-grid cols-2">
        <div class="adm-field"><label>Facebook</label><input type="url" name="facebook_url" value="<?= cfgVal('facebook_url','https://www.facebook.com/TecSJ/') ?>"></div>
        <div class="adm-field"><label>YouTube</label><input type="url" name="youtube_url" value="<?= cfgVal('youtube_url','https://www.youtube.com/@TecSuperiorJalisco') ?>"></div>
        <div class="adm-field"><label>Instagram</label><input type="url" name="instagram_url" value="<?= cfgVal('instagram_url') ?>" placeholder="https://instagram.com/…"></div>
        <div class="adm-field"><label>Twitter / X</label><input type="url" name="twitter_url" value="<?= cfgVal('twitter_url') ?>" placeholder="https://twitter.com/…"></div>
        <div class="adm-field"><label>LinkedIn</label><input type="url" name="linkedin_url" value="<?= cfgVal('linkedin_url') ?>" placeholder="https://linkedin.com/…"></div>
        <div class="adm-field"><label>Sitio web oficial</label><input type="url" name="sitio_oficial_url" value="<?= cfgVal('sitio_oficial_url','https://www.tecmm.edu.mx') ?>"></div>
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
    <div class="adm-form-title"><span class="material-symbols-rounded">lock</span> Cambiar contraseña del administrador</div>
    <div class="adm-pending" style="margin-bottom:16px">
      <span class="material-symbols-rounded">info</span>
      El nuevo hash se escribe automáticamente en <strong>shared/config.local.php</strong>.
    </div>
    <form data-proc="configuracion" data-accion="cambiar_password">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
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
