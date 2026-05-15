<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Materias — Gastronomía';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">
  <a href="index.php" class="top-right" aria-label="Volver al menú principal"><img src="imagenes/casa.png" alt=""></a>
  <h1 class="vis-page-title">Materias de Gastronomía</h1>
  <div class="container"><div class="tabla-scroll">
    <table><thead><tr><th scope="col">Materia</th></tr></thead><tbody id="materiaList"></tbody></table>
  </div></div>
</main>
<script>
(function(){
  'use strict';
  var materias = ["Fundamentos de Cocina","Higiene y Seguridad Alimentaria","Cocina Internacional","Panadería y Repostería","Nutrición y Dietética","Enología y Bebidas","Gestión de Alimentos y Bebidas","Cocina Molecular","Arte Culinario y Presentación de Platos","Costos y Presupuestos en Cocina"];
  var tbody = document.getElementById("materiaList");
  materias.forEach(function(m){var tr=document.createElement("tr"),td=document.createElement("td");td.textContent=m;tr.appendChild(td);tbody.appendChild(tr);});
})();
</script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
