<?php
/**
 * Funciones compartidas: validación, uploads, campos de convenio.
 * Incluir con require_once — seguro para múltiples includes.
 */

define('UPLOAD_DIR', dirname(__DIR__) . '/pages/upload/');

if (!function_exists('validarUrl')) {
    function validarUrl(string $url): ?string {
        $url = trim($url);
        if ($url === '') return null;
        if (!preg_match('/^https?:\/\//i', $url)) return null;
        if (!filter_var($url, FILTER_VALIDATE_URL)) return null;
        return $url;
    }
}

/**
 * Procesa la subida de un logo.
 * Devuelve null en éxito (con $outName poblado) o un string de error.
 *
 * @param array  $fileArray  Entrada de $_FILES['logo']
 * @param string $outName    Nombre del archivo guardado (solo basename, sin path)
 */
function procesarLogo(array $fileArray, string &$outName): ?string {
    if ($fileArray['error'] === UPLOAD_ERR_NO_FILE) return null;

    if ($fileArray['error'] !== UPLOAD_ERR_OK) {
        return 'Error al recibir el archivo (código ' . (int) $fileArray['error'] . ').';
    }

    $tmpName = $fileArray['tmp_name'];
    $size    = $fileArray['size'];

    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tmpName);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png'];
    $allowedExts  = ['jpg', 'jpeg', 'png'];
    $fileExt      = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION));

    if (!in_array($mimeType, $allowedMimes, true) || !in_array($fileExt, $allowedExts, true)) {
        return 'Tipo de archivo no permitido. Solo JPG y PNG.';
    }
    if (!getimagesize($tmpName)) {
        return 'El archivo no es una imagen válida.';
    }
    if ($size > 10 * 1024 * 1024) {
        return 'El archivo supera el tamaño máximo permitido (10 MB).';
    }

    $outName = bin2hex(random_bytes(16)) . '.' . $fileExt;
    if (!move_uploaded_file($tmpName, UPLOAD_DIR . $outName)) {
        $outName = '';
        return 'Error al guardar el archivo. Verifique permisos del directorio de subida.';
    }
    return null;
}

/**
 * Elimina un logo del disco de forma segura (basename para prevenir path traversal).
 *
 * @param string $logoName  Nombre o ruta del logo almacenado en BD.
 */
function eliminarLogo(string $logoName): void {
    $basename = basename($logoName);
    if ($basename === '' || $basename === '.') return;
    $path = UPLOAD_DIR . $basename;
    if (file_exists($path)) {
        unlink($path);
    }
}

/**
 * Valida y normaliza los campos comunes de un convenio.
 *
 * @param array  $post    $_POST crudo
 * @param array  &$fields Campos normalizados (salida)
 * @return string|null    Mensaje de error o null si todo es válido
 */
function validarCamposConvenio(array $post, array &$fields): ?string {
    static $carrerasValidas  = ['IADEV', 'IM', 'ISC', 'II', 'LG', 'IGE'];
    static $conveniosValidos = ['Servicio Social', 'Prácticas', 'Ambos'];

    $empresa      = trim($post['empresa']      ?? '');
    $tipoConvenio = trim($post['tipoConvenio'] ?? '');
    $contacto     = trim($post['contacto']     ?? '');
    $telefono     = trim($post['telefono']     ?? '');
    $email        = trim($post['email']        ?? '');
    $vencimiento  = trim($post['vencimiento']  ?? '');
    $carrera      = trim($post['carrera']      ?? '');

    if ($empresa === '')                          return 'El nombre de la empresa es obligatorio.';
    if (mb_strlen($empresa)           > 200)      return 'El nombre de la empresa es demasiado largo (máx. 200 caracteres).';
    if (!in_array($tipoConvenio, $conveniosValidos, true)) return 'Tipo de convenio inválido.';
    if ($contacto === '')                         return 'La persona de contacto es obligatoria.';
    if (mb_strlen($contacto)          > 200)      return 'El nombre de contacto es demasiado largo (máx. 200 caracteres).';
    if ($telefono === '')                         return 'El teléfono es obligatorio.';
    if (!preg_match('/^[0-9+\-\s()]{7,25}$/', $telefono)) return 'El teléfono contiene caracteres inválidos (solo números, +, -, espacios y paréntesis; entre 7 y 25 caracteres).';
    if ($email === '')                            return 'El correo electrónico es obligatorio.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return 'El correo electrónico no es válido.';
    if (mb_strlen($email)             > 254)      return 'El correo electrónico es demasiado largo.';
    if ($vencimiento === '')                      return 'La fecha de vencimiento es obligatoria.';

    $d = DateTime::createFromFormat('Y-m-d', $vencimiento);
    if (!$d || $d->format('Y-m-d') !== $vencimiento) return 'La fecha de vencimiento no tiene un formato válido (AAAA-MM-DD).';

    if (!in_array($carrera, $carrerasValidas, true)) return 'La carrera seleccionada no es válida.';

    $fields = [
        'empresa'      => $empresa,
        'tipoConvenio' => $tipoConvenio,
        'contacto'     => $contacto,
        'telefono'     => $telefono,
        'email'        => $email,
        'vencimiento'  => $vencimiento,
        'carrera'      => $carrera,
        'website'      => validarUrl($post['website']  ?? ''),
        'facebook'     => validarUrl($post['facebook'] ?? ''),
        'youtube'      => validarUrl($post['youtube']  ?? ''),
        'twitter'      => validarUrl($post['twitter']  ?? ''),
    ];
    return null;
}
