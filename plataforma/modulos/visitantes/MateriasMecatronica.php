<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Materias — Ingeniería Mecatrónica — TSJ Chapala';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">
  <a href="index.php" class="top-right" aria-label="Volver al menú principal"><img src="imagenes/casa.png" alt=""></a>
  <h1 class="vis-page-title">Materias de Ingeniería Mecatrónica</h1>
  <div class="container"><div class="tabla-scroll">
    <table><thead><tr><th scope="col">Materia</th></tr></thead><tbody id="materiaList"></tbody></table>
  </div></div>
</main>
<script>
(function(){
  'use strict';
  var materias = ["Controladores Lógicos Programables","Electrónica Analógica","Diseño Mecánico","Sistemas Neumáticos e Hidráulicos","Programación de Microcontroladores","Robótica Industrial","Sensores y Actuadores","Mantenimiento Industrial","Automatización de Procesos","Diseño de Circuitos"];
  var tbody = document.getElementById("materiaList");
  materias.forEach(function(m){var tr=document.createElement("tr"),td=document.createElement("td");td.textContent=m;tr.appendChild(td);tbody.appendChild(tr);});
})();
</script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
