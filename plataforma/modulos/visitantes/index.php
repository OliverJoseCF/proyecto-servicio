<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Tecnológico Superior de Chapala';
$tsj_extra_css  = ['style.css'];
$tsj_head_extra = '<style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #4e0d7c;
      color: white;
      text-align: center;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .header {
      background-color: #5555c9;
      padding: 15px 0;
      font-size: 24px;
      font-weight: bold;
    }

    .content {
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      flex: 1;
    }

    .menu {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      padding: 10px;
    }

    .menu a {
      color: white;
      text-decoration: none;
      padding: 12px 25px;
      margin: 10px;
      font-weight: bold;
      border-radius: 8px;
      background-color: #6a5acd;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
    }

    .menu a:hover {
      background-color: #c52f2f;
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }

    .footer {
      background: #333;
      color: white;
      padding: 20px 10px;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-around;
      align-items: center;
      gap: 15px;
    }

    .footer img {
      max-width: 100%;
      height: auto;
    }

    .footer-right {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .footer-right img {
      width: 40px;
    }

    @media (max-width: 600px) {
      .menu a {
        padding: 10px 15px;
        font-size: 14px;
      }

      .footer {
        flex-direction: column;
      }

      .footer img {
        width: 100px !important;
      }

      .footer-right {
        justify-content: center;
        flex-wrap: wrap;
      }

      .footer-right img {
        width: 35px;
      }
    }
  </style>';
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