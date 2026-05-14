<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Materias — Animación Digital y Efectos Visuales';
$tsj_extra_css  = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
  <a href="index.php" class="top-right"><img src="imagenes/casa.png" alt="Ir a inicio"></a>
  <h1>Materias de Animación Digital y Efectos Visuales</h1>
  <div class="container">
    <table><thead><tr><th>Materia</th></tr></thead><tbody id="materiaList"></tbody></table>
  </div>
  <script>
    const materias = [
      "Fundamentos del Dibujo", "Principios de Animación", "Animación 2D",
      "Animación 3D", "Modelado 3D", "Storyboard", "Ilustración Digital",
      "Guion y Narrativa Visual", "Postproducción y Efectos Visuales (VFX)", "Motion Graphics"
    ];
    const tbody = document.getElementById("materiaList");
    materias.forEach(m => { const tr=document.createElement("tr"), td=document.createElement("td"); td.textContent=m; tr.appendChild(td); tbody.appendChild(tr); });
  </script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
