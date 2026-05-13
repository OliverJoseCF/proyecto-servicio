<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Direccion';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <h1>Direccion</h1>

    <div class="botones">
        <a href="DatosContacto.php" class="button">DATOS DE CONTACTO</a>
        <a href="SolicitarCita.php" class="button">SOLICITAR CITA</a>
        <a href="Ubicacion.php" class="button">UBICACION EN EL CAMPUS</a>
    </div>

    <!-- Imagen en la esquina superior derecha como botón -->
    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a otra página" style="width: 80px; height: auto;"> <!-- Ajusta el tamaño según sea necesario -->
    </a>

    
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>