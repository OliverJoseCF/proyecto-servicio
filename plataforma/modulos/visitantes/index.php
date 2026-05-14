<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Tecnológico Superior de Chapala';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>

  <div class="header">
    <h1>Tecnológico Superior de Chapala</h1>
    <p class="header-sub">Directorio institucional — selecciona un área o programa educativo</p>
  </div>

  <div class="menu-wrapper">
    <p class="menu-section-label">Áreas administrativas</p>
    <nav class="menu menu--admin">
      <a href="Escolares.php">Escolares</a>
      <a href="Direccion.php">Dirección</a>
      <a href="Finanzas.php">Finanzas</a>
      <a href="ServiciosGenerales.php">Servicios Generales</a>
      <a href="Directorio.php">Directorio</a>
    </nav>

    <p class="menu-section-label">Programas educativos</p>
    <nav class="menu menu--carreras">
      <a href="Sistemas.php">Ingeniería en Sistemas Computacionales</a>
      <a href="Industrial.php">Ingeniería Industrial</a>
      <a href="Mecatronica.php">Ingeniería Mecatrónica</a>
      <a href="Animacion.php">Ingeniería en Animación Digital y Efectos Visuales</a>
      <a href="Gestion.php">Ingeniería en Gestión Empresarial</a>
      <a href="Gastronomia.php">Gastronomía</a>
    </nav>

    <p class="menu-section-label">Servicios al estudiante</p>
    <nav class="menu menu--servicios">
      <a href="Titulacion.php">Titulación</a>
      <a href="Egresados.php">Comprobante de Reinscripción</a>
      <a href="nuevoIngreso.php">Nuevo Ingreso</a>
      <a href="comprobante.php">Comprobantes</a>
      <a href="SolicitarCita.php">Secretarías</a>
      <a href="Ubicacion.php">Ubicación del Campus</a>
      <a href="DatosContacto.php">Datos de Contacto</a>
    </nav>
  </div>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
