<?php
$tsj_module    = 'requisitos';
$tsj_title     = 'Requisitos — Servicio Social';
$tsj_extra_css = [
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
    'assets/css/style.css',
];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main" class="contenedor">
  <div class="contenido-centrado">

    <h1 class="sr-only">Servicio Social — TSJ Chapala</h1>

    <nav class="navegacion-secciones" aria-label="Secciones del módulo">
      <a href="residencia.php" class="boton-navegacion">Residencia</a>
      <a href="servicio-social.php" class="boton-navegacion active" aria-current="page">Servicio Social</a>
    </nav>

    <!-- Barra de Progreso -->
    <section class="tarjeta" aria-label="Tu progreso de servicio social">
      <h2>Progreso de Servicio Social</h2>
      <div class="progress-container" role="progressbar"
           aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
           aria-label="Progreso de requisitos completados" id="progressContainer">
        <div class="progress-bar" id="progressBar" style="width:0%">0%</div>
      </div>
      <p>Marca los requisitos completados para ver tu progreso</p>
    </section>

    <!-- Timeline del Proceso -->
    <section class="tarjeta" aria-label="Timeline del proceso de servicio social">
      <h2><i class="fas fa-clock" aria-hidden="true"></i> Timeline del Proceso</h2>
      <ol class="timeline">
        <li class="timeline-item">
          <div class="timeline-dot" aria-hidden="true"></div>
          <h3>Fase 1: Requisitos</h3>
          <p>Cumplir con el 70% de créditos y obtener constancia</p>
        </li>
        <li class="timeline-item">
          <div class="timeline-dot" aria-hidden="true"></div>
          <h3>Fase 2: Documentos</h3>
          <p>Llenar y entregar solicitud, carta compromiso y plan de trabajo</p>
        </li>
        <li class="timeline-item">
          <div class="timeline-dot" aria-hidden="true"></div>
          <h3>Fase 3: Ejecución</h3>
          <p>Realizar 500 horas en mínimo 6 meses y máximo 2 años</p>
        </li>
        <li class="timeline-item">
          <div class="timeline-dot" aria-hidden="true"></div>
          <h3>Fase 4: Reportes</h3>
          <p>Entregar reportes bimestrales y evaluaciones</p>
        </li>
        <li class="timeline-item">
          <div class="timeline-dot" aria-hidden="true"></div>
          <h3>Fase 5: Liberación</h3>
          <p>Obtener carta de liberación y constancia final</p>
        </li>
      </ol>
    </section>

    <!-- Checklist Interactivo -->
    <section class="tarjeta" aria-label="Checklist de requisitos">
      <h2><i class="fas fa-tasks" aria-hidden="true"></i> Checklist de Requisitos</h2>
      <div class="checklist-item">
        <input type="checkbox" id="req1">
        <label for="req1">Tener acreditado el 70% de los créditos de la carrera</label>
      </div>
      <div class="checklist-item">
        <input type="checkbox" id="req2">
        <label for="req2">Solicitud de servicio social</label>
      </div>
      <div class="checklist-item">
        <input type="checkbox" id="req3">
        <label for="req3">Carta compromiso firmada</label>
      </div>
      <div class="checklist-item">
        <input type="checkbox" id="req4">
        <label for="req4">Plan de trabajo aprobado</label>
      </div>
      <div class="checklist-item">
        <input type="checkbox" id="req5">
        <label for="req5">Constancia de créditos del departamento</label>
      </div>
    </section>

    <!-- Información del Servicio Social -->
    <section class="tarjeta servicio-contenido" aria-label="Información del servicio social">
      <h2><i class="fas fa-info-circle" aria-hidden="true"></i> Información del Servicio Social</h2>
      <div class="searchable-content">
        <h3>Requisito Principal:</h3>
        <p>El requisito único que se requiere para el Servicio Social es tener acreditado el 70% de los créditos de la carrera, no es necesario algo más.</p>
        <h3>Duración y Créditos:</h3>
        <p>El servicio social consta de 500 horas, lo cual equivale a 10 créditos y lo deben realizar en un mínimo de 6 meses y un máximo de dos años.</p>
        <h3>Formatos Requeridos:</h3>
        <ul>
          <li>Solicitud de servicio social</li>
          <li>Carta compromiso, firmada por el interesado</li>
          <li>Plan de trabajo (llenado en conjunto con la persona que firmará los reportes)</li>
          <li>Constancia de créditos que expide el departamento de servicio social</li>
        </ul>
      </div>
    </section>

    <!-- Documentos para Descargar -->
    <section class="tarjeta" aria-label="Documentos para descargar">
      <h2><i class="fas fa-download" aria-hidden="true"></i> Documentos para Descargar</h2>
      <div class="descargas-servicio">
        <a href="assets/docs/servicio-social/evaluacion-cualitativa.pdf"
           target="_blank" rel="noopener noreferrer" class="boton-descarga"
           aria-label="Descargar Evaluación Cualitativa (PDF, abre en nueva pestaña)">
          <i class="fas fa-file-pdf" aria-hidden="true"></i> Evaluación Cualitativa
        </a>
        <a href="assets/docs/servicio-social/carta-compromiso.pdf"
           target="_blank" rel="noopener noreferrer" class="boton-descarga"
           aria-label="Descargar Carta Compromiso (PDF, abre en nueva pestaña)">
          <i class="fas fa-file-pdf" aria-hidden="true"></i> Carta Compromiso
        </a>
        <a href="assets/docs/servicio-social/reporte-bimestral-1.pdf"
           target="_blank" rel="noopener noreferrer" class="boton-descarga"
           aria-label="Descargar Reporte Bimestral (PDF, abre en nueva pestaña)">
          <i class="fas fa-file-pdf" aria-hidden="true"></i> Reporte Bimestral
        </a>
        <a href="assets/docs/servicio-social/formato-evaluacion.pdf"
           target="_blank" rel="noopener noreferrer" class="boton-descarga"
           aria-label="Descargar Formato de Evaluación (PDF, abre en nueva pestaña)">
          <i class="fas fa-file-pdf" aria-hidden="true"></i> Formato Evaluación
        </a>
      </div>
    </section>

    <!-- FAQ -->
    <section class="tarjeta" aria-label="Preguntas frecuentes">
      <h2><i class="fas fa-question-circle" aria-hidden="true"></i> Preguntas Frecuentes</h2>
      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          <span>¿Puedo hacer mi servicio social en una empresa privada?</span>
          <i class="fas fa-chevron-down faq-icon" aria-hidden="true"></i>
        </button>
        <div class="faq-answer" role="region">
          <p>Sí, siempre y cuando el proyecto beneficie directamente a la comunidad y cumpla con los objetivos del servicio social.</p>
        </div>
      </div>
      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          <span>¿Qué pasa si no puedo cumplir las 500 horas?</span>
          <i class="fas fa-chevron-down faq-icon" aria-hidden="true"></i>
        </button>
        <div class="faq-answer" role="region">
          <p>Debes completar las 500 horas obligatoriamente. Si tienes problemas, habla con el departamento de servicio social para buscar soluciones.</p>
        </div>
      </div>
      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          <span>¿Puedo hacer servicio social en mi propia comunidad?</span>
          <i class="fas fa-chevron-down faq-icon" aria-hidden="true"></i>
        </button>
        <div class="faq-answer" role="region">
          <p>Sí, siempre y cuando el proyecto esté debidamente documentado y aprobado por el departamento de servicio social.</p>
        </div>
      </div>
    </section>

  </div>
