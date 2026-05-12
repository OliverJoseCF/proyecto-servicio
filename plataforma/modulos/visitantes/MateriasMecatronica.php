<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Materias de Mecatrónica';
$tsj_extra_css  = ['style.css'];
$tsj_head_extra = '<style>
    body {
      margin: 0;
      padding: 0;
      font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #2c3e50, #34495e);
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
    }

    h1 {
      margin: 30px 0 10px;
      font-size: 2.5em;
    }

    .container {
      width: 90%;
      max-width: 800px;
      background-color: rgba(255, 255, 255, 0.1);
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.3);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 10px;
      text-align: left;
      border: 1px solid #ddd;
    }

    th {
      background-color: #e74c3c;
      color: white;
    }

    td input {
      width: 100%;
      background: transparent;
      color: white;
      border: none;
      font-size: 16px;
      outline: none;
      text-align: left;
    }

    td input:focus {
      border: 1px solid #e74c3c;
    }

    .top-right {
      position: absolute;
      top: 20px;
      right: 20px;
    }

    .top-right img {
      width: 60px;
      height: auto;
    }

    .footer {
      margin-top: auto;
      padding: 20px;
      text-align: center;
    }

    .footer img {
      width: 80px;
      margin: 0 10px;
    }
  </style>';
require_once __DIR__ . '/../../shared/header.php';
?>


  <a href="index.php" class="top-right">
    <img src="imagenes/casa.png" alt="Ir a inicio">
  </a>

  <h1>Materias de Mecatrónica</h1>

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
      "Controladores Lógicos Programables",
      "Electrónica Analógica",
      "Diseño Mecánico",
      "Sistemas Neumáticos e Hidráulicos",
      "Programación de Microcontroladores",
      "Robótica Industrial",
      "Sensores y Actuadores",
      "Mantenimiento Industrial",
      "Automatización de Procesos",
      "Diseño de Circuitos"
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