<?php
require_once __DIR__ . '/src/session.php';
require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/security_headers.php';
require_once __DIR__ . '/src/lib/RateLimit.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$loginError = null;
$rl         = new RateLimit(5, 900);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'])) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    if ($rl->isBlocked($ip)) {
        $loginError = 'Demasiados intentos fallidos. Por favor espera 15 minutos antes de intentarlo de nuevo.';
    } elseif (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $loginError = 'Petición inválida. Recarga la página e intenta de nuevo.';
        $rl->record($ip);
    } else {
        $email    = trim($_POST['email']);
        $password = $_POST['password'];
        if ($email === ADMIN_EMAIL && password_verify($password, ADMIN_PASSWORD_HASH)) {
            $rl->reset($ip);
            session_regenerate_id(true);
            $_SESSION['authenticated'] = true;
            $_SESSION['user']          = 'superuser';
            $_SESSION['last_activity'] = time();
            header('Location: vista_lista/lista.php');
            exit();
        } else {
            usleep(300000);
            $rl->record($ip);
            $loginError = 'Credenciales incorrectas.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header('Location: index.php');
        exit();
    }
}

$modalOpen   = ($loginError !== null) ? 'active' : '';
$modalHidden = ($loginError !== null) ? 'false'  : 'true';

$tsj_module    = 'convenios';
$tsj_title     = 'Convenios — TSJ Chapala';
$tsj_extra_css = [
    'src/output.css',
    'src/css/styles.css',
    'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
];
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">

  <div class="w-full p-4" style="background-color:#f5f5f5">
    <div class="oferta">
      <div class="w-full text-center" style="background-color:#f5f5f5">
        <div class="flex justify-center items-center gap-4 relative">
          <h1 class="text-convenios font-bold text-4xl text-base_blue-500 mb-11 -mt-7">Convenios</h1>
          <?php if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true): ?>
            <button class="login-link absolute right-5 top-1/2 transform -translate-y-1/2 text-base_blue-500 texto-inter text-sm"
                    id="loginPageBtn" aria-haspopup="dialog">
              Iniciar Sesión
            </button>
          <?php else: ?>
            <a href="vista_lista/lista.php"
               class="button absolute right-5 top-1/2 transform -translate-y-1/2"
               id="adminButton" aria-label="Ir al panel de administración">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
                <path fill="none" d="M0 0h24v24H0z"></path>
                <path fill="currentColor" d="M16.172 11l-5.364-5.364 1.414-1.414L20 12l-7.778 7.778-1.414-1.414L16.172 13H4v-2z"></path>
              </svg>
              <span class="text">Admin</span>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Cards de carreras -->
  <div class="cards-container w-full p-6 flex justify-center gap-6 flex-wrap">
    <a href="vista_lista/vista_convenios.php?carrera=IADEV" class="card">
      <div class="corner-box-top" aria-hidden="true"><img src="assets/images/logo/gear-svgrepo-com_copy.svg" alt="" class="corner-logo" /></div>
      <img class="ing-sistemas" src="assets/images/logo/imagenes/9M6A4513.webp"
           alt="Personas trabajando en computadoras con tabletas gráficas en un aula" width="400" height="300" loading="lazy" />
      <div class="card-text">Ingeniería en Animación Digital y Efectos Visuales</div>
      <div class="corner-box" aria-hidden="true"></div>
    </a>
    <a href="vista_lista/vista_convenios.php?carrera=IM" class="card">
      <div class="corner-box-top" aria-hidden="true"><img src="assets/images/logo/gear-svgrepo-com_copy.svg" alt="" class="corner-logo" /></div>
      <img class="ing-sistemas" src="assets/images/logo/imagenes/DSC02687_1.webp"
           alt="Tres personas trabajando en un proyecto de electrónica con componentes y una laptop" width="400" height="300" loading="lazy" />
      <div class="card-text">Ingeniería Mecatrónica</div>
      <div class="corner-box" aria-hidden="true"></div>
    </a>
    <a href="vista_lista/vista_convenios.php?carrera=ISC" class="card">
      <div class="corner-box-top" aria-hidden="true"><img src="assets/images/logo/gear-svgrepo-com_copy.svg" alt="" class="corner-logo" /></div>
      <img class="ing-sistemas" src="assets/images/logo/imagenes/DSC04199_1.webp"
           alt="Estudiantes trabajando en una sala de computadoras con equipos iMac" width="400" height="300" loading="lazy" />
      <div class="card-text">Ingeniería en Sistemas Computacionales</div>
      <div class="corner-box" aria-hidden="true"></div>
    </a>
    <a href="vista_lista/vista_convenios.php?carrera=II" class="card">
      <div class="corner-box-top" aria-hidden="true"><img src="assets/images/logo/gear-svgrepo-com_copy.svg" alt="" class="corner-logo" /></div>
      <img class="ing-sistemas" src="assets/images/logo/imagenes/DSC07193_1.webp"
           alt="Persona trabajando con herramienta eléctrica en un taller con equipo de protección" width="400" height="300" loading="lazy" />
      <div class="card-text">Ingeniería Industrial</div>
      <div class="corner-box" aria-hidden="true"></div>
    </a>
    <a href="vista_lista/vista_convenios.php?carrera=LG" class="card">
      <div class="corner-box-top-green" aria-hidden="true"><img src="assets/images/logo/graduation-svgrepo-com.svg" alt="" class="corner-logo" /></div>
      <img class="ing-sistemas" src="assets/images/logo/imagenes/DSC06661_1.webp"
           alt="Estudiante de gastronomía preparando una bebida en una coctelera" width="400" height="300" loading="lazy" />
      <div class="card-text">Gastronomía</div>
      <div class="corner-box-green" aria-hidden="true"></div>
    </a>
    <a href="vista_lista/vista_convenios.php?carrera=IGE" class="card">
      <div class="corner-box-top" aria-hidden="true"><img src="assets/images/logo/gear-svgrepo-com_copy.svg" alt="" class="corner-logo" /></div>
      <img class="ing-sistemas" src="assets/images/logo/imagenes/DSC08323_1.webp"
           alt="Dos mujeres trabajando en laptops en un espacio interior iluminado" width="400" height="300" loading="lazy" />
      <div class="card-text">Ingeniería en Gestión Empresarial</div>
      <div class="corner-box" aria-hidden="true"></div>
    </a>
  </div>

  <!-- Sugerir empresa -->
  <div class="w-full flex flex-col items-center gap-2 mb-6">
    <button class="login-button" id="openSuggestModalBtn"
            aria-expanded="false" aria-controls="suggestModalOverlay"
            aria-haspopup="dialog">
      Sugerir una empresa
    </button>
  </div>

  <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true): ?>
  <div class="w-full p-6 flex justify-center gap-6 flex-wrap bg-base_blue-500 text-white text-center rounded-lg mb-10">
    <a href="src/pages/form/formulario.php"
       class="px-6 py-2 bg-white text-base_blue-500 font-bold rounded hover:bg-gray-200 transition">
      Solicitar convenio
    </a>
  </div>
  <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js" defer></script>