</main>

<script>
(function () {
  'use strict';

  function updateProgress() {
    var checkboxes = document.querySelectorAll('.checklist-item input[type="checkbox"]');
    var checked    = document.querySelectorAll('.checklist-item input[type="checkbox"]:checked');
    var bar        = document.getElementById('progressBar');
    var container  = document.getElementById('progressContainer');
    var pct        = Math.round((checked.length / checkboxes.length) * 100);

    bar.style.width = pct + '%';
    bar.textContent = pct + '%';
    if (container) container.setAttribute('aria-valuenow', pct);

    var progress = [];
    checkboxes.forEach(function (cb, i) { progress[i] = cb.checked; });
    try { localStorage.setItem('servicioProgress', JSON.stringify(progress)); } catch (e) {}

    checkboxes.forEach(function (cb) {
      cb.parentElement.classList.toggle('completed', cb.checked);
    });
  }

  function loadProgress() {
    try {
      var saved = localStorage.getItem('servicioProgress');
      if (!saved) return;
      var progress  = JSON.parse(saved);
      var checkboxes = document.querySelectorAll('.checklist-item input[type="checkbox"]');
      progress.forEach(function (checked, i) { if (checkboxes[i]) checkboxes[i].checked = checked; });
      updateProgress();
    } catch (e) {}
  }

  document.querySelectorAll('.checklist-item input[type="checkbox"]').forEach(function (cb) {
    cb.addEventListener('change', updateProgress);
  });

  document.querySelectorAll('.faq-question').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item   = btn.parentElement;
      var answer = btn.nextElementSibling;
      var isOpen = item.classList.contains('active');
      item.classList.toggle('active', !isOpen);
      answer.classList.toggle('active', !isOpen);
      btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
    });
  });

  window.addEventListener('DOMContentLoaded', function () {
    loadProgress();
  });
})();
</script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
