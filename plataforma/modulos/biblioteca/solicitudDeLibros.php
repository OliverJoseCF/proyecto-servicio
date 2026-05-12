<?php
$titulo = isset($_GET['titulo']) ? $_GET['titulo'] : '';
$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : '';

$tsj_module     = 'biblioteca';
$tsj_title      = 'Biblioteca — Solicitud de Libro';
$tsj_extra_css  = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap',
];
$tsj_head_extra = '<style>
    :root {
      --bg-deep: #1e1040;
      --bg-darker: #150b30;
      --gold: #c9a050;
      --gold-dim: rgba(201, 160, 80, 0.5);
      --gold-glow: rgba(201, 160, 80, 0.08);
      --gold-border: rgba(201, 160, 80, 0.15);
      --text-primary: #f0ece4;
      --text-muted: rgba(240, 236, 228, 0.45);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: var(--bg-deep);
      font-family: 'Outfit', sans-serif;
      color: var(--text-primary);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
    }

    /* ── Ambient ── */
    body::before {
      content: '';
      position: fixed;
      top: -20%; left: -15%;
      width: 55%; height: 55%;
      background: radial-gradient(ellipse, rgba(80, 55, 160, 0.12) 0%, transparent 70%);
      pointer-events: none;
    }
    body::after {
      content: '';
      position: fixed;
      bottom: -25%; right: -15%;
      width: 50%; height: 50%;
      background: radial-gradient(ellipse, rgba(201, 160, 80, 0.05) 0%, transparent 70%);
      pointer-events: none;
    }

    .grain {
      position: fixed; inset: 0; z-index: 0; pointer-events: none; opacity: 0.02;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    }

    /* ── Navbar ── */
    .navbar-custom {
      position: relative; z-index: 10;
      background: rgba(21, 11, 48, 0.7);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid rgba(201, 160, 80, 0.08);
      padding: 0.85rem 0;
    }
    .navbar-custom .navbar-brand {
      font-family: 'Outfit', sans-serif;
      font-weight: 500;
      font-size: 0.82rem;
      letter-spacing: 1px;
      color: var(--text-primary) !important;
      text-decoration: none;
      transition: color 0.3s ease;
    }
    .navbar-custom .navbar-brand i {
      color: var(--gold-dim);
      margin-right: 0.5rem;
      transition: transform 0.3s ease;
    }
    .navbar-custom .navbar-brand:hover i { transform: translateX(-3px); }

    /* ── Form card ── */
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
      background: rgba(255, 255, 255, 0.02);
      backdrop-filter: blur(14px);
      border: 1px solid var(--gold-border);
      border-radius: 24px;
      padding: 3rem 2.5rem;
      position: relative;
      overflow: hidden;
      opacity: 0;
      animation: fadeUp 0.7s 0.3s ease forwards;
    }

    /* Top accent */
    .form-card::before {
      content: '';
      position: absolute;
      top: 0; left: 50%;
      transform: translateX(-50%);
      width: 60%;
      height: 2px;
      background: linear-gradient(90deg, transparent, var(--gold), transparent);
      opacity: 0.5;
    }

    /* ── Card header ── */
    .card-header-section {
      text-align: center;
      margin-bottom: 2.25rem;
    }
    .card-icon {
      width: 64px; height: 64px;
      border-radius: 50%;
      background: var(--gold-glow);
      border: 1px solid var(--gold-border);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.25rem;
    }
    .card-icon i {
      font-size: 1.4rem;
      color: var(--gold-dim);
    }
    .card-header-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.75rem;
      font-weight: 600;
      letter-spacing: 1px;
      color: var(--text-primary);
      margin-bottom: 0.35rem;
    }
    .card-header-title .gold { color: var(--gold); }
    .card-header-sub {
      font-size: 0.78rem;
      color: var(--text-muted);
      letter-spacing: 0.5px;
    }

    /* ── Form fields ── */
    .field-label {
      font-size: 0.68rem;
      font-weight: 600;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--gold-dim);
      margin-bottom: 0.4rem;
    }

    .form-control {
      background: rgba(255, 255, 255, 0.04) !important;
      border: 1px solid rgba(255, 255, 255, 0.08) !important;
      color: var(--text-primary) !important;
      border-radius: 12px;
      padding: 0.75rem 1rem;
      font-size: 0.88rem;
      font-family: 'Outfit', sans-serif;
      transition: all 0.3s ease;
    }
    .form-control::placeholder { color: var(--text-muted); }
    .form-control:focus {
      background: rgba(255, 255, 255, 0.07) !important;
      border-color: rgba(201, 160, 80, 0.35) !important;
      box-shadow: 0 0 20px rgba(201, 160, 80, 0.08);
      outline: none;
    }
    .form-control[readonly] {
      background: rgba(201, 160, 80, 0.04) !important;
      border-color: rgba(201, 160, 80, 0.12) !important;
      color: var(--gold) !important;
      cursor: default;
      opacity: 0.85;
    }

    /* Fix date picker icon */
    input::-webkit-calendar-picker-indicator {
      filter: invert(0.7) sepia(1) saturate(2) hue-rotate(10deg);
      cursor: pointer;
      opacity: 0.6;
    }
    input::-webkit-calendar-picker-indicator:hover { opacity: 1; }

    /* ── Readonly info badge ── */
    .readonly-hint {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      font-size: 0.6rem;
      color: var(--text-muted);
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-top: 0.35rem;
    }
    .readonly-hint i { font-size: 0.55rem; color: rgba(201, 160, 80, 0.3); }

    /* ── Divider ── */
    .form-divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
      margin: 1.5rem 0;
    }

    /* ── Submit button ── */
    .btn-submit {
      display: block;
      width: 100%;
      background: var(--gold);
      color: var(--bg-deep);
      font-weight: 700;
      font-size: 0.82rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      border: none;
      border-radius: 14px;
      padding: 1rem;
      margin-top: 2rem;
      transition: all 0.35s cubic-bezier(0.23, 1, 0.32, 1);
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }
    .btn-submit::before {
      content: '';
      position: absolute;
      top: 0; left: -100%;
      width: 100%; height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
      transition: left 0.6s ease;
    }
    .btn-submit:hover::before { left: 100%; }
    .btn-submit:hover {
      background: #d4ac5a;
      transform: translateY(-2px);
      box-shadow: 0 8px 28px rgba(201, 160, 80, 0.25);
    }
    .btn-submit:active { transform: translateY(0); }

    /* ── Animations ── */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(22px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Responsive ── */
    @media (max-width: 767.98px) {
      .form-card {
        padding: 2rem 1.5rem;
        border-radius: 18px;
      }
      .card-header-title { font-size: 1.4rem; }
      .card-icon { width: 56px; height: 56px; }
      .card-icon i { font-size: 1.2rem; }
      .form-wrapper { padding: 1.5rem 0.75rem; }
    }
    @media (max-width: 400px) {
      .form-card { padding: 1.75rem 1.25rem; }
    }
  </style>';
require_once __DIR__ . '/../../shared/header.php';
?>

  <div class="grain"></div>

  <!-- ── Form ── -->
  <div class="form-wrapper">
    <div class="form-card">

      <div class="card-header-section">
        <div class="card-icon">
          <i class="fas fa-book-open"></i>
        </div>
        <h2 class="card-header-title">Confirmar <span class="gold">Solicitud</span></h2>
        <p class="card-header-sub">Verifica los datos del libro y completa tu información</p>
      </div>

      <form action="procesos/guardar_solicitud_libro.php" method="POST">

        <!-- Libro seleccionado (readonly) -->
        <div class="mb-3">
          <label class="field-label">Libro Seleccionado</label>
          <input type="text" class="form-control" name="nombre_libro" value="<?php echo htmlspecialchars($titulo); ?>" readonly>
          <span class="readonly-hint"><i class="fas fa-lock"></i> Seleccionado desde el catálogo</span>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="field-label">Código del Libro</label>
            <input type="text" class="form-control" name="codigo_libro" value="<?php echo htmlspecialchars($codigo); ?>" readonly>
          </div>
          <div class="col-md-6">
            <label class="field-label">Tu Nombre de Usuario</label>
            <input type="text" class="form-control" name="nombre_usuario" required placeholder="Ingresa tu nombre">
          </div>
        </div>

        <div class="form-divider"></div>

        <div class="mb-0">
          <label class="field-label">Fecha de Solicitud</label>
          <input type="date" class="form-control" name="fecha_solicitud" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <button type="submit" class="btn-submit">
          Enviar Petición <i class="fas fa-paper-plane ms-2"></i>
        </button>
      </form>

    </div>
  </div>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>