<script src="src/js/script.js" defer></script>

<!-- Modal Sugerir Empresa -->
<div class="suggest-modal-overlay" id="suggestModalOverlay"
     role="dialog" aria-modal="true" aria-labelledby="suggestModalTitle" aria-hidden="true">
  <div class="suggest-modal">
    <button class="suggest-modal-close" id="closeSuggestModal"
            aria-label="Cerrar formulario de sugerencia">&times;</button>
    <h2 id="suggestModalTitle">Sugerir una empresa</h2>
    <form id="suggestForm">
      <input type="hidden" name="csrf_token"
             value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
      <div class="login-input-container">
        <label for="sug-empresa" class="sr-only">Nombre de la empresa</label>
        <input type="text" id="sug-empresa" name="nombre_empresa"
               placeholder="Nombre de la empresa" required maxlength="200"
               aria-label="Nombre de la empresa" />
      </div>
      <div class="login-input-container">
        <label for="sug-correo" class="sr-only">Correo de contacto con la empresa</label>
        <input type="email" id="sug-correo" name="correo_empresa"
               placeholder="Correo de contacto con la empresa" required maxlength="254"
               aria-label="Correo de contacto con la empresa" />
      </div>
      <div class="login-input-container">
        <label for="sug-contacto" class="sr-only">Nombre de la persona de contacto</label>
        <input type="text" id="sug-contacto" name="nombre_contacto"
               placeholder="Nombre de la persona con la que se contactará" required maxlength="200"
               aria-label="Nombre de la persona de contacto" />
      </div>
      <div id="suggestFormError" class="login-error" role="alert" style="display:none;"></div>
      <button type="submit" class="login-button">Enviar</button>
    </form>
  </div>
</div>

<!-- Modal de Login -->
<div class="login-modal-overlay <?= $modalOpen ?>" id="loginModalOverlay"
     role="dialog" aria-modal="true" aria-labelledby="loginModalTitle"
     aria-hidden="<?= $modalHidden ?>">
  <div class="login-modal">
    <button class="login-close-button" id="closeLoginModal"
            aria-label="Cerrar modal de inicio de sesión">&times;</button>
    <h2 id="loginModalTitle">Iniciar Sesión</h2>
    <p class="login-subtitle">Introduce tus datos para iniciar sesión</p>

    <?php if ($loginError !== null): ?>
      <p class="login-error" role="alert"><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form id="loginForm" method="POST" action="">
      <input type="hidden" name="csrf_token"
             value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

      <div class="login-form-group">
        <label for="conv-email">Correo</label>
        <div class="login-input-container">
          <span class="login-icon email-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <rect width="20" height="16" x="2" y="4" rx="2" />
              <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
            </svg>
          </span>
          <input type="email" id="conv-email" name="email"
                 placeholder="admin@mail.com" required autocomplete="email" maxlength="254">
        </div>
      </div>

      <div class="login-form-group">
        <label for="conv-password">Contraseña</label>
        <div class="login-input-container">
          <span class="login-icon lock-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
              <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
          </span>
          <input type="password" id="conv-password" name="password"
                 placeholder="••••••••" required autocomplete="current-password">
          <button type="button" class="toggle-password" id="togglePassword"
                  aria-label="Mostrar contraseña">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
              <circle cx="12" cy="12" r="3" />
            </svg>
          </button>
        </div>
      </div>
      <button type="submit" class="login-button">Acceder</button>
    </form>

    <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?>
      <form method="POST" action="" style="display:inline; margin-top:12px;">
        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="logout" value="1">
        <button type="submit" class="logout-button">Cerrar Sesión</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
