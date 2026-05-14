<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Materias — Ingeniería Industrial';
$tsj_extra_css  = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
  <a href="index.php" class="top-right"><img src="imagenes/casa.png" alt="Ir a inicio"></a>
  <h1>Materias de Ingeniería Industrial</h1>
  <div class="container">
    <table><thead><tr><th>Materia</th></tr></thead><tbody id="materiaList"></tbody></table>
  </div>
  <script>
    const materias = [
      "Fundamentos de Ingeniería Industrial", "Estadística Aplicada", "Investigación de Operaciones",
      "Gestión de la Producción", "Logística y Cadena de Suministro", "Seguridad e Higiene Industrial",
      "Gestión de Calidad", "Ergonomía", "Planeación y Control de la Producción", "Administración de Proyectos"
    ];
    const tbody = document.getElementById("materiaList");
    materias.forEach(m => { const tr=document.createElement("tr"), td=document.createElement("td"); td.textContent=m; tr.appendChild(td); tbody.appendChild(tr); });
  </script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
