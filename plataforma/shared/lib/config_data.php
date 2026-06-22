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
 * Los iconos están embebidos como SVG inline — no dependen de archivos externos.
 *
 * Fuente: claves *_url de `configuracion` (editables en Admin → Redes sociales).
 * Si una red no tiene URL configurada (o está vacía), no se incluye en el resultado.
 */
function tsjRedesSociales(): array
{
    $map = [
        'facebook_url' => [
            'label' => 'Facebook',
            'svg'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
        ],
        'youtube_url' => [
            'label' => 'YouTube',
            'svg'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.54C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/></svg>',
        ],
        'instagram_url' => [
            'label' => 'Instagram',
            'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
        ],
        'twitter_url' => [
            'label' => 'Twitter / X',
            'svg'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.259 5.63 5.905-5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        ],
        'linkedin_url' => [
            'label' => 'LinkedIn',
            'svg'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>',
        ],
    ];

    $out = [];
    foreach ($map as $clave => $meta) {
        $url = tsjConfig($clave, '');
        if ($url === '') {
            continue;
        }
        $out[] = [
            'label' => $meta['label'],
            'url'   => $url,
            'svg'   => $meta['svg'],
        ];
    }
    return $out;
}
