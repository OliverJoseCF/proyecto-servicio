<?php
require_once __DIR__ . '/../src/session.php';
require_once __DIR__ . '/../src/security_headers.php';

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: ../index.php');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../src/pages/conexion.php';

// Columnas explícitas: evita exponer datos futuros y reduce payload
$sql = 'SELECT id, nombre, convenio, logo, contacto, telefono, correo, vencimiento, carrera
        FROM convenios
        ORDER BY id DESC';

$resultado = $conn->query($sql);
$conn->close();

// Mensaje flash por URL — solo valores conocidos para evitar XSS
$mensajesValidos = ['agregado' => 'El convenio ha sido agregado correctamente.',
                    'editado'  => 'El convenio ha sido actualizado correctamente.',
                    'eliminado'=> 'El convenio ha sido eliminado correctamente.'];
$msgKey   = $_GET['mensaje'] ?? '';
$msgFlash = $mensajesValidos[$msgKey] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Lista de Convenios — TecSJ</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="estilo/estilo.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
  <style>
    .data-row td:not(.actions-cell) { cursor: pointer; }
    .actions-cell { white-space: nowrap; text-align: center; }
    .btn-agregar {
      background-color: #32129A; color: white;
      padding: 8px 16px; border-radius: 4px;
      text-decoration: none; font-weight: bold;
      display: inline-block; margin-left: 10px;
    }
    .btn-editar, .btn-eliminar-btn {
      display: inline-block; margin: 0 5px;
      font-size: 18px; text-decoration: none;
      background: none; border: none; cursor: pointer;
      padding: 0;
    }
    .btn-editar:hover, .btn-eliminar-btn:hover { transform: scale(1.2); }
    .alerta { padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align: center; }
    .alerta-exito { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .btn-cerrar-sesion { background-color: #d9534f; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; }
  </style>
</head>
<body>
  <div class="barra-rosa"></div>

  <header class="barra-purpura">
    <div class="logo-institucion">
      <img src="images/Grupo_10491.svg" alt="Tecnológico Superior de Jalisco" />
    </div>
    <nav class="menu-principal" aria-label="Menú principal">
      <a href="../index.php" style="color:white; text-decoration:none;">Inicio</a>
      <form method="POST" action="cerrar_sesion.php" style="display:inline;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" class="btn-cerrar-sesion">Cerrar Sesión</button>
      </form>
    </nav>
  </header>

  <?php if ($msgFlash !== null): ?>
  <div class="alerta alerta-exito" role="alert" id="flashMsg">
    <?= htmlspecialchars($msgFlash, ENT_QUOTES, 'UTF-8') ?>
  </div>
  <?php endif; ?>

  <div class="fila-busqueda">
    <h2>Lista de convenios</h2>
    <div class="botones-container">
      <a href="../index.php" class="btn-volver-link" aria-label="Volver al inicio">
        <img src="img/icono-regresar.png" alt="" aria-hidden="true" class="btn-volver" />
      </a>
      <a href="agregar_convenio.php" class="btn-agregar">Agregar Convenio</a>
    </div>
  </div>

  <div class="datatable-container">
    <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>Id</th>
          <th>Nombre</th>
          <th>Convenio</th>
          <th>Carrera</th>
          <th>Marca</th>
          <th>Contacto</th>
          <th>Teléfono</th>
          <th>Correo</th>
          <th>Vencimiento</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($resultado): while ($fila = $resultado->fetch_assoc()): ?>
          <?php
            $logoFile  = !empty($fila['logo']) ? basename($fila['logo']) : '';
            $id        = (int) $fila['id'];
            $fechaVenc = ($t = strtotime($fila['vencimiento'])) ? date('d/m/Y', $t) : '—';
          ?>
          <tr class="data-row" data-href="../vista_empresa/index.php?id=<?= $id ?>">
            <td><?= $id ?></td>
            <td><?= htmlspecialchars($fila['nombre'],   ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['convenio'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['carrera'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td>
              <?php if ($logoFile): ?>
                <img src="../src/pages/upload/<?= htmlspecialchars($logoFile, ENT_QUOTES, 'UTF-8') ?>"
                     width="50"
                     alt="Logo de <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>" />
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($fila['contacto'],  ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['telefono'],  ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['correo'],    ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fechaVenc, ENT_QUOTES, 'UTF-8') ?></td>
            <td class="actions-cell">
              <a href="editar_convenio.php?id=<?= $id ?>" class="btn-editar" title="Editar convenio de <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>">✏️</a>
              <a href="eliminar_convenio.php?id=<?= $id ?>" class="btn-eliminar-btn" title="Eliminar convenio de <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>">🗑️</a>
            </td>
          </tr>
        <?php endwhile; endif; ?>
      </tbody>
    </table>
  </div>

  <footer class="footer w-full p-10" role="contentinfo">
    <div class="footer-container flex justify-between items-center">
      <div class="footer-img">
        <img src="images/Grupo_10491.svg" alt="Tecnológico Superior de Jalisco" loading="lazy" />
      </div>
      <div class="footer-links flex gap-4">
        <a href="https://www.facebook.com/TecSJ" class="footer-img" target="_blank" rel="noopener noreferrer">
          <img src="images/facebook-svgrepo-com.svg" alt="Visitar Facebook del TecSJ" loading="lazy" />
        </a>
        <a href="https://www.youtube.com/@TecSJ" class="footer-img" target="_blank" rel="noopener noreferrer">
          <img src="images/youtube-svgrepo-com.svg" alt="Visitar YouTube del TecSJ" loading="lazy" />
        </a>
      </div>
      <div class="footer-text-links flex flex-col items-center gap-2">
        <a href="../index.php" class="footer-link">Módulo de convenios</a>
        <a href="https://consultapublicamx.plataformadetransparencia.org.mx/vut-web/faces/view/consultaPublica.xhtml?idEntidad=MTQ=&idSujetoObligado=MTM3OTE=#inicio" class="footer-link" target="_blank" rel="noopener noreferrer">Plataforma Nacional de Transparencia</a>
      </div>
    </div>
  </footer>

  <div class="extra-info w-full p-6 bg-gray-800 text-white text-center">
    <div class="additional-images">
      <img src="images/educacion.png" alt="Secretaría de Educación Pública" loading="lazy" />
      <img src="images/tecnologico.svg" alt="Tecnológico Nacional de México" loading="lazy" />
      <img src="images/innovacion.png" alt="Secretaría de Innovación, Ciencia y Tecnología de Jalisco" loading="lazy" />
      <img src="images/jalisco.png" alt="Gobierno de Jalisco" loading="lazy" />
    </div>
  </div>

  <!-- SRI: genera los hashes en https://www.srihash.org/ con las URLs exactas -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
          integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
          crossorigin="anonymous" defer></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js" defer></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.jquery.min.js" defer></script>
  <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>
  <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js" defer></script>
  <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js" defer></script>
  <script src="script.js" defer></script>

  <script>
  // Autoocultar mensaje flash tras 4 segundos
  document.addEventListener('DOMContentLoaded', function () {
    var flash = document.getElementById('flashMsg');
    if (flash) {
      setTimeout(function () {
        flash.style.transition = 'opacity 0.5s';
        flash.style.opacity = '0';
        setTimeout(function () { flash.remove(); }, 500);
      }, 4000);
    }
  });
  </script>
</body>
</html>
