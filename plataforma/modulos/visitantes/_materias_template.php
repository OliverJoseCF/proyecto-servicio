<?php
/**
 * Template compartido para páginas de materias por carrera.
 * Variables requeridas antes de incluir:
 *   $_carrera_clave  — ej. 'ISC'
 *   $_carrera_nombre — ej. 'Ingeniería en Sistemas Computacionales'
 *   $_color_acento   — color hex del acento de la carrera
 */
require_once __DIR__ . '/../../shared/header.php';
require_once __DIR__ . '/../../shared/config.php';

try {
    $db   = getPDO(DB_NAME);
    $stmt = $db->prepare(
        'SELECT m.nombre, m.orden
         FROM materias m
         JOIN carreras c ON m.carrera_id = c.id
         WHERE c.clave = ? AND m.activo = 1
         ORDER BY m.orden'
    );
    $stmt->execute([$_carrera_clave]);
    $materias = $stmt->fetchAll();
} catch (\Throwable $e) {
    $materias = [];
}
?>
<main id="main">

  <div class="tsj-page-header">
    <div class="tsj-page-header-line"></div>
    <h1>Plan de <span class="tsj-accent">Estudios</span></h1>
    <p class="tsj-page-header-sub"><?= htmlspecialchars($_carrera_nombre) ?></p>
  </div>

  <div class="container" style="padding-bottom:56px">

    <?php if (empty($materias)): ?>
      <p style="text-align:center;color:var(--tsj-gray-400,#9ca3af);padding:3rem 0">
        Sin materias registradas para esta carrera.
      </p>
    <?php else: ?>

    <div style="max-width:720px;margin:0 auto">

      <!-- Grid de cards de materias -->
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:12px;margin-bottom:24px">
        <?php foreach ($materias as $i => $m): ?>
        <div style="
            display:flex;align-items:center;gap:14px;
            background:#fff;
            border:1.5px solid #e8eaf2;
            border-left:4px solid <?= htmlspecialchars($_color_acento) ?>;
            border-radius:10px;
            padding:14px 18px;
            box-shadow:0 2px 8px rgba(51,23,156,.05);
            transition:transform .18s,box-shadow .18s;
            cursor:default"
             onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 18px rgba(51,23,156,.12)'"
             onmouseout="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(51,23,156,.05)'">
          <!-- Número -->
          <span style="
              min-width:32px;height:32px;
              background:<?= htmlspecialchars($_color_acento) ?>;
              color:#fff;border-radius:50%;
              display:flex;align-items:center;justify-content:center;
              font-size:12px;font-weight:700;flex-shrink:0;
              font-family:var(--tsj-font,'Poppins',sans-serif)">
            <?= $i + 1 ?>
          </span>
          <!-- Nombre -->
          <span style="
              font-size:13.5px;font-weight:600;
              color:#1a0960;line-height:1.35;
              font-family:var(--tsj-font,'Poppins',sans-serif)">
            <?= htmlspecialchars($m['nombre']) ?>
          </span>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Contador -->
      <p style="
          text-align:center;font-size:12.5px;
          color:var(--tsj-gray-400,#9ca3af);
          font-family:var(--tsj-font,'Poppins',sans-serif)">
        <span style="
            display:inline-block;
            background:#f0edff;color:<?= htmlspecialchars($_color_acento) ?>;
            padding:4px 14px;border-radius:99px;
            font-weight:700;font-size:13px">
          <?= count($materias) ?>
        </span>
        materias en el plan de estudios
      </p>

    </div>
    <?php endif; ?>

    <!-- Botón volver -->
    <div style="text-align:center;margin-top:28px">
      <a href="index.php" style="
          display:inline-flex;align-items:center;gap:8px;
          color:var(--tsj-blue,#33179c);font-size:13px;font-weight:600;
          text-decoration:none;padding:9px 20px;
          border:1.5px solid #e8eaf2;border-radius:8px;background:#fff;
          transition:border-color .2s,box-shadow .2s;
          font-family:var(--tsj-font,'Poppins',sans-serif)"
         onmouseover="this.style.borderColor='#33179c';this.style.boxShadow='0 2px 8px rgba(51,23,156,.1)'"
         onmouseout="this.style.borderColor='#e8eaf2';this.style.boxShadow='none'">
        ← Volver al portal institucional
      </a>
    </div>

  </div>
</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
