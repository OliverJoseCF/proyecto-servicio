<?php
/**
 * Template reutilizable para páginas de docentes por carrera.
 * Quien lo incluya debe definir antes:
 *   $_clave_carrera  — clave de la carrera en tabla `carreras` (ej. 'ISC')
 */

require_once __DIR__ . '/../../shared/config.php';

try {
    $db = getPDO(DB_NAME);

    $stmtC = $db->prepare('SELECT id, nombre, color FROM carreras WHERE clave=? AND activo=1 LIMIT 1');
    $stmtC->execute([$_clave_carrera]);
    $_carrera = $stmtC->fetch();

    if ($_carrera) {
        $stmtD = $db->prepare(
            'SELECT d.nombre, d.correo, d.foto
               FROM docentes d
               JOIN docente_carrera dc ON dc.docente_id = d.id
              WHERE dc.carrera_id = ? AND d.activo = 1
              ORDER BY d.orden, d.nombre'
        );
        $stmtD->execute([$_carrera['id']]);
        $_docentes = $stmtD->fetchAll();
    } else {
        $_docentes = [];
    }
    $_db_ok = true;
} catch (\Throwable $e) {
    $_carrera  = null;
    $_docentes = [];
    $_db_ok    = false;
}

$tsj_module    = 'visitantes';
$tsj_title     = 'Docentes' . ($_carrera ? ' — ' . $_carrera['nombre'] : '');
$tsj_extra_css = ['style.css'];

$_color    = htmlspecialchars($_carrera['color'] ?? '#32129a');
$_base_img = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/modulos/visitantes/imagenes/';

require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">

  <div class="tsj-page-header">
    <div class="tsj-page-header-line" style="background:<?= $_color ?>"></div>
    <h1>Docentes <span class="tsj-accent"><?= htmlspecialchars($_carrera['nombre'] ?? $_clave_carrera) ?></span></h1>
    <p class="tsj-page-header-sub">Personal docente activo de esta carrera</p>
  </div>

  <?php if (!$_db_ok): ?>
  <p style="text-align:center;padding:3rem;color:var(--tsj-gray-400)">Error al cargar los docentes.</p>

  <?php elseif (empty($_docentes)): ?>
  <p style="text-align:center;padding:3rem;color:var(--tsj-gray-400)">No hay docentes registrados para esta carrera.</p>

  <?php else: ?>
  <div class="doc-grid">
    <?php foreach ($_docentes as $d):
      $fotoSrc = $d['foto']
        ? $_base_img . htmlspecialchars($d['foto'])
        : null;
      $placeholder = "data:image/svg+xml," . rawurlencode(
          '<svg xmlns="http://www.w3.org/2000/svg" width="160" height="160">'
        . '<rect width="160" height="160" fill="#e5e7eb"/>'
        . '<circle cx="80" cy="60" r="30" fill="#9ca3af"/>'
        . '<path d="M10 160c0-35 31-55 70-55s70 20 70 55z" fill="#9ca3af"/>'
        . '</svg>'
      );
    ?>
    <div class="doc-card">
      <div class="doc-card-banner" style="background:<?= $_color ?>22;border-top:3px solid <?= $_color ?>"></div>
      <img class="doc-card-foto"
           src="<?= $fotoSrc ?? $placeholder ?>"
           alt="<?= htmlspecialchars($d['nombre']) ?>"
           loading="lazy"
           <?= $fotoSrc ? 'onerror="this.src=\'' . $placeholder . '\'"' : '' ?>>
      <div class="doc-card-body">
        <div class="doc-card-nombre"><?= htmlspecialchars($d['nombre']) ?></div>
        <?php if ($d['correo']): ?>
        <a href="mailto:<?= htmlspecialchars($d['correo']) ?>" class="doc-card-correo">
          <span class="material-symbols-rounded" aria-hidden="true" style="font-size:14px">mail</span>
          <?= htmlspecialchars($d['correo']) ?>
        </a>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</main>

<style>
.doc-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 20px;
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 20px 56px;
}
.doc-card {
  background: var(--tsj-white);
  border-radius: var(--tsj-radius-lg);
  box-shadow: 0 2px 10px rgba(20,10,80,.06);
  border: 1px solid var(--tsj-gray-200);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  transition: transform .2s ease, box-shadow .2s ease;
}
.doc-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 28px rgba(26,9,96,.11);
}
.doc-card-banner {
  width: 100%;
  height: 8px;
}
.doc-card-foto {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  object-fit: cover;
  object-position: top;
  margin: 16px 0 10px;
  border: 3px solid var(--tsj-gray-100);
}
.doc-card-body {
  padding: 0 14px 18px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  width: 100%;
}
.doc-card-nombre {
  font-size: .9rem;
  font-weight: 700;
  color: var(--tsj-blue-dark);
  line-height: 1.3;
}
.doc-card-correo {
  display: flex;
  align-items: center;
  gap: 4px;
  justify-content: center;
  font-size: .72rem;
  color: var(--tsj-blue);
  text-decoration: none;
  word-break: break-all;
}
.doc-card-correo:hover { text-decoration: underline; }
@media (max-width: 600px) {
  .doc-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 14px; }
  .doc-card-foto { width: 70px; height: 70px; }
}
</style>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
