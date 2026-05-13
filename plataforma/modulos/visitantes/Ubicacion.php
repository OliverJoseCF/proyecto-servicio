<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Ubicacion del campus';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <h1>Solicitar una Cita</h1>

    <div class="location">
        <p>Ubicación: <a href="https://maps.app.goo.gl/w3rApmQrocT3j5V88" target="_blank" style="color: #FFD700;">Ver en Google Maps</a></p>
    </div>

    <div class="map-container">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509198!2d144.9537353153164!3d-37.81627997975157!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11f1b3%3A0x5045675218ceed0!2sFederation%20Square!5e0!3m2!1sen!2sau!4v1616161616161!5m2!1sen!2sau" 
            width="400" 
            height="250" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy">
        </iframe>
    </div>

    <!-- Botón de regreso -->
    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a otra página" style="width: 80px; height: auto;">
    </a>

    
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>