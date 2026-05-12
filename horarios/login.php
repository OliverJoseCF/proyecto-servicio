<?php
session_start();

const ADMIN_EMAIL    = 'admin@admin.com';
const ADMIN_PASSWORD = 'admin123';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
        $_SESSION['logged_in']  = true;
        $_SESSION['user_email'] = $email;
        header('Location: VistaAdmin.php');
        exit;
    }
    $error = 'Correo o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — TSJ Chapala</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

    <!-- BARRA ROSA -->
    <div class="up-header"></div>

    <!-- TOOLBAR -->
    <nav class="toolbar">
        <div class="toolbar-logo">
            <img class="imgJalisco" src="Imagenes/logoblacno.svg" alt="Logo TSJ">
        </div>

        <div class="toolbar-center">
            <img class="logos2" src="Imagenes/logos2.png" alt="Logos institucionales">
        </div>

        <div class="toolbar-right">
            <div class="home-icon">
                <a href="index.php" title="Ir a búsqueda de maestros">
                    <img src="Imagenes/home.svg" alt="Inicio" width="26" height="26">
                </a>
            </div>
        </div>
    </nav>

    <!-- TARJETA DE LOGIN -->
    <main class="login-wrap">
        <div class="login-container">
            <h2>Iniciar Sesión</h2>
            <p class="login-subtitle">Acceso al panel de administración</p>

            <?php if ($error): ?>
                <div class="error-message" role="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
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
                               autocomplete="email" placeholder="USUARIO">
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

</body>
</html>
