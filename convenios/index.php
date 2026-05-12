<?php
require_once __DIR__ . '/src/session.php';
require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/security_headers.php';
require_once __DIR__ . '/src/lib/RateLimit.php';

// Generar CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$loginError = null;
$rl         = new RateLimit(5, 900); // 5 intentos / 15 min por IP

// Procesar login
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
            $_SESSION['authenticated']  = true;
            $_SESSION['user']           = 'superuser';
            $_SESSION['last_activity']  = time();
            header('Location: vista_lista/lista.php');
            exit();
        } else {
            usleep(300000); // 300 ms — dificulta timing attacks
            $rl->record($ip);
            $loginError = 'Credenciales incorrectas.';
        }
    }
}

// Cerrar sesión (POST + CSRF)
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

$modalOpen    = ($loginError !== null) ? 'active' : '';
$modalHidden  = ($loginError !== null) ? 'false'  : 'true';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Directorio oficial de convenios académicos, empresariales e internacionales del Tecnológico Superior de Jalisco. Consulta prácticas, servicio social y colaboración institucional.">
    <link rel="preload" href="https://fonts.gstatic.com/s/poppins/v20/pxiEyp8kv8JHgFVrJJfecg.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="https://fonts.gstatic.com/s/poppins/v20/pxiByp8kv8JHgFVrLGT9Z1xlFQ.woff2" as="font" type="font/woff2" crossorigin>
    <style>
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url(https://fonts.gstatic.com/s/poppins/v20/pxiEyp8kv8JHgFVrJJfecg.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 500;
            font-display: swap;
            src: url(https://fonts.gstatic.com/s/poppins/v20/pxiByp8kv8JHgFVrLGT9Z1xlFQ.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <link rel="icon" type="image/png" href="assets/images/logo/favicon.png" />
    <link href="src/output.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
    <title>Convenios — Tecnológico Superior de Jalisco</title>
</head>

<body>
    <div class="container">
        <div class="w-full flex-col h-full mt-15">
            <div id="siteHeader" class="w-full fixed top-0 left-0 z-[999]" style="max-height: 164px">
                <div class="up-header"></div>
                <div class="flex flex-wrap justify-center items-center w-full mx-auto">
                    <div class="toolbar flex w-full justify-center items-center mx-auto gap-20">
                        <div class="imgJalisco">
                            <img src="assets/images/logo/Grupo_10491.svg" alt="Tecnológico Superior de Jalisco" class="logo-navbar no-styles" />
                        </div>
                        <a href="index.php">
                            <img src="assets/images/logo/home.svg" alt="Ir al inicio" class="logo-navbar no-styles home-icon" />
                        </a>
                        <div class="desktop-nav">
                            <a href="#" class="text-white texto-inter">Visitantes</a>
                            <a href="#" class="text-white texto-inter">Servicio social</a>
                            <a href="#" class="text-white texto-inter">Préstamo de balones</a>
                            <a href="#" class="text-white texto-inter">Biblioteca</a>
                            <a href="#" class="text-white texto-inter">Residencias</a>
                            <a href="#" class="text-white texto-inter">Materias</a>
                            <a href="#" class="login-link" id="loginDesktopBtn">Iniciar Sesión</a>
                        </div>
                        <button id="menuBtn" class="menu-button" aria-label="Abrir menú de navegación">
                            <img src="assets/images/logo/menu-svgrepo-com.svg" alt="" aria-hidden="true" class="logo-navbar no-styles" />
                        </button>
                    </div>
                    <nav id="menuPanel" class="menu-panel" aria-label="Menú móvil">
                        <div class="menu-content">
                            <a href="#" class="menu-item">Visitantes</a>
                            <a href="#" class="menu-item">Servicio social</a>
                            <a href="#" class="menu-item">Préstamo de balones</a>
                            <a href="#" class="menu-item">Biblioteca</a>
                            <a href="#" class="menu-item">Residencias</a>
                            <a href="#" class="menu-item">Materias</a>
                            <a href="#" class="menu-item login-link" id="loginMobileBtn">Iniciar Sesión</a>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full p-4 mt-[164px]" style="background-color: #f5f5f5">
        <div class="oferta" title="Convenios">
            <div class="w-full text-center" style="background-color: #f5f5f5">
                <div class="flex justify-center items-center gap-4 relative">
                    <h5 class="text-convenios font-bold text-4xl text-base_blue-500 mb-11 -mt-7">Convenios</h5>
                    <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true && isset($_SESSION['user']) && $_SESSION['user'] === 'superuser'): ?>
                        <a href="vista_lista/lista.php" class="button absolute right-5 top-1/2 transform -translate-y-1/2" id="adminButton">
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

    <main>
        <div class="cards-container w-full p-6 flex justify-center gap-6 flex-wrap">
            <a href="vista_lista/vista_convenios.php?carrera=IADEV" class="card">
                <div class="corner-box-top"><img src="assets/images/logo/gear-svgrepo-com_copy.svg" alt="" aria-hidden="true" class="corner-logo" /></div>
                <img class="ing-sistemas" src="assets/images/logo/imagenes/9M6A4513.webp" alt="Personas trabajando en computadoras con tabletas gráficas en un aula, mientras en una pantalla se proyecta un modelo 3D de un personaje." width="400" height="300" loading="lazy" />
                <div class="card-text">Ingeniería en Animación Digital y Efectos Visuales</div>
                <div class="corner-box"></div>
            </a>
            <a href="vista_lista/vista_convenios.php?carrera=IM" class="card">
                <div class="corner-box-top"><img src="assets/images/logo/gear-svgrepo-com_copy.svg" alt="" aria-hidden="true" class="corner-logo" /></div>
                <img class="ing-sistemas" src="assets/images/logo/imagenes/DSC02687_1.webp" alt="Tres personas trabajando en un proyecto de electrónica con componentes y un portátil, mientras en el fondo se muestra código en una pantalla grande." width="400" height="300" loading="lazy" />
                <div class="card-text">Ingeniería Mecatrónica</div>
                <div class="corner-box"></div>
            </a>
            <a href="vista_lista/vista_convenios.php?carrera=ISC" class="card">
                <div class="corner-box-top"><img src="assets/images/logo/gear-svgrepo-com_copy.svg" alt="" aria-hidden="true" class="corner-logo" /></div>
                <img class="ing-sistemas" src="assets/images/logo/imagenes/DSC04199_1.webp" alt="Estudiantes trabajando en una sala de computadoras con equipos iMac, mientras dos de ellos colaboran revisando un libro y una laptop." width="400" height="300" loading="lazy" />
                <div class="card-text">Ingeniería en Sistemas Computacionales</div>
                <div class="corner-box"></div>
            </a>
            <a href="vista_lista/vista_convenios.php?carrera=II" class="card">
                <div class="corner-box-top"><img src="assets/images/logo/gear-svgrepo-com_copy.svg" alt="" aria-hidden="true" class="corner-logo" /></div>
                <img class="ing-sistemas" src="assets/images/logo/imagenes/DSC07193_1.webp" alt="Persona trabajando con una herramienta eléctrica en un taller, generando chispas mientras utiliza equipo de protección como gafas y guantes." width="400" height="300" loading="lazy" />
                <div class="card-text">Ingeniería Industrial</div>
                <div class="corner-box"></div>
            </a>
            <a href="vista_lista/vista_convenios.php?carrera=LG" class="card">
                <div class="corner-box-top-green"><img src="assets/images/logo/graduation-svgrepo-com.svg" alt="" aria-hidden="true" class="corner-logo" /></div>
                <img class="ing-sistemas" src="assets/images/logo/imagenes/DSC06661_1.webp" alt="Estudiante de gastronomía preparando una bebida en una coctelera, con varias botellas de licor en el fondo y una vista exterior a través de las ventanas." width="400" height="300" loading="lazy" />
                <div class="card-text">Gastronomía</div>
                <div class="corner-box-green"></div>
            </a>
            <a href="vista_lista/vista_convenios.php?carrera=IGE" class="card">
                <div class="corner-box-top"><img src="assets/images/logo/gear-svgrepo-com_copy.svg" alt="" aria-hidden="true" class="corner-logo" /></div>
                <img class="ing-sistemas" src="assets/images/logo/imagenes/DSC08323_1.webp" alt="Dos mujeres trabajando en laptops en un espacio interior iluminado, una en primer plano concentrada y la otra al fondo sonriendo." width="400" height="300" loading="lazy" />
                <div class="card-text">Ingeniería en Gestión Empresarial</div>
                <div class="corner-box"></div>
            </a>
        </div>

        <div class="w-full flex flex-col items-center gap-2 mb-6">
            <button class="login-button"
                    id="openSuggestModalBtn"
                    aria-expanded="false"
                    aria-controls="suggestModalOverlay">
                Sugerir una empresa
            </button>
        </div>

        <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true): ?>
        <div class="w-full p-6 flex justify-center gap-6 flex-wrap bg-base_blue-500 text-white text-center rounded-lg mb-10">
            <a href="src/pages/form/formulario.php" class="px-6 py-2 bg-white text-base_blue-500 font-bold rounded hover:bg-gray-200 transition">Solicitar convenio</a>
        </div>
        <?php endif; ?>
    </main>

    <footer class="footer w-full p-10" role="contentinfo">
        <div class="footer-container flex justify-between items-center">
            <div class="footer-img">
                <img src="assets/images/logo/Grupo_10491.svg" alt="Tecnológico Superior de Jalisco" loading="lazy" />
            </div>
            <div class="footer-links flex gap-4">
                <a href="https://www.facebook.com/TecSJ" class="footer-img" target="_blank" rel="noopener noreferrer">
                    <img src="assets/images/logo/facebook-svgrepo-com.svg" alt="Visitar Facebook del TecSJ" loading="lazy" />
                </a>
                <a href="https://www.youtube.com/@TecSJ" class="footer-img" target="_blank" rel="noopener noreferrer">
                    <img src="assets/images/logo/youtube-svgrepo-com.svg" alt="Visitar YouTube del TecSJ" loading="lazy" />
                </a>
            </div>
            <div class="footer-text-links flex flex-col items-center gap-2">
                <a href="index.php" class="footer-link">Módulo de convenios</a>
                <a href="https://consultapublicamx.plataformadetransparencia.org.mx/vut-web/faces/view/consultaPublica.xhtml?idEntidad=MTQ=&idSujetoObligado=MTM3OTE=#inicio" class="footer-link" target="_blank" rel="noopener noreferrer">Plataforma Nacional de Transparencia</a>
            </div>
        </div>
    </footer>

    <div class="extra-info w-full p-6 bg-gray-800 text-white text-center">
        <div class="additional-images">
            <img src="assets/images/logo/imagenes/educacion.png" alt="Secretaría de Educación Pública" loading="lazy" />
            <img src="assets/images/logo/imagenes/tecnologico.svg" alt="Tecnológico Nacional de México" loading="lazy" />
            <img src="assets/images/logo/imagenes/innovacion.png" alt="Secretaría de Innovación, Ciencia y Tecnología de Jalisco" loading="lazy" />
            <img src="assets/images/logo/imagenes/jalisco.png" alt="Gobierno de Jalisco" loading="lazy" />
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="src/js/script.js"></script>

    <!-- Modal Sugerir Empresa -->
    <div class="suggest-modal-overlay" id="suggestModalOverlay"
         role="dialog" aria-modal="true" aria-labelledby="suggestModalTitle" aria-hidden="true">
        <div class="suggest-modal">
            <button class="suggest-modal-close" id="closeSuggestModal" aria-label="Cerrar formulario de sugerencia">&times;</button>
            <h2 id="suggestModalTitle">Sugerir una empresa</h2>
            <form id="suggestForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="login-input-container">
                    <input type="text" name="nombre_empresa" placeholder="Nombre de la empresa" required maxlength="200" aria-label="Nombre de la empresa" />
                </div>
                <div class="login-input-container">
                    <input type="email" name="correo_empresa" placeholder="Correo de contacto con la empresa" required maxlength="254" aria-label="Correo de contacto con la empresa" />
                </div>
                <div class="login-input-container">
                    <input type="text" name="nombre_contacto" placeholder="Nombre de la persona con la que se contactará" required maxlength="200" aria-label="Nombre de la persona con la que se contactará" />
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
            <button class="login-close-button" id="closeLoginModal" aria-label="Cerrar modal de inicio de sesión">&times;</button>
            <h2 id="loginModalTitle">Iniciar Sesión</h2>
            <p class="login-subtitle">Introduce tus datos para iniciar sesión</p>

            <?php if ($loginError !== null): ?>
                <p class="login-error" role="alert"><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <form id="loginForm" method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                <div class="login-form-group">
                    <label for="email">Correo</label>
                    <div class="login-input-container">
                        <span class="login-icon email-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                        </span>
                        <input type="email" id="email" name="email" placeholder="admin@mail.com" required autocomplete="email" maxlength="254">
                    </div>
                </div>

                <div class="login-form-group">
                    <label for="password">Contraseña</label>
                    <div class="login-input-container">
                        <span class="login-icon lock-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </span>
                        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="toggle-password" id="togglePassword" aria-label="Mostrar contraseña">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="login-button">Acceder</button>
            </form>

            <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="logout" value="1">
                    <button type="submit" class="logout-button">Cerrar Sesión</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
