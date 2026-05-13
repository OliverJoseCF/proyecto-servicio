<?php
if (!defined('PLATAFORMA_URL')) {
    require_once __DIR__ . '/config.php';
}
$base = PLATAFORMA_URL;
?>
<style>
/* ── Footer rediseño ─────────────────────────────────── */
.tsj-footer {
  background: linear-gradient(160deg, #1a0a5e 0%, #32129a 55%, #3e1ab8 100%);
  padding: 56px 24px 36px;
  margin-top: 0;
  font-family: 'Poppins', Arial, sans-serif;
  position: relative;
}
.tsj-footer::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 4px;
  background: linear-gradient(90deg, #ec5a68, #f5a623, #ec5a68);
}

.tsj-footer-inner {
  display: grid;
  grid-template-columns: 1.4fr 1fr 1fr;
  gap: 48px;
  max-width: 1200px;
  margin: 0 auto;
}

/* Columna 1 */
.tsj-footer-logo {
  width: 180px;
  height: auto;
  margin-bottom: 14px;
  display: block;
  filter: brightness(0) invert(1);
}
.tsj-footer-tagline {
  color: rgba(255,255,255,.6);
  font-size: 13px;
  line-height: 1.7;
  margin: 0 0 22px;
}
.tsj-footer-social {
  display: flex;
  gap: 12px;
}
.tsj-social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255,255,255,.12);
  border: 1px solid rgba(255,255,255,.22);
  transition: background .25s, transform .2s;
  text-decoration: none;
}
.tsj-social-btn:hover {
  background: #ec5a68;
  transform: translateY(-3px);
}
.tsj-social-btn img {
  width: 20px;
  height: 20px;
  filter: brightness(0) invert(1);
}

/* Columnas 2 y 3 */
.tsj-footer-heading {
  color: #fff;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin: 0 0 18px;
  padding-bottom: 10px;
  border-bottom: 2px solid #ec5a68;
  display: inline-block;
}
.tsj-footer-col nav {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.tsj-footer-link {
  color: rgba(255,255,255,.72);
  text-decoration: none;
  font-size: 14px;
  font-weight: 400;
  transition: color .2s, padding-left .2s;
}
.tsj-footer-link:hover {
  color: #fff;
  padding-left: 8px;
}
.tsj-footer-copy {
  color: rgba(255,255,255,.38);
  font-size: 11px;
  line-height: 1.7;
  margin: 22px 0 0;
}

/* Divisor */
.tsj-footer-divider {
  max-width: 1200px;
  margin: 40px auto 28px;
  height: 1px;
  background: rgba(255,255,255,.14);
}

/* Logos institucionales */
.tsj-footer-gov-inner {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 40px;
  max-width: 1200px;
  margin: 0 auto;
  padding-bottom: 8px;
}
.tsj-footer-gov-inner img {
  height: 42px;
  width: auto;
  object-fit: contain;
  filter: brightness(0) invert(1);
  opacity: .7;
  transition: opacity .2s;
}
.tsj-footer-gov-inner img:hover { opacity: 1; }

/* Responsive */
@media (max-width: 900px) {
  .tsj-footer-inner {
    grid-template-columns: 1fr 1fr;
    gap: 36px;
  }
  .tsj-footer-col--brand {
    grid-column: 1 / -1;
  }
}
@media (max-width: 560px) {
  .tsj-footer-inner {
    grid-template-columns: 1fr;
    gap: 28px;
    text-align: center;
  }
  .tsj-footer-social { justify-content: center; }
  .tsj-footer-col nav { align-items: center; }
  .tsj-footer-logo { margin: 0 auto 14px; }
  .tsj-footer-gov-inner { gap: 20px; }
  .tsj-footer-gov-inner img { height: 32px; }
}
</style>

<footer class="tsj-footer" role="contentinfo">
  <div class="tsj-footer-inner">

    <!-- Col 1: Marca + redes -->
    <div class="tsj-footer-col tsj-footer-col--brand">
      <img src="<?= $base ?>/shared/assets/img/logo.svg"
           alt="Tecnológico Superior de Jalisco"
           class="tsj-footer-logo" loading="lazy" />
      <p class="tsj-footer-tagline">
        Tecnológico Superior de Jalisco<br>Campus Chapala
      </p>
      <div class="tsj-footer-social">
        <a href="https://www.facebook.com/TecSJ" target="_blank" rel="noopener noreferrer"
           aria-label="Facebook" class="tsj-social-btn">
          <img src="<?= $base ?>/shared/assets/img/facebook.svg" alt="Facebook" loading="lazy" />
        </a>
        <a href="https://www.youtube.com/@TecSJ" target="_blank" rel="noopener noreferrer"
           aria-label="YouTube" class="tsj-social-btn">
          <img src="<?= $base ?>/shared/assets/img/youtube.svg" alt="YouTube" loading="lazy" />
        </a>
      </div>
    </div>

    <!-- Col 2: Módulos -->
    <div class="tsj-footer-col">
      <h4 class="tsj-footer-heading">Módulos</h4>
      <nav aria-label="Módulos del sistema">
        <a class="tsj-footer-link" href="<?= $base ?>/">Portal principal</a>
        <a class="tsj-footer-link" href="<?= $base ?>/modulos/visitantes/index.php">Visitantes</a>
        <a class="tsj-footer-link" href="<?= $base ?>/modulos/biblioteca/buscar.php">Biblioteca</a>
        <a class="tsj-footer-link" href="<?= $base ?>/modulos/convenios/index.php">Convenios</a>
        <a class="tsj-footer-link" href="<?= $base ?>/modulos/horarios/index.php">Horarios</a>
        <a class="tsj-footer-link" href="<?= $base ?>/modulos/requisitos/residencia.php">Requisitos</a>
      </nav>
    </div>

    <!-- Col 3: Información -->
    <div class="tsj-footer-col">
      <h4 class="tsj-footer-heading">Información</h4>
      <nav aria-label="Información institucional">
        <a class="tsj-footer-link"
           href="https://consultapublicamx.plataformadetransparencia.org.mx/vut-web/faces/view/consultaPublica.xhtml?idEntidad=MTQ=&idSujetoObligado=MTM3OTE=#inicio"
           target="_blank" rel="noopener noreferrer">
          Plataforma de Transparencia
        </a>
      </nav>
      <p class="tsj-footer-copy">
        &copy; <?= date('Y') ?> Tecnológico Superior<br>
        de Jalisco. Todos los derechos<br>reservados.
      </p>
    </div>

  </div>

  <div class="tsj-footer-divider"></div>

  <!-- Logos institucionales -->
  <div class="tsj-footer-gov-inner">
    <img src="<?= $base ?>/shared/assets/img/educacion.png"
         alt="Secretaría de Educación Pública" loading="lazy" />
    <img src="<?= $base ?>/shared/assets/img/tecnologico.svg"
         alt="Tecnológico Nacional de México" loading="lazy" />
    <img src="<?= $base ?>/shared/assets/img/innovacion.png"
         alt="Secretaría de Innovación, Ciencia y Tecnología" loading="lazy" />
    <img src="<?= $base ?>/shared/assets/img/jalisco.png"
         alt="Gobierno de Jalisco" loading="lazy" />
  </div>
</footer>

<script src="<?= $base ?>/shared/assets/js/nav.js"></script>
</body>
</html>
