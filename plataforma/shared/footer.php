<?php
if (!defined('PLATAFORMA_URL')) {
    require_once __DIR__ . '/config.php';
}
if (!function_exists('isGlobalAdmin')) {
    require_once __DIR__ . '/lib/auth.php';
}
require_once __DIR__ . '/lib/config_data.php';
$base = PLATAFORMA_URL;

// Datos editables desde Admin → Configuración (con fallback si la BD no responde)
$f_eslogan   = tsjConfig('eslogan',          'Innovar para transformar a México');
$f_institu   = tsjConfig('nombre_institucion','Tecnológico Superior de Jalisco');
$f_campus    = tsjConfig('campus',           'Campus Chapala');
$f_desc      = tsjConfig('descripcion_portal',
    'Portal de servicios estudiantiles — consulta de convenios, biblioteca, horarios, requisitos y registro de visitantes.');
$f_direccion = tsjConfig('direccion',        'Carretera Chapala-Jocotepec km 7.5, Ajijic, Chapala, Jalisco');
$f_correo    = tsjConfig('correo_general',   'campus.chapala@tsj.edu.mx');
$f_horario   = tsjConfig('horario_atencion', 'Lun – Vie: 8:00 – 20:00 h');
$f_sitio     = tsjConfig('sitio_oficial_url','https://www.tecmm.edu.mx');
$f_redes     = tsjRedesSociales();

// Correos departamentales (solo mostrar los que tengan valor en BD)
$f_correos_dep = array_filter([
    'Biblioteca'      => tsjConfig('correo_biblioteca',  ''),
    'Vinculación'     => tsjConfig('correo_vinculacion', ''),
    'Control Escolar' => tsjConfig('correo_escolares',   ''),
    'Dirección'       => tsjConfig('correo_direccion',   ''),
    'Facturación'     => tsjConfig('correo_facturacion', ''),
    'Servicios'       => tsjConfig('correo_servicios',   ''),
]);

// Mapa módulo → página de admin
$_tsj_admin_links = [
    'visitantes'  => $base . '/admin/visitantes.php',
    'biblioteca'  => $base . '/admin/biblioteca.php',
    'convenios'   => $base . '/admin/convenios.php',
    'horarios'    => $base . '/admin/horarios.php',
    'requisitos'  => $base . '/admin/requisitos.php',
    'inscripcion' => $base . '/admin/visitantes.php',
];
$nav_items = $nav_items ?? [
    'visitantes' => ['label' => 'Directorio',               'href' => $base . '/modulos/visitantes/Directorio.php',  'icon' => 'contacts'],
    'biblioteca' => ['label' => 'Biblioteca',               'href' => $base . '/modulos/biblioteca/buscar.php',      'icon' => 'menu_book'],
    'convenios'  => ['label' => 'Convenios',                'href' => $base . '/modulos/convenios/index.php',        'icon' => 'handshake'],
    'horarios'   => ['label' => 'Buscar Maestro',           'href' => $base . '/modulos/horarios/index.php',         'icon' => 'manage_search'],
    'requisitos' => ['label' => 'Serv. Social / Residencia','href' => $base . '/modulos/requisitos/residencia.php',  'icon' => 'checklist'],
    'inscripcion'=> ['label' => 'Inscripción',              'href' => $base . '/modulos/visitantes/nuevoIngreso.php','icon' => 'school'],
];
?>
<?php if (isGlobalAdmin() && isset($tsj_module, $_tsj_admin_links[$tsj_module])): ?>
<!-- Botón flotante de admin (solo visible en sesión admin) -->
<a href="<?= $_tsj_admin_links[$tsj_module] ?>"
   class="tsj-fab-admin"
   title="Configurar este módulo (Admin)">
  <span class="material-symbols-rounded" aria-hidden="true">tune</span>
  <span class="tsj-fab-admin-label">Configurar</span>
</a>
<?php endif; ?>

