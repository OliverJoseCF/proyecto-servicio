<?php
/**
 * Template compartido para páginas de carrera — versión con tabs.
 * Variables requeridas antes de incluir:
 *   $_carrera_clave  — ej. 'ISC'
 *   $_carrera_nombre — ej. 'Ingeniería en Sistemas Computacionales'
 *   $_color_acento   — color hex del acento de la carrera (se leerá de BD si no se pasa)
 */
require_once __DIR__ . '/../../shared/header.php';
require_once __DIR__ . '/../../shared/config.php';

// Tab activo (por parámetro GET)
$tab = preg_replace('/[^a-z_]/', '', strtolower($_GET['tab'] ?? 'objetivo'));
$tabsValidos = ['objetivo','perfil','obj_edu','atributos','reticula','plan','contacto'];
if (!in_array($tab, $tabsValidos, true)) $tab = 'objetivo';

// Datos de la carrera
$carrera     = null;
$materias    = [];
$atributos   = [];
$coordinador = null;
$docentes    = [];
$colorFinal  = $_color_acento ?? '#32129a';

try {
    $db = getPDO(DB_NAME);

    // Carrera + textos nuevos (defensivo: columnas nuevas pueden no existir en BD antigua)
    try {
        $stmtC = $db->prepare(
            'SELECT id, nombre, color, reticula_url,
                    objetivo_general, perfil_profesional, objetivos_educacionales
             FROM carreras WHERE clave = ? AND activo = 1 LIMIT 1'
        );
        $stmtC->execute([$_carrera_clave]);
        $carrera = $stmtC->fetch();
    } catch (\Throwable $eC) {
        // Columnas nuevas no existen aún — traer solo las básicas
        $stmtC2 = $db->prepare(
            'SELECT id, nombre, color, reticula_url FROM carreras WHERE clave = ? AND activo = 1 LIMIT 1'
        );
        $stmtC2->execute([$_carrera_clave]);
        $carrera = $stmtC2->fetch();
        if ($carrera) {
            $carrera['objetivo_general']        = null;
            $carrera['perfil_profesional']      = null;
            $carrera['objetivos_educacionales'] = null;
        }
    }

    if ($carrera) {
        $carreraId  = (int)$carrera['id'];
        $colorFinal = $carrera['color'] ?: ($colorFinal);

        // Atributos de egreso
        try {
            $stmtA = $db->prepare(
                'SELECT texto FROM atributos_egreso WHERE carrera_id = ? AND activo = 1 ORDER BY orden'
            );
            $stmtA->execute([$carreraId]);
            $atributos = $stmtA->fetchAll();
        } catch (\Throwable $eA) { $atributos = []; }

        // Materias
        $stmtM = $db->prepare(
            'SELECT nombre FROM materias WHERE carrera_id = ? AND activo = 1 ORDER BY orden'
        );
        $stmtM->execute([$carreraId]);
        $materias = $stmtM->fetchAll();

        // Coordinador
        try {
            $stmtCo = $db->prepare(
                'SELECT nombre, correo FROM coordinadores WHERE carrera_id = ? AND activo = 1 LIMIT 1'
            );
            $stmtCo->execute([$carreraId]);
            $coordinador = $stmtCo->fetch() ?: null;
        } catch (\Throwable $eCo) { $coordinador = null; }

        // Docentes
        try {
            $stmtD = $db->prepare(
                'SELECT d.nombre, d.correo, d.foto
                 FROM docentes d
                 JOIN docente_carrera dc ON dc.docente_id = d.id
                 WHERE dc.carrera_id = ? AND d.activo = 1
                 ORDER BY d.orden, d.nombre'
            );
            $stmtD->execute([$carreraId]);
            $docentes = $stmtD->fetchAll();
        } catch (\Throwable $eD) { $docentes = []; }
    }

} catch (\Throwable $e) {
    $carrera = null;
}

$reticula_url = $carrera['reticula_url'] ?? null;
$base         = defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma';
$base_img     = $base . '/modulos/visitantes/imagenes/';

