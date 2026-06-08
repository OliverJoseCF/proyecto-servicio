<?php
require_once __DIR__ . '/../../shared/lib/auth.php';

$titulo = htmlspecialchars(isset($_GET['titulo']) ? $_GET['titulo'] : '', ENT_QUOTES, 'UTF-8');
$codigo = htmlspecialchars(isset($_GET['codigo']) ? $_GET['codigo'] : '', ENT_QUOTES, 'UTF-8');

$flash_ok    = $_SESSION['flash_ok']    ?? null; unset($_SESSION['flash_ok']);
$flash_error = $_SESSION['flash_error'] ?? null; unset($_SESSION['flash_error']);

$tsj_module     = 'biblioteca';
$tsj_title      = 'Biblioteca — Solicitud de Libro';
$tsj_extra_css  = [];
$tsj_head_extra = '<style>
@media (max-width: 540px) {
  .sol-grid { grid-template-columns: 1fr !important; }
}
</style>';
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">
  <div class="tsj-section tsj-section--narrow" style="padding-top:48px;padding-bottom:64px">

    <div style="text-align:center;margin-bottom:32px">
      <div style="display:inline-flex;align-items:center;justify-content:center;width:64px;height:64px;background:var(--tsj-blue-50);border-radius:50%;margin-bottom:16px">
        <span class="material-symbols-rounded" style="font-size:32px;color:var(--tsj-blue)" aria-hidden="true">menu_book</span>
      </div>
      <h1 style="font-family:var(--tsj-font);font-size:clamp(1.5rem,3vw,2rem);font-weight:700;color:var(--tsj-blue);margin:0 0 8px">
        Confirmar <span style="color:var(--tsj-pink)">Solicitud</span>
      </h1>
      <p style="font-size:0.95rem;color:var(--tsj-gray-500);margin:0">
        Verifica los datos del libro y completa tu información
      </p>
    </div>

    <?php if ($flash_ok): ?>
      <div class="tsj-alert tsj-alert--success" role="alert" style="margin-bottom:20px">
        <span class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;margin-right:6px" aria-hidden="true">check_circle</span>
        <?= htmlspecialchars($flash_ok, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
      <div class="tsj-alert tsj-alert--error" role="alert" style="margin-bottom:20px">
        <span class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;margin-right:6px" aria-hidden="true">error</span>
        <?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <div class="tsj-card">
      <form action="procesos/guardar_solicitud_libro.php" method="POST">
        <?= csrfField() ?>

        <div class="tsj-form-group">
          <label for="sol-libro" class="tsj-label">Libro seleccionado</label>
          <input type="text" id="sol-libro" class="tsj-input"
                 name="nombre_libro" value="<?= $titulo ?>" readonly
                 style="background:var(--tsj-blue-50);color:var(--tsj-blue);cursor:default">
          <span class="tsj-form-help" style="display:flex;align-items:center;gap:4px">
            <span class="material-symbols-rounded" style="font-size:14px;color:var(--tsj-gray-400)" aria-hidden="true">lock</span>
            Seleccionado desde el catálogo
          </span>
        </div>

        <div class="sol-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px">
          <div class="tsj-form-group" style="margin-bottom:0">
            <label for="sol-codigo" class="tsj-label">Código del libro</label>
            <input type="text" id="sol-codigo" class="tsj-input"
                   name="codigo_libro" value="<?= $codigo ?>" readonly
                   style="background:var(--tsj-blue-50);color:var(--tsj-blue);cursor:default">
          </div>
          <div class="tsj-form-group" style="margin-bottom:0">
            <label for="sol-usuario" class="tsj-label">Tu nombre completo</label>
            <input type="text" id="sol-usuario" class="tsj-input"
                   name="nombre_usuario" required
                   placeholder="Ingresa tu nombre completo"
                   autocomplete="name">
          </div>
        </div>

        <div class="tsj-form-group">
          <label for="sol-tipo" class="tsj-label">Tipo de solicitud</label>
          <select id="sol-tipo" name="tipo" class="tsj-input">
            <option value="prestamo">Préstamo (para llevar a casa)</option>
            <option value="consulta_sala">Consulta en sala</option>
          </select>
        </div>

        <button type="submit" class="tsj-btn tsj-btn--primary tsj-btn--block tsj-btn--lg" style="margin-top:8px">
          <span class="material-symbols-rounded" aria-hidden="true">send</span>
          Enviar Petición
        </button>
      </form>
    </div>

    <div style="text-align:center;margin-top:20px">
      <a href="buscar.php" class="tsj-btn tsj-btn--ghost tsj-btn--sm">
        <span class="material-symbols-rounded" aria-hidden="true">arrow_back</span>
        Volver al catálogo
      </a>
    </div>

  </div>
</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
