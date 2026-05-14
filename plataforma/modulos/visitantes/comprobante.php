<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Comprobantes — TSJ Chapala';
$tsj_extra_css  = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

    <h1>Comprobantes</h1>

    <div class="menu-wrapper">
        <p class="menu-section-label">Selecciona el comprobante que necesitas</p>
        <nav class="menu menu--admin">
            <a href="Egresados.php">Comprobante de Reinscripción</a>
            <a href="nuevoIngreso.php">Comprobante de Examen de Admisión</a>
        </nav>
    </div>

    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a inicio" style="width: 80px; height: auto;">
    </a>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
