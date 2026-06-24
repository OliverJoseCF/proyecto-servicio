<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Secretarías';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/config.php';

// Datos desde BD (editables desde el panel admin). Defensivo ante fallo de conexión.
try {
    $db          = getPDO(DB_NAME);
    $secretarias = $db->query(
        'SELECT nombre, rol, correo, telefono FROM secretarias WHERE activo=1 ORDER BY orden, nombre'
    )->fetchAll();
} catch (\Throwable $e) {
    $secretarias = [];
}

require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">
  <h1 class="vis-page-title">Secretarías</h1>

  <div class="seccion" role="note" style="background:#fffbeb;border-left:4px solid #f59e0b;border-top:none;text-align:left;max-width:1000px;">
    <p style="color:#92400e;margin:0;font-size:0.92rem;">
      <strong>Aviso:</strong> La información mostrada a continuación es de referencia y puede actualizarse. Para datos verificados, contacta directamente al Departamento Académico.
    </p>
  </div>

  <?php if (!empty($secretarias)): ?>
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th scope="col">Nombre</th>
          <th scope="col">Rol</th>
          <th scope="col">Correo</th>
          <th scope="col">Teléfono</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($secretarias as $s):
          $tel = preg_replace('/\D/', '', (string)($s['telefono'] ?? '')); ?>
        <tr>
          <td class="nombre"><?= htmlspecialchars($s['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($s['rol'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td class="correo">
            <?php if (!empty($s['correo'])): ?>
              <a href="mailto:<?= htmlspecialchars($s['correo'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($s['correo'], ENT_QUOTES, 'UTF-8') ?></a>
            <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($s['telefono'])): ?>
              <a href="tel:<?= htmlspecialchars($tel, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($s['telefono'], ENT_QUOTES, 'UTF-8') ?></a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="seccion" role="status" style="max-width:1000px;">
    <p style="margin:0;">No hay secretarías registradas por el momento. Contacta al Departamento Académico para más información.</p>
  </div>
  <?php endif; ?>

  <a href="index.php" class="top-right" aria-label="Volver al menú principal">
    <img src="imagenes/casa.png" alt="">
  </a>
</main>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
