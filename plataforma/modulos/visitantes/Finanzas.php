<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Finanzas';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <h1>Finanzas</h1>

    <div class="cuadro">
        <h2>¿Cómo realizar tu pago?</h2>
        <ol style="text-align: left;">
            <li><strong>Elige tu método de pago:</strong>
                <ul>
                    <li>Tarjeta de crédito o débito (Visa o MasterCard).</li>
                    <li>Transferencia bancaria.</li>
                </ul>
            </li>
            <li><strong>Si eliges tarjeta:</strong>
                <ul>
                    <li>Ingresa los datos de tu tarjeta en la plataforma de pago segura.</li>
                    <li>Verifica que los datos sean correctos antes de confirmar.</li>
                </ul>
            </li>
            <li><strong>Si eliges transferencia bancaria:</strong>
                <ul>
                    <li>Utiliza la siguiente CLABE: <strong>0123456789</strong>.</li>
                    <li>En el concepto, escribe tu nombre completo y motivo del pago.</li>
                    <li>Guarda el comprobante de pago.</li>
                </ul>
            </li>
            <li><strong>Para facturación:</strong>
                <ul>
                    <li>Envía tu comprobante junto con tus datos fiscales a:</li>
                    <li><strong>facturacion@chapala.tecmm.edu.mx</strong></li>
                </ul>
            </li>
        </ol>
    </div>
    

    <!-- Imagen en la esquina superior derecha como botón -->
    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a otra página" style="width: 80px; height: auto;"> <!-- Ajusta el tamaño según sea necesario -->
    </a>

    
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>