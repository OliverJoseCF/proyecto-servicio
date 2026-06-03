<?php
/**
 * Login central de la plataforma TSJ.
 * Punto de entrada único para acceso admin a todos los módulos.
 */
require_once __DIR__ . '/shared/lib/auth.php';
require_once __DIR__ . '/shared/lib/RateLimit.php';

// Si ya está autenticado, redirigir al portal
if (isGlobalAdmin()) {
    header('Location: ' . PLATAFORMA_URL . '/');
    exit;
}

$error    = '';
$redirect = filter_input(INPUT_GET, 'redirect', FILTER_SANITIZE_URL) ?? '';
// Solo permitir redirects relativos dentro de la plataforma
if (!preg_match('/^\/plataforma\//', $redirect)) {
    $redirect = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rl = new RateLimit(5, 900);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    if (!csrfVerify()) {
        $error = 'Petición inválida. Recarga la página e intenta de nuevo.';
    } elseif ($rl->isBlocked($ip)) {
        $error = 'Demasiados intentos fallidos. Por favor espera 15 minutos.';
    } else {
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        if (
            $email    === GLOBAL_ADMIN_EMAIL
            && GLOBAL_ADMIN_HASH !== ''
            && password_verify($password, GLOBAL_ADMIN_HASH)
        ) {
            $rl->reset($ip);
            globalLogin('admin');
            $dest = $redirect ?: PLATAFORMA_URL . '/';
            header('Location: ' . $dest);
            exit;
        } else {
            usleep(300_000); // 300 ms anti-timing
            $rl->record($ip);
            $error = 'Correo o contraseña incorrectos.';
        }
    }
}

