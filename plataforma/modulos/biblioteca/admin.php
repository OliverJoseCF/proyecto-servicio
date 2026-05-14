<?php
require_once __DIR__ . '/../../shared/lib/auth.php';
requireAuth('biblioteca', 'login.php');
include 'config/conexion.php';

try {
    $totalLibros = $conexion->query("SELECT COUNT(*) as total FROM libros")->fetch_assoc()['total'];
    $prestados   = $conexion->query("SELECT COUNT(*) as total FROM solicitud_libros WHERE estado='Aceptado' AND entregado=0")->fetch_assoc()['total'];
    $pendientes  = $conexion->query("SELECT COUNT(*) as total FROM solicitud_libros WHERE estado='Pendiente'")->fetch_assoc()['total'];

    $resultLibros    = $conexion->query("SELECT * FROM libros ORDER BY id DESC");
    $resultControles = $conexion->query("SELECT * FROM solicitud_controles ORDER BY id DESC");
    $resultSolLibros = $conexion->query("SELECT * FROM solicitud_libros WHERE entregado = 0 ORDER BY id DESC");
    $resultHistorial = $conexion->query("SELECT * FROM solicitud_libros WHERE entregado = 1 OR estado = 'Rechazado' ORDER BY fecha_devolucion DESC LIMIT 30");
} catch (Exception $e) {
    error_log('admin.php error: ' . $e->getMessage());
    die('Error al cargar los datos. Contacta al administrador.');
}

