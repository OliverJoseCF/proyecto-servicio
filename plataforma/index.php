<?php
$tsj_module    = '';
$tsj_title     = 'Portal de Servicios';
$tsj_has_hero  = true;
$tsj_extra_css = [];

require_once __DIR__ . '/shared/config.php';

// Cargar datos dinámicos de BD
try {
    $db = getPDO(DB_NAME);

    $carrusel = $db->query(
        'SELECT * FROM carrusel_fotos WHERE activo=1 ORDER BY orden LIMIT 6'
    )->fetchAll();

    $avisos = $db->query(
        'SELECT * FROM avisos WHERE activo=1 ORDER BY orden, fecha DESC LIMIT 5'
    )->fetchAll();

    $faqs_general = $db->query(
        'SELECT * FROM faq WHERE tipo="general" AND activo=1 ORDER BY orden LIMIT 10'
    )->fetchAll();

    $db_ok = true;
} catch (\Throwable $e) {
    $carrusel = $avisos = $faqs_general = [];
    $db_ok    = false;
}

$tsj_head_extra = '<style>
body { background: #f0f2f7; font-family: var(--tsj-font); }

/* ── Carrusel ───────────────────────────────────────────── */
.portal-carousel {
    position: relative;
    overflow: hidden;
    background: var(--tsj-blue-dark);
    max-height: 380px;
}
.portal-carousel-track {
    display: flex;
    transition: transform .5s ease;
}
.portal-carousel-slide {
    min-width: 100%;
    position: relative;
}
.portal-carousel-slide img {
    width: 100%;
    height: 380px;
    object-fit: cover;
    display: block;
}
.portal-carousel-caption {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    background: linear-gradient(transparent, rgba(10,5,50,.75));
    padding: 32px 40px 28px;
    color: #fff;
}
.portal-carousel-caption h2 { margin:0 0 4px; font-size:1.4rem; font-weight:700; }
.portal-carousel-caption p  { margin:0; font-size:.9rem; opacity:.85; }
.portal-carousel-btn {
    position: absolute; top: 50%; transform: translateY(-50%);
    background: rgba(255,255,255,.2); border: none; border-radius: 50%;
    width: 40px; height: 40px; cursor: pointer; color: #fff;
    display: flex; align-items: center; justify-content: center;
    backdrop-filter: blur(4px); transition: background .2s;
    font-size: 20px; line-height: 1;
}
.portal-carousel-btn:hover { background: rgba(255,255,255,.35); }
.portal-carousel-btn--prev { left: 16px; }
.portal-carousel-btn--next { right: 16px; }
.portal-carousel-dots {
    position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%);
    display: flex; gap: 6px;
}
.portal-carousel-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: rgba(255,255,255,.4); cursor: pointer;
    border: none; padding: 0; transition: background .2s;
}
.portal-carousel-dot.active { background: #fff; }

/* ── Hero ───────────────────────────────────────────────── */
.portal-hero {
    background: linear-gradient(135deg, var(--tsj-blue-dark) 0%, var(--tsj-blue) 45%, #2a1080 100%);
    padding: calc(var(--tsj-header-h) + 60px) 32px 72px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.portal-hero::before {
    content: \'\';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse at 20% 50%, rgba(236,90,104,.12) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 30%, rgba(50,18,154,.3) 0%, transparent 55%);
    pointer-events: none;
}
.portal-hero-label {
    display: flex; align-items: center; justify-content: center;
    gap: 14px; color: rgba(255,255,255,.45);
    font-size: 11px; font-weight: 500; letter-spacing: 3px;
    text-transform: uppercase; margin-bottom: 22px;
}
.portal-hero-label::before, .portal-hero-label::after {
    content: \'\'; width: 40px; height: 1px; background: rgba(255,255,255,.2);
}
.portal-hero h1 {
    font-size: clamp(2rem, 4.5vw, 3rem); font-weight: 800;
    color: #fff; margin: 0 0 14px; line-height: 1.15; letter-spacing: -0.5px;
}
.portal-hero h1 span { color: var(--tsj-pink); }
.portal-hero > p {
    font-size: 1rem; color: rgba(255,255,255,.6);
    max-width: 520px; margin: 0 auto; line-height: 1.7; font-weight: 300;
}
.portal-hero-wave {
    position: absolute; bottom: -1px; left: 0; right: 0;
    height: 40px; background: #f0f2f7;
    clip-path: ellipse(55% 100% at 50% 100%);
}

/* ── Sección genérica ───────────────────────────────────── */
.portal-section {
    max-width: 1140px; margin: 0 auto; padding: 48px 24px 56px;
}
.portal-section-header {
    display: flex; align-items: center; gap: 12px; margin-bottom: 32px;
}
.portal-section-line { flex: 1; height: 1px; background: #d8dce8; }
.portal-section-title {
    font-size: 12px; font-weight: 700; letter-spacing: 2.5px;
    text-transform: uppercase; color: #8892a8; white-space: nowrap;
}

/* ── Módulos ────────────────────────────────────────────── */
.portal-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
}
@media (max-width: 900px) { .portal-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px) { .portal-grid { grid-template-columns: 1fr; } }

.portal-card {
    background: #fff; border-radius: var(--tsj-radius-lg);
    padding: 28px 24px 24px; text-decoration: none; color: inherit;
    display: flex; flex-direction: column; gap: 12px;
    transition: transform .22s ease, box-shadow .22s ease;
    box-shadow: 0 2px 10px rgba(20,10,80,.06);
    border: 1px solid #e8eaf2; position: relative; overflow: hidden;
}
.portal-card::before {
    content: \'\'; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--tsj-blue-dark), var(--tsj-blue));
    opacity: 0; transition: opacity .22s;
}
.portal-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(26,9,96,.12); }
.portal-card:hover::before { opacity: 1; }
.portal-card-icon {
    width: 48px; height: 48px; border-radius: 12px;
    background: var(--tsj-blue-50);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.portal-card-icon svg { width:24px;height:24px;stroke:var(--tsj-blue);fill:none;stroke-width:1.75;stroke-linecap:round;stroke-linejoin:round; }
.portal-card-body { flex: 1; }
.portal-card h2 { font-size:1rem;font-weight:700;color:#1a0960;margin:0 0 6px; }
.portal-card p  { font-size:.82rem;color:var(--tsj-gray-600);margin:0;line-height:1.55; }
.portal-card-arrow {
    display:flex;align-items:center;gap:5px;font-size:12px;font-weight:600;
    color:var(--tsj-blue);margin-top:4px;opacity:0;
    transform:translateX(-4px);transition:opacity .2s,transform .2s;
}
.portal-card:hover .portal-card-arrow { opacity:1;transform:translateX(0); }
@media (hover:none) { .portal-card-arrow { opacity:1;transform:translateX(0); } }
.portal-card-arrow svg { width:14px;height:14px;stroke:var(--tsj-blue);fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round; }

/* ── Avisos ─────────────────────────────────────────────── */
.avisos-list { display:flex;flex-direction:column;gap:14px; }
.aviso-card {
    background:#fff;border-radius:var(--tsj-radius);
    border-left:4px solid var(--tsj-pink);
    padding:16px 20px;
    box-shadow:0 2px 8px rgba(20,10,80,.06);
    display:flex;gap:16px;align-items:flex-start;
}
.aviso-fecha {
    background:var(--tsj-blue-50);color:var(--tsj-blue);
    border-radius:8px;padding:6px 10px;
    font-size:11px;font-weight:700;text-align:center;
    min-width:52px;flex-shrink:0;line-height:1.3;
}
.aviso-fecha span { display:block;font-size:16px;font-weight:800; }
.aviso-body h3 { margin:0 0 4px;font-size:.95rem;font-weight:700;color:#1a0960; }
.aviso-body p  { margin:0;font-size:.82rem;color:var(--tsj-gray-600);line-height:1.5; }

/* ── FAQ ────────────────────────────────────────────────── */
.faq-list { display:flex;flex-direction:column;gap:10px; }
.faq-item {
    background:#fff;border-radius:var(--tsj-radius);
    border:1px solid #e8eaf2;
    box-shadow:0 2px 6px rgba(20,10,80,.04);overflow:hidden;
}
.faq-pregunta {
    width:100%;background:none;border:none;
    padding:16px 20px;text-align:left;cursor:pointer;
    display:flex;justify-content:space-between;align-items:center;
    font-family:var(--tsj-font);font-size:.92rem;font-weight:600;color:#1a0960;
    transition:background .2s;
}
.faq-pregunta:hover { background:#f8f9ff; }
.faq-pregunta svg {
    width:18px;height:18px;stroke:#8892a8;fill:none;
    stroke-width:2.5;flex-shrink:0;transition:transform .25s;
}
.faq-item.open .faq-pregunta svg { transform:rotate(180deg); }
.faq-respuesta {
    max-height:0;overflow:hidden;transition:max-height .3s ease;
    padding:0 20px;font-size:.88rem;color:var(--tsj-gray-600);line-height:1.6;
}
.faq-item.open .faq-respuesta { max-height:300px;padding:0 20px 16px; }

/* ── 2 columnas en escritorio para avisos+faq ────────── */
.portal-two-col {
    display:grid;grid-template-columns:1fr 1fr;gap:40px;
}
@media(max-width:720px){ .portal-two-col { grid-template-columns:1fr; } }
</style>';

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
    reset();
  })();
  </script>

<?php else: ?>
  <!-- ── Hero (sin carrusel) ──────────────────────────────── -->
  <section class="portal-hero" aria-label="Bienvenida">
    <p class="portal-hero-label" aria-hidden="true">Sistema de Servicios Institucionales</p>
    <h1>Tecnológico Superior<br><span>de Jalisco</span></h1>
    <p>Accede a los módulos y recursos académicos del Campus Chapala desde un solo lugar.</p>
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

      <a href="<?= $base ?>/modulos/visitantes/index.php" class="portal-card">
        <div class="portal-card-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="portal-card-body">
          <h2>Visitantes</h2>
          <p>Directorio institucional, docentes, carreras y servicios del tecnológico.</p>
          <div class="portal-card-arrow" aria-hidden="true">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
        </div>
      </a>

      <a href="<?= $base ?>/modulos/biblioteca/buscar.php" class="portal-card">
        <div class="portal-card-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        </div>
        <div class="portal-card-body">
          <h2>Biblioteca</h2>
          <p>Catálogo de libros, búsqueda y solicitud de préstamos bibliográficos.</p>
          <div class="portal-card-arrow" aria-hidden="true">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
        </div>
      </a>

      <a href="<?= $base ?>/modulos/convenios/index.php" class="portal-card">
        <div class="portal-card-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M9 14l-4-4 4-4"/><path d="M5 10h11a4 4 0 0 1 0 8h-1"/></svg>
        </div>
        <div class="portal-card-body">
          <h2>Convenios</h2>
          <p>Directorio de convenios por carrera, residencia y servicio social.</p>
          <div class="portal-card-arrow" aria-hidden="true">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
        </div>
      </a>

      <a href="<?= $base ?>/modulos/horarios/index.php" class="portal-card">
        <div class="portal-card-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="portal-card-body">
          <h2>Horarios</h2>
          <p>Búsqueda de maestros y consulta de horarios por carrera.</p>
          <div class="portal-card-arrow" aria-hidden="true">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
        </div>
      </a>

      <a href="<?= $base ?>/modulos/requisitos/residencia.php" class="portal-card">
        <div class="portal-card-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="portal-card-body">
          <h2>Requisitos</h2>
          <p>Residencia profesional y servicio social: checklist, documentos y descargas.</p>
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
                <?= htmlspecialchars($q['respuesta']) ?>
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
