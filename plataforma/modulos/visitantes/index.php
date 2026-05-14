<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Directorio — Tecnológico Superior de Jalisco, Campus Chapala';
$tsj_extra_css  = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">

  <div class="header">
    <h1>Tecnológico Superior de Jalisco</h1>
    <p class="header-sub">Campus Chapala — Directorio institucional y programas educativos</p>
  </div>

  <div class="menu-wrapper">

    <h2 class="menu-section-label">Áreas administrativas</h2>
    <nav class="menu menu--admin" aria-label="Áreas administrativas">
      <a href="Escolares.php">Escolares</a>
      <a href="Direccion.php">Dirección</a>
      <a href="Finanzas.php">Finanzas</a>
      <a href="ServiciosGenerales.php">Servicios Generales</a>
      <a href="Directorio.php">Directorio</a>
    </nav>

    <h2 class="menu-section-label">Programas educativos</h2>
    <nav class="menu menu--carreras" aria-label="Programas educativos">
      <a href="Sistemas.php">Ingeniería en Sistemas Computacionales</a>
      <a href="Industrial.php">Ingeniería Industrial</a>
      <a href="Mecatronica.php">Ingeniería Mecatrónica</a>
      <a href="Animacion.php">Ingeniería en Animación Digital y Efectos Visuales</a>
      <a href="Gestion.php">Ingeniería en Gestión Empresarial</a>
      <a href="Gastronomia.php">Gastronomía</a>
    </nav>

    <h2 class="menu-section-label">Servicios al estudiante</h2>
    <nav class="menu menu--servicios" aria-label="Servicios al estudiante">
      <a href="Titulacion.php">Titulación</a>
      <a href="Egresados.php">Comprobante de Reinscripción</a>
      <a href="nuevoIngreso.php">Nuevo Ingreso</a>
      <a href="comprobante.php">Comprobantes</a>
      <a href="SolicitarCita.php">Secretarías</a>
      <a href="Ubicacion.php">Ubicación del Campus</a>
      <a href="DatosContacto.php">Datos de Contacto</a>
    </nav>

  </div>

</main>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
