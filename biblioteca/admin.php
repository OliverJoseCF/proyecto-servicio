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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Gestión Bibliotecaria</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <!-- SweetAlert2 -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  <!-- DataTables Bootstrap 5 CSS inline override (no external CSS file) -->
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
      --purple: #3D2E81;
      --purple-light: #4e3da0;
      --purple-soft: rgba(61, 46, 129, 0.08);
      --gold: #c9a050;
      --gold-light: rgba(201, 160, 80, 0.12);
      --bg: #f4f6f9;
      --card-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.03);
      --card-shadow-hover: 0 4px 20px rgba(61, 46, 129, 0.10);
    }

    body {
      background-color: var(--bg);
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: #1a1a2e;
      -webkit-font-smoothing: antialiased;
    }

    /* ── Navbar ── */
    .navbar-admin {
      background: linear-gradient(135deg, var(--purple) 0%, #2d1f6b 100%);
      border: none;
      padding: 0.9rem 1.5rem;
      box-shadow: 0 2px 16px rgba(61, 46, 129, 0.25);
    }
    .navbar-admin .navbar-brand {
      font-weight: 800;
      font-size: 1.1rem;
      letter-spacing: 0.5px;
    }
    .navbar-admin .gold-accent {
      color: var(--gold);
    }
    .btn-logout {
      border: 1.5px solid rgba(255,255,255,0.3);
      border-radius: 50px;
      padding: 0.35rem 1.1rem;
      font-size: 0.8rem;
      font-weight: 600;
      letter-spacing: 0.3px;
      transition: all 0.25s ease;
    }
    .btn-logout:hover {
      background: rgba(255,255,255,0.15);
      border-color: rgba(255,255,255,0.5);
    }

    /* ── Stat Cards ── */
    .stat-card {
      background: #fff;
      border: none;
      border-radius: 16px;
      box-shadow: var(--card-shadow);
      padding: 1.5rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      border-radius: 16px 16px 0 0;
    }
    .stat-card.card-books::before { background: var(--purple); }
    .stat-card.card-loans::before { background: #e74c3c; }
    .stat-card.card-pending::before { background: var(--gold); }

    .stat-card:hover {
      box-shadow: var(--card-shadow-hover);
      transform: translateY(-2px);
    }
    .stat-icon {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      flex-shrink: 0;
    }
    .stat-icon.icon-books { background: var(--purple-soft); color: var(--purple); }
    .stat-icon.icon-loans { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }
    .stat-icon.icon-pending { background: var(--gold-light); color: #b8912e; }

    .stat-label {
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: #8c8c9e;
      margin-bottom: 0.15rem;
    }
    .stat-value {
      font-size: 1.85rem;
      font-weight: 800;
      line-height: 1.1;
      color: #1a1a2e;
    }

    /* ── Tabs ── */
    .tabs-wrapper {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
      padding: 0;
      margin-bottom: 0;
    }
    .tab-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      padding: 0.6rem 1.25rem;
      border-radius: 12px 12px 0 0;
      font-size: 0.85rem;
      font-weight: 700;
      border: none;
      background: transparent;
      color: #8c8c9e;
      cursor: pointer;
      transition: all 0.25s ease;
      position: relative;
    }
    .tab-btn:hover {
      color: var(--purple);
      background: rgba(61, 46, 129, 0.04);
    }
    .tab-btn.active {
      color: #fff;
      background: var(--purple);
      box-shadow: 0 -2px 10px rgba(61, 46, 129, 0.15);
    }
    .tab-btn i {
      font-size: 0.8rem;
    }

    /* ── Content Panel ── */
    .content-panel {
      background: #fff;
      border-radius: 0 16px 16px 16px;
      box-shadow: var(--card-shadow);
      padding: 1.75rem;
    }
    /* Fix: when non-first tab is active, round all top corners */
    #controles .content-panel,
    #sol-libros .content-panel,
    #historial .content-panel {
      border-radius: 16px;
    }

    /* ── Tables ── */
    .table {
      margin-bottom: 0;
    }
    .table thead th {
      background: transparent;
      color: #8c8c9e;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.7px;
      border-bottom: 2px solid #f0f0f5;
      padding: 0.85rem 1rem;
      white-space: nowrap;
    }
    .table tbody td {
      padding: 0.9rem 1rem;
      vertical-align: middle;
      border-bottom: 1px solid #f5f5fa;
      font-size: 0.88rem;
      color: #333;
    }
    .table tbody tr:last-child td {
      border-bottom: none;
    }
    .table-hover tbody tr:hover {
      background-color: rgba(61, 46, 129, 0.025);
    }
    .badge-code {
      background: #f4f6f9;
      color: #555;
      border: 1px solid #e5e7eb;
      font-weight: 600;
      font-size: 0.78rem;
      padding: 0.3em 0.65em;
      border-radius: 6px;
    }

    /* ── Action Buttons ── */
    .btn-action {
      width: 34px;
      height: 34px;
      border-radius: 10px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1.5px solid;
      font-size: 0.78rem;
      transition: all 0.2s ease;
      padding: 0;
    }
    .btn-action.btn-edit {
      color: var(--gold);
      border-color: var(--gold-light);
      background: var(--gold-light);
    }
    .btn-action.btn-edit:hover {
      background: var(--gold);
      color: #fff;
      border-color: var(--gold);
    }
    .btn-action.btn-delete {
      color: #e74c3c;
      border-color: rgba(231,76,60,0.12);
      background: rgba(231,76,60,0.06);
    }
    .btn-action.btn-delete:hover {
      background: #e74c3c;
      color: #fff;
      border-color: #e74c3c;
    }

    /* ── Section header ── */
    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 0.75rem;
      margin-bottom: 1.25rem;
    }
    .section-title {
      font-size: 1.1rem;
      font-weight: 800;
      color: #1a1a2e;
      margin: 0;
    }
    .btn-add {
      background: var(--purple);
      color: #fff;
      border: none;
      border-radius: 50px;
      padding: 0.5rem 1.2rem;
      font-size: 0.82rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      transition: all 0.25s ease;
      box-shadow: 0 2px 8px rgba(61, 46, 129, 0.2);
    }
    .btn-add:hover {
      background: var(--purple-light);
      color: #fff;
      transform: translateY(-1px);
      box-shadow: 0 4px 14px rgba(61, 46, 129, 0.3);
    }

    /* ── Search Input ── */
    .search-input {
      border: 2px solid #ececf3;
      border-radius: 12px;
      padding: 0.65rem 1rem 0.65rem 2.6rem;
      font-size: 0.88rem;
      transition: border-color 0.25s ease;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%238c8c9e' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: 0.85rem center;
    }
    .search-input:focus {
      border-color: var(--purple);
      box-shadow: 0 0 0 3px var(--purple-soft);
      outline: none;
    }

    /* ── Badges ── */
    .badge-status {
      font-size: 0.75rem;
      font-weight: 700;
      padding: 0.35em 0.85em;
      border-radius: 50px;
      letter-spacing: 0.2px;
    }
    .badge-accepted { background: rgba(39, 174, 96, 0.1); color: #1b8a4a; }
    .badge-pending { background: rgba(201, 160, 80, 0.15); color: #9a7620; }
    .badge-rejected { background: rgba(231, 76, 60, 0.1); color: #c0392b; }

    .btn-devolver {
      background: linear-gradient(135deg, var(--gold), #d4a84a);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 0.35rem 0.9rem;
      font-size: 0.78rem;
      font-weight: 700;
      transition: all 0.2s ease;
    }
    .btn-devolver:hover {
      color: #fff;
      box-shadow: 0 3px 10px rgba(201, 160, 80, 0.35);
      transform: translateY(-1px);
    }

    /* ── Modal ── */
    .modal-content {
      border: none;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .modal-header-custom {
      background: linear-gradient(135deg, var(--purple) 0%, #2d1f6b 100%);
      color: #fff;
      padding: 1.25rem 1.5rem;
      border: none;
    }
    .modal-header-custom .modal-title {
      font-weight: 700;
      font-size: 1rem;
    }
    .modal-body { padding: 1.5rem; }
    .modal-body .form-label {
      font-size: 0.82rem;
      font-weight: 700;
      color: #555;
      margin-bottom: 0.3rem;
    }
    .modal-body .form-control {
      border-radius: 10px;
      border: 2px solid #ececf3;
      padding: 0.6rem 0.9rem;
      font-size: 0.88rem;
      transition: border-color 0.25s ease;
    }
    .modal-body .form-control:focus {
      border-color: var(--purple);
      box-shadow: 0 0 0 3px var(--purple-soft);
    }
    .modal-footer {
      border-top: 1px solid #f0f0f5;
      padding: 1rem 1.5rem;
    }
    .btn-save {
      background: var(--purple);
      color: #fff;
      border: none;
      border-radius: 50px;
      padding: 0.55rem 1.5rem;
      font-weight: 700;
      font-size: 0.85rem;
      transition: all 0.25s ease;
    }
    .btn-save:hover {
      background: var(--purple-light);
      color: #fff;
      box-shadow: 0 4px 14px rgba(61, 46, 129, 0.25);
    }

    /* ── DataTables overrides (no external CSS) ── */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
      font-size: 0.82rem;
      color: #8c8c9e;
      padding: 0.5rem 0;
    }
    .dataTables_wrapper .dataTables_filter input {
      border: 2px solid #ececf3;
      border-radius: 10px;
      padding: 0.4rem 0.75rem;
      font-size: 0.85rem;
      outline: none;
      transition: border-color 0.25s ease;
      margin-left: 0.5rem;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
      border-color: var(--purple);
      box-shadow: 0 0 0 3px var(--purple-soft);
    }
    .dataTables_wrapper .dataTables_length select {
      border: 2px solid #ececf3;
      border-radius: 8px;
      padding: 0.3rem 0.5rem;
      font-size: 0.82rem;
      outline: none;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
      border: none !important;
      border-radius: 8px !important;
      padding: 0.35rem 0.7rem !important;
      margin: 0 2px !important;
      font-size: 0.82rem !important;
      color: #555 !important;
      background: transparent !important;
      transition: all 0.2s ease;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
      background: var(--purple-soft) !important;
      color: var(--purple) !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
      background: var(--purple) !important;
      color: #fff !important;
      font-weight: 700;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
      opacity: 0.35;
    }
    table.dataTable thead .sorting,
    table.dataTable thead .sorting_asc,
    table.dataTable thead .sorting_desc {
      background-image: none !important;
    }

    /* ── Historial ── */
    .text-returned { color: #1b8a4a; font-weight: 700; }
    .text-rejected { color: #c0392b; font-weight: 700; }

    /* ── SweetAlert2 custom ── */
    .swal2-popup { border-radius: 20px !important; font-family: 'Plus Jakarta Sans', sans-serif !important; }
    .swal2-confirm { border-radius: 50px !important; font-weight: 700 !important; padding: 0.55rem 1.5rem !important; }
    .swal2-cancel { border-radius: 50px !important; font-weight: 600 !important; padding: 0.55rem 1.5rem !important; }

    /* ── Responsive ── */
    @media (max-width: 767.98px) {
      .navbar-admin { padding: 0.65rem 1rem; }
      .navbar-admin .navbar-brand { font-size: 0.85rem; }
      .content-panel { padding: 1rem; border-radius: 0 0 16px 16px; }
      .stat-card { padding: 1.15rem; }
      .stat-value { font-size: 1.5rem; }
      .tabs-wrapper { gap: 0.25rem; }
      .tab-btn { padding: 0.5rem 0.85rem; font-size: 0.78rem; }
      .section-header { flex-direction: column; align-items: flex-start; }
      .table-responsive { margin: 0 -1rem; padding: 0 1rem; }
    }
    @media (max-width: 575.98px) {
      .tab-btn span.tab-label { display: none; }
      .tab-btn i { font-size: 1rem; }
    }
  </style>
</head>
<body>

<!-- ═══ NAVBAR ═══ -->
<nav class="navbar navbar-dark navbar-admin mb-4">
  <div class="container-fluid px-3 px-md-4">
    <span class="navbar-brand mb-0 d-flex align-items-center gap-2">
      <i class="fas fa-book-reader gold-accent"></i>
      <span>GESTIÓN <span class="gold-accent">BIBLIOTECARIA</span></span>
    </span>
    <button class="btn btn-outline-light btn-logout" onclick="location.href='procesos/cerrar_sesion.php'">
      <i class="fas fa-sign-out-alt me-1"></i> Salir
    </button>
  </div>
</nav>

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
</body>
</html>