<?php
require_once __DIR__ . '/../../shared/lib/auth.php';
require_once __DIR__ . '/../../shared/lib/RateLimit.php';

// Hash de la contraseña de admin — para cambiarla:
//   php -r "echo password_hash('TuNuevaClaveSegura', PASSWORD_BCRYPT, ['cost'=>12]);"
// Copia el hash resultante aquí abajo.
// Hash por defecto de 'horarios2024!':
define('HORARIOS_ADMIN_EMAIL', 'admin@tecsj.edu.mx');
define('HORARIOS_ADMIN_HASH', '$2y$12$tcY3G0HqLg.7VXpqG4VPGekGJTrQO7CRa9z2V08uT.BhS7Rk7MVnS'); // horarios2024!

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

    <!-- TARJETA DE LOGIN -->
    <main class="login-wrap">
        <div class="login-container">
            <h2>Iniciar Sesión</h2>
            <p class="login-subtitle">Acceso al panel de administración</p>

            <?php if ($error): ?>
                <div class="error-message" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <?= csrfField() ?>
                <div class="form-group">
                    <label for="email">Correo electrónico:</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                        <input type="email" id="email" name="email" required
                               autocomplete="email" placeholder="admin@tecsj.edu.mx">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input type="password" id="password" name="password" required
                               autocomplete="current-password" placeholder="••••••••••">
                    </div>
                </div>
                <button type="submit" class="submit-btn">ENTRAR</button>
            </form>

            <a href="index.php" class="volver-link">← Volver a búsqueda de maestros</a>
        </div>
    </main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
