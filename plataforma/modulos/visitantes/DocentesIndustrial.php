<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Docentes — Ingeniería Industrial — TSJ Chapala';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">
  <a href="index.php" class="top-right" aria-label="Volver al menú principal"><img src="imagenes/casa.png" alt=""></a>
  <h1 class="vis-page-title">Docentes de Ingeniería Industrial</h1>
  <div class="container"><div class="tabla-container">
    <table><thead><tr><th scope="col">Docente</th></tr></thead><tbody id="lista-docentes"></tbody></table>
  </div></div>
</main>
<script>
(function(){
  'use strict';
  var docentes = [
    { nombre:"Miguel Ángel Delgado", foto:"miguel.png" },
    { nombre:"Alberto Chavolla",     foto:"user.png" },
    { nombre:"Francisco Pocholo",    foto:"user.png" },
    { nombre:"Julio César Chávez",   foto:"user.png" },
    { nombre:"Francisco Javier",     foto:"user.png" },
    { nombre:"Edgar Martínez",       foto:"user.png" },
    { nombre:"José Jorge",           foto:"user.png" }
  ];
  var tbody=document.getElementById("lista-docentes");
  docentes.forEach(function(d){
    var tr=document.createElement("tr"), td=document.createElement("td");
    var img=document.createElement("img");
    img.src="imagenes/"+d.foto; img.alt=d.nombre;
    img.className="foto-mini"; img.width=36; img.height=36;
    img.onerror=function(){this.src="imagenes/user.png";};
    var b=document.createElement("b"); b.textContent=d.nombre;
    td.appendChild(img); td.append(" "); td.appendChild(b); tr.appendChild(td); tbody.appendChild(tr);
  });
})();
</script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
