<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Ubicación del Campus — TSJ Chapala';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>

    <h1>Ubicación del Campus</h1>

    <div class="location">
        <p>Ubicación: <a href="https://maps.app.goo.gl/w3rApmQrocT3j5V88" target="_blank" rel="noopener noreferrer" style="color: #FFD700;">Ver en Google Maps</a></p>
    </div>

    <div class="map-container">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3727.8!2d-103.1901!3d20.2985!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x842f4f1f7c6e5555%3A0x123456789abcdef!2sTecnol%C3%B3gico+Superior+de+Jalisco%2C+Chapala!5e0!3m2!1ses!2smx!4v1700000000000"
            width="600"
            height="350"
            style="border:0; max-width:100%;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Ubicación del Tecnológico Superior de Jalisco, Campus Chapala">
        </iframe>
    </div>

    <!-- Botón de regreso -->
    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a inicio" style="width: 80px; height: auto;">
    </a>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
