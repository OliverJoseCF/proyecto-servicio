<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Servicios Generales';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <h1>Escolares</h1>

    <div class="contenido">
        <div class="egresados">
            <h2>Titulacion</h2>
            <p>Bienvenidos a la sección de egresados. Aquí encontrarás toda la información necesaria para tu proceso de titulación.</p>

            <div class="titulacion">
                <h3>Titulación</h3>
                <p>Para obtener tu título, es necesario cumplir con los siguientes requisitos:</p>
                <ul>
                    <li>Título Universitario.</li>
                    <li>Tarjeta de Identidad.</li>
                    <li>Haber completado todos los créditos requeridos.</li>
                    <li>Presentar un historial académico actualizado.</li>
                    <li>Realizar el pago de los derechos de titulación.</li>
                </ul>
                <p>Para iniciar el proceso de titulación, descarga la solicitud haciendo clic en el siguiente enlace:</p>
                <a href="documentos/SolicitudTitulacion.pdf" class="download-button" download="Solicitud_Titulacion.pdf">
                    Descargar Solicitud de Titulación
                </a>
            </div>
        </div>
    </div>

    <!-- Botón de regreso -->
    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a otra página" style="width: 80px; height: auto;">
    </a>

    
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>