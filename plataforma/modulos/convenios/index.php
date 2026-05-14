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

$tsj_module    = 'convenios';
$tsj_title     = 'Convenios';
$tsj_extra_css = [
    'src/output.css',
    'src/css/styles.css',
    'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
];
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../shared/header.php';
?>

    <div class="w-full p-4" style="background-color: #f5f5f5">
        <div class="oferta" title="Convenios">
            <div class="w-full text-center" style="background-color: #f5f5f5">
                <div class="flex justify-center items-center gap-4 relative">
                    <h5 class="text-convenios font-bold text-4xl text-base_blue-500 mb-11 -mt-7">Convenios</h5>
                    <?php if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true): ?>
                        <a href="#" class="login-link absolute right-5 top-1/2 transform -translate-y-1/2 text-base_blue-500 texto-inter text-sm" id="loginPageBtn">Iniciar Sesión</a>
                    <?php else: ?>
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

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
