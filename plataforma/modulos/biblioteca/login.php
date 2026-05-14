<?php
require_once __DIR__ . '/../../shared/lib/auth.php';
require_once __DIR__ . '/../../shared/lib/RateLimit.php';
require_once __DIR__ . '/../../shared/config.php';

if (BIBLIOTECA_ADMIN_HASH === '') {
    error_log('[biblioteca/login] BIBLIOTECA_ADMIN_HASH no configurado. Define el hash en shared/config.local.php');
    die('El sistema aún no está configurado. Contacta al administrador.');
}

$error = '';
$rl    = new RateLimit(5, 900);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    if (!csrfVerify()) {
        $error = 'Petición inválida. Recarga la página e inténtalo de nuevo.';
    } elseif ($rl->isBlocked($ip)) {
        $error = 'Demasiados intentos fallidos. Espera 15 minutos antes de intentar de nuevo.';
    } else {
        $usuario = trim($_POST['usuario'] ?? '');
        $clave   = $_POST['clave'] ?? '';

        if ($usuario === BIBLIOTECA_ADMIN_USER && password_verify($clave, BIBLIOTECA_ADMIN_HASH)) {
            $rl->reset($ip);
            authLogin('biblioteca');
            header('Location: admin.php');
            exit;
        }
        usleep(300000);
        $rl->record($ip);
        $error = 'Usuario o contraseña incorrectos.';
    }
}

$tsj_module    = 'biblioteca';
$tsj_title     = 'Biblioteca — Acceso Administrativo';
$tsj_extra_css = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
    'assets/css/login.css',
];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main" class="lib-login-page">
  <div class="login-card">
    <div class="login-brand">
      <div class="login-icon-wrap" aria-hidden="true">
        <i class="fas fa-book-open" aria-hidden="true"></i>
      </div>
      <h1 class="login-title">Biblioteca</h1>
      <p class="login-subtitle">Acceso Administrativo</p>
    </div>

    <?php if ($error): ?>
      <div class="error-msg" role="alert">
        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <?= csrfField() ?>
      <div class="lib-input-group">
        <label for="bib-usuario">Usuario</label>
        <div class="lib-input-wrap">
          <i class="fas fa-user" aria-hidden="true"></i>
          <input type="text" id="bib-usuario" name="usuario"
                 required autocomplete="username" placeholder="Administrador">
        </div>
      </div>
      <div class="lib-input-group">
        <label for="bib-clave">Contraseña</label>
        <div class="lib-input-wrap">
          <i class="fas fa-lock" aria-hidden="true"></i>
          <input type="password" id="bib-clave" name="clave"
                 required autocomplete="current-password" placeholder="••••••••••">
        </div>
      </div>
      <button type="submit" class="btn-submit">Ingresar al Sistema</button>
    </form>
  </div>
</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
