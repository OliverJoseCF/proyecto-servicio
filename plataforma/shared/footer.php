<?php
if (!defined('PLATAFORMA_URL')) {
    require_once __DIR__ . '/config.php';
}
$base = PLATAFORMA_URL;
?>
<footer class="tsj-footer" role="contentinfo">
  <div class="tsj-footer-inner">
    <!-- Logo -->
    <div class="tsj-footer-logo">
      <img src="<?= $base ?>/shared/assets/img/logo.svg" alt="Tecnológico Superior de Jalisco" loading="lazy" />
    </div>

    <!-- Redes sociales -->
    <div class="tsj-footer-social">
      <a href="https://www.facebook.com/TecSJ" target="_blank" rel="noopener noreferrer" aria-label="Facebook del TecSJ">
        <img src="<?= $base ?>/shared/assets/img/facebook.svg" alt="Facebook" loading="lazy" />
      </a>
      <a href="https://www.youtube.com/@TecSJ" target="_blank" rel="noopener noreferrer" aria-label="YouTube del TecSJ">
        <img src="<?= $base ?>/shared/assets/img/youtube.svg" alt="YouTube" loading="lazy" />
      </a>
    </div>

    <!-- Links de módulos -->
    <div class="tsj-footer-links">
      <a class="tsj-footer-link" href="<?= $base ?>/modulos/visitantes/index.php">Visitantes</a>
      <a class="tsj-footer-link" href="<?= $base ?>/modulos/biblioteca/buscar.php">Biblioteca</a>
      <a class="tsj-footer-link" href="<?= $base ?>/modulos/convenios/index.php">Convenios</a>
      <a class="tsj-footer-link" href="<?= $base ?>/modulos/horarios/index.php">Horarios</a>
      <a class="tsj-footer-link" href="https://consultapublicamx.plataformadetransparencia.org.mx/vut-web/faces/view/consultaPublica.xhtml?idEntidad=MTQ=&idSujetoObligado=MTM3OTE=#inicio"
         target="_blank" rel="noopener noreferrer">Plataforma Nacional de Transparencia</a>
    </div>
  </div>
</footer>

<!-- Logos institucionales -->
<div class="tsj-footer-gov">
  <div class="tsj-footer-gov-inner">
    <img src="<?= $base ?>/shared/assets/img/educacion.png" alt="Secretaría de Educación Pública" loading="lazy" />
    <img src="<?= $base ?>/shared/assets/img/tecnologico.svg" alt="Tecnológico Nacional de México" loading="lazy" />
    <img src="<?= $base ?>/shared/assets/img/innovacion.png" alt="Secretaría de Innovación, Ciencia y Tecnología de Jalisco" loading="lazy" />
    <img src="<?= $base ?>/shared/assets/img/jalisco.png" alt="Gobierno de Jalisco" loading="lazy" />
  </div>
</div>

<!-- Script del menú móvil compartido -->
<script src="<?= $base ?>/shared/assets/js/nav.js"></script>
</body>
</html>
