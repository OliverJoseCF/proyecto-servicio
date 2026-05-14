<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Materias — Ingeniería en Sistemas Computacionales';
$tsj_extra_css  = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

  <a href="index.php" class="top-right">
    <img src="imagenes/casa.png" alt="Ir a inicio">
  </a>

  <h1>Materias de Sistemas Computacionales</h1>

  <div class="container">
    <table>
      <thead><tr><th>Materia</th></tr></thead>
      <tbody id="materiaList"></tbody>
    </table>
  </div>

  <script>
    const materias = [
      "Fundamentos de Programación",
      "Estructuras de Datos",
      "Bases de Datos",
      "Redes de Computadoras",
      "Sistemas Operativos",
      "Ingeniería de Software",
      "Análisis de Sistemas",
      "Arquitectura de Computadoras",
      "Desarrollo Web",
      "Programación Orientada a Objetos"
    ];
    const tbody = document.getElementById("materiaList");
    materias.forEach(m => {
      const tr = document.createElement("tr");
      const td = document.createElement("td");
      td.textContent = m;
      tr.appendChild(td);
      tbody.appendChild(tr);
    });
  </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
