<?php
if (!defined('PLATAFORMA_URL')) {
    require_once __DIR__ . '/config.php';
}
if (!function_exists('isGlobalAdmin')) {
    require_once __DIR__ . '/lib/auth.php';
}
$base = PLATAFORMA_URL;

// Mapa módulo → página de admin
$_tsj_admin_links = [
    'visitantes' => $base . '/admin/visitantes.php',
    'biblioteca' => $base . '/admin/biblioteca.php',
    'convenios'  => $base . '/admin/convenios.php',
    'horarios'   => $base . '/admin/horarios.php',
    'requisitos' => $base . '/admin/requisitos.php',
];
$nav_items = $nav_items ?? [
    'visitantes' => ['label' => 'Visitantes',  'href' => $base . '/modulos/visitantes/index.php'],
    'biblioteca' => ['label' => 'Biblioteca',   'href' => $base . '/modulos/biblioteca/buscar.php'],
    'convenios'  => ['label' => 'Convenios',    'href' => $base . '/modulos/convenios/index.php'],
    'horarios'   => ['label' => 'Horarios',     'href' => $base . '/modulos/horarios/index.php'],
    'requisitos' => ['label' => 'Requisitos',   'href' => $base . '/modulos/requisitos/residencia.php'],
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
        <p class="tsj-footer-tagline">Innovar para transformar a México</p>
        <p class="tsj-footer-name">Tecnológico Superior de Jalisco</p>
        <p class="tsj-footer-campus">Campus Chapala</p>
        <div class="tsj-footer-divider" aria-hidden="true"></div>
        <p class="tsj-footer-desc">
          Portal de servicios estudiantiles — consulta de convenios,
          biblioteca, horarios, requisitos y registro de visitantes.
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
          <span>Carretera Chapala‑Jocotepec km 7.5,<br>Ajijic, Chapala, Jalisco</span>
        </div>

        <div class="tsj-footer-contact-item">
          <span class="material-symbols-rounded tsj-fi" aria-hidden="true">mail</span>
          <span>campus.chapala<br>@tsj.edu.mx</span>
        </div>

        <div class="tsj-footer-contact-item">
          <span class="material-symbols-rounded tsj-fi" aria-hidden="true">schedule</span>
          <span>Lun – Vie: 8:00 – 20:00 h</span>
        </div>

        <p class="tsj-footer-social-title">Redes sociales</p>
        <div class="tsj-footer-social">
          <a href="https://www.facebook.com/TecSJ/" target="_blank" rel="noopener noreferrer"
             aria-label="Facebook del Tecnológico Superior de Jalisco">
            <img src="<?= $base ?>/shared/assets/img/facebook.svg"
                 alt="Facebook" loading="lazy" />
          </a>
          <a href="https://www.youtube.com/@TecSuperiorJalisco" target="_blank" rel="noopener noreferrer"
             aria-label="YouTube del Tecnológico Superior de Jalisco">
            <img src="<?= $base ?>/shared/assets/img/youtube.svg"
                 alt="YouTube" loading="lazy" />
          </a>
        </div>
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
      © <?= date('Y') ?> Tecnológico Superior de Jalisco — Campus Chapala.
      Todos los derechos reservados.
      <a href="https://www.tecmm.edu.mx" target="_blank" rel="noopener">tecmm.edu.mx</a>
    </p>
  </div>

</footer>

<script src="<?= $base ?>/shared/assets/js/nav.js" defer></script>
</body>
</html>