$tsj_module    = 'biblioteca';
$tsj_title     = 'Biblioteca — Gestión Bibliotecaria';
$tsj_extra_css = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
    'https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css',
    'assets/css/admin.css',
];
$tsj_head_extra = '<meta name="_csrf" content="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main" class="container pb-5">

  <h1 class="sr-only">Panel de Gestión Bibliotecaria</h1>

  <!-- ═══ STAT CARDS ═══ -->
  <div class="row mb-4 g-3" role="region" aria-label="Estadísticas generales">
    <div class="col-sm-6 col-md-4">
      <div class="stat-card card-books">
        <div class="d-flex align-items-center gap-3">
          <div class="stat-icon icon-books" aria-hidden="true"><i class="fas fa-book-open"></i></div>
          <div>
            <p class="stat-label mb-0">Total Libros</p>
            <p class="stat-value mb-0"><?= (int)$totalLibros ?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="stat-card card-loans">
        <div class="d-flex align-items-center gap-3">
          <div class="stat-icon icon-loans" aria-hidden="true"><i class="fas fa-hand-holding-heart"></i></div>
          <div>
            <p class="stat-label mb-0">En Préstamo</p>
            <p class="stat-value mb-0"><?= (int)$prestados ?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="stat-card card-pending">
        <div class="d-flex align-items-center gap-3">
          <div class="stat-icon icon-pending" aria-hidden="true"><i class="fas fa-clock"></i></div>
          <div>
            <p class="stat-label mb-0">Solicitudes</p>
            <p class="stat-value mb-0"><?= (int)$pendientes ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══ TABS ═══ -->
  <div class="tabs-wrapper" id="adminTabs" role="tablist" aria-label="Secciones de administración">
    <button class="tab-btn active"
            data-bs-toggle="tab" data-bs-target="#libros"
            role="tab" aria-selected="true" aria-controls="libros" id="tab-libros">
      <i class="fas fa-list" aria-hidden="true"></i> <span class="tab-label">Inventario</span>
    </button>
    <button class="tab-btn"
            data-bs-toggle="tab" data-bs-target="#controles"
            role="tab" aria-selected="false" aria-controls="controles" id="tab-controles">
      <i class="fas fa-gamepad" aria-hidden="true"></i> <span class="tab-label">Controles</span>
    </button>
    <button class="tab-btn"
            data-bs-toggle="tab" data-bs-target="#sol-libros"
            role="tab" aria-selected="false" aria-controls="sol-libros" id="tab-solicitudes">
      <i class="fas fa-clipboard-check" aria-hidden="true"></i> <span class="tab-label">Solicitudes</span>
    </button>
    <button class="tab-btn"
            data-bs-toggle="tab" data-bs-target="#historial"
            role="tab" aria-selected="false" aria-controls="historial" id="tab-historial">
      <i class="fas fa-history" aria-hidden="true"></i> <span class="tab-label">Historial</span>
    </button>
  </div>

  <!-- ═══ CONTENT ═══ -->
  <div class="tab-content">

    <!-- ── INVENTARIO ── -->
    <div class="tab-pane fade show active" id="libros" role="tabpanel" aria-labelledby="tab-libros">
      <div class="content-panel">
        <div class="section-header">
          <h2 class="section-title"><i class="fas fa-book me-2" style="color:var(--tsj-blue)" aria-hidden="true"></i>Libros en Sistema</h2>
          <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            <i class="fas fa-plus" aria-hidden="true"></i> Nuevo Libro
          </button>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle" id="tablaLibros">
            <thead><tr>
              <th scope="col">Libro</th>
              <th scope="col">Editorial</th>
              <th scope="col">Autor</th>
              <th scope="col">Código</th>
              <th scope="col" class="text-center">Acciones</th>
            </tr></thead>
            <tbody>
              <?php while ($row = $resultLibros->fetch_assoc()): ?>
              <tr>
                <td class="fw-bold"><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['editorial'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['autor'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><span class="badge-code"><?= htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') ?></span></td>
                <td class="text-center">
                  <div class="d-flex gap-2 justify-content-center">
                    <a href="procesos/editar_libro.php?codigo=<?= urlencode($row['codigo']) ?>"
                       class="btn-action btn-edit"
                       aria-label="Editar libro <?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                      <i class="fas fa-edit" aria-hidden="true"></i>
                    </a>
                    <button class="btn-action btn-delete"
                            onclick="eliminarLibro(<?= (int)$row['id'] ?>)"
                            aria-label="Eliminar libro <?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                      <i class="fas fa-trash" aria-hidden="true"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ── CONTROLES ── -->
    <div class="tab-pane fade" id="controles" role="tabpanel" aria-labelledby="tab-controles">
      <div class="content-panel">
        <div class="section-header">
          <h2 class="section-title"><i class="fas fa-gamepad me-2" style="color:var(--tsj-blue)" aria-hidden="true"></i>Controles</h2>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle" id="tablaControles">
            <thead><tr>
              <th scope="col">Fecha</th>
              <th scope="col">Docente</th>
              <th scope="col">Aula</th>
              <th scope="col">Recibo</th>
              <th scope="col">Salida</th>
              <th scope="col">Entrega</th>
            </tr></thead>
            <tbody>
              <?php
              if ($resultControles && $resultControles->num_rows > 0):
                while ($ctrl = $resultControles->fetch_assoc()):
              ?>
              <tr>
                <td><?= htmlspecialchars($ctrl['fecha'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td class="fw-bold"><?= htmlspecialchars($ctrl['nombre_docente'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($ctrl['aula'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($ctrl['recibo'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($ctrl['hora_prestamo'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($ctrl['hora_entrega'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <?php endwhile; else: ?>
              <tr><td colspan="6" class="text-center text-muted py-4">No hay registros de controles.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ── SOLICITUDES ── -->
    <div class="tab-pane fade" id="sol-libros" role="tabpanel" aria-labelledby="tab-solicitudes">
      <div class="content-panel">
        <div class="section-header mb-3">
          <h2 class="section-title"><i class="fas fa-clipboard-check me-2" style="color:var(--tsj-blue)" aria-hidden="true"></i>Solicitudes de Libros</h2>
        </div>
        <label for="searchSolicitudes" class="sr-only">Buscar solicitudes</label>
        <input type="text" id="searchSolicitudes" class="form-control search-input mb-3"
               placeholder="Buscar por alumno o libro..."
               oninput="filterTable('searchSolicitudes', 'tableSolicitudes')">
        <div class="table-responsive">
          <table class="table table-hover align-middle text-center" id="tableSolicitudes">
            <thead><tr>
              <th scope="col">Fecha</th>
              <th scope="col">Alumno</th>
              <th scope="col">Libro</th>
              <th scope="col">Estado</th>
              <th scope="col">Acciones</th>
            </tr></thead>
            <tbody>
              <?php while ($sl = $resultSolLibros->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($sl['fecha_solicitud'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td class="fw-bold"><?= htmlspecialchars($sl['nombre_usuario'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($sl['nombre_libro'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <?php if ($sl['estado'] === 'Aceptado'): ?>
                    <span class="badge-status badge-accepted"><?= htmlspecialchars($sl['estado'], ENT_QUOTES, 'UTF-8') ?></span>
                  <?php elseif ($sl['estado'] === 'Pendiente'): ?>
                    <span class="badge-status badge-pending"><?= htmlspecialchars($sl['estado'], ENT_QUOTES, 'UTF-8') ?></span>
                  <?php else: ?>
                    <span class="badge-status badge-rejected"><?= htmlspecialchars($sl['estado'], ENT_QUOTES, 'UTF-8') ?></span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($sl['estado'] === 'Pendiente'): ?>
                    <button class="btn btn-success btn-sm rounded-pill px-3"
                            onclick="cambiarEstado(<?= (int)$sl['id'] ?>, 'Aceptado')">
                      <i class="fas fa-check me-1" aria-hidden="true"></i>Aceptar
                    </button>
                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3 ms-1"
                            onclick="cambiarEstado(<?= (int)$sl['id'] ?>, 'Rechazado')">
                      <i class="fas fa-times me-1" aria-hidden="true"></i>Rechazar
                    </button>
                  <?php elseif ($sl['estado'] === 'Aceptado'): ?>
                    <button class="btn btn-devolver btn-sm"
                            onclick="marcarDevolucion(<?= (int)$sl['id'] ?>)">
                      <i class="fas fa-undo me-1" aria-hidden="true"></i>Devolver
                    </button>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ── HISTORIAL ── -->
    <div class="tab-pane fade" id="historial" role="tabpanel" aria-labelledby="tab-historial">
      <div class="content-panel">
        <div class="section-header">
          <h2 class="section-title"><i class="fas fa-history me-2" style="color:var(--tsj-blue)" aria-hidden="true"></i>Historial de Préstamos</h2>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle" id="tablaHistorial">
            <thead><tr>
              <th scope="col">Alumno</th>
              <th scope="col">Libro</th>
              <th scope="col">Solicitud</th>
              <th scope="col">Devolución / Cierre</th>
            </tr></thead>
            <tbody>
              <?php while ($h = $resultHistorial->fetch_assoc()): ?>
              <tr>
                <td class="fw-bold"><?= htmlspecialchars($h['nombre_usuario'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($h['nombre_libro'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><small class="text-muted"><?= htmlspecialchars($h['fecha_solicitud'] ?? '', ENT_QUOTES, 'UTF-8') ?></small></td>
                <td>
                  <?php if ($h['fecha_devolucion']): ?>
                    <span class="text-returned"><i class="fas fa-check-circle me-1" aria-hidden="true"></i><?= htmlspecialchars($h['fecha_devolucion'], ENT_QUOTES, 'UTF-8') ?></span>
                  <?php else: ?>
                    <span class="text-rejected"><i class="fas fa-ban me-1" aria-hidden="true"></i>Rechazado</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</main>

<!-- ═══ MODAL AGREGAR LIBRO ═══ -->
<div class="modal fade" id="modalAgregar" tabindex="-1"
     aria-labelledby="modalAgregarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal-header-custom">
        <h2 class="modal-title h5" id="modalAgregarLabel">
          <i class="fas fa-book me-2" aria-hidden="true"></i>Añadir Nuevo Libro
        </h2>
        <button type="button" class="btn-close btn-close-white"
                data-bs-dismiss="modal" aria-label="Cerrar modal"></button>
      </div>
      <form id="formAgregarLibro">
        <?= csrfField() ?>
        <div class="modal-body">
          <div class="mb-3">
            <label for="agr-nombre" class="form-label">Título del Libro</label>
            <input type="text" id="agr-nombre" class="form-control" name="nombre" required>
          </div>
          <div class="mb-3">
            <label for="agr-editorial" class="form-label">Editorial</label>
            <input type="text" id="agr-editorial" class="form-control" name="editorial" required>
          </div>
          <div class="mb-3">
            <label for="agr-clasif" class="form-label">Clasificación</label>
            <input type="text" id="agr-clasif" class="form-control" name="clasificacion" required>
          </div>
          <div class="mb-3">
            <label for="agr-autor" class="form-label">Autor</label>
            <input type="text" id="agr-autor" class="form-control" name="autor" required>
          </div>
          <div class="mb-3">
            <label for="agr-codigo" class="form-label">Código (Ej. LIB-004)</label>
            <input type="text" id="agr-codigo" class="form-control" name="codigo" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light rounded-pill px-3 fw-bold"
                  data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-save">Guardar Libro</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ═══ SCRIPTS ═══ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
(function () {
  'use strict';

  var CSRF = document.querySelector('meta[name="_csrf"]')?.content || '';

  function filterTable(inputId, tableId) {
    var input = document.getElementById(inputId).value.toLowerCase();
    document.querySelectorAll('#' + tableId + ' tbody tr').forEach(function (row) {
      row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
    });
  }
  window.filterTable = filterTable;

  function postAction(url, data) {
    var form = document.createElement('form');
    form.method = 'POST'; form.action = url;
    data['_csrf'] = CSRF;
    Object.entries(data).forEach(function ([k, v]) {
      var inp = document.createElement('input');
      inp.type = 'hidden'; inp.name = k; inp.value = v;
      form.appendChild(inp);
    });
    document.body.appendChild(form);
    form.submit();
  }

  window.cambiarEstado = function (id, n) {
    Swal.fire({
      title: n === 'Aceptado' ? '¿Aceptar solicitud?' : '¿Rechazar solicitud?',
      text:  n === 'Aceptado' ? 'El libro quedará registrado como prestado.' : 'La solicitud será rechazada.',
      icon:  n === 'Aceptado' ? 'question' : 'warning',
      showCancelButton: true,
      confirmButtonColor: n === 'Aceptado' ? '#27ae60' : '#e74c3c',
      cancelButtonColor: '#95a5a6',
      confirmButtonText: n === 'Aceptado' ? 'Sí, aceptar' : 'Sí, rechazar',
      cancelButtonText: 'Cancelar'
    }).then(function (result) {
      if (result.isConfirmed) postAction('procesos/estado_libro.php', { id: id, accion: n });
    });
  };

  window.marcarDevolucion = function (id) {
    Swal.fire({
      title: '¿Confirmas la devolución?',
      text: '¿Confirmas que el libro fue entregado?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#27ae60',
      cancelButtonColor: '#95a5a6',
      confirmButtonText: '<i class="fas fa-check me-1"></i> Sí, entregar',
      cancelButtonText: 'Cancelar'
    }).then(function (result) {
      if (result.isConfirmed) postAction('procesos/marcar_devuelto.php', { id: id });
    });
  };

  window.eliminarLibro = function (id) {
    Swal.fire({
      title: '¿Eliminar este libro?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e74c3c',
      cancelButtonColor: '#95a5a6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(function (result) {
      if (result.isConfirmed) {
        fetch('procesos/eliminar_Libro.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'id=' + encodeURIComponent(id) + '&_csrf=' + encodeURIComponent(CSRF)
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.success) {
            Swal.fire({ title: 'Eliminado', text: 'El libro ha sido eliminado.', icon: 'success', timer: 1500, showConfirmButton: false });
            setTimeout(function () { location.reload(); }, 1200);
          } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'No se pudo eliminar.' });
          }
        })
        .catch(function () {
          Swal.fire({ icon: 'error', title: 'Error de red', text: 'No se pudo conectar con el servidor.' });
        });
      }
    });
  };

  /* Formulario agregar libro */
  document.getElementById('formAgregarLibro').addEventListener('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    fetch('procesos/agregar_Libro.php', { method: 'POST', body: formData })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success) {
          bootstrap.Modal.getInstance(document.getElementById('modalAgregar')).hide();
          Swal.mixin({
            toast: true, position: 'top-end',
            showConfirmButton: false, timer: 1800, timerProgressBar: true
          }).fire({ icon: 'success', title: '¡Libro agregado correctamente!' });
          setTimeout(function () { location.reload(); }, 1200);
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Error al guardar: ' + (data.error || '') });
        }
      })
      .catch(function () {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Hubo un problema al procesar la solicitud.' });
      });
  });

  /* Tabs — sincronizar estado activo */
  document.querySelectorAll('.tab-btn').forEach(function (btn) {
    btn.addEventListener('shown.bs.tab', function () {
      document.querySelectorAll('.tab-btn').forEach(function (b) {
        b.classList.remove('active');
        b.setAttribute('aria-selected', 'false');
      });
      this.classList.add('active');
      this.setAttribute('aria-selected', 'true');
    });
  });

  /* DataTables */
  var dtLang = {
    search: "Buscar:", lengthMenu: "Mostrar _MENU_ registros",
    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
    infoEmpty: "Sin registros disponibles", infoFiltered: "(filtrado de _MAX_ totales)",
    zeroRecords: "No se encontraron resultados", emptyTable: "No hay datos disponibles",
    paginate: { first: "Primero", previous: "Anterior", next: "Siguiente", last: "Último" }
  };

  $(document).ready(function () {
    $('#tablaLibros').DataTable({
      language: dtLang, pageLength: 10,
      lengthMenu: [5, 10, 25, 50], order: [],
      columnDefs: [{ orderable: false, targets: -1 }],
      dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rt<"row align-items-center mt-3"<"col-sm-5"i><"col-sm-7"p>>'
    });

    var historialInit = false;
    document.querySelector('[data-bs-target="#historial"]').addEventListener('shown.bs.tab', function () {
      if (!historialInit) {
        $('#tablaHistorial').DataTable({
          language: dtLang, pageLength: 10,
          lengthMenu: [5, 10, 25, 50], order: [],
          dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rt<"row align-items-center mt-3"<"col-sm-5"i><"col-sm-7"p>>'
        });
        historialInit = true;
      }
    });
  });
})();
</script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
