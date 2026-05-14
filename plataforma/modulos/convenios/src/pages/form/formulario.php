<?php
require_once __DIR__ . '/../../session.php';
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: ../../../index.php');
    exit();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$formError = $_SESSION['form_error'] ?? null;
unset($_SESSION['form_error']);

$carreras = [
    'IADEV' => 'Ingeniería en Animación Digital y Efectos Visuales',
    'IM'    => 'Ingeniería Mecatrónica',
    'ISC'   => 'Ingeniería en Sistemas Computacionales',
    'II'    => 'Ingeniería Industrial',
    'LG'    => 'Gastronomía',
    'IGE'   => 'Ingeniería en Gestión Empresarial',
];

$tsj_module    = 'convenios';
$tsj_title     = 'Convenios — Registro de Convenio';
$tsj_extra_css = ['../../output.css', 'formulario.css'];
$tsj_head_extra = '<style>
body { background-color: #f3f4f6; }
.form-page-wrapper {
  min-height: calc(100vh - var(--tsj-header-h, 80px));
  display: flex; align-items: center; justify-content: center;
  padding: 1rem; position: relative;
}
</style>';
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../../../../shared/header.php';
?>

<main id="main" class="form-page-wrapper">
  <!-- Fondo con imagen borrosa -->
  <div aria-hidden="true" style="position:absolute;inset:0;z-index:0;">
    <img src="../../../assets/images/logo/imagenes/chapala01-2.webp" alt=""
         style="width:100%;height:100%;object-fit:cover;">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);"></div>
  </div>

  <div style="background:#fff;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.15);padding:2rem;width:100%;max-width:672px;position:relative;z-index:10;">

    <!-- Logo institucional -->
    <div class="flex items-center justify-center mb-4">
      <img src="../../../assets/images/logo/favicon.png"
           alt="" aria-hidden="true" style="height:80px;width:auto;">
      <div style="margin-left:12px;font-size:14px;font-weight:700;color:#32129A;line-height:1.4;">
        Tecnológico Superior de Jalisco
      </div>
    </div>

    <h1 style="font-size:1.75rem;font-weight:700;text-align:center;color:#1f2937;margin-bottom:8px;">
      Registro de Convenio
    </h1>
    <p style="text-align:center;color:#4b5563;margin-bottom:2rem;">
      Completa el formulario para registrar el convenio
    </p>

    <?php if ($formError !== null): ?>
      <div role="alert" style="margin-bottom:16px;padding:12px;background:#fef2f2;border:1px solid #dc2626;color:#991b1b;border-radius:6px;">
        <?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <!-- Indicador de pasos -->
    <div class="flex justify-between mb-6" role="tablist" aria-label="Pasos del formulario">
      <div class="step-indicator active" id="step1Indicator"
           role="tab" aria-selected="true" aria-controls="step1">
        <span class="font-medium">Paso 1: Información básica</span>
      </div>
      <div class="step-indicator" id="step2Indicator"
           role="tab" aria-selected="false" aria-controls="step2">
        <span class="font-medium">Paso 2: Información adicional</span>
      </div>
    </div>

    <!-- Región de anuncio de paso para lectores de pantalla -->
    <div role="status" id="step-announce" class="sr-only" aria-live="polite" aria-atomic="true"></div>

    <form action="guardarConvenio.php" id="convenioForm" method="POST" enctype="multipart/form-data"
          novalidate>
      <input type="hidden" name="csrf_token"
             value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

      <!-- Paso 1 -->
      <div id="step1" role="tabpanel" aria-labelledby="step1Indicator">

        <div style="margin-bottom:16px;">
          <label for="cv-empresa" style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:4px;">
            Nombre de la empresa <span aria-hidden="true">*</span><span class="sr-only">(obligatorio)</span>
          </label>
          <input type="text" id="cv-empresa" name="empresa" required maxlength="200"
                 style="width:100%;padding:8px 16px;border:1px solid #d1d5db;border-radius:6px;">
        </div>

        <div style="margin-bottom:16px;">
          <label for="cv-carrera" style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:4px;">
            Carrera <span aria-hidden="true">*</span><span class="sr-only">(obligatorio)</span>
          </label>
          <select id="cv-carrera" name="carrera" required
                  style="width:100%;padding:8px 16px;border:1px solid #d1d5db;border-radius:6px;">
            <option value="" disabled selected>Seleccione una carrera</option>
            <?php foreach ($carreras as $clave => $nombre): ?>
              <option value="<?= htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div style="margin-bottom:16px;">
          <label for="cv-tipo" style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:4px;">
            Tipo de convenio <span aria-hidden="true">*</span><span class="sr-only">(obligatorio)</span>
          </label>
          <select id="cv-tipo" name="tipoConvenio" required
                  style="width:100%;padding:8px 16px;border:1px solid #d1d5db;border-radius:6px;">
            <option value="" disabled selected>Seleccione una opción</option>
            <option value="Servicio Social">Servicio Social</option>
            <option value="Prácticas">Prácticas</option>
            <option value="Ambos">Ambos</option>
          </select>
        </div>

        <div style="margin-bottom:16px;">
          <label for="cv-vencimiento" style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:4px;">
            Fecha de vencimiento <span aria-hidden="true">*</span><span class="sr-only">(obligatorio)</span>
          </label>
          <input type="date" id="cv-vencimiento" name="vencimiento" required
                 min="<?= date('Y-m-d') ?>"
                 style="width:100%;padding:8px 16px;border:1px solid #d1d5db;border-radius:6px;">
        </div>

        <div style="margin-bottom:16px;">
          <label for="cv-contacto" style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:4px;">
            Persona de contacto <span aria-hidden="true">*</span><span class="sr-only">(obligatorio)</span>
          </label>
          <input type="text" id="cv-contacto" name="contacto" required maxlength="200"
                 style="width:100%;padding:8px 16px;border:1px solid #d1d5db;border-radius:6px;">
        </div>

        <div style="margin-bottom:16px;">
          <label for="cv-telefono" style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:4px;">
            Teléfono <span aria-hidden="true">*</span><span class="sr-only">(obligatorio)</span>
          </label>
          <input type="tel" id="cv-telefono" name="telefono" required maxlength="25"
                 pattern="[0-9+\-\s()]{7,25}"
                 title="Solo números, +, -, espacios y paréntesis (entre 7 y 25 caracteres)"
                 style="width:100%;padding:8px 16px;border:1px solid #d1d5db;border-radius:6px;">
        </div>

        <div style="margin-bottom:24px;">
          <label for="cv-email" style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:4px;">
            Correo electrónico <span aria-hidden="true">*</span><span class="sr-only">(obligatorio)</span>
          </label>
          <input type="email" id="cv-email" name="email" required maxlength="254"
                 style="width:100%;padding:8px 16px;border:1px solid #d1d5db;border-radius:6px;">
        </div>

        <div style="display:flex;justify-content:center;padding-top:16px;">
          <button type="button" id="continueBtn" class="btn-primary"
                  aria-label="Continuar al paso 2">
            <strong>Continuar</strong>
          </button>
        </div>
      </div>

      <!-- Paso 2 -->
      <div id="step2" hidden role="tabpanel" aria-labelledby="step2Indicator">

        <div style="margin-bottom:16px;">
          <label style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:4px;">
            Logo de la empresa
            <span aria-hidden="true">*</span><span class="sr-only">(obligatorio)</span>
          </label>
          <div style="display:flex;align-items:center;gap:16px;">
            <div style="flex:1;">
              <label style="display:flex;flex-direction:column;align-items:center;padding:8px 16px;background:#fff;border-radius:6px;border:1px solid #d1d5db;cursor:pointer;"
                     for="cv-logo" class="file-input-label">
                <span style="font-size:14px;">Seleccionar archivo</span>
                <input type="file" id="cv-logo" name="logo"
                       accept="image/jpeg,image/png,image/jpg" style="display:none;" required>
              </label>
            </div>
            <div id="logoPreview" style="width:64px;height:64px;border:1px solid #d1d5db;border-radius:6px;background:#f3f4f6;display:none;"
                 aria-hidden="true">
              <span style="font-size:11px;color:#9ca3af;text-align:center;padding:4px;display:block;">Vista previa</span>
            </div>
          </div>
          <p id="logoName" style="margin-top:4px;font-size:12px;color:#4b5563;" aria-live="polite">
            Ningún archivo seleccionado
          </p>
        </div>

        <fieldset style="border:none;padding:0;margin:0 0 16px;">
          <legend style="font-size:16px;font-weight:500;color:#1f2937;margin-bottom:12px;">
            Redes sociales <span style="font-size:12px;font-weight:400;color:#6b7280;">(Opcional)</span>
          </legend>
          <div style="margin-bottom:12px;">
            <label for="cv-website" style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:4px;">Sitio web</label>
            <input type="url" id="cv-website" name="website" placeholder="https://www.ejemplo.com" maxlength="500"
                   style="width:100%;padding:8px 16px;border:1px solid #d1d5db;border-radius:6px;">
          </div>
          <div style="margin-bottom:12px;">
            <label for="cv-facebook" style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:4px;">Facebook</label>
            <input type="url" id="cv-facebook" name="facebook" placeholder="https://www.facebook.com/ejemplo" maxlength="500"
                   style="width:100%;padding:8px 16px;border:1px solid #d1d5db;border-radius:6px;">
          </div>
          <div style="margin-bottom:12px;">
            <label for="cv-youtube" style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:4px;">YouTube</label>
            <input type="url" id="cv-youtube" name="youtube" placeholder="https://www.youtube.com/ejemplo" maxlength="500"
                   style="width:100%;padding:8px 16px;border:1px solid #d1d5db;border-radius:6px;">
          </div>
          <div>
            <label for="cv-twitter" style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:4px;">X (Twitter)</label>
            <input type="url" id="cv-twitter" name="twitter" placeholder="https://x.com/ejemplo" maxlength="500"
                   style="width:100%;padding:8px 16px;border:1px solid #d1d5db;border-radius:6px;">
          </div>
        </fieldset>

        <div style="padding-top:16px;display:flex;justify-content:space-between;">
          <button type="button" id="backBtn"
                  style="padding:0 24px;height:48px;border:2px solid #d1d5db;border-radius:6px;background:#fff;color:#6b7280;cursor:pointer;font-weight:600;"
                  aria-label="Volver al paso 1">
            <strong>Volver</strong>
          </button>
          <button type="submit" class="btn-primary" aria-label="Registrar convenio">
            <strong>Registrar Convenio</strong>
          </button>
        </div>
      </div>

    </form>
  </div>
</main>

<script src="script.js" defer></script>

<?php require_once __DIR__ . '/../../../../../shared/footer.php'; ?>
