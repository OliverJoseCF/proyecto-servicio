<?php
$tsj_module    = 'inicio';
$tsj_title     = 'Portal de Servicios';
$tsj_has_hero  = true;
$tsj_extra_css = ['shared/assets/css/portal.css'];

require_once __DIR__ . '/shared/config.php';

// Cargar datos dinámicos de BD
try {
    $db = getPDO(DB_NAME);

    $carrusel = $db->query(
        'SELECT url, titulo, subtitulo FROM carrusel_fotos WHERE activo=1 ORDER BY orden LIMIT 6'
    )->fetchAll();

    // Defensivo: las columnas de vigencia pueden no existir aún en BDs previas
    try {
        $avisos = $db->query(
            'SELECT fecha, titulo, descripcion FROM avisos WHERE activo=1
               AND (publicar_desde IS NULL OR publicar_desde <= CURDATE())
               AND (publicar_hasta IS NULL OR publicar_hasta >= CURDATE())
             ORDER BY orden, fecha DESC LIMIT 5'
        )->fetchAll();
    } catch (\PDOException $eCol) {
        $avisos = $db->query(
            'SELECT fecha, titulo, descripcion FROM avisos WHERE activo=1 ORDER BY orden, fecha DESC LIMIT 5'
        )->fetchAll();
    }

    $faqs_general = $db->query(
        'SELECT pregunta, respuesta FROM faq WHERE tipo="general" AND activo=1 ORDER BY orden LIMIT 10'
    )->fetchAll();

    $db_ok = true;
} catch (\Throwable $e) {
    $carrusel = $avisos = $faqs_general = [];
    $db_ok    = false;
}

// Datos del portal para el hero fallback (config_data cargado vía header)
if (!function_exists('tsjConfig')) {
    require_once __DIR__ . '/shared/lib/config_data.php';
}
$_hero_institu = tsjConfig('nombre_institucion', 'Tecnológico Superior de Jalisco');
$_hero_campus  = tsjConfig('campus',             'Campus Chapala');
$_hero_desc    = tsjConfig('descripcion_portal',
    'Accede a los módulos y recursos académicos del Campus Chapala desde un solo lugar.');

// CSS del portal extraído a shared/assets/css/portal.css (cargado vía $tsj_extra_css)
$tsj_head_extra = '';

require_once __DIR__ . '/shared/header.php';
?>

<main id="main">

<?php if (!empty($carrusel)): ?>
  <!-- ── Carrusel ────────────────────────────────────────── -->
  <div class="portal-carousel" id="carousel">
    <div class="portal-carousel-track" id="carousel-track">
      <?php foreach ($carrusel as $slide): ?>
      <div class="portal-carousel-slide">
        <img src="<?= htmlspecialchars($slide['url']) ?>"
             alt="<?= htmlspecialchars($slide['titulo'] ?? '') ?>"
             loading="lazy">
        <?php if ($slide['titulo'] || $slide['subtitulo']): ?>
        <div class="portal-carousel-caption">
          <?php if ($slide['titulo']): ?>
            <h2><?= htmlspecialchars($slide['titulo']) ?></h2>
          <?php endif; ?>
          <?php if ($slide['subtitulo']): ?>
            <p><?= htmlspecialchars($slide['subtitulo']) ?></p>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if (count($carrusel) > 1): ?>
    <button class="portal-carousel-btn portal-carousel-btn--prev" onclick="carouselMove(-1)" aria-label="Anterior">&#8249;</button>
    <button class="portal-carousel-btn portal-carousel-btn--next" onclick="carouselMove(1)"  aria-label="Siguiente">&#8250;</button>
    <div class="portal-carousel-dots" id="carousel-dots">
      <?php foreach ($carrusel as $i => $s): ?>
      <button class="portal-carousel-dot <?= $i===0?'active':'' ?>"
              onclick="carouselGo(<?= $i ?>)" aria-label="Diapositiva <?= $i+1 ?>"></button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <script>
  (function(){
    var track = document.getElementById('carousel-track');
    var dots  = document.querySelectorAll('.portal-carousel-dot');
    var total = <?= count($carrusel) ?>;
    var cur   = 0;
    var timer;

    function go(n){
      cur = (n + total) % total;
      track.style.transform = 'translateX(-' + (cur * 100) + '%)';
      dots.forEach(function(d,i){ d.classList.toggle('active', i===cur); });
    }
    window.carouselMove = function(d){ go(cur+d); reset(); };
    window.carouselGo   = function(n){ go(n); reset(); };

    function reset(){
      clearInterval(timer);
      timer = setInterval(function(){ go(cur+1); }, 5000);
    }
    // Pausar el autoavance cuando la pestaña no está visible (Page Visibility API)
    document.addEventListener('visibilitychange', function(){
      if (document.hidden) { clearInterval(timer); } else { reset(); }
    });
    reset();
  })();
  </script>

<?php else: ?>
  <!-- ── Hero (sin carrusel) ──────────────────────────────── -->
  <section class="portal-hero" aria-label="Bienvenida">
    <p class="portal-hero-label" aria-hidden="true">Sistema de Servicios Institucionales</p>
    <h1><?= htmlspecialchars($_hero_institu, ENT_QUOTES, 'UTF-8') ?></h1>
    <p><?= htmlspecialchars($_hero_desc, ENT_QUOTES, 'UTF-8') ?></p>
    <div class="portal-hero-wave" aria-hidden="true"></div>
  </section>
