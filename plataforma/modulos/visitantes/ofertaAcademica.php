<?php
$tsj_module = 'oferta';
$tsj_title  = 'Oferta Académica';

require_once __DIR__ . '/../../shared/config.php';

// Carreras con su info y página de materias
$carreras = [
    'ISC' => [
        'nombre'   => 'Ing. en Sistemas Computacionales',
        'color'    => '#32129a',
        'icono'    => 'computer',
        'desc'     => 'Desarrolla software, sistemas y soluciones tecnológicas de alto impacto.',
        'href'     => 'MateriasSistemas.php',
    ],
    'II'  => [
        'nombre'   => 'Ingeniería Industrial',
        'color'    => '#b45309',
        'icono'    => 'precision_manufacturing',
        'desc'     => 'Optimiza procesos productivos y gestiona sistemas industriales.',
        'href'     => 'MateriasIndustrial.php',
    ],
    'IM'  => [
        'nombre'   => 'Ingeniería Mecatrónica',
        'color'    => '#0369a1',
        'icono'    => 'settings_suggest',
        'desc'     => 'Integra mecánica, electrónica y control automático.',
        'href'     => 'MateriasMecatronica.php',
    ],
    'IADEV' => [
        'nombre'   => 'Ing. en Animación Digital y Efectos Visuales',
        'color'    => '#7c3aed',
        'icono'    => 'animation',
        'desc'     => 'Crea personajes, mundos virtuales y efectos para cine y videojuegos.',
        'href'     => 'MateriasAnimacion.php',
    ],
    'IGE' => [
        'nombre'   => 'Ing. en Gestión Empresarial',
        'color'    => '#059669',
        'icono'    => 'business_center',
        'desc'     => 'Dirige organizaciones con enfoque estratégico e innovación.',
        'href'     => 'MateriasGestion.php',
    ],
    'LG'  => [
        'nombre'   => 'Gastronomía',
        'color'    => '#dc2626',
        'icono'    => 'restaurant',
        'desc'     => 'Domina el arte culinario, gestión de restaurantes y cocina creativa.',
        'href'     => 'GastronomiaMaterias.php',
    ],
];

$base = defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma';

// Cargar descripciones guardadas por el admin desde BD
$defaults = [
    'desc_ISC'   => 'Desarrolla software, sistemas y soluciones tecnológicas de alto impacto.',
    'desc_II'    => 'Optimiza procesos productivos y gestiona sistemas industriales.',
    'desc_IM'    => 'Integra mecánica, electrónica y control automático.',
    'desc_IADEV' => 'Crea personajes, mundos virtuales y efectos para cine y videojuegos.',
    'desc_IGE'   => 'Dirige organizaciones con enfoque estratégico e innovación.',
    'desc_LG'    => 'Domina el arte culinario, gestión de restaurantes y cocina creativa.',
];
$descsBD = $defaults;
try {
    $dbCfg = getPDO(DB_NAME);
    $rows  = $dbCfg->query("SELECT clave,valor FROM configuracion WHERE clave LIKE 'desc_%'")->fetchAll();
    foreach ($rows as $r) {
        if (isset($descsBD[$r['clave']]) && $r['valor'] !== '') {
            $descsBD[$r['clave']] = $r['valor'];
        }
    }
} catch (\Throwable $e) {}

// Inyectar descripciones en el array de carreras
$carreras['ISC']['desc']   = $descsBD['desc_ISC'];
$carreras['II']['desc']    = $descsBD['desc_II'];
$carreras['IM']['desc']    = $descsBD['desc_IM'];
$carreras['IADEV']['desc'] = $descsBD['desc_IADEV'];
$carreras['IGE']['desc']   = $descsBD['desc_IGE'];
$carreras['LG']['desc']    = $descsBD['desc_LG'];

$tsj_head_extra = '<style>
.oa-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 24px;
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 20px 64px;
}
.oa-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e8eaf2;
  box-shadow: 0 2px 12px rgba(20,10,80,.06);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  text-decoration: none;
  color: inherit;
  transition: transform .22s ease, box-shadow .22s ease;
  position: relative;
}
.oa-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 14px 36px rgba(20,10,80,.13);
}
.oa-card-top {
  height: 6px;
}
.oa-card-body {
  padding: 24px 22px 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.oa-card-icon {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 4px;
}
.oa-card-icon .material-symbols-rounded {
  font-size: 28px;
  color: #fff;
}
.oa-card-clave {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: #8892a8;
}
.oa-card-nombre {
  font-size: 1rem;
  font-weight: 700;
  color: #1a0960;
  line-height: 1.3;
}
.oa-card-desc {
  font-size: .83rem;
  color: #4a5170;
  line-height: 1.55;
  flex: 1;
}
.oa-card-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: .8rem;
  font-weight: 700;
  margin-top: 6px;
  opacity: 0;
  transform: translateX(-4px);
  transition: opacity .2s, transform .2s;
}
.oa-card:hover .oa-card-link {
  opacity: 1;
  transform: translateX(0);
}
@media (hover:none) { .oa-card-link { opacity:1; transform:translateX(0); } }
@media (max-width:640px) {
  .oa-grid { grid-template-columns: 1fr; gap: 16px; }
}
</style>';

require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">

  <div class="tsj-page-header">
    <div class="tsj-page-header-line"></div>
    <h1>Oferta <span class="tsj-accent">Académica</span></h1>
    <p class="tsj-page-header-sub">Conoce las carreras del Campus Chapala y su plan de estudios</p>
  </div>

  <div class="oa-grid">
    <?php foreach ($carreras as $clave => $c): ?>
    <a href="<?= htmlspecialchars($base . '/modulos/visitantes/' . $c['href']) ?>" class="oa-card">
      <div class="oa-card-top" style="background:<?= $c['color'] ?>"></div>
      <div class="oa-card-body">
        <div class="oa-card-icon" style="background:<?= $c['color'] ?>">
          <span class="material-symbols-rounded" aria-hidden="true"><?= $c['icono'] ?></span>
        </div>
        <div class="oa-card-clave"><?= htmlspecialchars($clave) ?></div>
        <div class="oa-card-nombre"><?= htmlspecialchars($c['nombre']) ?></div>
        <div class="oa-card-desc"><?= htmlspecialchars($c['desc']) ?></div>
        <div class="oa-card-link" style="color:<?= $c['color'] ?>">
          Ver plan de estudios
          <span class="material-symbols-rounded" style="font-size:16px">arrow_forward</span>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>

</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
