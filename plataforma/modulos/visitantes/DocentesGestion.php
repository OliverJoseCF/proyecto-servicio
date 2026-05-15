<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Docentes — Ingeniería en Gestión Empresarial';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
<main id="main">
  <a href="index.php" class="top-right" aria-label="Volver al menú principal"><img src="imagenes/casa.png" alt=""></a>
  <h1 class="vis-page-title">Docentes de Ingeniería en Gestión Empresarial</h1>
  <div class="container"><div class="tabla-container">
    <table><thead><tr><th scope="col">Docente</th></tr></thead><tbody id="lista-docentes"></tbody></table>
  </div></div>
</main>
<script>
(function(){
  'use strict';
  var docentes = [
    { nombre:"Carlos Ramírez",  foto:"user.png" },
    { nombre:"Fidel Rodríguez", foto:"user.png" },
    { nombre:"Alberto Chavoya", foto:"user.png" },
    { nombre:"Alma González",   foto:"user.png" },
    { nombre:"José Aguilera",   foto:"user.png" },
    { nombre:"María Estrada",   foto:"user.png" }
  ];
  var tbody=document.getElementById("lista-docentes");
  docentes.forEach(function(d){
    var tr=document.createElement("tr"), td=document.createElement("td");
    var img=document.createElement("img");
    img.src="imagenes/"+d.foto; img.alt=d.nombre;
    img.className="foto-mini"; img.width=36; img.height=36; img.loading="lazy";
    img.onerror=function(){this.src="imagenes/user.png";};
    var b=document.createElement("b"); b.textContent=d.nombre;
    td.appendChild(img); td.append(" "); td.appendChild(b); tr.appendChild(td); tbody.appendChild(tr);
  });
})();
</script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
