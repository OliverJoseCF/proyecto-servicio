<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Materias de Sistemas';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


  <a href="index.php" class="top-right">
    <img src="imagenes/casa.png" alt="Ir a inicio">
  </a>

  <h1>Materias de Sistemas</h1>

  <div class="container">
    <table id="materiaTable">
      <thead>
        <tr>
          <th>Materia</th>
        </tr>
      </thead>
      <tbody id="materiaList"></tbody>
    </table>

    <!-- Botón de guardar -->
    <button class="save-button" onclick="guardarCambios()">Guardar Cambios</button>
  </div>

  <script>
    const tabla = document.getElementById("materiaList");

    let materias = [
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

    function cargarMaterias() {
      const datosGuardados = localStorage.getItem("materiasSistemas");
      if (datosGuardados) {
        materias = JSON.parse(datosGuardados);
      }

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

    function guardarCambios() {
      const inputs = document.querySelectorAll(".edit-input");
      inputs.forEach((input) => {
        const index = input.getAttribute("data-index");
        materias[index] = input.value.trim();
      });

      localStorage.setItem("materiasSistemas", JSON.stringify(materias));
      alert("¡Cambios guardados correctamente!");
    }

    cargarMaterias();
  </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>