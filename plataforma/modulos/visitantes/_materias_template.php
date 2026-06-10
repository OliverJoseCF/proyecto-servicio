<?php
/**
 * Template compartido para páginas de carrera.
 * Variables requeridas antes de incluir:
 *   $_carrera_clave  — ej. 'ISC'
 *   $_carrera_nombre — ej. 'Ingeniería en Sistemas Computacionales'
 *   $_color_acento   — color hex del acento de la carrera
 */
require_once __DIR__ . '/../../shared/header.php';
require_once __DIR__ . '/../../shared/config.php';

try {
    $db = getPDO(DB_NAME);

    // Materias del plan de estudios
    $stmt = $db->prepare(
        'SELECT m.nombre, m.orden
         FROM materias m
         JOIN carreras c ON m.carrera_id = c.id
         WHERE c.clave = ? AND m.activo = 1
         ORDER BY m.orden'
    );
    $stmt->execute([$_carrera_clave]);
    $materias = $stmt->fetchAll();

    // URL de la retícula — consulta separada para que un error
    // en esta columna no afecte la carga de materias
    $reticula_url = null;
    try {
        $stmtR = $db->prepare('SELECT reticula_url FROM carreras WHERE clave=? LIMIT 1');
        $stmtR->execute([$_carrera_clave]);
        $reticula_url = $stmtR->fetchColumn() ?: null;
    } catch (\Throwable $eR) {
        // La columna reticula_url puede no existir en BD antiguas — ignorar
    }

} catch (\Throwable $e) {
    $materias     = [];
    $reticula_url = null;
}

