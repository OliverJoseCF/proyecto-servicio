<?php
if (!defined('PLATAFORMA_URL')) {
    require_once __DIR__ . '/config.php';
}
$base = PLATAFORMA_URL;
?>
<footer class="tsj-footer">

  <!-- Franja oscura: logos principales -->
  <div class="tsj-footer-dark">
    <div class="tsj-footer-dark-inner">
      <p class="tsj-footer-motto">Innovar para transformar a México</p>
      <div class="tsj-footer-logos-dark">
        <img src="<?= $base ?>/shared/assets/img/logo.svg"
             alt="Tecnológico Superior de Jalisco" loading="lazy" />
        <img src="<?= $base ?>/shared/assets/img/tecnologico.svg"
             alt="Tecnológico Nacional de México" loading="lazy" />
        <img src="<?= $base ?>/shared/assets/img/jalisco.png"
             alt="Gobierno del Estado de Jalisco" loading="lazy" />
      </div>
    </div>
  </div>

  <!-- Franja blanca: logos institucionales secundarios -->
  <div class="tsj-footer-white">
    <div class="tsj-footer-white-inner">
      <img src="<?= $base ?>/shared/assets/img/educacion.png"
           alt="Secretaría de Educación Pública" loading="lazy" />
      <img src="<?= $base ?>/shared/assets/img/innovacion.png"
           alt="Innovación, Ciencia y Tecnología" loading="lazy" />
    </div>
  </div>

</footer>

<script src="<?= $base ?>/shared/assets/js/nav.js" defer></script>
</body>
</html>
