<?php
$tsj_module    = 'requisitos';
$tsj_title     = 'Servicio Social';
$tsj_extra_css = [
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
    'assets/css/style.css',
];
require_once __DIR__ . '/../../shared/config.php';

try {
    $db   = getPDO(DB_NAME);
    $tipo = 'servicio_social';

    $requisitos  = $db->prepare('SELECT texto FROM requisitos_items WHERE tipo=? AND activo=1 ORDER BY orden');
    $requisitos->execute([$tipo]);
    $requisitos  = $requisitos->fetchAll();

    $fases       = $db->prepare('SELECT * FROM timeline_fases WHERE tipo=? AND activo=1 ORDER BY orden');
    $fases->execute([$tipo]);
    $fases       = $fases->fetchAll();

    $documentos  = $db->prepare('SELECT * FROM documentos_descargables WHERE tipo=? AND activo=1 ORDER BY orden');
    $documentos->execute([$tipo]);
    $documentos  = $documentos->fetchAll();

    $faqs        = $db->prepare('SELECT pregunta, respuesta FROM faq WHERE tipo=? AND activo=1 ORDER BY orden');
    $faqs->execute([$tipo]);
    $faqs        = $faqs->fetchAll();
} catch (\Throwable $e) {
    $requisitos = $fases = $documentos = $faqs = [];
}

require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main" class="contenedor">
  <div class="contenido-centrado">

    <div class="tsj-page-header">
      <div class="tsj-page-header-line"></div>
      <h1><span class="tsj-accent">Servicio Social</span></h1>
      <p class="tsj-page-header-sub">Requisitos, documentos y pasos del proceso</p>
    </div>

    <nav class="navegacion-secciones" aria-label="Secciones del módulo">
      <a href="residencia.php" class="boton-navegacion">Residencia</a>
      <a href="servicio-social.php" class="boton-navegacion active" aria-current="page">Servicio Social</a>
    </nav>

    <?php if (!empty($fases)): ?>
    <!-- Timeline del Proceso -->
    <section class="tarjeta" aria-label="Timeline del proceso de servicio social">
      <h2><i class="fas fa-clock" aria-hidden="true"></i> Timeline del Proceso</h2>
      <ol class="timeline">
        <?php foreach ($fases as $i => $f): ?>
        <li class="timeline-item">
          <div class="timeline-dot" aria-hidden="true"></div>
          <h3>Fase <?= $i + 1 ?>: <?= htmlspecialchars($f['titulo']) ?></h3>
          <?php if ($f['descripcion']): ?>
            <p><?= htmlspecialchars($f['descripcion']) ?></p>
          <?php endif; ?>
          <?php if ($f['tiempo_referencia']): ?>
            <p><small><?= htmlspecialchars($f['tiempo_referencia']) ?></small></p>
          <?php endif; ?>
        </li>
        <?php endforeach; ?>
      </ol>
    </section>
    <?php endif; ?>

    <?php if (!empty($requisitos)): ?>
    <!-- Checklist Interactivo -->
    <section class="tarjeta" aria-label="Checklist de requisitos">
      <h2><i class="fas fa-tasks" aria-hidden="true"></i> Checklist de Requisitos</h2>
      <?php foreach ($requisitos as $i => $req): ?>
      <div class="checklist-item">
        <input type="checkbox" id="req<?= $i + 1 ?>">
        <label for="req<?= $i + 1 ?>"><?= htmlspecialchars($req['texto']) ?></label>
      </div>
      <?php endforeach; ?>
    </section>

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
    <?php endif; ?>

    <?php if (!empty($documentos)): ?>
    <!-- Documentos para Descargar -->
    <section class="tarjeta" aria-label="Documentos para descargar">
      <h2><i class="fas fa-download" aria-hidden="true"></i> Documentos para Descargar</h2>
      <div class="descargas-servicio">
        <?php foreach ($documentos as $doc): ?>
        <a href="<?= htmlspecialchars($doc['url']) ?>"
           target="_blank" rel="noopener noreferrer" class="boton-descarga"
           aria-label="Descargar <?= htmlspecialchars($doc['nombre']) ?> (abre en nueva pestaña)">
          <i class="fas fa-file-pdf" aria-hidden="true"></i> <?= htmlspecialchars($doc['nombre']) ?>
        </a>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($faqs)): ?>
    <!-- FAQ -->
    <section class="tarjeta" aria-label="Preguntas frecuentes">
      <h2><i class="fas fa-question-circle" aria-hidden="true"></i> Preguntas Frecuentes</h2>
      <?php foreach ($faqs as $faq): ?>
      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          <span><?= htmlspecialchars($faq['pregunta']) ?></span>
          <i class="fas fa-chevron-down faq-icon" aria-hidden="true"></i>
        </button>
        <div class="faq-answer" role="region">
          <p><?= htmlspecialchars($faq['respuesta']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </section>
    <?php endif; ?>

  </div>
</main>

<script src="assets/js/servicio-social.js"></script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
