<?php
session_start();
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: login.php");
    exit;
}
include 'config/conexion.php';

// Consultas para las tarjetas
$totalLibros = $conexion->query("SELECT COUNT(*) as total FROM libros")->fetch_assoc()['total'];
$prestados = $conexion->query("SELECT COUNT(*) as total FROM solicitud_libros WHERE estado='Aceptado' AND entregado=0")->fetch_assoc()['total'];
$pendientes = $conexion->query("SELECT COUNT(*) as total FROM solicitud_libros WHERE estado='Pendiente'")->fetch_assoc()['total'];

// Consultas para Tablas
$resultLibros = $conexion->query("SELECT * FROM libros ORDER BY id DESC");
$resultControles = $conexion->query("SELECT * FROM solicitud_controles ORDER BY id DESC");
$resultSolLibros = $conexion->query("SELECT * FROM solicitud_libros WHERE entregado = 0 ORDER BY id DESC");
$resultHistorial = $conexion->query("SELECT * FROM solicitud_libros WHERE entregado = 1 OR estado = 'Rechazado' ORDER BY fecha_devolucion DESC LIMIT 30");

$tsj_module    = 'biblioteca';
$tsj_title     = 'Biblioteca — Gestión Bibliotecaria';
$tsj_extra_css = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
    'assets/css/admin.css',
];
require_once __DIR__ . '/../../shared/header.php';
?>
<!-- estilos en assets/css/admin.css -->

