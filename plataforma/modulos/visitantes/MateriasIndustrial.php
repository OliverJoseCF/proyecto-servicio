<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Materias — Ingeniería Industrial — TSJ Chapala';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">
  <a href="index.php" class="top-right" aria-label="Volver al menú principal"><img src="imagenes/casa.png" alt=""></a>
  <h1 class="vis-page-title">Materias de Ingeniería Industrial</h1>
  <div class="container"><div class="tabla-scroll">
    <table><thead><tr><th scope="col">Materia</th></tr></thead><tbody id="materiaList"></tbody></table>
  </div></div>
</main>
<script>
(function(){
  'use strict';
  var materias = ["Fundamentos de Ingeniería Industrial","Estadística Aplicada","Investigación de Operaciones","Gestión de la Producción","Logística y Cadena de Suministro","Seguridad e Higiene Industrial","Gestión de Calidad","Ergonomía","Planeación y Control de la Producción","Administración de Proyectos"];
  var tbody = document.getElementById("materiaList");
  materias.forEach(function(m){var tr=document.createElement("tr"),td=document.createElement("td");td.textContent=m;tr.appendChild(td);tbody.appendChild(tr);});
})();
</script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
