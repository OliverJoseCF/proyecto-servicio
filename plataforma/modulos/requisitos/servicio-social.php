<?php
$tsj_module    = 'requisitos';
$tsj_title     = 'Requisitos — Servicio Social';
$tsj_extra_css  = [
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
    'assets/css/style.css',
];
$tsj_head_extra = '<style>
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
                <a href="residencia.php" class="boton-navegacion">Residencia</a>
                <a href="servicio-social.php" class="boton-navegacion active">Servicio Social</a>
            </div>

            <!-- Barra de Progreso -->
            <div class="tarjeta">
                <h3>Progreso de Servicio Social</h3>
                <div class="progress-container">
                    <div class="progress-bar" id="progressBar" style="width: 0%">0%</div>
                </div>
                <p>Marca los requisitos completados para ver tu progreso</p>
            </div>

            <!-- Timeline del Proceso -->
            <div class="tarjeta">
                <h3><i class="fas fa-clock"></i> Timeline del Proceso</h3>
                <div class="timeline">
                    <div class="timeline-item fade-in">
                        <div class="timeline-dot"></div>
                        <h4>Fase 1: Requisitos</h4>
                        <p>Cumplir con el 70% de créditos y obtener constancia</p>
                    </div>
                    <div class="timeline-item fade-in">
                        <div class="timeline-dot"></div>
                        <h4>Fase 2: Documentos</h4>
                        <p>Llenar y entregar solicitud, carta compromiso y plan de trabajo</p>
                    </div>
                    <div class="timeline-item fade-in">
                        <div class="timeline-dot"></div>
                        <h4>Fase 3: Ejecución</h4>
                        <p>Realizar 500 horas en mínimo 6 meses y máximo 2 años</p>
                    </div>
                    <div class="timeline-item fade-in">
                        <div class="timeline-dot"></div>
                        <h4>Fase 4: Reportes</h4>
                        <p>Entregar reportes bimestrales y evaluaciones</p>
                    </div>
                    <div class="timeline-item fade-in">
                        <div class="timeline-dot"></div>
                        <h4>Fase 5: Liberación</h4>
                        <p>Obtener carta de liberación y constancia final</p>
                    </div>
                </div>
            </div>

            <!-- Checklist Interactivo -->
            <div class="tarjeta">
                <h3><i class="fas fa-tasks"></i> Checklist de Requisitos</h3>
                <div class="checklist-item">
                    <input type="checkbox" id="req1" onchange="updateProgress()">
                    <label for="req1">Tener acreditado el 70% de los créditos de la carrera</label>
                </div>
                <div class="checklist-item">
                    <input type="checkbox" id="req2" onchange="updateProgress()">
                    <label for="req2">Solicitud de servicio social</label>
                </div>
                <div class="checklist-item">
                    <input type="checkbox" id="req3" onchange="updateProgress()">
                    <label for="req3">Carta compromiso firmada</label>
                </div>
                <div class="checklist-item">
                    <input type="checkbox" id="req4" onchange="updateProgress()">
                    <label for="req4">Plan de trabajo aprobado</label>
                </div>
                <div class="checklist-item">
                    <input type="checkbox" id="req5" onchange="updateProgress()">
                    <label for="req5">Constancia de créditos del departamento</label>
                </div>
            </div>

            <!-- Información del Servicio Social -->
            <div class="servicio-contenido tarjeta">
                <h3><i class="fas fa-info-circle"></i> Información del Servicio Social</h3>
                <div class="searchable-content">
                    <h4>Requisito Principal:</h4>
                    <p>El requisito único que se requiere para el Servicio Social es tener acreditado el 70% de los créditos de la carrera, no es necesario algo más.</p>

                    <h4>Duración y Créditos:</h4>
                    <p>El servicio social consta de 500 horas, lo cual equivale a 10 créditos y lo deben realizar en un mínimo de 6 meses y un máximo de dos años.</p>

                    <h4>Formatos Requeridos:</h4>
                    <ul>
                        <li>Solicitud de servicio social</li>
                        <li>Carta compromiso, firmada por el interesado</li>
                        <li>Plan de trabajo (llenado en conjunto con la persona que firmará los reportes)</li>
                        <li>Constancia de créditos que expide el departamento de servicio social</li>
                    </ul>
                </div>
            </div>

            <!-- Documentos para Descargar -->
            <div class="tarjeta">
                <h3><i class="fas fa-download"></i> Documentos para Descargar</h3>
                <div class="descargas-servicio">
                    <a href="assets/docs/servicio-social/Evaluación cualitativa .pdf" target="_blank" class="boton-descarga">
                        <i class="fas fa-file-pdf"></i> Evaluación Cualitativa
                    </a>
                    <a href="assets/docs/servicio-social/carta compromiso.pdf" target="_blank" class="boton-descarga">
                        <i class="fas fa-file-pdf"></i> Carta Compromiso
                    </a>
                    <a href="assets/docs/servicio-social/Reporte bimestral 1.pdf" target="_blank" class="boton-descarga">
                        <i class="fas fa-file-pdf"></i> Reporte Bimestral
                    </a>
                    <a href="assets/docs/servicio-social/formato evaluacion.pdf" target="_blank" class="boton-descarga">
                        <i class="fas fa-file-pdf"></i> Formato Evaluación
                    </a>
                </div>
            </div>

            <!-- FAQ -->
            <div class="tarjeta">
                <h3><i class="fas fa-question-circle"></i> Preguntas Frecuentes</h3>
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(this)">
                        <span>¿Puedo hacer mi servicio social en una empresa privada?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Sí, siempre y cuando el proyecto beneficie directamente a la comunidad y cumpla con los objetivos del servicio social.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(this)">
                        <span>¿Qué pasa si no puedo cumplir las 500 horas?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Debes completar las 500 horas obligatoriamente. Si tienes problemas, habla con el departamento de servicio social para buscar soluciones.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(this)">
                        <span>¿Puedo hacer servicio social en mi propia comunidad?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Sí, siempre y cuando el proyecto esté debidamente documentado y aprobado por el departamento de servicio social.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            animateOnScroll();
            loadProgress();
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', saveProgress);
            });
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
            localStorage.setItem('servicioProgress', JSON.stringify(progress));
            checkboxes.forEach(cb => { cb.parentElement.classList.toggle('completed', cb.checked); });
        }

        function loadProgress() {
            const saved = localStorage.getItem('servicioProgress');
            if (!saved) return;
            const progress   = JSON.parse(saved);
            const checkboxes = document.querySelectorAll('.checklist-item input[type="checkbox"]');
            progress.forEach((checked, i) => { if (checkboxes[i]) checkboxes[i].checked = checked; });
            updateProgress();
        }

        function saveProgress() { updateProgress(); }

        function toggleFAQ(button) {
            button.parentElement.classList.toggle('active');
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
