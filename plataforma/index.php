<?php
$tsj_module    = '';
$tsj_title     = 'Portal';
$tsj_extra_css = [];
$tsj_head_extra = '<style>
body { background-color: #f5f5f5; font-family: \'Poppins\', \'Arial\', sans-serif; }

.portal-hero {
    text-align: center;
    padding: 48px 24px 32px;
}
.portal-hero h1 {
    font-family: \'Poppins\', sans-serif;
    font-size: clamp(1.8rem, 4vw, 2.6rem);
    font-weight: 800;
    color: #32129a;
    margin-bottom: 12px;
}
.portal-hero p {
    font-size: 1rem;
    color: #555;
    max-width: 560px;
    margin: 0 auto;
}

.portal-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 24px;
    max-width: 1100px;
    margin: 0 auto 0;
    padding: 0 24px 40px;
}

.portal-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(50, 18, 154, 0.08);
    padding: 36px 28px;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    border-top: 4px solid #32129a;
    text-align: center;
}
.portal-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 28px rgba(50, 18, 154, 0.15);
}
.portal-card.pending {
    border-top-color: #ccc;
    opacity: 0.55;
    pointer-events: none;
}

.portal-card-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: rgba(50, 18, 154, 0.07);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}
.portal-card h2 {
    font-family: \'Poppins\', sans-serif;
    font-size: 1.15rem;
    font-weight: 700;
    color: #32129a;
    margin: 0;
}
.portal-card p {
    font-size: 0.875rem;
    color: #666;
    margin: 0;
    line-height: 1.5;
}
.portal-card .badge-pending {
    font-size: 0.72rem;
    font-weight: 700;
    color: #999;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>';
require_once __DIR__ . '/shared/header.php';
?>

<section class="portal-hero">
    <h1>Plataforma TSJ Chapala</h1>
    <p>Selecciona el módulo al que deseas acceder</p>
</section>

<div class="portal-grid">

    <a href="modulos/visitantes/index.php" class="portal-card">
        <div class="portal-card-icon">🏫</div>
        <h2>Visitantes</h2>
        <p>Directorio institucional, docentes, carreras y servicios del tecnológico.</p>
    </a>

    <a href="modulos/biblioteca/buscar.php" class="portal-card">
        <div class="portal-card-icon">📚</div>
        <h2>Biblioteca</h2>
        <p>Catálogo de libros, búsqueda y solicitud de préstamos bibliográficos.</p>
    </a>

    <a href="modulos/convenios/index.php" class="portal-card">
        <div class="portal-card-icon">🤝</div>
        <h2>Convenios</h2>
        <p>Directorio de convenios académicos, empresariales e internacionales.</p>
    </a>

    <a href="modulos/horarios/index.php" class="portal-card">
        <div class="portal-card-icon">📅</div>
        <h2>Horarios</h2>
        <p>Búsqueda de maestros y consulta de horarios por carrera.</p>
    </a>

    <a href="modulos/requisitos/residencia.php" class="portal-card">
        <div class="portal-card-icon">📋</div>
        <h2>Requisitos</h2>
        <p>Residencia profesional y servicio social: checklist, documentos y descargas.</p>
    </a>

</div>

<?php require_once __DIR__ . '/shared/footer.php'; ?>
