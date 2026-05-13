<?php
$tsj_module    = 'requisitos';
$tsj_title     = 'Requisitos — Residencia Profesional';
$tsj_extra_css  = [
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
    'assets/css/style.css',
];
$tsj_head_extra = '<style>
  /* Restaurar header compartido sobre los estilos del módulo */
  header.tsj-header, header.tsj-header::before { all: unset; }
  header.tsj-header { position: fixed; top: 0; left: 0; width: 100%; z-index: 9999; font-family: var(--tsj-font); }
  header.tsj-header .tsj-top-bar { display: block; height: 10px; background: var(--tsj-pink); width: 100%; }
  header.tsj-header .tsj-toolbar { background: var(--tsj-blue); box-shadow: 0 3px 9px rgba(0,0,0,.16); color: #fff; padding: 10px 20px; display: flex; justify-content: center; align-items: center; gap: 40px; }
</style>';
require_once __DIR__ . '/../../shared/header.php';
?>

  <main class="contenedor">
    <div class="contenido-centrado">
      <div class="navegacion-secciones">
        <a href="residencia.php" class="boton-navegacion active">Residencia</a>
        <a href="servicio-social.php" class="boton-navegacion">Servicio Social</a>
      </div>

      <!-- Calculadora de Créditos -->
      <div class="calculator-container tarjeta">
        <h3><i class="fas fa-calculator"></i> Calculadora de Créditos</h3>
        <p>Calcula cuántos créditos necesitas para iniciar residencia</p>
        <input type="number" class="calculator-input" id="totalCredits" placeholder="Créditos totales de tu carrera" onkeyup="calculateCredits()">
        <input type="number" class="calculator-input" id="currentCredits" placeholder="Créditos acreditados" onkeyup="calculateCredits()">
        <div class="calculator-result" id="creditResult">
          Ingresa tus créditos para calcular
        </div>
      </div>

      <!-- Timeline del Proceso -->
      <div class="tarjeta">
        <h3><i class="fas fa-clock"></i> Timeline del Proceso</h3>
        <div class="timeline">
          <div class="timeline-item fade-in">
            <div class="timeline-dot"></div>
            <h4>Fase 1: Preparación</h4>
            <p>Reunir documentos y cumplir requisitos académicos (70-80% créditos)</p>
          </div>
          <div class="timeline-item fade-in">
            <div class="timeline-dot"></div>
            <h4>Fase 2: Búsqueda</h4>
            <p>Encontrar empresa y obtener carta de aceptación</p>
          </div>
          <div class="timeline-item fade-in">
            <div class="timeline-dot"></div>
            <h4>Fase 3: Anteproyecto</h4>
            <p>Elaborar y presentar anteproyecto</p>
          </div>
          <div class="timeline-item fade-in">
            <div class="timeline-dot"></div>
            <h4>Fase 4: Ejecución</h4>
            <p>Realizar residencia (480 horas)</p>
          </div>
          <div class="timeline-item fade-in">
            <div class="timeline-dot"></div>
            <h4>Fase 5: Evaluación</h4>
            <p>Presentar informe final y recibir evaluación</p>
          </div>
        </div>
      </div>

      <!-- Barra de Progreso -->
      <div class="tarjeta">
        <h3>Progreso de Residencia</h3>
        <div class="progress-container">
          <div class="progress-bar" id="progressBar" style="width: 0%">0%</div>
        </div>
        <p>Marca los requisitos completados para ver tu progreso</p>
      </div>

      <!-- Checklist Interactivo -->
      <div class="tarjeta">
        <h3><i class="fas fa-tasks"></i> Checklist de Requisitos</h3>
        <div class="checklist-item">
          <input type="checkbox" id="req1" onchange="updateProgress()">
          <label for="req1">Haber cursado entre el 70% y el 80% de los créditos</label>
        </div>
        <div class="checklist-item">
          <input type="checkbox" id="req2" onchange="updateProgress()">
          <label for="req2">No tener materias reprobadas</label>
        </div>
        <div class="checklist-item">
          <input type="checkbox" id="req3" onchange="updateProgress()">
          <label for="req3">Tener el servicio social liberado o en proceso</label>
        </div>
        <div class="checklist-item">
          <input type="checkbox" id="req4" onchange="updateProgress()">
          <label for="req4">Carta de presentación de la universidad</label>
        </div>
        <div class="checklist-item">
          <input type="checkbox" id="req5" onchange="updateProgress()">
          <label for="req5">Carta de aceptación de la empresa</label>
        </div>
        <div class="checklist-item">
          <input type="checkbox" id="req6" onchange="updateProgress()">
          <label for="req6">Anteproyecto aprobado</label>
        </div>
        <div class="checklist-item">
          <input type="checkbox" id="req7" onchange="updateProgress()">
          <label for="req7">Kárdex o historial académico actualizado</label>
        </div>
        <div class="checklist-item">
          <input type="checkbox" id="req8" onchange="updateProgress()">
          <label for="req8">CURP, INE y comprobante de domicilio</label>
        </div>
        <div class="checklist-item">
          <input type="checkbox" id="req9" onchange="updateProgress()">
          <label for="req9">Seguro facultativo o particular vigente</label>
        </div>
      </div>

      <!-- Documentación Detallada -->
      <div class="tarjeta">
        <h3><i class="fas fa-file-alt"></i> Documentación Detallada</h3>
        <div class="searchable-content">
          <h4>Requisitos Académicos:</h4>
          <ul>
            <li>Haber cursado entre el 70% y el 80% del total de créditos de la carrera.</li>
            <li>No tener materias reprobadas (algunas permiten una o dos).</li>
            <li>Tener el servicio social liberado o en proceso.</li>
          </ul>

          <h4>Documentación Solicitada:</h4>
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
      </div>

      <!-- Descargas -->
      <div class="tarjeta">
        <h3><i class="fas fa-download"></i> Documentos para Descargar</h3>
        <div class="descargas-servicio">
          <a href="https://drive.google.com/file/d/1oJR4zSpAX6o99eMSuqot4T2DOYhlbAFX/view?usp=sharing"
             target="_blank" class="boton-descarga">
            <i class="fas fa-file-pdf"></i> Solicitud de Residencia
          </a>
          <a href="https://drive.google.com/file/d/1oMtGJNoBKg2Z8n6q1hL04VrRIbzKaWNC/view?usp=sharing"
             target="_blank" class="boton-descarga">
            <i class="fas fa-file-pdf"></i> Seguimiento y Evaluación
          </a>
        </div>
      </div>

      <!-- FAQ -->
      <div class="tarjeta">
        <h3><i class="fas fa-question-circle"></i> Preguntas Frecuentes</h3>
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFAQ(this)">
            <span>¿Cuánto dura la residencia profesional?</span>
            <i class="fas fa-chevron-down faq-icon"></i>
          </button>
          <div class="faq-answer">
            <p>La residencia profesional tiene una duración de 480 horas, que equivalen a 6 meses de tiempo completo.</p>
          </div>
        </div>
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFAQ(this)">
            <span>¿Puedo hacer residencia en mi propio trabajo?</span>
            <i class="fas fa-chevron-down faq-icon"></i>
          </button>
          <div class="faq-answer">
            <p>Sí, siempre y cuando las actividades estén relacionadas con tu carrera y cumplan con los requisitos académicos.</p>
          </div>
        </div>
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFAQ(this)">
            <span>¿Qué pasa si no encuentro empresa?</span>
            <i class="fas fa-chevron-down faq-icon"></i>
          </button>
          <div class="faq-answer">
            <p>El departamento de residencias tiene convenios con empresas y puede ayudarte a encontrar una opción adecuada.</p>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    window.addEventListener('DOMContentLoaded', () => {
      animateOnScroll();
      loadProgress();
    });

    function updateProgress() {
      const checkboxes = document.querySelectorAll('.checklist-item input[type="checkbox"]');
      const checked    = document.querySelectorAll('.checklist-item input[type="checkbox"]:checked');
      const bar        = document.getElementById('progressBar');
      const pct        = Math.round((checked.length / checkboxes.length) * 100);
      bar.style.width  = pct + '%';
      bar.textContent  = pct + '%';
      const progress   = [];
      checkboxes.forEach((cb, i) => { progress[i] = cb.checked; });
      localStorage.setItem('residenciaProgress', JSON.stringify(progress));
      checkboxes.forEach(cb => { cb.parentElement.classList.toggle('completed', cb.checked); });
    }

    function loadProgress() {
      const saved = localStorage.getItem('residenciaProgress');
      if (!saved) return;
      const progress  = JSON.parse(saved);
      const checkboxes = document.querySelectorAll('.checklist-item input[type="checkbox"]');
      progress.forEach((checked, i) => { if (checkboxes[i]) checkboxes[i].checked = checked; });
      updateProgress();
    }

    function calculateCredits() {
      const total   = parseFloat(document.getElementById('totalCredits').value)   || 0;
      const current = parseFloat(document.getElementById('currentCredits').value) || 0;
      const result  = document.getElementById('creditResult');
      if (total > 0 && current >= 0) {
        const pct      = Math.round((current / total) * 100);
        const needed70 = Math.round(total * 0.7);
        const remaining = needed70 - current;
        if (pct >= 80) {
          result.innerHTML = `<div style="color:#4CAF50;font-size:1.2rem;margin-bottom:10px;">✅ ¡FELICIDADES!</div><div>Ya puedes iniciar tu residencia</div><div style="color:#666;font-size:.9rem;margin-top:5px;">Tienes ${pct}% de créditos</div>`;
        } else if (pct >= 70) {
          result.innerHTML = `<div style="color:#FF9800;font-size:1.2rem;margin-bottom:10px;">⚠️ ¡CASI LISTO!</div><div>Ya puedes comenzar el proceso</div><div style="color:#666;font-size:.9rem;margin-top:5px;">Tienes ${pct}%</div>`;
        } else {
          result.innerHTML = `<div style="color:#f44336;font-size:1.2rem;margin-bottom:10px;">❌ AÚN NO</div><div>Necesitas más créditos</div><div style="color:#666;font-size:.9rem;margin-top:5px;">Tienes ${pct}% — te faltan ${remaining} créditos</div>`;
        }
      } else {
        result.textContent = 'Ingresa tus créditos para calcular';
      }
    }

    function toggleFAQ(button) {
      const item = button.parentElement;
      item.classList.toggle('active');
      button.nextElementSibling.classList.toggle('active');
    }

    function animateOnScroll() {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity   = '1';
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, { threshold: 0.1 });
      document.querySelectorAll('.timeline-item, .tarjeta').forEach(el => {
        el.style.opacity    = '0';
        el.style.transform  = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
      });
    }
  </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
