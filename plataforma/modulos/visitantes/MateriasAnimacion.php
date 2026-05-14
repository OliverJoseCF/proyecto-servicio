<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Materias — Animación Digital y Efectos Visuales — TSJ Chapala';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">
  <a href="index.php" class="top-right" aria-label="Volver al menú principal"><img src="imagenes/casa.png" alt=""></a>
  <h1 class="vis-page-title">Materias de Animación Digital y Efectos Visuales</h1>
  <div class="container"><div class="tabla-scroll">
    <table><thead><tr><th scope="col">Materia</th></tr></thead><tbody id="materiaList"></tbody></table>
  </div></div>
</main>
<script>
(function(){
  'use strict';
  var materias = ["Fundamentos del Dibujo","Principios de Animación","Animación 2D","Animación 3D","Modelado 3D","Storyboard","Ilustración Digital","Guion y Narrativa Visual","Postproducción y Efectos Visuales (VFX)","Motion Graphics"];
  var tbody = document.getElementById("materiaList");
  materias.forEach(function(m){var tr=document.createElement("tr"),td=document.createElement("td");td.textContent=m;tr.appendChild(td);tbody.appendChild(tr);});
})();
</script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
