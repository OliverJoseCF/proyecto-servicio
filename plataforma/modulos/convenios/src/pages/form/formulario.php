<?php
require_once __DIR__ . '/../../session.php';
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: ../../../index.php');
    exit();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$formError = $_SESSION['form_error'] ?? null;
unset($_SESSION['form_error']);

$carreras = [
    'IADEV' => 'Ingeniería en Animación Digital y Efectos Visuales',
    'IM'    => 'Ingeniería Mecatrónica',
    'ISC'   => 'Ingeniería en Sistemas Computacionales',
    'II'    => 'Ingeniería Industrial',
    'LG'    => 'Gastronomía',
    'IGE'   => 'Ingeniería en Gestión Empresarial',
];

$tsj_module     = 'convenios';
$tsj_title      = 'Convenios — Registro de Convenio';
$tsj_extra_css  = ['../../output.css', 'formulario.css'];
$tsj_head_extra = '<style>
body { background-color: #f3f4f6; }
.form-page-wrapper {
    min-height: calc(100vh - 103px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    position: relative;
}
</style>';
$tsj_no_security_headers = true;
require_once __DIR__ . '/../../../../../shared/header.php';
?>

<div class="form-page-wrapper">
    <div class="absolute inset-0 z-0" style="position:absolute;inset:0;z-index:0;">
        <img src="../../../assets/images/logo/imagenes/chapala01-2.webp" alt="" role="presentation" class="w-full h-full object-cover" style="width:100%;height:100%;object-fit:cover;">
        <div class="absolute inset-0 bg-black bg-opacity-40 backdrop-blur-sm" style="position:absolute;inset:0;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);"></div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-2xl relative z-10" style="background:#fff;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.15);padding:2rem;width:100%;max-width:672px;position:relative;z-index:10;">
        <div class="flex items-center justify-center mb-4">
            <img src="../../../assets/images/logo/favicon.png" alt="Logo Tecnológico Superior de Jalisco" class="h-20 w-auto">
            <div class="ml-3 text-sm leading-tight" style="color: #32129A;">
                <p class="font-bold">Tecnológico</p>
                <p class="font-bold">Superior</p>
                <p class="font-bold">de Jalisco</p>
            </div>
        </div>

        <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">Registro de Convenio</h1>
        <p class="text-center text-gray-600 mb-8">Completa el formulario para registrar el convenio</p>

        <?php if ($formError !== null): ?>
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded" role="alert">
                <?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- Indicador de pasos -->
        <div class="flex justify-between mb-6">
            <div class="step-indicator active" id="step1Indicator">
                <span class="font-medium">Paso 1: Información básica</span>
            </div>
            <div class="step-indicator" id="step2Indicator">
                <span class="font-medium">Paso 2: Información adicional</span>
            </div>
        </div>

        <form action="guardarConvenio.php" id="convenioForm" class="space-y-6" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <!-- Paso 1: Información básica -->
            <div id="step1" class="space-y-6">
                <div>
                    <label for="empresa" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la empresa *</label>
                    <input type="text" id="empresa" name="empresa" required maxlength="200"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none">
                </div>

                <div>
                    <label for="carrera" class="block text-sm font-medium text-gray-700 mb-1">Carrera *</label>
                    <select id="carrera" name="carrera" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none">
                        <option value="" disabled selected>Seleccione una carrera</option>
                        <?php foreach ($carreras as $clave => $nombre): ?>
                            <option value="<?= htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="tipoConvenio" class="block text-sm font-medium text-gray-700 mb-1">Tipo de convenio *</label>
                    <select id="tipoConvenio" name="tipoConvenio" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none">
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="Servicio Social">Servicio Social</option>
                        <option value="Prácticas">Prácticas</option>
                        <option value="Ambos">Ambos</option>
                    </select>
                </div>

                <div>
                    <label for="vencimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha de vencimiento *</label>
                    <input type="date" id="vencimiento" name="vencimiento" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none">
                </div>

                <div>
                    <label for="contacto" class="block text-sm font-medium text-gray-700 mb-1">Persona de contacto *</label>
                    <input type="text" id="contacto" name="contacto" required maxlength="200"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none">
                </div>

                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                    <input type="tel" id="telefono" name="telefono" required maxlength="25"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none"
                        pattern="[0-9+\-\s()]{7,25}"
                        title="Solo números, +, -, espacios y paréntesis (entre 7 y 25 caracteres)">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico *</label>
                    <input type="email" id="email" name="email" required maxlength="254"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none">
                </div>

                <div class="pt-4 flex justify-center">
                    <button type="button" id="continueBtn"
                        class="group font-medium tracking-wide select-none text-base relative inline-flex items-center justify-center cursor-pointer h-12 border-2 border-solid py-0 px-6 rounded-md overflow-hidden z-10 transition-all duration-300 ease-in-out outline-0 btn-primary text-white hover:text-blue-500 focus:text-blue-500">
                        <strong class="font-medium">Continuar</strong>
                        <span class="absolute bg-white bottom-0 w-0 left-1/2 h-full -translate-x-1/2 transition-all ease-in-out duration-300 group-hover:w-[105%] -z-[1] group-focus:w-[105%]"></span>
                    </button>
                </div>
            </div>

            <!-- Paso 2: Información adicional -->
            <div id="step2" class="space-y-6 hidden">
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">
                        Logo de la empresa <span class="text-red-500" aria-hidden="true">*</span>
                        <span class="sr-only">(obligatorio)</span>
                    </label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <label class="flex flex-col items-center px-4 py-2 bg-white rounded-md border cursor-pointer hover:bg-blue-50 file-input-label">
                                <span class="text-sm">Seleccionar archivo</span>
                                <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/jpg" class="hidden" required>
                            </label>
                        </div>
                        <div id="logoPreview" class="w-16 h-16 border border-gray-300 rounded-md items-center justify-center bg-gray-100" style="display: none;" aria-hidden="true">
                            <span class="text-gray-400 text-xs text-center">Vista previa</span>
                        </div>
                    </div>
                    <p id="logoName" class="mt-1 text-xs text-gray-500" aria-live="polite">Ningún archivo seleccionado</p>
                </div>

                <!-- Redes sociales -->
                <div>
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Redes sociales (Opcional)</h3>

                    <div class="mb-3">
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Sitio web</label>
                        <input type="url" id="website" name="website" placeholder="https://www.ejemplo.com" maxlength="500"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none">
                    </div>

                    <div class="mb-3">
                        <label for="facebook" class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
                        <input type="url" id="facebook" name="facebook" placeholder="https://www.facebook.com/ejemplo" maxlength="500"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none">
                    </div>

                    <div class="mb-3">
                        <label for="youtube" class="block text-sm font-medium text-gray-700 mb-1">YouTube</label>
                        <input type="url" id="youtube" name="youtube" placeholder="https://www.youtube.com/ejemplo" maxlength="500"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none">
                    </div>

                    <div>
                        <label for="twitter" class="block text-sm font-medium text-gray-700 mb-1">X (Twitter)</label>
                        <input type="url" id="twitter" name="twitter" placeholder="https://x.com/ejemplo" maxlength="500"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none">
                    </div>
                </div>

                <div class="pt-4 flex justify-between">
                    <button type="button" id="backBtn"
                        class="group font-medium tracking-wide select-none text-base relative inline-flex items-center justify-center cursor-pointer h-12 border-2 border-solid py-0 px-6 rounded-md overflow-hidden z-10 transition-all duration-300 ease-in-out outline-0 bg-white text-gray-500 border-gray-300 hover:bg-gray-50">
                        <strong class="font-medium">Volver</strong>
                    </button>
                    <button type="submit"
                        class="group font-medium tracking-wide select-none text-base relative inline-flex items-center justify-center cursor-pointer h-12 border-2 border-solid py-0 px-6 rounded-md overflow-hidden z-10 transition-all duration-300 ease-in-out outline-0 btn-primary text-white hover:text-blue-500 focus:text-blue-500">
                        <strong class="font-medium">Registrar Convenio</strong>
                        <span class="absolute bg-white bottom-0 w-0 left-1/2 h-full -translate-x-1/2 transition-all ease-in-out duration-300 group-hover:w-[105%] -z-[1] group-focus:w-[105%]"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="script.js"></script>

<?php require_once __DIR__ . '/../../../../../shared/footer.php'; ?>
