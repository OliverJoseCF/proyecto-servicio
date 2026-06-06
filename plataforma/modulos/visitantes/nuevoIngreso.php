<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Nuevo Ingreso';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/config.php';

// Cargar configuración dinámica desde BD
try {
    $db  = getPDO(DB_NAME);
    $ni  = $db->query('SELECT * FROM nuevo_ingreso_config LIMIT 1')->fetch();
} catch (\Throwable $e) {
    $ni = null;
}

$dia_examen   = (int)($ni['dia_examen']   ?? 20);
$hora_examen  = htmlspecialchars($ni['hora_examen']  ?? '08:00:00');
$lugar_examen = htmlspecialchars($ni['lugar_examen'] ?? 'Tecnológico Superior de Jalisco, Campus Chapala');
$requisitos   = json_decode($ni['requisitos'] ?? '[]', true) ?: [
    'Copia de la identificación oficial.',
    'Certificado de estudios anteriores (original y copia).',
    'Comprobante de domicilio.',
    'Fotografías tamaño infantil (4 piezas).',
    'Formulario de inscripción llenado.',
];

// Formatear hora para mostrar (08:00:00 → 8:00 AM)
$horaObj    = DateTime::createFromFormat('H:i:s', $ni['hora_examen'] ?? '08:00:00')
           ?: DateTime::createFromFormat('H:i', $ni['hora_examen'] ?? '08:00');
$hora_label = $horaObj ? $horaObj->format('g:i A') : $hora_examen;

require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">

  <div class="tsj-page-header">
    <div class="tsj-page-header-line"></div>
    <h1>Inscripción y <span class="tsj-accent">Reinscripción</span></h1>
    <p class="tsj-page-header-sub">Requisitos de admisión y generación de comprobante para el examen</p>
  </div>

  <div class="contenido">
    <div class="requisitos">
      <h2 class="vis-h2">Requisitos de Admisión</h2>
      <h3 class="vis-h3">Documentación requerida:</h3>
      <ul>
        <?php foreach ($requisitos as $req): ?>
        <li><?= htmlspecialchars($req) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="examen">
      <h2 class="vis-h3">Examen de Admisión</h2>
      <p>
        <label for="ni-nombre" style="display:block;margin-bottom:6px;font-weight:600;">Nombre del aspirante:</label>
        <input type="text" id="ni-nombre" style="width:100%;padding:8px;border:1.5px solid var(--tsj-gray-200, #e5e7eb);border-radius:6px;" autocomplete="name" required minlength="3" maxlength="100">
      </p>
      <p>El examen de admisión se llevará a cabo el día <strong><?= $dia_examen ?></strong>
         a las <strong><?= $hora_label ?></strong>.<br>
         Lugar: <strong><?= $lugar_examen ?></strong>
      </p>
      <p>Asegúrate de traer los siguientes documentos:</p>
      <ul>
        <li>Identificación oficial.</li>
        <li>Comprobante de registro al examen.</li>
      </ul>

      <button id="generarBtn" class="download-button" type="button" disabled>
        Cargando librería…
      </button>
      <p id="ni-error" role="alert" aria-live="polite"
         style="display:none;margin-top:12px;color:#991b1b;font-size:0.88rem;"></p>
    </div>
  </div>

</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"
        integrity="sha384-JcnsjUPPylna1s1fvi1u12X5qjY5OL56iySh75FdtrwhO/SWXgMjoVqcKyIIWOLk"
        crossorigin="anonymous" defer></script>
<script>
(function(){
  'use strict';
  var btn   = document.getElementById('generarBtn');
  var errEl = document.getElementById('ni-error');
  var DIA   = <?= $dia_examen ?>;
  var HORA  = <?= json_encode($hora_label) ?>;
  var LUGAR = <?= json_encode($lugar_examen) ?>;

  function checkReady(){
    if (window.jspdf) {
      btn.disabled = false;
      btn.textContent = 'Generar Comprobante del Examen';
    } else {
      setTimeout(checkReady, 200);
    }
  }
  checkReady();

  btn.addEventListener('click', function(){
    errEl.style.display = 'none';
    var nombre = (document.getElementById('ni-nombre').value || '').trim();

    if (!nombre) {
      errEl.textContent = 'Por favor ingresa tu nombre.';
      errEl.style.display = 'block';
      return;
    }
    nombre = String(nombre).replace(/[<>\n\r]/g,'').slice(0,100);

    try {
      var jsPDF = window.jspdf.jsPDF;
      var doc = new jsPDF();
      var fecha = new Date();
      fecha.setDate(DIA);
      var fechaFormateada = fecha.toLocaleDateString("es-MX", { day:'numeric', month:'long', year:'numeric' });

      doc.setFontSize(16);
      doc.text("Comprobante de Examen de Admisión", 20, 20);
      doc.setFontSize(12);
      doc.text("El aspirante se ha registrado para presentar el examen de admisión.", 20, 32);
      doc.text("Detalles del examen:", 20, 45);
      doc.text("Fecha: " + fechaFormateada, 20, 55);
      doc.text("Hora: " + HORA, 20, 65);
      doc.text("Lugar: " + LUGAR, 20, 75);
      doc.text("Nombre del Aspirante: " + nombre, 20, 90);
      doc.setFontSize(9);
      doc.text("Documento generado automáticamente — solo válido como recordatorio.", 20, 270);
      doc.save("Comprobante_Examen.pdf");
    } catch (e) {
      errEl.textContent = 'No se pudo generar el PDF. Inténtalo de nuevo.';
      errEl.style.display = 'block';
    }
  });
})();
</script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
