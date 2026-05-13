<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Tecnológico Superior de Chapala';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>

  <div class="header">
    <h1>Tecnológico Superior de Chapala</h1>
  </div>

  <div class="content">
    <img src="imagenes/portada.png" alt="Imagen portada" style="width: 550px;" />
    <nav class="menu">
      <a href="Escolares.php">Escolares</a>
      <a href="Direccion.php">Dirección</a>
      <a href="Finanzas.php">Finanzas</a>
      <a href="ServiciosGenerales.php">Servicios Generales</a>
      <a href="Directorio.php">Directorio</a>
      <a href="Sistemas.php">Ingeniería Sistemas Computacionales</a>
      <a href="Industrial.php">Ingeniería Industrial</a>
      <a href="Mecatronica.php">Ingeniería Mecatrónica</a>
      <a href="Animacion.php">Ingeniería en Animación Digital</a>
      <a href="Gestion.php">Ingeniería En Gestión Empresarial</a>
      <a href="Gastronomia.php">Gastronomía</a>
    </nav>
  </div>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>