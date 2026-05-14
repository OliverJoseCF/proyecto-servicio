<?php
require_once __DIR__ . '/../../shared/lib/auth.php';
require_once __DIR__ . '/../../shared/lib/RateLimit.php';
require_once __DIR__ . '/../../shared/config.php';

// BIBLIOTECA_ADMIN_USER y BIBLIOTECA_ADMIN_HASH se cargan desde shared/config.php
// (que a su vez carga shared/config.local.php si existe).
// Genera el hash con:  php tools/setup_password.php
// Luego ponlo en shared/config.local.php:
//   define('BIBLIOTECA_ADMIN_HASH', '$2y$12$...');
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

  <div class="login-page">
    <div class="login-card">
      <div class="login-brand">
        <div class="login-icon-wrap">
          <i class="fas fa-book-open"></i>
        </div>
        <h2 class="login-title">Biblioteca</h2>
        <p class="login-subtitle">Acceso Administrativo</p>
      </div>

      <?php if ($error): ?>
        <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <?= csrfField() ?>
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="usuario" placeholder="Usuario" required autocomplete="username">
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="clave" placeholder="Contraseña" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn-submit">Ingresar al Sistema</button>
      </form>
    </div>
  </div>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
