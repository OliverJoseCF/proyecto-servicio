<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Re-inscripción';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">
  <h1 class="vis-page-title">Re-inscripción</h1>

  <div class="container">
    <div class="seccion" role="alert" style="background:#fffbeb;border-left:4px solid #f59e0b;border-top:none;">
      <p style="color:#92400e;font-size:0.92rem;margin:0;">
        <strong>Aviso:</strong> Este comprobante es un documento de referencia generado localmente y NO sustituye al comprobante oficial. Para el comprobante válido, acude a la ventanilla de Control Escolar.
      </p>
    </div>

    <div class="seccion">
      <label for="rein-nombre">Nombre completo:</label>
      <input type="text" id="rein-nombre" placeholder="Escribe tu nombre completo" autocomplete="name" required minlength="3" maxlength="100">

      <label for="rein-control">Número de control:</label>
      <input type="text" id="rein-control" placeholder="Número de control" required minlength="8" maxlength="10" pattern="[0-9A-Za-z]{8,10}">

      <label for="rein-carrera">Carrera:</label>
      <select id="rein-carrera" required>
        <option value="">Selecciona tu carrera</option>
        <option>Ingeniería Mecatrónica</option>
        <option>Ingeniería en Sistemas Computacionales</option>
        <option>Ingeniería Industrial</option>
        <option>Ingeniería en Animación Digital y Efectos Visuales</option>
        <option>Gastronomía</option>
        <option>Ingeniería en Gestión Empresarial</option>
      </select>

      <div id="rein-error" role="alert" aria-live="polite"
           style="display:none;margin-top:14px;padding:10px 14px;background:#fef2f2;border-left:4px solid #dc2626;color:#991b1b;border-radius:6px;font-size:0.88rem;"></div>

      <button id="generarBtn" class="download-button" type="button">
        Generar referencia (no oficial)
      </button>
    </div>
  </div>

  <a href="index.php" class="top-right" aria-label="Volver al menú principal">
    <img src="imagenes/casa.png" alt="">
  </a>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"
        integrity="sha384-JcnsjUPPylna1s1fvi1u12X5qjY5OL56iySh75FdtrwhO/SWXgMjoVqcKyIIWOLk"
        crossorigin="anonymous" defer></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.4/build/qrcode.min.js"
        integrity="sha384-Izc791esqyEy3BEIC42q7jbE0AaOkACziN+dyyXgYeDmpeMCLz0xA+xYN3aCd5zz"
        crossorigin="anonymous" defer></script>
<script>
(function(){
  'use strict';
  var btn = document.getElementById('generarBtn');
  var errEl = document.getElementById('rein-error');

  function mostrarError(msg) {
    errEl.textContent = msg;
    errEl.style.display = 'block';
  }
  function ocultarError() { errEl.style.display = 'none'; errEl.textContent = ''; }

  function sanitize(s){ return String(s || '').replace(/[<>\n\r]/g, '').slice(0, 100); }

  btn.addEventListener('click', async function() {
    ocultarError();
    var nombre  = sanitize(document.getElementById('rein-nombre').value.trim());
    var control = sanitize(document.getElementById('rein-control').value.trim());
    var carrera = document.getElementById('rein-carrera').value;

    if (!nombre || !control || !carrera) {
      mostrarError('Por favor llena todos los campos.');
      return;
    }
    if (!window.jspdf || !window.QRCode) {
      mostrarError('Las librerías para generar el PDF aún no están listas. Por favor espera unos segundos.');
      return;
    }

    btn.disabled = true;
    btn.textContent = 'Generando…';

    try {
      var jsPDF = window.jspdf.jsPDF;
      var doc   = new jsPDF();
      var fecha = new Date().toLocaleDateString("es-MX", { day:'numeric', month:'long', year:'numeric' });
      var folio = "REF-" + Math.floor(100000 + Math.random() * 900000);

      var datosQR = "Folio: " + folio + "\nNombre: " + nombre + "\nControl: " + control + "\nCarrera: " + carrera + "\nFecha: " + fecha;
      var qrDataURL = await QRCode.toDataURL(datosQR);

      doc.setFillColor(51, 23, 156);
      doc.rect(0, 0, 210, 30, "F");
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(16);
      doc.text("Tecnológico Superior de Jalisco — Campus Chapala", 105, 14, { align: "center" });
      doc.setFontSize(11);
      doc.text("REFERENCIA NO OFICIAL — Documento de uso interno", 105, 22, { align: "center" });

      doc.setTextColor(0, 0, 0);
      doc.setFontSize(14);
      doc.text("Comprobante de Reinscripción (Referencia)", 105, 42, { align: "center" });

      doc.setFontSize(11);
      doc.text("Fecha de emisión: " + fecha, 20, 55);
      doc.text("Folio interno: " + folio, 130, 55);

      doc.setFillColor(245, 245, 250);
      doc.setDrawColor(200);
      doc.rect(15, 65, 180, 60, "FD");

      doc.text("Nombre del Alumno: " + nombre,  20, 75);
      doc.text("Número de Control: " + control, 20, 85);
      doc.text("Carrera: " + carrera,           20, 95);
      doc.text("Concepto: Reinscripción Ciclo Escolar", 20, 105);

      doc.addImage(qrDataURL, "PNG", 150, 70, 35, 35);

      doc.setFontSize(10);
      doc.setTextColor(80, 80, 80);
      doc.text("ESTE DOCUMENTO ES UNA REFERENCIA Y NO TIENE VALIDEZ OFICIAL.", 105, 140, { align: "center" });
      doc.text("Para el comprobante oficial acude a Control Escolar.", 105, 148, { align: "center" });

      doc.save("Referencia_Reinscripcion.pdf");
    } catch (e) {
      mostrarError('No se pudo generar el PDF. Inténtalo de nuevo.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Generar referencia (no oficial)';
    }
  });
})();
</script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