<?php endif; ?>

  <!-- ── Módulos disponibles ──────────────────────────────── -->
  <div class="portal-section">
    <div class="portal-section-header" aria-hidden="true">
      <div class="portal-section-line"></div>
      <span class="portal-section-title">Módulos disponibles</span>
      <div class="portal-section-line"></div>
    </div>

    <div class="portal-grid">

      <a href="<?= $base ?>/modulos/visitantes/Ubicacion.php" class="portal-card">
        <div class="portal-card-icon" style="background:#ede9ff" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <div class="portal-card-body">
          <h2>Ubicación del Campus</h2>
          <p>Cómo llegar al Campus Chapala: dirección, mapa y referencias de acceso.</p>
          <div class="portal-card-arrow" aria-hidden="true">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
        </div>
      </a>

      <a href="<?= $base ?>/modulos/biblioteca/buscar.php" class="portal-card">
        <div class="portal-card-icon" style="background:#dcfce7" aria-hidden="true">
          <svg viewBox="0 0 24 24" style="stroke:#16a34a"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        </div>
        <div class="portal-card-body">
          <h2>Biblioteca</h2>
          <p>Consulta el catálogo de libros y solicita préstamos del acervo bibliográfico.</p>
          <div class="portal-card-arrow" aria-hidden="true">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
        </div>
      </a>

      <a href="<?= $base ?>/modulos/convenios/index.php" class="portal-card">
        <div class="portal-card-icon" style="background:#fef3c7" aria-hidden="true">
          <svg viewBox="0 0 24 24" style="stroke:#b45309"><path d="M9 14l-4-4 4-4"/><path d="M5 10h11a4 4 0 0 1 0 8h-1"/></svg>
        </div>
        <div class="portal-card-body">
          <h2>Convenios</h2>
          <p>Empresas vinculadas para residencia profesional, servicio social y prácticas.</p>
          <div class="portal-card-arrow" aria-hidden="true">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
        </div>
      </a>

      <a href="<?= $base ?>/modulos/horarios/index.php" class="portal-card">
        <div class="portal-card-icon" style="background:#e0e7ff" aria-hidden="true">
          <svg viewBox="0 0 24 24" style="stroke:#4338ca"><circle cx="11" cy="11" r="7"/><path d="M11 8v3l2 2"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
        <div class="portal-card-body">
          <h2>Buscar Maestro</h2>
          <p>Encuentra a tus maestros, consulta sus horarios y datos de contacto.</p>
          <div class="portal-card-arrow" aria-hidden="true">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
        </div>
      </a>

      <a href="<?= $base ?>/modulos/requisitos/residencia.php" class="portal-card">
        <div class="portal-card-icon" style="background:#ffe4e8" aria-hidden="true">
          <svg viewBox="0 0 24 24" style="stroke:#ec5a68"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="portal-card-body">
          <h2>Serv. Social / Residencia</h2>
          <p>Requisitos, documentos descargables, fases del proceso y preguntas frecuentes.</p>
          <div class="portal-card-arrow" aria-hidden="true">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
        </div>
      </a>

      <a href="<?= $base ?>/modulos/visitantes/nuevoIngreso.php" class="portal-card">
        <div class="portal-card-icon" style="background:#f0fdf4" aria-hidden="true">
          <svg viewBox="0 0 24 24" style="stroke:#16a34a"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/></svg>
        </div>
        <div class="portal-card-body">
          <h2>Admisiones</h2>
          <p>Nuevo ingreso y reinscripción: requisitos, examen de admisión y generación de comprobantes.</p>
          <div class="portal-card-arrow" aria-hidden="true">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
        </div>
      </a>

    </div>
  </div>

<?php if (!empty($avisos) || !empty($faqs_general)): ?>
  <!-- ── Avisos + FAQ ──────────────────────────────────────── -->
  <div style="background:#fff;border-top:1px solid #e8eaf2;padding:48px 24px 64px">
    <div class="portal-section" style="padding:0;max-width:1140px">
      <div class="portal-two-col">

        <?php if (!empty($avisos)): ?>
        <!-- Avisos importantes -->
        <div>
          <div class="portal-section-header" style="margin-bottom:24px">
            <div class="portal-section-line"></div>
            <span class="portal-section-title">Avisos importantes</span>
            <div class="portal-section-line"></div>
          </div>
          <div class="avisos-list">
            <?php foreach ($avisos as $av):
              $dt  = new DateTime($av['fecha']);
              $mes = ['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'][(int)$dt->format('n')];
            ?>
            <div class="aviso-card">
              <div class="aviso-fecha">
                <span><?= $dt->format('d') ?></span>
                <?= $mes ?>
              </div>
              <div class="aviso-body">
                <h3><?= htmlspecialchars($av['titulo']) ?></h3>
                <?php if ($av['descripcion']): ?>
                  <p><?= htmlspecialchars($av['descripcion']) ?></p>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($faqs_general)): ?>
        <!-- Dudas frecuentes -->
        <div>
          <div class="portal-section-header" style="margin-bottom:24px">
            <div class="portal-section-line"></div>
            <span class="portal-section-title">Dudas frecuentes</span>
            <div class="portal-section-line"></div>
          </div>
          <div class="faq-list">
            <?php foreach ($faqs_general as $i => $q): ?>
            <div class="faq-item" id="faq-<?= $i ?>">
              <button class="faq-pregunta" onclick="toggleFaq(<?= $i ?>)" aria-expanded="false">
                <span><?= htmlspecialchars($q['pregunta']) ?></span>
                <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
              </button>
              <div class="faq-respuesta">
                <div class="faq-respuesta-inner"><?= htmlspecialchars($q['respuesta']) ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <script>
  function toggleFaq(i) {
    var item = document.getElementById('faq-' + i);
    var open = item.classList.toggle('open');
    item.querySelector('.faq-pregunta').setAttribute('aria-expanded', open);
  }
  </script>
<?php endif; ?>

</main>

<?php require_once __DIR__ . '/shared/footer.php'; ?>