// Labels de tabs
$tabsInfo = [
    'objetivo'  => ['label' => 'Objetivo General',        'icon' => 'flag'],
    'perfil'    => ['label' => 'Perfil Profesional',      'icon' => 'person_check'],
    'obj_edu'   => ['label' => 'Objetivos Educacionales', 'icon' => 'checklist'],
    'atributos' => ['label' => 'Atributos de Egreso',     'icon' => 'workspace_premium'],
    'reticula'  => ['label' => 'Retícula',                'icon' => 'account_tree'],
    'plan'      => ['label' => 'Plan de Estudios',        'icon' => 'list_alt'],
    'contacto'  => ['label' => 'Contacto',                'icon' => 'group'],
];

// Parámetro de carrera para las URLs de tab
$carreraParam = 'carrera=' . urlencode($_GET['carrera'] ?? $_carrera_clave ?? '');
$currentUrl   = strtok($_SERVER['REQUEST_URI'] ?? '', '?');

function tabUrl(string $key, string $base, string $cparam): string {
    return htmlspecialchars($base . '?' . $cparam . '&tab=' . $key);
}

$imgPlaceholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='72' height='72'%3E%3Crect width='72' height='72' fill='%23e5e7eb' rx='36'/%3E%3Ctext x='36' y='46' text-anchor='middle' font-size='28' fill='%239ca3af'%3E%3F%3C/text%3E%3C/svg%3E";
?>
<style>
/* ── Contenedor principal ───────────────────────────────────── */
.car-wrap {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 20px 48px;
}

/* ── Layout dos columnas: sidebar + contenido ───────────────── */
.car-layout {
  display: grid;
  grid-template-columns: 230px 1fr;
  gap: 24px;
  align-items: start;
}