// Vista activa: 'plan' o 'reticula' — por GET o por defecto 'plan'
$vista = (isset($_GET['vista']) && $_GET['vista'] === 'reticula') ? 'reticula' : 'plan';
// Si pidieron retícula pero no existe, caer a plan
if ($vista === 'reticula' && !$reticula_url) $vista = 'plan';
?>
<main id="main">

  <div class="tsj-page-header">
    <div class="tsj-page-header-line"></div>
    <h1><?= htmlspecialchars($_carrera_nombre) ?></h1>
    <p class="tsj-page-header-sub">Consulta el plan de estudios o el mapa curricular de la carrera</p>
  </div>

  <div class="container" style="padding-bottom:56px">

    <!-- ── Selector de vista ── -->
    <div style="display:flex;justify-content:center;gap:12px;margin-bottom:32px;flex-wrap:wrap">
      <a href="?vista=plan"
         style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;border-radius:10px;font-family:var(--tsj-font,'Poppins',sans-serif);font-size:.9rem;font-weight:700;text-decoration:none;transition:all .2s;
                <?= $vista==='plan'
                  ? 'background:'.htmlspecialchars($_color_acento).';color:#fff;box-shadow:0 4px 14px rgba(0,0,0,.15)'
                  : 'background:#fff;color:#4a5170;border:1.5px solid #e8eaf2' ?>">
        <span class="material-symbols-rounded" style="font-size:20px">list_alt</span>
        Plan de Estudios
      </a>

      <?php if ($reticula_url): ?>
      <a href="?vista=reticula"
         style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;border-radius:10px;font-family:var(--tsj-font,'Poppins',sans-serif);font-size:.9rem;font-weight:700;text-decoration:none;transition:all .2s;
                <?= $vista==='reticula'
                  ? 'background:'.htmlspecialchars($_color_acento).';color:#fff;box-shadow:0 4px 14px rgba(0,0,0,.15)'
                  : 'background:#fff;color:#4a5170;border:1.5px solid #e8eaf2' ?>">
        <span class="material-symbols-rounded" style="font-size:20px">account_tree</span>
        Retícula
      </a>
      <?php else: ?>
      <span title="La retícula aún no está disponible para esta carrera"
            style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;border-radius:10px;font-family:var(--tsj-font,'Poppins',sans-serif);font-size:.9rem;font-weight:700;color:#c4c8d8;border:1.5px dashed #e0e4f0;cursor:not-allowed">
        <span class="material-symbols-rounded" style="font-size:20px">account_tree</span>
        Retícula
      </span>
      <?php endif; ?>
    </div>

    <!-- ── Vista: Plan de Estudios ── -->
    <?php if ($vista === 'plan'): ?>
      <?php if (empty($materias)): ?>
        <p style="text-align:center;color:var(--tsj-gray-400,#9ca3af);padding:3rem 0;font-family:var(--tsj-font,'Poppins',sans-serif)">
          Sin materias registradas para esta carrera.
        </p>
      <?php else: ?>
      <div style="max-width:720px;margin:0 auto">
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
            <span style="
                min-width:32px;height:32px;
                background:<?= htmlspecialchars($_color_acento) ?>;
                color:#fff;border-radius:50%;
                display:flex;align-items:center;justify-content:center;
                font-size:12px;font-weight:700;flex-shrink:0;
                font-family:var(--tsj-font,'Poppins',sans-serif)">
              <?= $i + 1 ?>
            </span>
            <span style="
                font-size:13.5px;font-weight:600;
                color:#1a0960;line-height:1.35;
                font-family:var(--tsj-font,'Poppins',sans-serif)">
              <?= htmlspecialchars($m['nombre']) ?>
            </span>
          </div>
          <?php endforeach; ?>
        </div>
        <p style="text-align:center;font-size:12.5px;color:var(--tsj-gray-400,#9ca3af);font-family:var(--tsj-font,'Poppins',sans-serif)">
          <span style="display:inline-block;background:#f0edff;color:<?= htmlspecialchars($_color_acento) ?>;padding:4px 14px;border-radius:99px;font-weight:700;font-size:13px">
            <?= count($materias) ?>
          </span>
          materias en el plan de estudios
        </p>
      </div>
      <?php endif; ?>

    <!-- ── Vista: Retícula ── -->
    <?php elseif ($vista === 'reticula' && $reticula_url): ?>
      <div style="max-width:960px;margin:0 auto">
        <?php
        $ext = strtolower(pathinfo(parse_url($reticula_url, PHP_URL_PATH), PATHINFO_EXTENSION));
        $esPdf = $ext === 'pdf' || strpos($reticula_url, 'drive.google.com') !== false;
        ?>
        <?php if ($esPdf): ?>
          <!-- PDF: embed con fallback a enlace -->
          <div style="border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(20,10,80,.1);border:1px solid #e8eaf2">
            <object data="<?= htmlspecialchars($reticula_url) ?>"
                    type="application/pdf"
                    width="100%" height="720"
                    style="display:block">
              <div style="padding:40px;text-align:center;background:#f8f9ff">
                <span class="material-symbols-rounded" style="font-size:48px;color:#8892a8;display:block;margin-bottom:12px">picture_as_pdf</span>
                <p style="font-family:var(--tsj-font,'Poppins',sans-serif);color:#4a5170;margin-bottom:16px">
                  Tu navegador no puede mostrar el PDF directamente.
                </p>
                <a href="<?= htmlspecialchars($reticula_url) ?>" target="_blank" rel="noopener noreferrer"
                   style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:<?= htmlspecialchars($_color_acento) ?>;color:#fff;border-radius:8px;font-family:var(--tsj-font,'Poppins',sans-serif);font-weight:700;font-size:.88rem;text-decoration:none">
                  <span class="material-symbols-rounded" style="font-size:18px">open_in_new</span>
                  Abrir retícula
                </a>
              </div>
            </object>
          </div>
          <div style="text-align:center;margin-top:14px">
            <a href="<?= htmlspecialchars($reticula_url) ?>" target="_blank" rel="noopener noreferrer"
               style="display:inline-flex;align-items:center;gap:6px;color:<?= htmlspecialchars($_color_acento) ?>;font-family:var(--tsj-font,'Poppins',sans-serif);font-size:.85rem;font-weight:600;text-decoration:none">
              <span class="material-symbols-rounded" style="font-size:17px">open_in_new</span>
              Abrir en pantalla completa
            </a>
          </div>
        <?php else: ?>
          <!-- Imagen -->
          <div style="text-align:center;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(20,10,80,.1);border:1px solid #e8eaf2">
            <img src="<?= htmlspecialchars($reticula_url) ?>"
                 alt="Retícula de <?= htmlspecialchars($_carrera_nombre) ?>"
                 style="width:100%;height:auto;display:block"
                 onerror="this.parentElement.innerHTML='<p style=\'padding:2rem;color:#9ca3af\'>No se pudo cargar la imagen de la retícula.</p>'">
          </div>
          <div style="text-align:center;margin-top:14px">
            <a href="<?= htmlspecialchars($reticula_url) ?>" target="_blank" rel="noopener noreferrer"
               style="display:inline-flex;align-items:center;gap:6px;color:<?= htmlspecialchars($_color_acento) ?>;font-family:var(--tsj-font,'Poppins',sans-serif);font-size:.85rem;font-weight:600;text-decoration:none">
              <span class="material-symbols-rounded" style="font-size:17px">open_in_new</span>
              Ver en tamaño completo
            </a>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- Botón volver -->
    <div style="text-align:center;margin-top:32px">
      <a href="ofertaAcademica.php" style="
          display:inline-flex;align-items:center;gap:8px;
          color:var(--tsj-blue,#33179c);font-size:13px;font-weight:600;
          text-decoration:none;padding:9px 20px;
          border:1.5px solid #e8eaf2;border-radius:8px;background:#fff;
          transition:border-color .2s,box-shadow .2s;
          font-family:var(--tsj-font,'Poppins',sans-serif)"
         onmouseover="this.style.borderColor='#33179c';this.style.boxShadow='0 2px 8px rgba(51,23,156,.1)'"
         onmouseout="this.style.borderColor='#e8eaf2';this.style.boxShadow='none'">
        ← Volver a Oferta Académica
      </a>
    </div>

  </div>
</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
