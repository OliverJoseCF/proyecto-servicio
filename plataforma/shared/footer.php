<?php
if (!defined('PLATAFORMA_URL')) {
    require_once __DIR__ . '/config.php';
}
$base = PLATAFORMA_URL;
?>
<style>
/* ── Footer TECMM-style ──────────────────────────────────── */

/* Neutraliza estilos heredados de theme.css */
.tsj-footer {
  background: none !important;
  padding: 0 !important;
  font-family: 'Poppins', Arial, sans-serif;
}
.tsj-footer::before {
  display: none !important;
}

/* Franja oscura: motto + 3 logos principales */
.tsj-footer-dark {
  background: #2a2d36;
  padding: 28px 40px;
}
.tsj-footer-dark-inner {
  max-width: 1100px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
}
.tsj-footer-motto {
  color: rgba(255,255,255,.9);
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  text-align: center;
  margin: 0;
}
.tsj-footer-logos-dark {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 60px;
  flex-wrap: wrap;
  width: 100%;
}
.tsj-footer-logos-dark img {
  height: 52px;
  width: auto;
  object-fit: contain;
  filter: brightness(0) invert(1);
  opacity: .85;
  transition: opacity .2s;
}
.tsj-footer-logos-dark img:hover { opacity: 1; }

/* Franja blanca: logos institucionales secundarios */
.tsj-footer-white {
  background: #ffffff;
  padding: 20px 40px;
  border-top: 1px solid #e5e7eb;
}
.tsj-footer-white-inner {
  max-width: 1100px;
  margin: 0 auto;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 60px;
  flex-wrap: wrap;
}
.tsj-footer-white-inner img {
  height: 42px;
  width: auto;
  object-fit: contain;
  opacity: .85;
  transition: opacity .2s;
}
.tsj-footer-white-inner img:hover { opacity: 1; }

/* Responsive */
@media (max-width: 640px) {
  .tsj-footer-dark { padding: 24px 20px; }
  .tsj-footer-white { padding: 18px 20px; }
  .tsj-footer-logos-dark { gap: 28px; }
  .tsj-footer-white-inner { gap: 28px; }
  .tsj-footer-logos-dark img { height: 40px; }
  .tsj-footer-white-inner img { height: 32px; }
  .tsj-footer-motto { font-size: 12px; letter-spacing: 1.2px; }
}
</style>

<footer class="tsj-footer" role="contentinfo">

  <!-- Franja oscura -->
  <div class="tsj-footer-dark">
    <div class="tsj-footer-dark-inner">
      <p class="tsj-footer-motto">Innovar para transformar a México</p>
      <div class="tsj-footer-logos-dark">
        <img src="<?= $base ?>/shared/assets/img/logo.svg"
             alt="Tecnologico Superior de Jalisco" loading="lazy" />
        <img src="<?= $base ?>/shared/assets/img/tecnologico.svg"
             alt="Tecnologico Nacional de Mexico" loading="lazy" />
        <img src="<?= $base ?>/shared/assets/img/jalisco.png"
             alt="Jalisco Gobierno del Estado" loading="lazy" />
      </div>
    </div>
  </div>

  <!-- Franja blanca -->
  <div class="tsj-footer-white">
    <div class="tsj-footer-white-inner">
      <img src="<?= $base ?>/shared/assets/img/educacion.png"
           alt="Secretaria de Educacion Publica" loading="lazy" />
      <img src="<?= $base ?>/shared/assets/img/innovacion.png"
           alt="Innovacion Ciencia y Tecnologia" loading="lazy" />
    </div>
  </div>

</footer>

<script src="<?= $base ?>/shared/assets/js/nav.js"></script>
</body>
</html>
