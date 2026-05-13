<?php
if (!defined('PLATAFORMA_URL')) {
    require_once __DIR__ . '/config.php';
}
$base = PLATAFORMA_URL;
?>
<style>
.tsj-footer {
  background: #1f2128;
  padding: 36px 24px 20px;
  font-family: 'Poppins', Arial, sans-serif;
  position: relative;
  border-top: 3px solid #ec5a68;
}
.tsj-footer-inner {
  display: grid;
  grid-template-columns: 1.6fr 1fr 1fr;
  gap: 40px;
  max-width: 1100px;
  margin: 0 auto 24px;
  align-items: start;
}
/* Col 1 */
.tsj-footer-logo {
  width: 150px;
  height: auto;
  display: block;
  margin-bottom: 8px;
  filter: brightness(0) invert(1);
  opacity: .9;
}
.tsj-footer-tagline {
  color: rgba(255,255,255,.45);
  font-size: 12px;
  line-height: 1.6;
  margin: 0 0 14px;
}
.tsj-footer-social {
  display: flex;
  gap: 8px;
}
.tsj-social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: 6px;
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  transition: background .2s, transform .15s;
  text-decoration: none;
}
.tsj-social-btn:hover {
  background: #ec5a68;
  transform: translateY(-2px);
}
.tsj-social-btn img {
  width: 17px;
  height: 17px;
  filter: brightness(0) invert(1);
}
/* Cols 2–3 */
.tsj-footer-heading {
  color: rgba(255,255,255,.9);
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.8px;
  margin: 0 0 12px;
  display: block;
}
.tsj-footer-col nav {
  display: flex;
  flex-direction: column;
  gap: 7px;
}
.tsj-footer-link {
  color: rgba(255,255,255,.52);
  text-decoration: none;
  font-size: 13px;
  transition: color .18s;
}
.tsj-footer-link:hover { color: #ec5a68; }
.tsj-footer-copy {
  color: rgba(255,255,255,.28);
  font-size: 11px;
  line-height: 1.6;
  margin: 14px 0 0;
}
/* Divisor */
.tsj-footer-divider {
  max-width: 1100px;
  margin: 0 auto 16px;
  height: 1px;
  background: rgba(255,255,255,.08);
}
/* Logos */
.tsj-footer-gov-inner {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 28px;
  max-width: 1100px;
  margin: 0 auto;
  padding-bottom: 4px;
}
.tsj-footer-gov-inner img {
  height: 34px;
  width: auto;
  object-fit: contain;
  filter: brightness(0) invert(1);
  opacity: .45;
  transition: opacity .2s;
}
.tsj-footer-gov-inner img:hover { opacity: .75; }
/* Responsive */
@media (max-width: 860px) {
  .tsj-footer-inner {
    grid-template-columns: 1fr 1fr;
    gap: 28px;
  }
  .tsj-footer-col--brand { grid-column: 1 / -1; }
}
@media (max-width: 520px) {
  .tsj-footer-inner {
    grid-template-columns: 1fr;
    gap: 22px;
    text-align: center;
  }
  .tsj-footer-social { justify-content: center; }
  .tsj-footer-col nav { align-items: center; }
  .tsj-footer-logo { margin: 0 auto 8px; }
  .tsj-footer-gov-inner { gap: 16px; }
}
</style>

<footer class="tsj-footer" role="contentinfo">
  <div class="tsj-footer-inner">

    <div class="tsj-footer-col tsj-footer-col--brand">
      <img src="<?= $base ?>/shared/assets/img/logo.svg" alt="Tecnológico Superior de Jalisco" class="tsj-footer-logo" loading="lazy" />
      <p class="tsj-footer-tagline">Tecnológico Superior de Jalisco · Campus Chapala</p>
      <div class="tsj-footer-social">
        <a href="https://www.facebook.com/TecSJ" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="tsj-social-btn">
          <img src="<?= $base ?>/shared/assets/img/facebook.svg" alt="" />
        </a>
        <a href="https://www.youtube.com/@TecSJ" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="tsj-social-btn">
          <img src="<?= $base ?>/shared/assets/img/youtube.svg" alt="" />
        </a>
      </div>
    </div>

    <div class="tsj-footer-col">
      <span class="tsj-footer-heading">Módulos</span>
      <nav aria-label="Módulos">
        <a class="tsj-footer-link" href="<?= $base ?>/">Portal</a>
        <a class="tsj-footer-link" href="<?= $base ?>/modulos/visitantes/index.php">Visitantes</a>
        <a class="tsj-footer-link" href="<?= $base ?>/modulos/biblioteca/buscar.php">Biblioteca</a>
        <a class="tsj-footer-link" href="<?= $base ?>/modulos/convenios/index.php">Convenios</a>
        <a class="tsj-footer-link" href="<?= $base ?>/modulos/horarios/index.php">Horarios</a>
        <a class="tsj-footer-link" href="<?= $base ?>/modulos/requisitos/residencia.php">Requisitos</a>
      </nav>
    </div>

    <div class="tsj-footer-col">
      <span class="tsj-footer-heading">Información</span>
      <nav aria-label="Información">
        <a class="tsj-footer-link"
           href="https://consultapublicamx.plataformadetransparencia.org.mx/vut-web/faces/view/consultaPublica.xhtml?idEntidad=MTQ=&idSujetoObligado=MTM3OTE=#inicio"
           target="_blank" rel="noopener noreferrer">Plataforma de Transparencia</a>
      </nav>
      <p class="tsj-footer-copy">&copy; <?= date('Y') ?> Tecnológico Superior de Jalisco.<br>Todos los derechos reservados.</p>
    </div>

  </div>

  <div class="tsj-footer-divider"></div>

  <div class="tsj-footer-gov-inner">
    <img src="<?= $base ?>/shared/assets/img/educacion.png" alt="SEP" loading="lazy" />
    <img src="<?= $base ?>/shared/assets/img/tecnologico.svg" alt="TecNM" loading="lazy" />
    <img src="<?= $base ?>/shared/assets/img/innovacion.png" alt="SICYT" loading="lazy" />
    <img src="<?= $base ?>/shared/assets/img/jalisco.png" alt="Jalisco" loading="lazy" />
  </div>
</footer>

<script src="<?= $base ?>/shared/assets/js/nav.js"></script>
</body>
</html>
