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
    'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap',
    'assets/css/login.css',
];
require_once __DIR__ . '/../../shared/header.php';
?>

  <div class="bg-layer"></div>
  <div class="bg-dots"></div>

  <div class="login-container">
    <div class="login-box">
      <h2 class="login-title">Bienvenido</h2>
      <p class="login-subtitle">Acceso Administrativo</p>

      <?php if ($error): ?>
        <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="usuario" placeholder="Usuario" required>
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="clave" placeholder="Contraseña" required>
        </div>
        <button type="submit" class="btn-submit">Ingresar al Sistema</button>
      </form>
    </div>
  </div>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
