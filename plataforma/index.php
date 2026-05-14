<?php
$tsj_module    = '';
$tsj_title     = 'Portal de Servicios';
$tsj_extra_css = [];
$tsj_no_offset = true;
$tsj_head_extra = '<style>
body { margin: 0; padding: 0; background: #f0f2f7; font-family: \'Poppins\', Arial, sans-serif; }

/* ── Hero ─────────────────────────────────────────────── */
.portal-hero {
    background: linear-gradient(135deg, #0d0640 0%, #1a0960 45%, #2a1080 100%);
    padding: 144px 32px 72px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.portal-hero::before {
    content: \'\';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse at 20% 50%, rgba(236,90,104,.12) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 30%, rgba(50,18,154,.3) 0%, transparent 55%);
    pointer-events: none;
}
.portal-hero-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    color: rgba(255,255,255,.45);
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 22px;
}
.portal-hero-label::before,
.portal-hero-label::after {
    content: \'\';
    width: 40px;
    height: 1px;
    background: rgba(255,255,255,.2);
}
.portal-hero h1 {
    font-size: clamp(2rem, 4.5vw, 3rem);
    font-weight: 800;
    color: #fff;
    margin: 0 0 14px;
    line-height: 1.15;
    letter-spacing: -.5px;
}
.portal-hero h1 span {
    color: #ec5a68;
}
.portal-hero p {
    font-size: 1rem;
    color: rgba(255,255,255,.6);
    max-width: 520px;
    margin: 0 auto;
    line-height: 1.7;
    font-weight: 300;
}
.portal-hero-wave {
    position: absolute;
    bottom: -1px; left: 0; right: 0;
    height: 40px;
    background: #f0f2f7;
    clip-path: ellipse(55% 100% at 50% 100%);
}

/* ── Sección de módulos ──────────────────────────────── */
.portal-section {
    max-width: 1140px;
    margin: 0 auto;
    padding: 48px 24px 56px;
}
.portal-section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 32px;
}
.portal-section-line {
    flex: 1;
    height: 1px;
    background: #d8dce8;
}
.portal-section-title {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: #8892a8;
    white-space: nowrap;
}

/* ── Cards ──────────────────────────────────────────── */
.portal-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
@media (max-width: 900px) { .portal-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px) { .portal-grid { grid-template-columns: 1fr; } }

.portal-card {
    background: #fff;
    border-radius: 14px;
    padding: 28px 24px 24px;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    gap: 12px;
    transition: transform .22s ease, box-shadow .22s ease;
    box-shadow: 0 2px 10px rgba(20,10,80,.06);
    border: 1px solid #e8eaf2;
    position: relative;
    overflow: hidden;
}
.portal-card::before {
    content: \'\';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #1a0960, #32129a);
    opacity: 0;
    transition: opacity .22s;
}
.portal-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(26,9,96,.12);
}
.portal-card:hover::before { opacity: 1; }

.portal-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #f0eef8;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.portal-card-icon svg {
    width: 24px;
    height: 24px;
    stroke: #32129a;
    fill: none;
    stroke-width: 1.75;
    stroke-linecap: round;
    stroke-linejoin: round;
}
.portal-card-body { flex: 1; }
.portal-card h2 {
    font-size: 1rem;
    font-weight: 700;
    color: #1a0960;
    margin: 0 0 6px;
}
.portal-card p {
    font-size: 0.82rem;
    color: #6b7280;
    margin: 0;
    line-height: 1.55;
}
.portal-card-arrow {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    color: #32129a;
    margin-top: 4px;
    opacity: 0;
    transform: translateX(-4px);
    transition: opacity .2s, transform .2s;
}
.portal-card:hover .portal-card-arrow {
    opacity: 1;
    transform: translateX(0);
}
.portal-card-arrow svg {
    width: 14px; height: 14px;
    stroke: #32129a; fill: none;
    stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
}
</style>';
require_once __DIR__ . '/shared/header.php';
?>

<!-- Hero -->
<section class="portal-hero">
    <p class="portal-hero-label">Sistema de Servicios Institucionales</p>
    <h1>Tecnológico Superior<br><span>de Jalisco</span></h1>
    <p>Accede a los módulos y recursos académicos del Campus Chapala desde un solo lugar.</p>
    <div class="portal-hero-wave"></div>
</section>

<!-- Módulos -->
<div class="portal-section">
    <div class="portal-section-header">
        <div class="portal-section-line"></div>
        <span class="portal-section-title">Módulos disponibles</span>
        <div class="portal-section-line"></div>
    </div>

    <div class="portal-grid">

        <a href="modulos/visitantes/index.php" class="portal-card">
            <div class="portal-card-icon">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="portal-card-body">
                <h2>Visitantes</h2>
                <p>Directorio institucional, docentes, carreras y servicios del tecnológico.</p>
                <div class="portal-card-arrow">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
            </div>
        </a>

        <a href="modulos/biblioteca/buscar.php" class="portal-card">
            <div class="portal-card-icon">
                <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <div class="portal-card-body">
                <h2>Biblioteca</h2>
                <p>Catálogo de libros, búsqueda y solicitud de préstamos bibliográficos.</p>
                <div class="portal-card-arrow">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
            </div>
        </a>

        <a href="modulos/convenios/index.php" class="portal-card">
            <div class="portal-card-icon">
                <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </div>
            <div class="portal-card-body">
                <h2>Convenios</h2>
                <p>Directorio de convenios académicos, empresariales e internacionales.</p>
                <div class="portal-card-arrow">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
            </div>
        </a>

        <a href="modulos/horarios/index.php" class="portal-card">
            <div class="portal-card-icon">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="portal-card-body">
                <h2>Horarios</h2>
                <p>Búsqueda de maestros y consulta de horarios por carrera.</p>
                <div class="portal-card-arrow">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
            </div>
        </a>

        <a href="modulos/requisitos/residencia.php" class="portal-card">
            <div class="portal-card-icon">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <div class="portal-card-body">
                <h2>Requisitos</h2>
                <p>Residencia profesional y servicio social: checklist, documentos y descargas.</p>
                <div class="portal-card-arrow">Acceder <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></div>
            </div>
        </a>

    </div>
</div>

<?php require_once __DIR__ . '/shared/footer.php'; ?>
