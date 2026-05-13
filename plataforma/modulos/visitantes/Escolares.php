<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Escolares';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <h1>Escolares</h1>

    <div class="botones">
        <a href="nuevoIngreso.php" class="button">NUEVO INGRESO</a>
        <a href="Egresados.php" class="button">REINSCRIPCION</a>
        <a href="Titulacion.php" class="button">TITULACION</a>
    </div>

    <!-- Imagen en la esquina superior derecha como botón -->
    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a otra página" style="width: 80px; height: auto;"> <!-- Ajusta el tamaño según sea necesario -->
    </a>

    
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>