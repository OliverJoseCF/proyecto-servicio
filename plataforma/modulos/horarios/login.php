<?php
require_once __DIR__ . '/../../shared/lib/auth.php';
require_once __DIR__ . '/../../shared/lib/RateLimit.php';
require_once __DIR__ . '/../../shared/config.php';

if (HORARIOS_ADMIN_HASH === '') {
    error_log('[horarios/login] HORARIOS_ADMIN_HASH no configurado. Define el hash en shared/config.local.php');
    die('El sistema aún no está configurado. Contacta al administrador.');
}

$error = '';
$rl    = new RateLimit(5, 900);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    if (!csrfVerify()) {
        $error = 'Petición inválida. Recarga la página e inténtalo de nuevo.';
    } elseif ($rl->isBlocked($ip)) {
        $error = 'Demasiados intentos. Espera 15 minutos antes de intentar de nuevo.';
    } else {
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === HORARIOS_ADMIN_EMAIL && password_verify($password, HORARIOS_ADMIN_HASH)) {
            $rl->reset($ip);
            authLogin('horarios');
            header('Location: VistaAdmin.php');
            exit;
        }
        usleep(300000);
        $rl->record($ip);
        $error = 'Correo o contraseña incorrectos.';
    }
}

$tsj_module    = 'horarios';
$tsj_title     = 'Horarios — Iniciar Sesión';
$tsj_extra_css = ['css/normalize.css', 'css/login.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main" class="login-wrap">
  <div class="login-container">
    <h1>Iniciar Sesión</h1>
    <p class="login-subtitle">Acceso al panel de administración de horarios</p>

    <?php if ($error): ?>
      <div class="error-message" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <?= csrfField() ?>
      <div class="form-group">
        <label for="hor-email">Correo electrónico</label>
        <div class="input-wrap">
          <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
               viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="2" y="4" width="20" height="16" rx="2"/>
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
          </svg>
          <input type="email" id="hor-email" name="email" required
                 autocomplete="email" placeholder="admin@tecsj.edu.mx">
        </div>
      </div>
      <div class="form-group">
        <label for="hor-password">Contraseña</label>
        <div class="input-wrap">
          <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
               viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="3" y="11" width="18" height="11" rx="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          <input type="password" id="hor-password" name="password" required
                 autocomplete="current-password" placeholder="••••••••••">
        </div>
      </div>
      <button type="submit" class="submit-btn">ENTRAR</button>
    </form>

    <a href="index.php" class="volver-link">← Volver a búsqueda de maestros</a>
  </div>
</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
