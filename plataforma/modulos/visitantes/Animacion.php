<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Ingeniería en Animación Digital y Efectos Visuales';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <h1>Ingeniería en Animación Digital y Efectos Visuales</h1>

    <div class="botones">
        <a href="MateriasAnimacion.php" class="button">MATERIAS</a>
        <a href="DocentesAnimacion.php" class="button">DOCENTES</a>
        <a href="coordinadorAnimacion.php" class="button">COORDINADOR/A</a>
    </div>

    <!-- Imagen en la esquina superior derecha como botón -->
    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a otra página" style="width: 80px; height: auto;"> <!-- Ajusta el tamaño según sea necesario -->
    </a>

    
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>