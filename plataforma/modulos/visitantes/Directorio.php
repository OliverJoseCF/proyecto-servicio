<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Directorio Institucional';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
require_once __DIR__ . '/../../shared/config.php';

try {
    $db        = getPDO(DB_NAME);
    $stmt      = $db->query('SELECT * FROM directorio WHERE activo=1 ORDER BY orden, nombre');
    $personas  = $stmt->fetchAll();
    $db_ok     = true;
} catch (\Throwable $e) {
    $personas = [];
    $db_ok    = false;
}

$base_img = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/modulos/visitantes/imagenes/';
?>

<main id="main">
  <a href="index.php" class="top-right" aria-label="Volver al menú principal">
    <img src="imagenes/casa.png" alt="">
  </a>

  <h1 class="vis-page-title">Directorio Institucional</h1>

  <div class="container">
    <div class="tabla-scroll">
      <table>
        <thead>
          <tr>
            <th scope="col">Foto</th>
            <th scope="col">Nombre</th>
            <th scope="col">Departamento / Puesto</th>
            <th scope="col">Extensión</th>
            <th scope="col">Ubicación</th>
            <th scope="col">Correo</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($personas)): ?>
          <tr>
            <td colspan="6" style="text-align:center;padding:2rem;color:#9ca3af;">
              <?= $db_ok ? 'No hay personas registradas en el directorio.' : 'Error al cargar el directorio.' ?>
            </td>
          </tr>
          <?php endif; ?>
          <?php foreach ($personas as $p):
            $foto_src = $p['foto']
              ? $base_img . htmlspecialchars($p['foto'])
              : null;
          ?>
          <tr>
            <td>
              <?php if ($foto_src): ?>
                <img src="<?= $foto_src ?>" alt="<?= htmlspecialchars($p['nombre']) ?>"
                     class="foto-tabla"
                     onerror="this.onerror=null;this.src='<?= htmlspecialchars($base_img) ?>placeholder.svg'">
              <?php else: ?>
                <img src="data:image/svg+xml,<?= rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"><rect width="48" height="48" fill="#e5e7eb"/><circle cx="24" cy="19" r="8" fill="#9ca3af"/><path d="M8 44c0-9 7-14 16-14s16 5 16 14z" fill="#9ca3af"/></svg>') ?>"
                     alt="Sin foto" class="foto-tabla">
              <?php endif; ?>
            </td>
            <td style="font-weight:600"><?= htmlspecialchars($p['nombre']) ?></td>
            <td><?= htmlspecialchars($p['puesto'] ?? '—') ?></td>
            <td><?= htmlspecialchars($p['extension'] ?? 'S/N') ?></td>
            <td style="font-size:0.85em;color:#555"><?= htmlspecialchars($p['ubicacion_fisica'] ?? '—') ?></td>
            <td>
              <?php if ($p['correo']): ?>
                <a href="mailto:<?= htmlspecialchars($p['correo']) ?>" style="color:#32129a">
                  <?= htmlspecialchars($p['correo']) ?>
                </a>
              <?php else: ?>
                <span style="color:#9ca3af">—</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
