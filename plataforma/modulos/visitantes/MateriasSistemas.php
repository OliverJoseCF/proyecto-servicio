<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Materias — Ingeniería en Sistemas Computacionales';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">
  <a href="index.php" class="top-right" aria-label="Volver al menú principal"><img src="imagenes/casa.png" alt=""></a>
  <h1 class="vis-page-title">Materias de Sistemas Computacionales</h1>
  <div class="container">
    <div class="tabla-scroll">
      <table>
        <thead><tr><th scope="col">Materia</th></tr></thead>
        <tbody id="materiaList"></tbody>
      </table>
    </div>
  </div>
</main>
<script>
(function(){
  'use strict';
  var materias = [
    "Fundamentos de Programación","Estructuras de Datos","Bases de Datos",
    "Redes de Computadoras","Sistemas Operativos","Ingeniería de Software",
    "Análisis de Sistemas","Arquitectura de Computadoras","Desarrollo Web",
    "Programación Orientada a Objetos"
  ];
  var tbody = document.getElementById("materiaList");
  materias.forEach(function(m) {
    var tr = document.createElement("tr"), td = document.createElement("td");
    td.textContent = m; tr.appendChild(td); tbody.appendChild(tr);
  });
})();
</script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
