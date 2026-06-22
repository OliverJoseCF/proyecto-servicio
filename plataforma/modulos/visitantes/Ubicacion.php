<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Ubicación del Campus';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
require_once __DIR__ . '/../../shared/config.php';

// Leer URLs desde BD (configuradas en admin → Configuración → Portal)
try {
    $db       = getPDO(DB_NAME);
    $cfgRows  = $db->query("SELECT clave, valor FROM configuracion WHERE clave IN ('maps_embed_url','maps_link_url','direccion','telefono','horario_atencion')")->fetchAll();
    $cfg      = [];
    foreach ($cfgRows as $r) $cfg[$r['clave']] = $r['valor'];
} catch (\Throwable $e) {
    $cfg = [];
}

$maps_embed = $cfg['maps_embed_url'] ?? (defined('MAPS_EMBED_DEFAULT') ? MAPS_EMBED_DEFAULT : '');
$maps_link  = $cfg['maps_link_url']  ?? 'https://maps.app.goo.gl/w3rApmQrocT3j5V88';
$direccion  = $cfg['direccion']      ?? 'Carretera Chapala-Jocotepec km 7.5, Ajijic, Chapala, Jalisco';
$telefono   = $cfg['telefono']       ?? '376-766-0000';
$horario    = $cfg['horario_atencion'] ?? 'Lun – Vie: 8:00 – 20:00 h';
?>

<main id="main">

  <div class="tsj-page-header">
    <div class="tsj-page-header-line"></div>
    <h1>Ubicación del <span class="tsj-accent">Campus</span></h1>
    <p class="tsj-page-header-sub">Encuéntranos en Ajijic, Chapala, Jalisco</p>
  </div>

  <div class="container" style="padding-bottom:56px">
    <div style="max-width:860px;margin:0 auto">

      <!-- Tarjetas de info -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:28px">

        <div style="background:#fff;border:1.5px solid #e8eaf2;border-radius:12px;padding:20px 22px;display:flex;align-items:flex-start;gap:14px;box-shadow:0 2px 8px rgba(51,23,156,.05)">
          <span class="material-symbols-rounded" style="color:var(--tsj-pink);font-size:26px;flex-shrink:0;margin-top:2px">location_on</span>
          <div>
            <p style="margin:0 0 4px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--tsj-gray-400)">Dirección</p>
            <p style="margin:0;font-size:14px;font-weight:600;color:#1a0960;line-height:1.4"><?= htmlspecialchars($direccion) ?></p>
          </div>
        </div>

        <div style="background:#fff;border:1.5px solid #e8eaf2;border-radius:12px;padding:20px 22px;display:flex;align-items:flex-start;gap:14px;box-shadow:0 2px 8px rgba(51,23,156,.05)">
          <span class="material-symbols-rounded" style="color:var(--tsj-pink);font-size:26px;flex-shrink:0;margin-top:2px">phone</span>
          <div>
            <p style="margin:0 0 4px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--tsj-gray-400)">Teléfono</p>
            <p style="margin:0;font-size:14px;font-weight:600;color:#1a0960"><?= htmlspecialchars($telefono) ?></p>
          </div>
        </div>

        <div style="background:#fff;border:1.5px solid #e8eaf2;border-radius:12px;padding:20px 22px;display:flex;align-items:flex-start;gap:14px;box-shadow:0 2px 8px rgba(51,23,156,.05)">
          <span class="material-symbols-rounded" style="color:var(--tsj-pink);font-size:26px;flex-shrink:0;margin-top:2px">schedule</span>
          <div>
            <p style="margin:0 0 4px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--tsj-gray-400)">Horario de atención</p>
            <p style="margin:0;font-size:14px;font-weight:600;color:#1a0960"><?= htmlspecialchars($horario) ?></p>
          </div>
        </div>

      </div>

      <!-- Mapa -->
      <div style="border-radius:14px;overflow:hidden;border:1.5px solid #e8eaf2;box-shadow:0 4px 20px rgba(51,23,156,.10);margin-bottom:20px">
        <?php if ($maps_embed): ?>
          <iframe
            src="<?= htmlspecialchars($maps_embed) ?>"
            width="100%" height="420"
            style="border:0;display:block"
            allowfullscreen
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Mapa: Tecnológico Superior de Jalisco, Campus Chapala">
          </iframe>
        <?php else: ?>
          <div style="height:420px;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#f8f9ff;gap:12px">
            <span class="material-symbols-rounded" style="font-size:48px;color:var(--tsj-gray-300)">map</span>
            <p style="margin:0;color:var(--tsj-gray-400);font-size:14px">Mapa no configurado</p>
            <p style="margin:0;color:var(--tsj-gray-300);font-size:12px">Configúralo en Admin → Configuración → Portal → Embed de Google Maps</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Botón Google Maps -->
      <div style="display:flex;gap:12px;align-items:center;justify-content:center;flex-wrap:wrap">
        <a href="<?= htmlspecialchars($maps_link) ?>" target="_blank" rel="noopener noreferrer"
           style="display:inline-flex;align-items:center;gap:8px;background:var(--tsj-blue);color:#fff;padding:11px 22px;border-radius:9px;text-decoration:none;font-size:14px;font-weight:700;font-family:var(--tsj-font,'Poppins',sans-serif);transition:background .2s,transform .18s;box-shadow:0 3px 12px rgba(51,23,156,.25)"
           onmouseover="this.style.background='#1a0960';this.style.transform='translateY(-1px)'"
           onmouseout="this.style.background='var(--tsj-blue)';this.style.transform=''">
          <span class="material-symbols-rounded" style="font-size:18px">open_in_new</span>
          Abrir en Google Maps
        </a>
        <a href="<?= PLATAFORMA_URL ?>/modulos/visitantes/Directorio.php"
           style="display:inline-flex;align-items:center;gap:8px;color:var(--tsj-blue);font-size:13px;font-weight:600;text-decoration:none;padding:10px 20px;border:1.5px solid #e8eaf2;border-radius:9px;background:#fff;font-family:var(--tsj-font,'Poppins',sans-serif);transition:border-color .2s"
           onmouseover="this.style.borderColor='#33179c'"
           onmouseout="this.style.borderColor='#e8eaf2'">
          ← Volver al Directorio
        </a>
      </div>

    </div>
  </div>

</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
