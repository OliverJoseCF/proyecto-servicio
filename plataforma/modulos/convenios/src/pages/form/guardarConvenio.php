<?php
require_once __DIR__ . '/../../session.php';
require_once __DIR__ . '/../../lib/helpers.php';

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: ../../../index.php');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario.php');
    exit();
}

// Validar CSRF
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['form_error'] = 'Petición inválida. Recarga la página e intenta de nuevo.';
    header('Location: formulario.php');
    exit();
}

// Validar y normalizar campos
$fields = [];
$error  = validarCamposConvenio($_POST, $fields);

// Procesar logo
$logoName = '';
if ($error === null) {
    if (isset($_FILES['logo'])) {
        $error = procesarLogo($_FILES['logo'], $logoName);
    }
    if ($error === null && $logoName === '' && isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_NO_FILE) {
        $_SESSION['form_error'] = 'El logo de la empresa es obligatorio.';
        $conn->close();
        header('Location: formulario.php');
        exit();
    }
}

if ($error !== null) {
    $_SESSION['form_error'] = $error;
    $conn->close();
    header('Location: formulario.php');
    exit();
}

$sql = 'INSERT INTO convenios
            (nombre, convenio, logo, contacto, telefono, correo, vencimiento, web, facebook, youtube, twitter, carrera)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

try {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssssssss',
        $fields['empresa'], $fields['tipoConvenio'], $logoName,
        $fields['contacto'], $fields['telefono'], $fields['email'],
        $fields['vencimiento'], $fields['website'], $fields['facebook'],
        $fields['youtube'], $fields['twitter'], $fields['carrera']
    );
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header('Location: ../../../vista_lista/lista.php?mensaje=agregado');
    exit();
} catch (mysqli_sql_exception $e) {
    error_log('Error al registrar convenio: ' . $e->getMessage());
    if (!empty($logoName)) eliminarLogo($logoName); // revertir logo si el INSERT falló
    $_SESSION['form_error'] = 'Error al registrar el convenio. Intente de nuevo.';
    $conn->close();
    header('Location: formulario.php');
    exit();
}