<div class="container pb-5">

  <!-- ═══ STAT CARDS ═══ -->
  <div class="row mb-4 g-3">
    <div class="col-sm-6 col-md-4">
      <div class="stat-card card-books">
        <div class="d-flex align-items-center gap-3">
          <div class="stat-icon icon-books"><i class="fas fa-book-open"></i></div>
          <div>
            <p class="stat-label mb-0">Total Libros</p>
            <p class="stat-value mb-0"><?= $totalLibros ?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="stat-card card-loans">
        <div class="d-flex align-items-center gap-3">
          <div class="stat-icon icon-loans"><i class="fas fa-hand-holding-heart"></i></div>
          <div>
            <p class="stat-label mb-0">En Préstamo</p>
            <p class="stat-value mb-0"><?= $prestados ?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="stat-card card-pending">
        <div class="d-flex align-items-center gap-3">
          <div class="stat-icon icon-pending"><i class="fas fa-clock"></i></div>
          <div>
            <p class="stat-label mb-0">Solicitudes</p>
            <p class="stat-value mb-0"><?= $pendientes ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══ TABS ═══ -->
  <div class="tabs-wrapper" id="adminTabs" role="tablist">
    <button class="tab-btn active" data-bs-toggle="tab" data-bs-target="#libros" role="tab"><i class="fas fa-list"></i> <span class="tab-label">Inventario</span></button>
    <button class="tab-btn" data-bs-toggle="tab" data-bs-target="#controles" role="tab"><i class="fas fa-gamepad"></i> <span class="tab-label">Controles</span></button>
    <button class="tab-btn" data-bs-toggle="tab" data-bs-target="#sol-libros" role="tab"><i class="fas fa-clipboard-check"></i> <span class="tab-label">Solicitudes</span></button>
    <button class="tab-btn" data-bs-toggle="tab" data-bs-target="#historial" role="tab"><i class="fas fa-history"></i> <span class="tab-label">Historial</span></button>
  </div>

  <!-- ═══ CONTENT ═══ -->
  <div class="tab-content">

      <!-- ── INVENTARIO ── -->
      <div class="tab-pane fade show active" id="libros">
      <div class="content-panel">
        <div class="section-header">
          <h4 class="section-title"><i class="fas fa-book me-2" style="color:var(--purple)"></i>Libros en Sistema</h4>
          <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalAgregar"><i class="fas fa-plus"></i> Nuevo Libro</button>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle" id="tablaLibros">
            <thead><tr><th>Libro</th><th>Editorial</th><th>Autor</th><th>Código</th><th class="text-center">Acciones</th></tr></thead>
            <tbody>
              <?php while ($row = $resultLibros->fetch_assoc()): ?>
              <tr>
                <td class="fw-bold"><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['editorial']) ?></td>
                <td><?= htmlspecialchars($row['autor']) ?></td>
                <td><span class="badge-code"><?= htmlspecialchars($row['codigo']) ?></span></td>
                <td class="text-center">
                  <div class="d-flex gap-2 justify-content-center">
                    <button class="btn-action btn-edit" onclick="location.href='procesos/editar_libro.php?codigo=<?= urlencode($row['codigo']) ?>'"><i class="fas fa-edit"></i></button>
                    <button class="btn-action btn-delete" onclick="eliminarLibro(<?= $row['id'] ?>, this)"><i class="fas fa-trash"></i></button>
                  </div>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div><!-- content-panel -->
      </div><!-- /tab-pane libros -->

      <!-- ── CONTROLES ── -->
      <div class="tab-pane fade" id="controles">
      <div class="content-panel">
        <div class="section-header">
          <h4 class="section-title"><i class="fas fa-gamepad me-2" style="color:var(--purple)"></i>Controles</h4>
        </div>
        <!-- Contenido de controles aquí -->
      </div><!-- content-panel -->
      </div><!-- /tab-pane controles -->

      <!-- ── SOLICITUDES ── -->
      <div class="tab-pane fade" id="sol-libros">
      <div class="content-panel">
        <div class="section-header mb-3">
          <h4 class="section-title"><i class="fas fa-clipboard-check me-2" style="color:var(--purple)"></i>Solicitudes de Libros</h4>
        </div>
        <input type="text" id="searchSolicitudes" class="form-control search-input mb-3" placeholder="Buscar por alumno o libro..." onkeyup="filterTable('searchSolicitudes', 'tableSolicitudes')">
        <div class="table-responsive">
          <table class="table table-hover align-middle text-center" id="tableSolicitudes">
            <thead><tr><th>Fecha</th><th>Alumno</th><th>Libro</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
              <?php while ($sl = $resultSolLibros->fetch_assoc()): ?>
              <tr>
                <td><?= $sl['fecha_solicitud'] ?></td>
                <td class="fw-bold"><?= htmlspecialchars($sl['nombre_usuario']) ?></td>
                <td><?= htmlspecialchars($sl['nombre_libro']) ?></td>
                <td>
                  <?php if ($sl['estado'] === 'Aceptado'): ?>
                    <span class="badge-status badge-accepted"><?= $sl['estado'] ?></span>
                  <?php elseif ($sl['estado'] === 'Pendiente'): ?>
                    <span class="badge-status badge-pending"><?= $sl['estado'] ?></span>
                  <?php else: ?>
                    <span class="badge-status badge-rejected"><?= $sl['estado'] ?></span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($sl['estado'] === 'Pendiente'): ?>
                    <button class="btn btn-success btn-sm rounded-pill px-3" onclick="cambiarEstado(<?= $sl['id'] ?>, 'Aceptado')"><i class="fas fa-check me-1"></i>Aceptar</button>
                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3 ms-1" onclick="cambiarEstado(<?= $sl['id'] ?>, 'Rechazado')"><i class="fas fa-times me-1"></i>Rechazar</button>
                  <?php elseif ($sl['estado'] === 'Aceptado'): ?>
                    <button class="btn btn-devolver btn-sm" onclick="marcarDevolucion(<?= $sl['id'] ?>)"><i class="fas fa-undo me-1"></i>Devolver</button>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div><!-- content-panel -->
      </div><!-- /tab-pane sol-libros -->

      <!-- ── HISTORIAL ── -->
      <div class="tab-pane fade" id="historial">
      <div class="content-panel">
        <div class="section-header">
          <h4 class="section-title"><i class="fas fa-history me-2" style="color:var(--purple)"></i>Historial de Préstamos</h4>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle" id="tablaHistorial">
            <thead><tr><th>Alumno</th><th>Libro</th><th>Solicitud</th><th>Devolución / Cierre</th></tr></thead>
            <tbody>
              <?php while ($h = $resultHistorial->fetch_assoc()): ?>
              <tr>
                <td class="fw-bold"><?= htmlspecialchars($h['nombre_usuario']) ?></td>
                <td><?= htmlspecialchars($h['nombre_libro']) ?></td>
                <td><small class="text-muted"><?= $h['fecha_solicitud'] ?></small></td>
                <td>
                  <?php if ($h['fecha_devolucion']): ?>
                    <span class="text-returned"><i class="fas fa-check-circle me-1"></i><?= $h['fecha_devolucion'] ?></span>
                  <?php else: ?>
                    <span class="text-rejected"><i class="fas fa-ban me-1"></i>Rechazado</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div><!-- content-panel -->
      </div><!-- /tab-pane historial -->

  </div><!-- /tab-content -->
