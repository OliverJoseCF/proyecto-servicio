<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Ingeniería Industrial';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <h1>Ingeniería Industrial</h1>

    <div class="botones">
        <a href="MateriasIndustrial.php" class="button">MATERIAS</a>
        <a href="DocentesIndustrial.php" class="button">DOCENTES</a>
        <a href="CordinadorIndustrial.php" class="button">COORDINADOR/A</a>
    </div>

    <!-- Imagen en la esquina superior derecha como botón -->
    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a otra página" style="width: 80px; height: auto;"> <!-- Ajusta el tamaño según sea necesario -->
    </a>

    
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>