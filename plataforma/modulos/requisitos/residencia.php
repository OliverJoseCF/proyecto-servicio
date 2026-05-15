<?php
$tsj_module    = 'requisitos';
$tsj_title     = 'Requisitos — Residencia Profesional';
$tsj_extra_css = [
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
    'assets/css/style.css',
];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main" class="contenedor">
  <div class="contenido-centrado">

    <h1 class="sr-only">Residencia Profesional — TSJ Chapala</h1>

    <nav class="navegacion-secciones" aria-label="Secciones del módulo">
      <a href="residencia.php" class="boton-navegacion active" aria-current="page">Residencia</a>
      <a href="servicio-social.php" class="boton-navegacion">Servicio Social</a>
    </nav>

    <!-- Calculadora de Créditos -->
    <section class="tarjeta" aria-label="Calculadora de créditos">
      <h2><i class="fas fa-calculator" aria-hidden="true"></i> Calculadora de Créditos</h2>
      <p>Calcula cuántos créditos necesitas para iniciar residencia</p>
      <label for="totalCredits">Créditos totales de tu carrera</label>
      <input type="number" class="calculator-input" id="totalCredits"
             placeholder="Ej. 240" min="1" oninput="calculateCredits()">
      <label for="currentCredits">Créditos acreditados</label>
      <input type="number" class="calculator-input" id="currentCredits"
             placeholder="Ej. 180" min="0" oninput="calculateCredits()">
      <div class="calculator-result" id="creditResult" aria-live="polite" role="status">
        Ingresa tus créditos para calcular
      </div>
    </section>

    <!-- Timeline del Proceso -->
    <section class="tarjeta" aria-label="Timeline del proceso de residencia">
      <h2><i class="fas fa-clock" aria-hidden="true"></i> Timeline del Proceso</h2>
      <ol class="timeline">
        <li class="timeline-item">
          <div class="timeline-dot" aria-hidden="true"></div>
          <h3>Fase 1: Preparación</h3>
          <p>Reunir documentos y cumplir requisitos académicos (70–80% créditos)</p>
        </li>
        <li class="timeline-item">
          <div class="timeline-dot" aria-hidden="true"></div>
          <h3>Fase 2: Búsqueda</h3>
          <p>Encontrar empresa y obtener carta de aceptación</p>
        </li>
        <li class="timeline-item">
          <div class="timeline-dot" aria-hidden="true"></div>
          <h3>Fase 3: Anteproyecto</h3>
          <p>Elaborar y presentar anteproyecto</p>
        </li>
        <li class="timeline-item">
          <div class="timeline-dot" aria-hidden="true"></div>
          <h3>Fase 4: Ejecución</h3>
          <p>Realizar residencia (480 horas)</p>
        </li>
        <li class="timeline-item">
          <div class="timeline-dot" aria-hidden="true"></div>
          <h3>Fase 5: Evaluación</h3>
          <p>Presentar informe final y recibir evaluación</p>
        </li>
      </ol>
    </section>

    <!-- Checklist Interactivo -->
    <section class="tarjeta" aria-label="Checklist de requisitos">
      <h2><i class="fas fa-tasks" aria-hidden="true"></i> Checklist de Requisitos</h2>
      <div class="checklist-item">
        <input type="checkbox" id="req1">
        <label for="req1">Haber cursado entre el 70% y el 80% de los créditos</label>
      </div>
      <div class="checklist-item">
        <input type="checkbox" id="req2">
        <label for="req2">No tener materias reprobadas</label>
      </div>
      <div class="checklist-item">
        <input type="checkbox" id="req3">
        <label for="req3">Tener el servicio social liberado o en proceso</label>
      </div>
      <div class="checklist-item">
        <input type="checkbox" id="req4">
        <label for="req4">Carta de presentación de la universidad</label>
      </div>
      <div class="checklist-item">
        <input type="checkbox" id="req5">
        <label for="req5">Carta de aceptación de la empresa</label>
      </div>
      <div class="checklist-item">
        <input type="checkbox" id="req6">
        <label for="req6">Anteproyecto aprobado</label>
      </div>
      <div class="checklist-item">
        <input type="checkbox" id="req7">
        <label for="req7">Kárdex o historial académico actualizado</label>
      </div>
      <div class="checklist-item">
        <input type="checkbox" id="req8">
        <label for="req8">CURP, INE y comprobante de domicilio</label>
      </div>
      <div class="checklist-item">
        <input type="checkbox" id="req9">
        <label for="req9">Seguro facultativo o particular vigente</label>
      </div>
    </section>

    <!-- Barra de Progreso -->
    <section class="tarjeta" aria-label="Tu progreso de requisitos">
      <h2>Progreso de Residencia</h2>
      <div class="progress-container" role="progressbar"
           aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
           aria-label="Progreso de requisitos completados" id="progressContainer">
        <div class="progress-bar" id="progressBar" style="width:0%">0%</div>
      </div>
      <p>Marca los requisitos completados para ver tu progreso</p>
    </section>

    <!-- Documentación Detallada -->
    <section class="tarjeta" aria-label="Documentación detallada">
      <h2><i class="fas fa-file-alt" aria-hidden="true"></i> Documentación Detallada</h2>
      <div class="searchable-content">
        <h3>Requisitos Académicos:</h3>
        <ul>
          <li>Haber cursado entre el 70% y el 80% del total de créditos de la carrera.</li>
          <li>No tener materias reprobadas (algunas permiten una o dos).</li>
          <li>Tener el servicio social liberado o en proceso.</li>
        </ul>
        <h3>Documentación Solicitada:</h3>
        <ul>
          <li>Carta de presentación de la universidad.</li>
          <li>Carta de aceptación de la empresa u organización.</li>
          <li>Anteproyecto o plan de trabajo:
            <ul>
              <li>Objetivo del proyecto</li>
              <li>Actividades a realizar</li>
              <li>Cronograma</li>
              <li>Resultados esperados</li>
            </ul>
          </li>
          <li>Kárdex o historial académico actualizado</li>
          <li>CURP, INE y comprobante de domicilio</li>
          <li>Seguro facultativo o particular vigente</li>
        </ul>
      </div>
    </section>

    <!-- Descargas -->
    <section class="tarjeta" aria-label="Documentos para descargar">
      <h2><i class="fas fa-download" aria-hidden="true"></i> Documentos para Descargar</h2>
      <div class="descargas-servicio">
        <a href="https://drive.google.com/file/d/1oJR4zSpAX6o99eMSuqot4T2DOYhlbAFX/view?usp=sharing"
           target="_blank" rel="noopener noreferrer" class="boton-descarga"
           aria-label="Descargar Solicitud de Residencia (PDF, abre en nueva pestaña)">
          <i class="fas fa-file-pdf" aria-hidden="true"></i> Solicitud de Residencia
        </a>
        <a href="https://drive.google.com/file/d/1oMtGJNoBKg2Z8n6q1hL04VrRIbzKaWNC/view?usp=sharing"
           target="_blank" rel="noopener noreferrer" class="boton-descarga"
           aria-label="Descargar Formato de Seguimiento y Evaluación (PDF, abre en nueva pestaña)">
          <i class="fas fa-file-pdf" aria-hidden="true"></i> Seguimiento y Evaluación
        </a>
      </div>
    </section>

    <!-- FAQ -->
    <section class="tarjeta" aria-label="Preguntas frecuentes">
      <h2><i class="fas fa-question-circle" aria-hidden="true"></i> Preguntas Frecuentes</h2>
      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          <span>¿Cuánto dura la residencia profesional?</span>
          <i class="fas fa-chevron-down faq-icon" aria-hidden="true"></i>
        </button>
        <div class="faq-answer" role="region">
          <p>La residencia profesional tiene una duración de 480 horas, equivalentes a 6 meses de tiempo completo.</p>
        </div>
      </div>
      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          <span>¿Puedo hacer residencia en mi propio trabajo?</span>
          <i class="fas fa-chevron-down faq-icon" aria-hidden="true"></i>
        </button>
        <div class="faq-answer" role="region">
          <p>Sí, siempre y cuando las actividades estén relacionadas con tu carrera y cumplan con los requisitos académicos.</p>
        </div>
      </div>
      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          <span>¿Qué pasa si no encuentro empresa?</span>
          <i class="fas fa-chevron-down faq-icon" aria-hidden="true"></i>
        </button>
        <div class="faq-answer" role="region">
          <p>El departamento de residencias tiene convenios con empresas y puede ayudarte a encontrar una opción adecuada.</p>
        </div>
      </div>
    </section>

  </div>
</main>

<script src="assets/js/residencia.js"></script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
