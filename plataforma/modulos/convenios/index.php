<?php
require_once __DIR__ . '/src/security_headers.php';

$tsj_module    = 'convenios';
$tsj_title     = 'Convenios';
$tsj_extra_css = ['src/output.css', 'src/css/styles.css'];
$tsj_head_extra = '<link rel="stylesheet"'
    . ' href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css"'
    . ' integrity="sha384-9zhnRArCpusIVIudEVdI3QmXKH9nCjEGc2rNvdcQ1utx3a3zbLtW3rBOeJ2PvupL"'
    . ' crossorigin="anonymous" />';
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../shared/lib/auth.php';
require_once __DIR__ . '/../../shared/config.php';
require_once __DIR__ . '/../../shared/header.php';

// CSRF para el modal de sugerir empresa
$_csrf_token = csrfToken();

// Carreras desde BD
$carrerasConv = [];
try {
    $dbConv = getPDO(DB_NAME);
    $carrerasConv = $dbConv->query('SELECT clave, nombre FROM carreras WHERE activo=1 ORDER BY orden')->fetchAll();
} catch (\Throwable $e) {}

// Imágenes y estilos por clave conocida
$convImgs = [
    'IADEV' => ['img'=>'assets/images/logo/imagenes/9M6A4513.webp',   'alt'=>'Personas trabajando con tabletas gráficas',                   'box'=>'corner-box-top',       'corner'=>'corner-box'],
    'IM'    => ['img'=>'assets/images/logo/imagenes/DSC02687_1.webp', 'alt'=>'Personas trabajando en electrónica con laptop',               'box'=>'corner-box-top',       'corner'=>'corner-box'],
    'ISC'   => ['img'=>'assets/images/logo/imagenes/DSC04199_1.webp', 'alt'=>'Estudiantes en sala de computadoras con iMac',                'box'=>'corner-box-top',       'corner'=>'corner-box'],
    'II'    => ['img'=>'assets/images/logo/imagenes/DSC07193_1.webp', 'alt'=>'Persona con herramienta eléctrica en taller',                 'box'=>'corner-box-top',       'corner'=>'corner-box'],
    'LG'    => ['img'=>'assets/images/logo/imagenes/DSC06661_1.webp', 'alt'=>'Estudiante de gastronomía preparando una bebida',             'box'=>'corner-box-top-green', 'corner'=>'corner-box-green'],
    'IGE'   => ['img'=>'assets/images/logo/imagenes/DSC08323_1.webp', 'alt'=>'Dos mujeres trabajando en laptops',                           'box'=>'corner-box-top',       'corner'=>'corner-box'],
];
$convLogoDefault = 'assets/images/logo/gear-svgrepo-com_copy.svg';
$convImgDefault  = ['img'=>'assets/images/logo/imagenes/DSC04199_1.webp','alt'=>'Carrera','box'=>'corner-box-top','corner'=>'corner-box'];
?>

<main id="main">

  <div class="tsj-page-header">
    <div class="tsj-page-header-line"></div>
    <h1>Convenios <span class="tsj-accent">Empresariales</span></h1>
    <p class="tsj-page-header-sub">Empresas vinculadas para residencia profesional, servicio social y prácticas</p>
  </div>

  <!-- Cards de carreras (dinámicas desde BD) -->
  <div class="cards-container w-full p-6 flex justify-center gap-6 flex-wrap">
    <?php foreach ($carrerasConv as $car):
      $est = $convImgs[$car['clave']] ?? $convImgDefault;
      $logo = ($car['clave'] === 'LG')
        ? 'assets/images/logo/graduation-svgrepo-com.svg'
        : $convLogoDefault;
    ?>
    <a href="vista_lista/vista_convenios.php?carrera=<?= urlencode($car['clave']) ?>" class="card">
      <div class="<?= $est['box'] ?>" aria-hidden="true">
        <img src="<?= $logo ?>" alt="" class="corner-logo" />
      </div>
      <img class="ing-sistemas" src="<?= htmlspecialchars($est['img']) ?>"
           alt="<?= htmlspecialchars($est['alt']) ?>" width="400" height="300" loading="lazy" />
      <div class="card-text"><?= htmlspecialchars($car['nombre']) ?></div>
      <div class="<?= $est['corner'] ?>" aria-hidden="true"></div>
    </a>
    <?php endforeach; ?>
    <?php if (empty($carrerasConv)): ?>
    <p style="text-align:center;color:#9ca3af;padding:2rem">No hay carreras registradas.</p>
    <?php endif; ?>
  </div>

  <!-- Sugerir empresa -->
  <div class="w-full flex flex-col items-center gap-2 mb-6">
    <button class="login-button" id="openSuggestModalBtn"
            aria-expanded="false" aria-controls="suggestModalOverlay"
            aria-haspopup="dialog">
      Sugerir una empresa
    </button>
  </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"
        integrity="sha384-YB/DdIkloKoRpclWB8bNcYXWakt57USgtQPDzvnIDHYU0lasD5eWlXVo1S4ODukY"
        crossorigin="anonymous" defer></script>
<script src="src/js/script.js" defer></script>

<!-- Modal Sugerir Empresa -->
<div class="suggest-modal-overlay" id="suggestModalOverlay"
     role="dialog" aria-modal="true" aria-labelledby="suggestModalTitle" aria-hidden="true">
  <div class="suggest-modal">
    <button class="suggest-modal-close" id="closeSuggestModal"
            aria-label="Cerrar formulario de sugerencia">&times;</button>
    <h2 id="suggestModalTitle">Sugerir una empresa</h2>
    <form id="suggestForm">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_csrf_token, ENT_QUOTES, 'UTF-8') ?>">
      <div class="login-input-container">
        <label for="sug-empresa" class="sr-only">Nombre de la empresa</label>
        <input type="text" id="sug-empresa" name="nombre_empresa"
               placeholder="Nombre de la empresa" required maxlength="200" />
      </div>
      <div class="login-input-container">
        <label for="sug-correo" class="sr-only">Correo de contacto</label>
        <input type="email" id="sug-correo" name="correo_empresa"
               placeholder="Correo de contacto con la empresa" required maxlength="254" />
      </div>
      <div class="login-input-container">
        <label for="sug-contacto" class="sr-only">Nombre del contacto</label>
        <input type="text" id="sug-contacto" name="nombre_contacto"
               placeholder="Nombre de la persona con la que se contactará" required maxlength="200" />
      </div>
      <div id="suggestFormError" class="login-error" role="alert" style="display:none;"></div>
      <button type="submit" class="login-button">Enviar</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
