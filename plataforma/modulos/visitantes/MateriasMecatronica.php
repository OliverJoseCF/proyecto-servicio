<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Materias — Ingeniería Mecatrónica';
$tsj_extra_css  = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
  <a href="index.php" class="top-right"><img src="imagenes/casa.png" alt="Ir a inicio"></a>
  <h1>Materias de Ingeniería Mecatrónica</h1>
  <div class="container">
    <table><thead><tr><th>Materia</th></tr></thead><tbody id="materiaList"></tbody></table>
  </div>
  <script>
    const materias = [
      "Controladores Lógicos Programables", "Electrónica Analógica", "Diseño Mecánico",
      "Sistemas Neumáticos e Hidráulicos", "Programación de Microcontroladores",
      "Robótica Industrial", "Sensores y Actuadores", "Mantenimiento Industrial",
      "Automatización de Procesos", "Diseño de Circuitos"
    ];
    const tbody = document.getElementById("materiaList");
    materias.forEach(m => { const tr=document.createElement("tr"), td=document.createElement("td"); td.textContent=m; tr.appendChild(td); tbody.appendChild(tr); });
  </script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
