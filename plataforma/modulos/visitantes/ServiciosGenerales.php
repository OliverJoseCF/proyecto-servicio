<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Servicios Generales';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <!-- Botón de regreso en la esquina superior derecha -->
    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir al inicio" style="width: 50px; height: auto;">
    </a>

    <h1>Servicios Generales</h1>

    <div class="contenido">

        <div class="seccion">
            <h2>Contacto</h2>
            <p>Correo electrónico: <strong>auditorio@chapala.tecmm.edu.mx</strong></p>
        </div>

        <div class="seccion">
            <h2>Jefe de Servicios Generales</h2>
            <p><strong>Nombre:</strong> Ing. Juan Pérez López</p>
            <p><strong>Teléfono:</strong> (376) 765 1234 ext. 105</p>
            <p><strong>Correo:</strong> juan.perez@chapala.tecmm.edu.mx</p>
        </div>

        <div class="seccion">
            <h2>Servicios Ofrecidos a los Estudiantes</h2>
            <ul>
                <li>Renta del Auditorio para eventos escolares</li>
                <li>Préstamo del Auditorio para actividades académicas</li>
                <li>Atención y recuperación de objetos perdidos</li>
                <li>Apoyo logístico para eventos institucionales</li>
            </ul>
        </div>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>