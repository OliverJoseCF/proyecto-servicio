<?php
require_once __DIR__ . '/../../shared/lib/auth.php';

$titulo = htmlspecialchars(isset($_GET['titulo']) ? $_GET['titulo'] : '', ENT_QUOTES, 'UTF-8');
$codigo = htmlspecialchars(isset($_GET['codigo']) ? $_GET['codigo'] : '', ENT_QUOTES, 'UTF-8');

$flash_ok    = $_SESSION['flash_ok']    ?? null; unset($_SESSION['flash_ok']);
$flash_error = $_SESSION['flash_error'] ?? null; unset($_SESSION['flash_error']);

$tsj_module     = 'biblioteca';
$tsj_title      = 'Biblioteca — Solicitud de Libro';
$tsj_extra_css  = [];
$tsj_head_extra = '<link rel="stylesheet"'
    . ' href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"'
    . ' integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"'
    . ' crossorigin="anonymous" />'
    . '<link rel="stylesheet"'
    . ' href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"'
    . ' integrity="sha384-3B6NwesSXE7YJlcLI9RpRqGf2p/EgVH8BgoKTaUrmKNDkHPStTQ3EyoYjCGXaOTS"'
    . ' crossorigin="anonymous" />'
    . '<link rel="stylesheet"'
    . ' href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" />'
    . '<style>
