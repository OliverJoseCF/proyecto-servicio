<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Materias de Ingeniería Industrial';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


  <a href="index.php" class="top-right">
    <img src="imagenes/casa.png" alt="Ir a inicio">
  </a>

  <h1>Materias de Ingeniería Industrial</h1>

  <div class="container">
    <table id="materiaTable">
      <thead>
        <tr>
          <th>Materia</th>
        </tr>
      </thead>
      <tbody id="materiaList"></tbody>
    </table>
  </div>

  <script>
    const tabla = document.getElementById("materiaList");

    const materias = [
      "Fundamentos de Ingeniería Industrial",
"Estadística Aplicada",
"Investigación de Operaciones",
"Gestión de la Producción",
"Logística y Cadena de Suministro",
"Seguridad e Higiene Industrial",
"Gestión de Calidad",
"Ergonomía",
"Planeación y Control de la Producción",
"Administración de Proyectos"

    ];

    function cargarMaterias() {
      tabla.innerHTML = "";
      materias.forEach((materia, index) => {
        const tr = document.createElement("tr");

        const td = document.createElement("td");
        const input = document.createElement("input");
        input.type = "text";
        input.value = materia;
        input.setAttribute("data-index", index);
        input.className = "edit-input";
        input.onchange = (e) => actualizarMateria(e.target);

        td.appendChild(input);
        tr.appendChild(td);
        tabla.appendChild(tr);
      });
    }

    function actualizarMateria(input) {
      const index = input.getAttribute("data-index");
      materias[index] = input.value.trim();
    }

    cargarMaterias();
  </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>