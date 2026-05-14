<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Materias — Ingeniería en Gestión Empresarial';
$tsj_extra_css  = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
  <a href="index.php" class="top-right"><img src="imagenes/casa.png" alt="Ir a inicio"></a>
  <h1>Materias de Ingeniería en Gestión Empresarial</h1>
  <div class="container">
    <table><thead><tr><th>Materia</th></tr></thead><tbody id="materiaList"></tbody></table>
  </div>
  <script>
    const materias = [
      "Administración de Empresas", "Contabilidad Financiera", "Economía",
      "Gestión del Talento Humano", "Marketing", "Finanzas Empresariales",
      "Emprendimiento e Innovación", "Gestión de Proyectos",
      "Comportamiento Organizacional", "Planeación Estratégica"
    ];
    const tbody = document.getElementById("materiaList");
    materias.forEach(m => { const tr=document.createElement("tr"), td=document.createElement("td"); td.textContent=m; tr.appendChild(td); tbody.appendChild(tr); });
  </script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