/* ── Tema oscuro Biblioteca — Solicitud ─────────────────── */
:root {
  --bib-bg:     #1e1040;
  --bib-gold:   #c9a050;
  --bib-gold-d: rgba(201,160,80,0.5);
  --bib-gold-g: rgba(201,160,80,0.08);
  --bib-gold-b: rgba(201,160,80,0.15);
  --bib-text:   #f0ece4;
  --bib-muted:  rgba(240,236,228,0.45);
}
body {
  background: var(--bib-bg);
  font-family: var(--tsj-font, "Outfit", sans-serif);
  color: var(--bib-text);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  overflow-x: hidden;
}
body::before {
  content: "";
  position: fixed;
  top: -20%; left: -15%;
  width: 55%; height: 55%;
  background: radial-gradient(ellipse, rgba(80,55,160,0.12) 0%, transparent 70%);
  pointer-events: none;
}
body::after {
  content: "";
  position: fixed;
  bottom: -25%; right: -15%;
  width: 50%; height: 50%;
  background: radial-gradient(ellipse, rgba(201,160,80,0.05) 0%, transparent 70%);
  pointer-events: none;
}
.form-wrapper {
  position: relative; z-index: 10;
  flex-grow: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2.5rem 1rem;
}
.form-card {
  width: 100%;
  max-width: 680px;
  background: rgba(255,255,255,0.02);
  backdrop-filter: blur(14px);
  border: 1px solid var(--bib-gold-b);
  border-radius: 24px;
  padding: 3rem 2.5rem;
  position: relative;
  overflow: hidden;
  opacity: 0;
  animation: fadeUp 0.7s 0.3s ease forwards;
}
.form-card::before {
  content: "";
  position: absolute;
  top: 0; left: 50%;
  transform: translateX(-50%);
  width: 60%; height: 2px;
  background: linear-gradient(90deg, transparent, var(--bib-gold), transparent);
  opacity: 0.5;
}
.card-header-section { text-align: center; margin-bottom: 2.25rem; }
.card-icon {
  width: 64px; height: 64px;
  border-radius: 50%;
  background: var(--bib-gold-g);
  border: 1px solid var(--bib-gold-b);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 1.25rem;
}
.card-icon i { font-size: 1.4rem; color: var(--bib-gold-d); }
.card-header-title {
  font-family: "Cormorant Garamond", serif;
  font-size: 1.75rem; font-weight: 600;
  letter-spacing: 1px; color: var(--bib-text);
  margin-bottom: 0.35rem;
}
.card-header-title .gold { color: var(--bib-gold); }
.card-header-sub { font-size: 0.78rem; color: var(--bib-muted); letter-spacing: 0.5px; }
.field-label {
  font-size: 0.68rem; font-weight: 600;
  letter-spacing: 2px; text-transform: uppercase;
  color: var(--bib-gold-d); margin-bottom: 0.4rem;
  display: block;
}
.bib-form-control {
  width: 100%;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  color: var(--bib-text);
  border-radius: 12px;
  padding: 0.75rem 1rem;
  font-size: 0.88rem;
  font-family: inherit;
  transition: all 0.3s ease;
}
.bib-form-control:focus {
  background: rgba(255,255,255,0.07);
  border-color: rgba(201,160,80,0.35);
  box-shadow: 0 0 20px rgba(201,160,80,0.08);
  outline: 2px solid rgba(201,160,80,0.4);
  outline-offset: 2px;
}
.bib-form-control[readonly] {
  background: rgba(201,160,80,0.04);
  border-color: rgba(201,160,80,0.12);
  color: var(--bib-gold);
  cursor: default; opacity: 0.85;
}
.bib-form-control::placeholder { color: var(--bib-muted); }
input::-webkit-calendar-picker-indicator {
  filter: invert(0.7) sepia(1) saturate(2) hue-rotate(10deg);
  cursor: pointer; opacity: 0.6;
}
.readonly-hint {
  display: inline-flex; align-items: center; gap: 0.35rem;
  font-size: 0.6rem; color: var(--bib-muted);
  letter-spacing: 1px; text-transform: uppercase; margin-top: 0.35rem;
}
.form-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
  margin: 1.5rem 0;
}
.bib-alert-ok   { background:#dcfce7; border-left:4px solid #16a34a; color:#065f46; padding:12px 16px; border-radius:6px; margin-bottom:16px; }
.bib-alert-err  { background:#fef2f2; border-left:4px solid #dc2626; color:#991b1b; padding:12px 16px; border-radius:6px; margin-bottom:16px; }
.btn-submit {
  display: block; width: 100%;
  background: var(--bib-gold); color: #1e1040;
  font-weight: 700; font-size: 0.82rem;
  letter-spacing: 2px; text-transform: uppercase;
  border: none; border-radius: 14px; padding: 1rem; margin-top: 2rem;
  transition: all 0.35s cubic-bezier(0.23,1,0.32,1);
  cursor: pointer; position: relative; overflow: hidden;
}
.btn-submit:hover   { background: #d4ac5a; transform: translateY(-2px); box-shadow: 0 8px 28px rgba(201,160,80,0.25); }
.btn-submit:active  { transform: translateY(0); }
.btn-submit:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(22px); }
  to   { opacity: 1; transform: translateY(0); }
}
@media (max-width: 767.98px) {
  .form-card { padding: 2rem 1.5rem; border-radius: 18px; }
  .card-header-title { font-size: 1.4rem; }
  .card-icon { width: 56px; height: 56px; }
  .form-wrapper { padding: 1.5rem 0.75rem; }
}
@media (max-width: 480px) {
  .form-card { padding: 1.75rem 1.25rem; }
}
</style>';
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main" class="form-wrapper">
  <div class="form-card">

    <div class="card-header-section">
      <div class="card-icon" aria-hidden="true">
        <i class="fas fa-book-open" aria-hidden="true"></i>
      </div>
      <h1 class="card-header-title">Confirmar <span class="gold">Solicitud</span></h1>
      <p class="card-header-sub">Verifica los datos del libro y completa tu información</p>
    </div>

    <?php if ($flash_ok): ?>
      <div class="bib-alert-ok" role="alert"><?= htmlspecialchars($flash_ok, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
      <div class="bib-alert-err" role="alert"><?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form action="procesos/guardar_solicitud_libro.php" method="POST">
      <?= csrfField() ?>

      <div class="mb-3">
        <label for="sol-libro" class="field-label">Libro Seleccionado</label>
        <input type="text" id="sol-libro" class="bib-form-control"
               name="nombre_libro" value="<?= $titulo ?>" readonly>
        <span class="readonly-hint">
          <i class="fas fa-lock" style="font-size:0.55rem;color:rgba(201,160,80,0.3);" aria-hidden="true"></i>
          Seleccionado desde el catálogo
        </span>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <label for="sol-codigo" class="field-label">Código del Libro</label>
          <input type="text" id="sol-codigo" class="bib-form-control"
                 name="codigo_libro" value="<?= $codigo ?>" readonly>
        </div>
        <div class="col-md-6">
          <label for="sol-usuario" class="field-label">Tu Nombre</label>
          <input type="text" id="sol-usuario" class="bib-form-control"
                 name="nombre_usuario" required placeholder="Ingresa tu nombre completo"
                 autocomplete="name">
        </div>
      </div>

      <div class="form-divider" aria-hidden="true"></div>

      <div class="mb-0">
        <label for="sol-fecha" class="field-label">Fecha de Solicitud</label>
        <input type="date" id="sol-fecha" class="bib-form-control"
               name="fecha_solicitud"
               value="<?= date('Y-m-d') ?>"
               min="<?= date('Y-m-d') ?>"
               required>
      </div>

      <button type="submit" class="btn-submit">
        Enviar Petición <i class="fas fa-paper-plane ms-2" aria-hidden="true"></i>
      </button>
    </form>

  </div>
</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
