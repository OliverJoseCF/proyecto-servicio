<?php
require_once __DIR__ . '/../src/security_headers.php';
require_once __DIR__ . '/../src/pages/conexion.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: 404.html');
    exit();
}

$fila = null;
try {
    $stmt = $conn->prepare(
        'SELECT id, nombre, convenio, logo, contacto, telefono, correo, vencimiento, web, facebook, youtube, twitter
         FROM convenios WHERE id = ?'
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
} catch (mysqli_sql_exception $e) {
    error_log('Error prepare vista_empresa: ' . $e->getMessage());
    header('Location: 404.html');
    exit();
}

if (!$fila) {
    header('Location: 404.html');
    exit();
}

function h(string $val): string {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

function safeUrl(?string $url): string {
    if (empty($url)) return '';
    $url = trim($url);
    if (!preg_match('/^https?:\/\//i', $url)) return '';
    return h($url);
}

$logoFile = !empty($fila['logo']) ? basename($fila['logo']) : '';
$fechaVenc = ($t = strtotime($fila['vencimiento'])) ? date('d/m/Y', $t) : '—';
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
    <title><?= h($fila['nombre']) ?> — Convenios TecSJ</title>
</head>

<body>
    <div class="container">
        <div class="w-full flex-col h-full mt-15">
            <div class="w-full fixed top-0 left-0 z-[999]" style="max-height: 164px">
                <div class="up-header"></div>
                <div class="flex flex-wrap justify-center items-center w-full mx-auto">
                    <div class="toolbar flex w-full justify-center items-center mx-auto gap-20">
                        <div class="imgJalisco">
                            <img src="assets/images/logo/Grupo_10491.svg" alt="Tecnológico Superior de Jalisco" class="logo-navbar no-styles" />
                        </div>
                        <a href="../index.php">
                            <img src="assets/images/logo/home.svg" alt="Ir al inicio" class="logo-navbar no-styles home-icon" />
                        </a>
                        <div class="desktop-nav">
                            <a href="#" class="text-white texto-inter">Unidades académicas</a>
                            <a href="#" class="text-white texto-inter">Contacto</a>
                        </div>
                        <button id="menuBtn" class="menu-button" aria-label="Abrir menú de navegación">
                            <img src="assets/images/logo/menu-svgrepo-com.svg" alt="" aria-hidden="true" class="logo-navbar no-styles" />
                        </button>
                    </div>
                    <nav id="menuPanel" class="menu-panel" aria-label="Menú móvil">
                        <div class="menu-content">
                            <a href="#" class="menu-item">Unidades académicas</a>
                            <a href="#" class="menu-item">Contacto</a>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full p-4 mt-[164px]" style="background-color: #f5f5f5">
        <div class="oferta" title="Convenios">
            <div class="w-full relative py-2" style="background-color: #f5f5f5">
                <a href="../vista_lista/lista.php" aria-label="Volver al listado de convenios">
                    <img src="assets/images/logo/imagenes/icono-regresar.png" alt="" aria-hidden="true" class="btn-volver" />
                </a>
                <h5 class="text-convenios font-bold text-4xl text-base_blue-500">Acerca de</h5>
            </div>
        </div>
    </div>

    <main class="cards-container w-full p-6 flex justify-center gap-6 flex-wrap">
        <div class="company-card bg-white rounded-lg shadow-md p-6 w-full max-w-4xl">
            <div class="flex flex-row">
                <div class="company-logo-container">
                    <?php if ($logoFile): ?>
                        <img src="../src/pages/upload/<?= h($logoFile) ?>"
                             alt="Logo de <?= h($fila['nombre']) ?>"
                             class="company-logo">
                    <?php else: ?>
                        <div class="company-logo bg-gray-100 flex items-center justify-center text-gray-400 text-sm">Sin logo</div>
                    <?php endif; ?>
                </div>

                <div class="company-info">
                    <h2 class="company-title"><?= h($fila['nombre']) ?></h2>

                    <div class="company-details">
                        <p><strong>Tipo de convenio:</strong> <?= h($fila['convenio']) ?></p>
                        <p><strong>Contacto:</strong> <?= h($fila['contacto']) ?></p>
                        <p><strong>Correo:</strong> <?= h($fila['correo']) ?></p>
                        <p><strong>Teléfono:</strong> <?= h($fila['telefono']) ?></p>
                        <p><strong>Fecha de vencimiento:</strong> <?= h($fechaVenc) ?></p>
                    </div>

                    <div class="social-links">
                        <div class="flex items-center gap-4">

                            <?php $webUrl = safeUrl($fila['web']); ?>
                            <?php if ($webUrl): ?>
                            <a href="<?= $webUrl ?>" class="social-icon-link" title="Sitio web de <?= h($fila['nombre']) ?>" target="_blank" rel="noopener noreferrer">
                                <img src="assets/images/logo/link-svgrepo-com.svg" alt="Sitio web" class="social-icon">
                            </a>
                            <?php else: ?>
                            <span class="social-icon-link" title="Sitio web no disponible" aria-label="Sitio web no disponible">
                                <img src="assets/images/logo/link-svgrepo-com.svg" alt="" aria-hidden="true" class="social-icon opacity-50">
                            </span>
                            <?php endif; ?>

                            <?php $fbUrl = safeUrl($fila['facebook']); ?>
                            <?php if ($fbUrl): ?>
                            <a href="<?= $fbUrl ?>" class="social-icon-link" title="Facebook de <?= h($fila['nombre']) ?>" target="_blank" rel="noopener noreferrer">
                                <img src="assets/images/logo/facebook-color-svgrepo-com.svg" alt="Facebook" class="social-icon">
                            </a>
                            <?php else: ?>
                            <span class="social-icon-link" title="Facebook no disponible" aria-label="Facebook no disponible">
                                <img src="assets/images/logo/facebook-color-svgrepo-com.svg" alt="" aria-hidden="true" class="social-icon opacity-50">
                            </span>
                            <?php endif; ?>

                            <?php $twUrl = safeUrl($fila['twitter']); ?>
                            <?php if ($twUrl): ?>
                            <a href="<?= $twUrl ?>" class="social-icon-link" title="X (Twitter) de <?= h($fila['nombre']) ?>" target="_blank" rel="noopener noreferrer">
                                <img src="assets/images/logo/icons8-x.svg" alt="X (Twitter)" class="social-icon">
                            </a>
                            <?php else: ?>
                            <span class="social-icon-link" title="X (Twitter) no disponible" aria-label="X (Twitter) no disponible">
                                <img src="assets/images/logo/icons8-x.svg" alt="" aria-hidden="true" class="social-icon opacity-50">
                            </span>
                            <?php endif; ?>

                            <?php $ytUrl = safeUrl($fila['youtube']); ?>
                            <?php if ($ytUrl): ?>
                            <a href="<?= $ytUrl ?>" class="social-icon-link" title="YouTube de <?= h($fila['nombre']) ?>" target="_blank" rel="noopener noreferrer">
                                <img src="assets/images/logo/youtube-168-svgrepo-com.svg" alt="YouTube" class="social-icon">
                            </a>
                            <?php else: ?>
                            <span class="social-icon-link" title="YouTube no disponible" aria-label="YouTube no disponible">
                                <img src="assets/images/logo/youtube-168-svgrepo-com.svg" alt="" aria-hidden="true" class="social-icon opacity-50">
                            </span>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                <a href="../index.php" class="footer-link">Módulo de convenios</a>
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

    <script src="src/js/script.js"></script>
</body>
</html>
