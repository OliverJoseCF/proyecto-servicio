<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Materias — Gastronomía';
$tsj_extra_css  = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
  <a href="index.php" class="top-right"><img src="imagenes/casa.png" alt="Ir a inicio"></a>
  <h1>Materias de Gastronomía</h1>
  <div class="container">
    <table><thead><tr><th>Materia</th></tr></thead><tbody id="materiaList"></tbody></table>
  </div>
  <script>
    const materias = [
      "Fundamentos de Cocina", "Higiene y Seguridad Alimentaria", "Cocina Internacional",
      "Panadería y Repostería", "Nutrición y Dietética", "Enología y Bebidas",
      "Gestión de Alimentos y Bebidas", "Cocina Molecular",
      "Arte Culinario y Presentación de Platos", "Costos y Presupuestos en Cocina"
    ];
    const tbody = document.getElementById("materiaList");
    materias.forEach(m => { const tr=document.createElement("tr"), td=document.createElement("td"); td.textContent=m; tr.appendChild(td); tbody.appendChild(tr); });
  </script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