</div><!-- /container -->

<!-- ═══ MODAL AGREGAR LIBRO ═══ -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal-header-custom">
        <h5 class="modal-title" id="modalAgregarLabel"><i class="fas fa-book me-2"></i>Añadir Nuevo Libro</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAgregarLibro">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Título del Libro</label>
            <input type="text" class="form-control" name="nombre" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Editorial</label>
            <input type="text" class="form-control" name="editorial" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Clasificación</label>
            <input type="text" class="form-control" name="clasificacion" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Autor</label>
            <input type="text" class="form-control" name="autor" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Código (Ej. LIB-004)</label>
            <input type="text" class="form-control" name="codigo" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light rounded-pill px-3 fw-bold" data-bs-dismiss="modal">Cancelar</button>
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
    // ═══ FUNCIONES ORIGINALES (con SweetAlert2) ═══

    function filterTable(inputId, tableId) {
        let input = document.getElementById(inputId).value.toLowerCase();
        let rows = document.querySelectorAll(`#${tableId} tbody tr`);
        rows.forEach(row => { row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none"; });
    }

    function cambiarEstado(id, n) {
        Swal.fire({
            title: n === 'Aceptado' ? '¿Aceptar solicitud?' : '¿Rechazar solicitud?',
            text: n === 'Aceptado' ? 'El libro quedará registrado como prestado.' : 'La solicitud será rechazada.',
            icon: n === 'Aceptado' ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: n === 'Aceptado' ? '#27ae60' : '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: n === 'Aceptado' ? 'Sí, aceptar' : 'Sí, rechazar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `procesos/estado_libro.php?id=${id}&accion=${n}`;
            }
        });
    }

    function marcarDevolucion(id) {
        Swal.fire({
            title: '¿Confirmas la devolución?',
            text: '¿Confirmas que el libro fue entregado?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#27ae60',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: '<i class="fas fa-check me-1"></i> Sí, entregar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'swal2-popup' }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `procesos/marcar_devuelto.php?id=${id}`;
            }
        });
    }

    function eliminarLibro(id, btn) {
        Swal.fire({
            title: '¿Eliminar este libro?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí tu lógica de eliminación original
                // Ejemplo: fetch o redirect
                Swal.fire({ title: 'Eliminado', text: 'El libro ha sido eliminado.', icon: 'success', timer: 1500, showConfirmButton: false });
            }
        });
    }

    // ═══ FORMULARIO AGREGAR LIBRO (con Toast) ═══
    document.getElementById('formAgregarLibro').addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        fetch('procesos/agregar_Libro.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('modalAgregar')).hide();
                // Toast de éxito
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1800,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({ icon: 'success', title: '¡Libro agregado correctamente!' });
                setTimeout(() => location.reload(), 1200);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Error al guardar: ' + data.error });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Hubo un problema al procesar la solicitud.' });
        });
    });

    // ═══ TABS - manejar active state y visibilidad del panel ═══
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function () {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // ═══ DATATABLES (Español, sin CSS externo) ═══
    $(document).ready(function() {
        const dtLang = {
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Sin registros disponibles",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles",
            paginate: { first: "Primero", previous: "Anterior", next: "Siguiente", last: "Último" }
        };

        $('#tablaLibros').DataTable({
            language: dtLang,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order: [],
            columnDefs: [{ orderable: false, targets: -1 }],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rt<"row align-items-center mt-3"<"col-sm-5"i><"col-sm-7"p>>'
        });

        // Historial: inicializar al cambiar a su tab (lazy init)
        let historialInit = false;
        document.querySelector('[data-bs-target="#historial"]').addEventListener('shown.bs.tab', function() {
            if (!historialInit) {
                $('#tablaHistorial').DataTable({
                    language: dtLang,
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50],
                    order: [],
                    dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rt<"row align-items-center mt-3"<"col-sm-5"i><"col-sm-7"p>>'
                });
                historialInit = true;
            }
        });
    });
</script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
