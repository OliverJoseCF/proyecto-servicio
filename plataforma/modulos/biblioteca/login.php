<?php
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $clave   = $_POST['clave'];

    if ($usuario === 'admin' && $clave === '1234') {
        $_SESSION['logueado'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
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
        <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
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
