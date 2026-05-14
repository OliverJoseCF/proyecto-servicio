<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Titulación — TSJ Chapala';
$tsj_extra_css  = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

    <h1>Titulación</h1>

    <div class="contenido">
        <div class="egresados">
            <p>Bienvenidos a la sección de egresados. Aquí encontrarás toda la información necesaria para tu proceso de titulación.</p>

            <div class="titulacion">
                <h3>Requisitos de Titulación</h3>
                <p>Para obtener tu título, es necesario cumplir con los siguientes requisitos:</p>
                <ul>
                    <li>Haber completado todos los créditos requeridos por tu programa educativo.</li>
                    <li>Tener el servicio social y residencia profesional liberados.</li>
                    <li>Presentar un historial académico actualizado.</li>
                    <li>Realizar el pago de los derechos de titulación.</li>
                    <li>Contar con identificación oficial vigente.</li>
                </ul>
                <p>Para iniciar el proceso de titulación, descarga la solicitud:</p>
                <a href="documentos/SolicitudTitulacion.pdf" class="download-button" download="Solicitud_Titulacion.pdf">
                    Descargar Solicitud de Titulación
                </a>
            </div>
        </div>
    </div>

    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a inicio" style="width: 80px; height: auto;">
    </a>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
