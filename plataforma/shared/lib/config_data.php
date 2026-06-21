<?php
/**
 * Acceso a la configuración editable del portal (tabla `configuracion`) y a las
 * redes sociales activas (claves *_url de `configuracion`).
 *
 * Lo usa el footer/header compartido para reflejar lo que el admin guarda en
 * Admin → Configuración. Todas las funciones degradan a un valor por defecto si
 * la BD no está disponible, de modo que el portal nunca se rompe.
 */

if (!function_exists('getPDO')) {
    require_once __DIR__ . '/../config.php';
}

/**
 * Devuelve todo el mapa clave→valor de `configuracion`, cacheado por request.
 */
function tsjConfigAll(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $cache = [];
    try {
        $db   = getPDO(DB_NAME);
        $rows = $db->query('SELECT clave, valor FROM configuracion')->fetchAll();
        foreach ($rows as $r) {
            $cache[$r['clave']] = $r['valor'];
        }
    } catch (\Throwable $e) {
        // BD no disponible — se usan los defaults de cada llamada.
        $cache = [];
    }
    return $cache;
}

/**
 * Devuelve el valor de una clave de configuración, o $default si está ausente o vacía.
 */
function tsjConfig(string $clave, string $default = ''): string
{
    $all = tsjConfigAll();
    $v   = $all[$clave] ?? '';
    return ($v !== '' && $v !== null) ? $v : $default;
}

/**
 * Devuelve las redes sociales que deben mostrarse, ya filtradas (solo con URL).
 * Cada entrada: ['plataforma'=>..., 'url'=>..., 'icono'=>archivo svg, 'label'=>...].
 *
 * Fuente: claves *_url de `configuracion` (editables en Admin → Redes sociales).
 * El icono SVG se resuelve por convención de nombre en shared/assets/img/.
 */
function tsjRedesSociales(): array
{
    $map = [
        'facebook_url'  => ['plataforma' => 'Facebook',  'icono' => 'facebook.svg',  'label' => 'Facebook'],
        'youtube_url'   => ['plataforma' => 'YouTube',   'icono' => 'youtube.svg',   'label' => 'YouTube'],
        'instagram_url' => ['plataforma' => 'Instagram', 'icono' => 'instagram.svg', 'label' => 'Instagram'],
        'twitter_url'   => ['plataforma' => 'Twitter',   'icono' => 'twitter.svg',   'label' => 'Twitter / X'],
        'linkedin_url'  => ['plataforma' => 'LinkedIn',  'icono' => 'linkedin.svg',  'label' => 'LinkedIn'],
    ];

    $imgDir = __DIR__ . '/../assets/img/';

    $out = [];
    foreach ($map as $clave => $meta) {
        $url = tsjConfig($clave, '');
        if ($url === '') {
            continue;
        }
        $out[] = [
            'plataforma' => $meta['plataforma'],
            'url'        => $url,
            'icono'      => $meta['icono'],
            'label'      => $meta['label'],
            // Solo hay SVG propio para algunas redes; el footer usa un fallback textual si no.
            'tiene_svg'  => is_file($imgDir . $meta['icono']),
        ];
    }
    return $out;
}
