<?php
if (!defined('PLATAFORMA_URL')) {
    require_once __DIR__ . '/config.php';
}
$base = PLATAFORMA_URL;
?>
<footer class="tsj-footer" role="contentinfo">
  <div class="tsj-footer-inner">

    <!-- Columna 1: Institución + redes -->
    <div class="tsj-footer-col tsj-footer-col--brand">
      <img src="<?= $base ?>/shared/assets/img/logo.svg" alt="Tecnológico Superior de Jalisco" class="tsj-footer-logo" loading="lazy" />
      <p class="tsj-footer-tagline">Tecnológico Superior de Jalisco<br>Campus Chapala</p>
      <div class="tsj-footer-social">
        <a href="https://www.facebook.com/TecSJ" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="tsj-social-btn">
          <img src="<?= $base ?>/shared/assets/img/facebook.svg" alt="Facebook" loading="lazy" />
        </a>
        <a href="https://www.youtube.com/@TecSJ" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="tsj-social-btn">
          <img src="<?= $base ?>/shared/assets/img/youtube.svg" alt="YouTube" loading="lazy" />
        </a>
      </div>
    </div>

    <!-- Columna 2: Módulos -->
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

    <!-- Columna 3: Transparencia + info -->
    <div class="tsj-footer-col">
      <h4 class="tsj-footer-heading">Información</h4>
      <nav aria-label="Información institucional">
        <a class="tsj-footer-link"
           href="https://consultapublicamx.plataformadetransparencia.org.mx/vut-web/faces/view/consultaPublica.xhtml?idEntidad=MTQ=&idSujetoObligado=MTM3OTE=#inicio"
           target="_blank" rel="noopener noreferrer">
          Plataforma de Transparencia
        </a>
      </nav>
      <p class="tsj-footer-copy">&copy; <?= date('Y') ?> Tecnológico Superior de Jalisco. Todos los derechos reservados.</p>
    </div>

  </div>

  <!-- Divisor -->
  <div class="tsj-footer-divider"></div>

  <!-- Logos institucionales -->
  <div class="tsj-footer-gov-inner">
    <img src="<?= $base ?>/shared/assets/img/educacion.png" alt="Secretaría de Educación Pública" loading="lazy" />
    <img src="<?= $base ?>/shared/assets/img/tecnologico.svg" alt="Tecnológico Nacional de México" loading="lazy" />
    <img src="<?= $base ?>/shared/assets/img/innovacion.png" alt="Secretaría de Innovación, Ciencia y Tecnología" loading="lazy" />
    <img src="<?= $base ?>/shared/assets/img/jalisco.png" alt="Gobierno de Jalisco" loading="lazy" />
  </div>
</footer>

<!-- Script del menú móvil compartido -->
<script src="<?= $base ?>/shared/assets/js/nav.js"></script>
</body>
</html>