<footer class="tsj-footer" aria-label="Pie de página institucional">

  <!-- ── Sección principal: 3 columnas ────────────────────── -->
  <div class="tsj-footer-main">
    <div class="tsj-footer-inner">

      <!-- Col 1: Identidad de marca -->
      <div class="tsj-footer-brand-col">
        <a href="<?= $base ?>/" aria-label="Inicio del portal">
          <img class="tsj-footer-logo-img"
               src="<?= $base ?>/shared/assets/img/logo.svg"
               alt="Tecnológico Superior de Jalisco" loading="lazy" />
        </a>
        <p class="tsj-footer-tagline"><?= htmlspecialchars($f_eslogan, ENT_QUOTES, 'UTF-8') ?></p>
        <p class="tsj-footer-name"><?= htmlspecialchars($f_institu, ENT_QUOTES, 'UTF-8') ?></p>
        <p class="tsj-footer-campus"><?= htmlspecialchars($f_campus, ENT_QUOTES, 'UTF-8') ?></p>
        <div class="tsj-footer-divider" aria-hidden="true"></div>
        <p class="tsj-footer-desc">
          <?= htmlspecialchars($f_desc, ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <!-- Col 2: Módulos del portal -->
      <div class="tsj-footer-links-col">
        <p class="tsj-footer-col-title">Módulos</p>
        <ul class="tsj-footer-links">
          <?php foreach ($nav_items as $item): ?>
            <li>
              <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Col 3: Contacto y redes -->
      <div class="tsj-footer-contact-col">
        <p class="tsj-footer-col-title">Contacto</p>

        <div class="tsj-footer-contact-item">
          <span class="material-symbols-rounded tsj-fi" aria-hidden="true">location_on</span>
          <span><?= nl2br(htmlspecialchars($f_direccion, ENT_QUOTES, 'UTF-8')) ?></span>
        </div>

        <div class="tsj-footer-contact-item">
          <span class="material-symbols-rounded tsj-fi" aria-hidden="true">mail</span>
          <span><a href="mailto:<?= htmlspecialchars($f_correo, ENT_QUOTES, 'UTF-8') ?>" style="color:inherit"><?= htmlspecialchars($f_correo, ENT_QUOTES, 'UTF-8') ?></a></span>
        </div>

        <div class="tsj-footer-contact-item">
          <span class="material-symbols-rounded tsj-fi" aria-hidden="true">schedule</span>
          <span><?= htmlspecialchars($f_horario, ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <?php if (!empty($f_correos_dep)): ?>
        <p class="tsj-footer-social-title" style="margin-top:14px">Contacto por área</p>
        <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:4px">
          <?php foreach ($f_correos_dep as $area => $email): ?>
          <li style="font-size:12px;color:var(--tsj-footer-muted,#94a3b8)">
            <span style="font-weight:600;color:var(--tsj-footer-text,#cbd5e1)"><?= htmlspecialchars($area, ENT_QUOTES, 'UTF-8') ?>:</span>
            <a href="mailto:<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
               style="color:inherit;text-decoration:none;word-break:break-all"
               onmouseover="this.style.textDecoration='underline'"
               onmouseout="this.style.textDecoration='none'">
              <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <?php if (!empty($f_redes)): ?>
        <p class="tsj-footer-social-title" style="margin-top:14px">Redes sociales</p>
        <div class="tsj-footer-social">
          <?php foreach ($f_redes as $red): ?>
          <a href="<?= htmlspecialchars($red['url'], ENT_QUOTES, 'UTF-8') ?>"
             target="_blank" rel="noopener noreferrer"
             aria-label="<?= htmlspecialchars($red['label'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($f_institu, ENT_QUOTES, 'UTF-8') ?>"
             title="<?= htmlspecialchars($red['label'], ENT_QUOTES, 'UTF-8') ?>">
            <?= $red['svg'] ?>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <!-- ── Banda de logos principales (fondo oscuro) ─────────── -->
  <div class="tsj-footer-logos-band">
    <div class="tsj-footer-logos-inner">
      <img src="<?= $base ?>/shared/assets/img/logo.svg"
           alt="Tecnológico Superior de Jalisco" loading="lazy" />
      <img src="<?= $base ?>/shared/assets/img/tecnologico.svg"
           alt="Tecnológico Nacional de México" loading="lazy" />
      <img src="<?= $base ?>/shared/assets/img/jalisco.png"
           alt="Gobierno del Estado de Jalisco" loading="lazy" />
    </div>
  </div>

  <!-- ── Banda de logos de gobierno (fondo blanco) ─────────── -->
  <div class="tsj-footer-logos-gov">
    <div class="tsj-footer-logos-gov-inner">
      <img src="<?= $base ?>/shared/assets/img/educacion.png"
           alt="Secretaría de Educación Pública" loading="lazy" />
      <img src="<?= $base ?>/shared/assets/img/innovacion.png"
           alt="Innovación, Ciencia y Tecnología Jalisco" loading="lazy" />
    </div>
  </div>

  <!-- ── Barra de copyright ────────────────────────────────── -->
  <div class="tsj-footer-copy">
    <p>
      © <?= date('Y') ?> <?= htmlspecialchars($f_institu, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($f_campus, ENT_QUOTES, 'UTF-8') ?>.
      Todos los derechos reservados.
      <a href="<?= htmlspecialchars($f_sitio, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><?= htmlspecialchars(preg_replace('#^https?://(www\.)?#', '', $f_sitio), ENT_QUOTES, 'UTF-8') ?></a>
    </p>
  </div>

</footer>

<script src="<?= $base ?>/shared/assets/js/nav.js" defer></script>
</body>
</html>