/* ── Sidebar de navegación ──────────────────────────────────── */
.car-tabs-sidebar {
  background: var(--tsj-white, #fff);
  border: 1px solid var(--tsj-gray-200, #e5e7eb);
  border-radius: var(--tsj-radius-lg, 16px);
  box-shadow: 0 1px 4px rgba(0,0,0,.05);
  overflow: hidden;
  position: sticky;
  top: 84px;
}
.car-tabs-sidebar-header {
  padding: 14px 18px 12px;
  background: var(--tsj-bg, #f8f9fc);
  border-bottom: 1px solid var(--tsj-gray-200, #e5e7eb);
}
.car-tabs-sidebar-title {
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: var(--tsj-gray-400, #9ca3af);
}
.car-tabs-nav {
  padding: 8px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.car-tab {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 10px;
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  font-size: 13px;
  font-weight: 500;
  color: var(--tsj-gray-600, #4b5563);
  text-decoration: none;
  transition: background .15s, color .15s, box-shadow .15s;
  position: relative;
}
.car-tab:hover {
  background: color-mix(in srgb, var(--car-accent, #32129a) 8%, transparent);
  color: var(--car-accent, #32129a);
}
.car-tab.active {
  background: var(--car-accent, #32129a);
  color: #fff;
  font-weight: 600;
  box-shadow: 0 3px 12px color-mix(in srgb, var(--car-accent, #32129a) 35%, transparent);
}
.car-tab .material-symbols-rounded { font-size: 17px; flex-shrink: 0; }
.car-tab-label { flex: 1; line-height: 1.3; }

/* ── Panel de contenido ─────────────────────────────────────── */
.car-panels-wrap {
  background: var(--tsj-white, #fff);
  border: 1px solid var(--tsj-gray-200, #e5e7eb);
  border-radius: var(--tsj-radius-lg, 16px);
  box-shadow: 0 1px 4px rgba(0,0,0,.05);
  min-height: 360px;
}
.car-panel { display: none; animation: carFadeIn .18s ease; }
.car-panel.active { display: block; }
.car-panel-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 20px 28px 16px;
  border-bottom: 1px solid var(--tsj-gray-200, #e5e7eb);
  background: var(--tsj-bg, #f8f9fc);
  border-radius: var(--tsj-radius-lg, 16px) var(--tsj-radius-lg, 16px) 0 0;
}
.car-panel-header-icon {
  width: 38px; height: 38px;
  border-radius: 10px;
  background: var(--car-accent, #32129a);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.car-panel-header-icon .material-symbols-rounded { font-size: 20px; color: #fff; }
.car-panel-header-title {
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  font-size: 15px;
  font-weight: 700;
  color: var(--tsj-blue-dark, #1a0960);
}
.car-panel-body { padding: 28px 28px 24px; }
@keyframes carFadeIn {
  from { opacity: 0; transform: translateY(6px); }
  to   { opacity: 1; transform: none; }
}

/* ── Responsive: mobile pasa a scroll horizontal ────────────── */
@media (max-width: 820px) {
  .car-layout {
    grid-template-columns: 1fr;
    gap: 14px;
  }
  .car-tabs-sidebar {
    position: static;
    border-radius: 12px;
  }
  .car-tabs-sidebar-header { display: none; }
  .car-tabs-nav {
    flex-direction: row;
    overflow-x: auto;
    padding: 10px;
    gap: 6px;
    scrollbar-width: none;
  }
  .car-tabs-nav::-webkit-scrollbar { display: none; }
  .car-tab {
    white-space: nowrap;
    flex-shrink: 0;
    padding: 8px 14px;
    border-radius: 999px;
    border: 1.5px solid var(--tsj-gray-200, #e5e7eb);
    font-size: 12px;
  }
  .car-tab:hover { border-color: var(--car-accent, #32129a); }
  .car-tab.active { border-color: var(--car-accent, #32129a); box-shadow: none; }
}

/* ── Tarjeta de texto (objetivo, perfil, objetivos edu) ─────── */
.car-text-card {
  max-width: 760px;
  margin: 0 auto;
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  font-size: 14.5px;
  color: var(--tsj-gray-700, #374151);
  line-height: 1.8;
}
.car-text-card p { margin: 0 0 .8em; }

/* ── Mensaje "próximamente" ─────────────────────────────────── */
.car-soon {
  text-align: center;
  padding: 3rem 0 2rem;
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  color: var(--tsj-gray-400, #9ca3af);
  font-size: 14.5px;
}
.car-soon .material-symbols-rounded { font-size: 44px; display: block; margin-bottom: 10px; color: var(--tsj-gray-200, #e5e7eb); }

/* ── Lista numerada (atributos / materias) ──────────────────── */
.car-num-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 10px;
  margin-bottom: 20px;
}
.car-num-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  background: var(--tsj-bg, #f8f9fc);
  border: 1px solid var(--tsj-gray-200, #e5e7eb);
  border-left: 4px solid var(--car-accent, #32129a);
  border-radius: var(--tsj-radius, 10px);
  padding: 12px 16px;
  transition: background .15s, box-shadow .15s;
}
.car-num-item:hover {
  background: #fff;
  box-shadow: 0 2px 10px rgba(51,23,156,.07);
}
.car-num-badge {
  min-width: 28px; height: 28px;
  background: var(--car-accent, #32129a);
  color: #fff; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 700; flex-shrink: 0;
  font-family: var(--tsj-font, 'Poppins', sans-serif);
}
.car-num-label {
  font-size: 13px; font-weight: 500;
  color: var(--tsj-blue-dark, #1a0960); line-height: 1.4;
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  padding-top: 4px;
}
.car-num-count {
  text-align: center;
  margin-top: 4px;
  font-size: 12.5px;
  color: var(--tsj-gray-400, #9ca3af);
  font-family: var(--tsj-font, 'Poppins', sans-serif);
}
.car-num-count span {
  display: inline-block;
  background: var(--tsj-blue-50, #f0edff);
  color: var(--car-accent, #32129a);
  padding: 3px 12px; border-radius: 999px;
  font-weight: 700; font-size: 13px;
}

/* ── Sección retícula ───────────────────────────────────────── */
.car-ret-section {
  text-align: center;
  padding: 2rem 0 1rem;
}
.car-ret-section p {
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  font-size: 13.5px;
  color: var(--tsj-gray-600, #4b5563);
  margin-bottom: 20px;
}
.car-ret-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 13px 28px;
  border-radius: var(--tsj-radius, 10px);
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  font-size: 14px; font-weight: 700;
  text-decoration: none;
  color: #fff;
  background: var(--car-accent, #32129a);
  box-shadow: 0 4px 14px rgba(51,23,156,.22);
  transition: transform .18s, box-shadow .18s;
}
.car-ret-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(51,23,156,.3); }

/* ── Card coordinador ───────────────────────────────────────── */
.car-coord-wrap { max-width: 520px; margin: 0 auto 28px; }
.car-coord {
  background: var(--tsj-blue-50, #f0edff);
  border: 1.5px solid var(--car-accent, #32129a);
  border-radius: var(--tsj-radius-lg, 16px);
  padding: 18px 22px;
  display: flex;
  align-items: center;
  gap: 16px;
}
.car-coord-icon {
  width: 48px; height: 48px;
  border-radius: var(--tsj-radius, 10px);
  background: var(--car-accent, #32129a);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.car-coord-icon .material-symbols-rounded { font-size: 24px; color: #fff; }
.car-coord-tag {
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  font-size: 10px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .07em;
  color: var(--car-accent, #32129a);
  margin-bottom: 2px;
}
.car-coord-nombre {
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  font-size: 15px; font-weight: 700;
  color: var(--tsj-blue-dark, #1a0960);
  margin-bottom: 3px;
}
.car-coord-mail {
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  font-size: 12px;
  color: var(--car-accent, #32129a);
  text-decoration: none;
}
.car-coord-mail:hover { text-decoration: underline; }

/* ── Encabezado de sección de docentes ──────────────────────── */
.car-doc-heading {
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  font-size: 14px; font-weight: 700;
  color: var(--tsj-blue-dark, #1a0960);
  text-align: center;
  margin: 0 0 4px;
}
.car-doc-sub {
  text-align: center;
  font-size: 12.5px;
  color: var(--tsj-gray-400, #9ca3af);
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  margin-bottom: 16px;
}
.car-doc-sub span {
  display: inline-block;
  background: var(--tsj-blue-50, #f0edff);
  color: var(--car-accent, #32129a);
  padding: 2px 11px; border-radius: 999px;
  font-weight: 700; font-size: 13px;
}

/* ── Grid de docentes ───────────────────────────────────────── */
.car-doc-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 14px;
}
.car-doc-card {
  background: var(--tsj-bg, #f8f9fc);
  border: 1px solid var(--tsj-gray-200, #e5e7eb);
  border-radius: var(--tsj-radius-lg, 16px);
  padding: 20px 14px 16px;
  text-align: center;
  transition: background .15s, box-shadow .15s, transform .18s;
  position: relative;
  overflow: hidden;
}
.car-doc-card::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: var(--car-accent, #32129a);
  opacity: 0;
  transition: opacity .18s;
}
.car-doc-card:hover {
  background: #fff;
  box-shadow: 0 6px 20px rgba(51,23,156,.1);
  transform: translateY(-3px);
}
.car-doc-card:hover::before { opacity: 1; }
.car-doc-card img {
  width: 68px; height: 68px;
  border-radius: 50%; object-fit: cover;
  border: 2.5px solid var(--tsj-gray-200, #e5e7eb);
  margin-bottom: 10px;
  display: block; margin-left: auto; margin-right: auto;
}
.car-doc-nombre {
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  font-size: 13px; font-weight: 700;
  color: var(--tsj-blue-dark, #1a0960);
  margin-bottom: 5px; line-height: 1.3;
}
.car-doc-mail {
  font-family: var(--tsj-font, 'Poppins', sans-serif);
  font-size: 11px;
  color: var(--car-accent, #32129a);
  text-decoration: none;
  word-break: break-all;
}
.car-doc-mail:hover { text-decoration: underline; }
.car-doc-no-mail {
  font-size: 11px; color: var(--tsj-gray-400, #9ca3af);
  font-family: var(--tsj-font, 'Poppins', sans-serif);
}

/* ── Botón volver ───────────────────────────────────────────── */
.car-footer {
  text-align: center;
  margin-top: 32px;
  padding-top: 20px;
  border-top: 1px solid var(--tsj-gray-200, #e5e7eb);
}
.car-volver {
  display: inline-flex; align-items: center; gap: 7px;
  color: var(--tsj-blue, #33179c);
  font-size: 13px; font-weight: 600;
  text-decoration: none;
  padding: 8px 18px;
  border: 1.5px solid var(--tsj-gray-200, #e5e7eb);
  border-radius: var(--tsj-radius-sm, 6px);
  background: var(--tsj-white, #fff);
  transition: border-color .18s, box-shadow .18s;
  font-family: var(--tsj-font, 'Poppins', sans-serif);
}
.car-volver:hover {
  border-color: var(--tsj-blue, #33179c);
  box-shadow: 0 2px 8px rgba(51,23,156,.1);
}

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 640px) {
  .car-panel-body { padding: 20px 16px 16px; }
  .car-num-list { grid-template-columns: 1fr; }
  .car-doc-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
  .car-coord { flex-direction: column; text-align: center; }
}
</style>

<main id="main">

  <div class="tsj-page-header">
    <div class="tsj-page-header-line"></div>
    <h1><?= htmlspecialchars($_carrera_nombre) ?></h1>
    <p class="tsj-page-header-sub">Conoce el programa académico, perfil de egreso y cuerpo docente</p>
  </div>

  <div style="--car-accent:<?= htmlspecialchars($colorFinal) ?>">
  <div class="car-wrap">
  <div class="car-layout">

    <!-- ── Sidebar de navegación ── -->
    <aside class="car-tabs-sidebar">
      <div class="car-tabs-sidebar-header">
        <div class="car-tabs-sidebar-title">Secciones</div>
      </div>
      <nav class="car-tabs-nav">
        <?php foreach ($tabsInfo as $key => $info): ?>
        <a href="<?= tabUrl($key, $currentUrl, $carreraParam) ?>"
           class="car-tab <?= $tab === $key ? 'active' : '' ?>"
           data-tab="<?= $key ?>">
          <span class="material-symbols-rounded"><?= $info['icon'] ?></span>
          <span class="car-tab-label"><?= $info['label'] ?></span>
        </a>
        <?php endforeach; ?>
      </nav>
    </aside>

    <!-- ── Panel de contenido ── -->
    <div class="car-panels-wrap">

      <!-- ══ PANEL: Objetivo General ════════════════════════════ -->
      <div class="car-panel <?= $tab === 'objetivo' ? 'active' : '' ?>" data-tab="objetivo">
        <div class="car-panel-header">
          <div class="car-panel-header-icon"><span class="material-symbols-rounded">flag</span></div>
          <div class="car-panel-header-title">Objetivo General</div>
        </div>
        <div class="car-panel-body">
          <?php $texto = trim($carrera['objetivo_general'] ?? ''); ?>
          <?php if ($texto): ?>
            <div class="car-text-card"><?= nl2br(htmlspecialchars($texto)) ?></div>
          <?php else: ?>
            <div class="car-soon">
              <span class="material-symbols-rounded">flag</span>
              Próximamente
            </div>
          <?php endif; ?>
          <div class="car-footer">
            <a href="ofertaAcademica.php" class="car-volver">← Volver a Oferta Académica</a>
          </div>
        </div>
      </div>

      <!-- ══ PANEL: Perfil Profesional ══════════════════════════ -->
      <div class="car-panel <?= $tab === 'perfil' ? 'active' : '' ?>" data-tab="perfil">
        <div class="car-panel-header">
          <div class="car-panel-header-icon"><span class="material-symbols-rounded">person_check</span></div>
          <div class="car-panel-header-title">Perfil Profesional</div>
        </div>
        <div class="car-panel-body">
          <?php $texto = trim($carrera['perfil_profesional'] ?? ''); ?>
          <?php if ($texto): ?>
            <div class="car-text-card"><?= nl2br(htmlspecialchars($texto)) ?></div>
          <?php else: ?>
            <div class="car-soon">
              <span class="material-symbols-rounded">person_check</span>
              Próximamente
            </div>
          <?php endif; ?>
          <div class="car-footer">
            <a href="ofertaAcademica.php" class="car-volver">← Volver a Oferta Académica</a>
          </div>
        </div>
      </div>

      <!-- ══ PANEL: Objetivos Educacionales ═════════════════════ -->
      <div class="car-panel <?= $tab === 'obj_edu' ? 'active' : '' ?>" data-tab="obj_edu">
        <div class="car-panel-header">
          <div class="car-panel-header-icon"><span class="material-symbols-rounded">checklist</span></div>
          <div class="car-panel-header-title">Objetivos Educacionales</div>
        </div>
        <div class="car-panel-body">
          <?php $texto = trim($carrera['objetivos_educacionales'] ?? ''); ?>
          <?php if ($texto): ?>
            <div class="car-text-card"><?= nl2br(htmlspecialchars($texto)) ?></div>
          <?php else: ?>
            <div class="car-soon">
              <span class="material-symbols-rounded">checklist</span>
              Próximamente
            </div>
          <?php endif; ?>
          <div class="car-footer">
            <a href="ofertaAcademica.php" class="car-volver">← Volver a Oferta Académica</a>
          </div>
        </div>
      </div>

      <!-- ══ PANEL: Atributos de Egreso ═════════════════════════ -->
      <div class="car-panel <?= $tab === 'atributos' ? 'active' : '' ?>" data-tab="atributos">
        <div class="car-panel-header">
          <div class="car-panel-header-icon"><span class="material-symbols-rounded">workspace_premium</span></div>
          <div class="car-panel-header-title">Atributos de Egreso</div>
        </div>
        <div class="car-panel-body">
          <?php if (!empty($atributos)): ?>
            <div class="car-num-list">
              <?php foreach ($atributos as $i => $a): ?>
              <div class="car-num-item">
                <span class="car-num-badge"><?= $i + 1 ?></span>
                <span class="car-num-label"><?= htmlspecialchars($a['texto']) ?></span>
              </div>
              <?php endforeach; ?>
            </div>
            <div class="car-num-count">
              <span><?= count($atributos) ?></span> atributos de egreso
            </div>
          <?php else: ?>
            <div class="car-soon">
              <span class="material-symbols-rounded">workspace_premium</span>
              Próximamente
            </div>
          <?php endif; ?>
          <div class="car-footer">
            <a href="ofertaAcademica.php" class="car-volver">← Volver a Oferta Académica</a>
          </div>
        </div>
      </div>

      <!-- ══ PANEL: Retícula ════════════════════════════════════ -->
      <div class="car-panel <?= $tab === 'reticula' ? 'active' : '' ?>" data-tab="reticula">
        <div class="car-panel-header">
          <div class="car-panel-header-icon"><span class="material-symbols-rounded">account_tree</span></div>
          <div class="car-panel-header-title">Retícula</div>
        </div>
        <div class="car-panel-body">
          <?php if ($reticula_url): ?>
            <div class="car-ret-section">
              <p>Haz clic en el botón para abrir el mapa curricular en una nueva pestaña.</p>
              <a href="<?= htmlspecialchars($reticula_url) ?>" target="_blank" rel="noopener noreferrer"
                 class="car-ret-btn">
                <span class="material-symbols-rounded">account_tree</span>
                Abrir Retícula (PDF)
                <span class="material-symbols-rounded" style="font-size:15px">open_in_new</span>
              </a>
            </div>
          <?php else: ?>
            <div class="car-soon">
              <span class="material-symbols-rounded">account_tree</span>
              Próximamente
            </div>
          <?php endif; ?>
          <div class="car-footer">
            <a href="ofertaAcademica.php" class="car-volver">← Volver a Oferta Académica</a>
          </div>
        </div>
      </div>

      <!-- ══ PANEL: Plan de Estudios ════════════════════════════ -->
      <div class="car-panel <?= $tab === 'plan' ? 'active' : '' ?>" data-tab="plan">
        <div class="car-panel-header">
          <div class="car-panel-header-icon"><span class="material-symbols-rounded">list_alt</span></div>
          <div class="car-panel-header-title">Plan de Estudios</div>
        </div>
        <div class="car-panel-body">
          <?php if (!empty($materias)): ?>
            <div class="car-num-list">
              <?php foreach ($materias as $i => $m): ?>
              <div class="car-num-item">
                <span class="car-num-badge"><?= $i + 1 ?></span>
                <span class="car-num-label"><?= htmlspecialchars($m['nombre']) ?></span>
              </div>
              <?php endforeach; ?>
            </div>
            <div class="car-num-count">
              <span><?= count($materias) ?></span> materias en el plan de estudios
            </div>
          <?php else: ?>
            <div class="car-soon">
              <span class="material-symbols-rounded">list_alt</span>
              Sin materias registradas para esta carrera.
            </div>
          <?php endif; ?>
          <div class="car-footer">
            <a href="ofertaAcademica.php" class="car-volver">← Volver a Oferta Académica</a>
          </div>
        </div>
      </div>

      <!-- ══ PANEL: Contacto ════════════════════════════════════ -->
      <div class="car-panel <?= $tab === 'contacto' ? 'active' : '' ?>" data-tab="contacto">
        <div class="car-panel-header">
          <div class="car-panel-header-icon"><span class="material-symbols-rounded">group</span></div>
          <div class="car-panel-header-title">Contacto</div>
        </div>
        <div class="car-panel-body">
          <?php $hayContacto = $coordinador || !empty($docentes); ?>
          <?php if ($hayContacto): ?>

            <?php if ($coordinador): ?>
            <div class="car-coord-wrap">
              <div class="car-coord">
                <div class="car-coord-icon">
                  <span class="material-symbols-rounded">manage_accounts</span>
                </div>
                <div>
                  <div class="car-coord-tag">Coordinador de la carrera</div>
                  <div class="car-coord-nombre"><?= htmlspecialchars($coordinador['nombre']) ?></div>
                  <?php if (!empty($coordinador['correo'])): ?>
                  <a href="mailto:<?= htmlspecialchars($coordinador['correo']) ?>" class="car-coord-mail">
                    <?= htmlspecialchars($coordinador['correo']) ?>
                  </a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($docentes)): ?>
            <p class="car-doc-heading">Docentes de la carrera</p>
            <p class="car-doc-sub">
              <span><?= count($docentes) ?></span> docentes registrados
            </p>
            <div class="car-doc-grid">
              <?php foreach ($docentes as $d):
                $foto = $d['foto'] ? htmlspecialchars($base_img . $d['foto']) : $imgPlaceholder;
              ?>
              <div class="car-doc-card">
                <img src="<?= $foto ?>" alt="" onerror="this.src='<?= $imgPlaceholder ?>'">
                <div class="car-doc-nombre"><?= htmlspecialchars($d['nombre']) ?></div>
                <?php if (!empty($d['correo'])): ?>
                <a href="mailto:<?= htmlspecialchars($d['correo']) ?>" class="car-doc-mail">
                  <?= htmlspecialchars($d['correo']) ?>
                </a>
                <?php else: ?>
                <span class="car-doc-no-mail">Sin correo registrado</span>
                <?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>

          <?php else: ?>
            <div class="car-soon">
              <span class="material-symbols-rounded">group</span>
              Próximamente
            </div>
          <?php endif; ?>
          <div class="car-footer">
            <a href="ofertaAcademica.php" class="car-volver">← Volver a Oferta Académica</a>
          </div>
        </div>
      </div>

    </div><!-- /car-panels-wrap -->
  </div><!-- /car-layout -->
  </div><!-- /car-wrap -->
  </div><!-- /car-accent -->

</main>

<script>
(function () {
  // Navegación de secciones SIN recarga de página: los paneles ya están en el
  // DOM, solo alternamos la clase .active. Mantiene el href como fallback sin JS.
  var tabs   = document.querySelectorAll('.car-tab');
  var panels = document.querySelectorAll('.car-panel');
  if (!tabs.length) return;

  function activar(key, push, href) {
    var destino = document.querySelector('.car-panel[data-tab="' + key + '"]');
    if (!destino) return false;
    tabs.forEach(function (t)   { t.classList.toggle('active', t.dataset.tab === key); });
    panels.forEach(function (p) { p.classList.remove('active'); });
    destino.classList.add('active');
    if (push && href) history.pushState({ tab: key }, '', href);
    return true;
  }

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function (e) {
      // Respeta clic con modificadores (abrir en pestaña nueva, etc.)
      if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
      if (activar(tab.dataset.tab, true, tab.href)) e.preventDefault();
    });
  });

  // Botón atrás/adelante del navegador
  window.addEventListener('popstate', function () {
    var params = new URLSearchParams(window.location.search);
    activar(params.get('tab') || 'objetivo', false);
  });
})();
</script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