$base = PLATAFORMA_URL;
$csrf = csrfToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Acceso Administrador — TSJ Chapala</title>
  <link rel="icon" type="image/png" href="<?= $base ?>/shared/assets/img/favicon.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <?php
    $theme_v = filemtime(__DIR__ . '/shared/assets/css/theme.css');
  ?>
  <link rel="stylesheet" href="<?= $base ?>/shared/assets/css/theme.css?v=<?= $theme_v ?>" />
  <style>
    .material-symbols-rounded {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      vertical-align: middle; line-height: 1;
    }
    /* ── Layout de login ── */
    .login-page {
      min-height: 100dvh;
      display: grid;
      grid-template-columns: 1fr 1fr;
    }
    /* Panel izquierdo — brand */
    .login-brand {
      background: linear-gradient(145deg, #1a0960 0%, #33179c 55%, #4a22c0 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 48px 40px;
      position: relative;
      overflow: hidden;
    }
    .login-brand::before {
      content: '';
      position: absolute;
      width: 500px; height: 500px;
      border-radius: 50%;
      background: rgba(255,255,255,.04);
      top: -120px; right: -120px;
    }
    .login-brand::after {
      content: '';
      position: absolute;
      width: 300px; height: 300px;
      border-radius: 50%;
      background: rgba(255,255,255,.03);
      bottom: -80px; left: -60px;
    }
    .login-brand-inner { position: relative; z-index: 1; text-align: center; }
    .login-brand-logo {
      height: 80px; width: auto;
      filter: brightness(0) invert(1);
      opacity: .92;
      margin-bottom: 28px;
    }
    .login-brand-stripe {
      width: 40px; height: 4px;
      background: var(--tsj-pink);
      border-radius: 2px;
      margin: 0 auto 20px;
    }
    .login-brand-title {
      font-size: 1.55rem; font-weight: 700;
      color: #fff; margin: 0 0 8px;
      line-height: 1.3;
    }
    .login-brand-sub {
      font-size: .88rem; color: rgba(255,255,255,.55);
      margin: 0 0 40px;
      letter-spacing: .5px;
    }
    .login-brand-badges {
      display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;
    }
    .login-brand-badge {
      font-size: 11px; font-weight: 600; letter-spacing: .8px;
      text-transform: uppercase; color: rgba(255,255,255,.5);
      border: 1px solid rgba(255,255,255,.15);
      padding: 5px 12px; border-radius: 999px;
    }
    /* Panel derecho — formulario */
    .login-form-panel {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 48px 40px;
      background: #fff;
    }
    .login-form-box {
      width: 100%;
      max-width: 400px;
    }
    .login-form-header { margin-bottom: 32px; }
    .login-form-title {
      font-size: 1.6rem; font-weight: 700;
      color: var(--tsj-blue); margin: 0 0 6px;
    }
    .login-form-desc {
      font-size: .88rem; color: var(--tsj-gray-600);
      margin: 0;
    }
    /* Campo de formulario */
    .login-field {
      margin-bottom: 18px;
    }
    .login-field label {
      display: block;
      font-size: 12.5px; font-weight: 600;
      color: var(--tsj-gray-700);
      letter-spacing: .3px;
      margin-bottom: 7px;
    }
    .login-input-wrap {
      position: relative;
    }
    .login-input-wrap .mat-icon {
      position: absolute;
      left: 13px; top: 50%;
      transform: translateY(-50%);
      color: var(--tsj-gray-400);
      font-size: 20px;
      pointer-events: none;
    }
    .login-input-wrap input {
      width: 100%;
      padding: 12px 14px 12px 42px;
      font-family: var(--tsj-font);
      font-size: 14px;
      color: var(--tsj-text);
      background: #fff;
      border: 1.5px solid var(--tsj-gray-200);
      border-radius: var(--tsj-radius);
      transition: border-color .2s, box-shadow .2s;
      box-sizing: border-box;
    }
    .login-input-wrap input:focus {
      outline: none;
      border-color: var(--tsj-blue);
      box-shadow: 0 0 0 3px rgba(51,23,156,.10);
    }
    .login-input-wrap input::placeholder { color: var(--tsj-gray-400); }
    /* Toggle contraseña */
    .login-toggle-pw {
      position: absolute;
      right: 10px; top: 50%;
      transform: translateY(-50%);
      background: none; border: none;
      cursor: pointer; padding: 4px;
      color: var(--tsj-gray-400);
      display: flex; align-items: center;
      transition: color .2s;
    }
    .login-toggle-pw:hover { color: var(--tsj-blue); }
    .login-toggle-pw .material-symbols-rounded { font-size: 20px; }
    /* Error */
    .login-error {
      background: #fef2f2;
      border: 1px solid #fecaca;
      border-left: 4px solid var(--tsj-danger);
      border-radius: var(--tsj-radius);
      padding: 12px 14px;
      font-size: 13.5px;
      color: #991b1b;
      margin-bottom: 20px;
      display: flex; align-items: center; gap: 8px;
    }
    /* Botón submit */
    .login-submit {
      width: 100%;
      padding: 13px;
      background: var(--tsj-blue);
      color: #fff;
      font-family: var(--tsj-font);
      font-size: 15px; font-weight: 700;
      border: none; border-radius: var(--tsj-radius);
      cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      transition: background .2s, transform .15s, box-shadow .2s;
      margin-top: 8px;
    }
    .login-submit:hover {
      background: var(--tsj-blue-dark);
      transform: translateY(-1px);
      box-shadow: var(--tsj-shadow-navy);
    }
    .login-submit:active { transform: translateY(0); }
    .login-submit:focus-visible {
      outline: 2px solid var(--tsj-pink);
      outline-offset: 2px;
    }
    /* Volver */
    .login-back {
      display: inline-flex; align-items: center; gap: 6px;
      margin-top: 28px;
      color: var(--tsj-gray-600);
      font-size: 13px; text-decoration: none;
      transition: color .2s;
    }
    .login-back:hover { color: var(--tsj-blue); }
    .login-back .material-symbols-rounded { font-size: 18px; }
    /* Responsive */
    @media (max-width: 700px) {
      .login-page { grid-template-columns: 1fr; }
      .login-brand { display: none; }
      .login-form-panel { padding: 40px 24px; min-height: 100dvh; }
    }
  </style>
</head>
<body style="background:#fff; display:block; min-height:unset;">

<div class="login-page">

  <!-- Panel brand -->
  <div class="login-brand" aria-hidden="true">
    <div class="login-brand-inner">
      <img class="login-brand-logo"
           src="<?= $base ?>/shared/assets/img/logo.svg"
           alt="Tecnológico Superior de Jalisco" />
      <div class="login-brand-stripe"></div>
      <p class="login-brand-title">Tecnológico Superior<br>de Jalisco</p>
      <p class="login-brand-sub">Campus Chapala — Sistema de administración</p>
      <div class="login-brand-badges">
        <span class="login-brand-badge">Visitantes</span>
        <span class="login-brand-badge">Biblioteca</span>
        <span class="login-brand-badge">Convenios</span>
        <span class="login-brand-badge">Horarios</span>
        <span class="login-brand-badge">Requisitos</span>
      </div>
    </div>
  </div>

  <!-- Panel formulario -->
  <div class="login-form-panel">
    <div class="login-form-box">

      <div class="login-form-header">
        <h1 class="login-form-title">Acceso administrador</h1>
        <p class="login-form-desc">Ingresa tus credenciales para entrar en modo admin.</p>
      </div>

      <?php if ($error): ?>
      <div class="login-error" role="alert">
        <span class="material-symbols-rounded" aria-hidden="true">error</span>
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="" novalidate>
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
        <?php if ($redirect): ?>
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <div class="login-field">
          <label for="email">Correo electrónico</label>
          <div class="login-input-wrap">
            <span class="material-symbols-rounded mat-icon" aria-hidden="true">mail</span>
            <input type="email" id="email" name="email"
                   placeholder="admin@chapala.tecmm.edu.mx"
                   autocomplete="username email"
                   required autofocus
                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : '' ?>" />
          </div>
        </div>

        <div class="login-field">
          <label for="password">Contraseña</label>
          <div class="login-input-wrap">
            <span class="material-symbols-rounded mat-icon" aria-hidden="true">lock</span>
            <input type="password" id="password" name="password"
                   placeholder="••••••••••••"
                   autocomplete="current-password"
                   required />
            <button type="button" class="login-toggle-pw"
                    aria-label="Mostrar/ocultar contraseña"
                    onclick="togglePw()">
              <span class="material-symbols-rounded" id="pw-icon">visibility</span>
            </button>
          </div>
        </div>

        <button type="submit" class="login-submit">
          <span class="material-symbols-rounded" aria-hidden="true">login</span>
          Entrar al sistema
        </button>
      </form>

      <a href="<?= $base ?>/" class="login-back">
        <span class="material-symbols-rounded" aria-hidden="true">arrow_back</span>
        Volver al portal
      </a>

    </div>
  </div>

</div>

<script>
function togglePw() {
  var inp  = document.getElementById('password');
  var icon = document.getElementById('pw-icon');
  if (inp.type === 'password') {
    inp.type  = 'text';
    icon.textContent = 'visibility_off';
  } else {
    inp.type  = 'password';
    icon.textContent = 'visibility';
  }
}
</script>
</body>
</html>
