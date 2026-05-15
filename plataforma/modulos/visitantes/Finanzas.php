<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Finanzas';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">
  <h1 class="vis-page-title">Finanzas</h1>

  <div class="contenido">
    <div class="seccion">
      <h2 class="vis-h2">¿Cómo realizar tu pago?</h2>
      <ol style="text-align:left;padding-left:1.5rem;margin-top:12px;line-height:1.8;color:#374151;">
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
            <li>La CLABE interbancaria será proporcionada por el área de Finanzas del plantel. Comunícate directamente con la oficina para obtenerla.</li>
            <li>En el concepto, escribe tu nombre completo y motivo del pago.</li>
            <li>Guarda el comprobante de pago.</li>
          </ul>
        </li>
        <li><strong>Para facturación:</strong>
          <ul>
            <li>Envía tu comprobante junto con tus datos fiscales a:</li>
            <li><a href="mailto:facturacion@chapala.tecmm.edu.mx">facturacion@chapala.tecmm.edu.mx</a></li>
          </ul>
        </li>
      </ol>
    </div>
  </div>

  <a href="index.php" class="top-right" aria-label="Volver al menú principal">
    <img src="imagenes/casa.png" alt="">
  </a>
</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
