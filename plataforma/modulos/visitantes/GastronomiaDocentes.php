<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'DOCENTES - Gestión';
$tsj_extra_css  = ['style.css'];
$tsj_head_extra = '<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #4B0082;
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
        }

        h1 {
            background-color: #5757c0;
            padding: 30px 0;
            font-size: 40px;
            font-weight: bold;
            margin: 0;
        }

        .docentes {
            background-color: white;
            color: black;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            margin: 20px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: left;
        }

        .docentes h2 { text-align: center; color: #4B0082; }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background-color 0.3s ease;
        }

        li:hover { background-color: #f9f9f9; }

        .foto-perfil {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
            border: 1px solid #ccc;
        }

        .nombre-contenedor { flex-grow: 1; font-size: 19px; }

        /* Botones de acción rápidos */
        .btn-accion {
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-left: 5px;
        }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; color: white; }

        /* PANEL DE ADMINISTRACIÓN (ABAJO) */
        .admin-panel {
            background-color: white;
            color: black;
            padding: 25px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            margin: 20px auto 40px auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }
        .admin-panel input {
            width: 90%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .btn-guardar {
            background-color: #28a745;
            color: white;
            padding: 12px;
            width: 95%;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }

        .footer {
            background: #333;
            padding: 20px;
            width: 100%;
            margin-top: auto;
        }
        .footer img { margin: 15px; }

        .top-right {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>';
require_once __DIR__ . '/../../shared/header.php';
?>


    <h1>DOCENTES</h1>

    <div class="docentes">
        <h2>Lista de Docentes</h2>
        <ul id="listaDocentes">
            </ul>
    </div>

    <div class="admin-panel">
        <h3 id="titulo-form" style="margin-top:0;">Agregar / Editar Docente</h3>
        <input type="hidden" id="edit-index" value="">
        <input type="text" id="nombre-input" placeholder="Nombre del docente">
        <input type="text" id="foto-input" placeholder="Imagen (ej: lina.png)">
        <button class="btn-guardar" onclick="guardarDocente()">Guardar Cambios</button>
    </div>

    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Inicio" style="width: 80px; height: auto;">
    </a>

    <script>
        // Lista inicial de docentes
        const iniciales = [
            { nombre: "Lina Corona", foto: "user.png" },
            { nombre: "Jessica Álvarez", foto: "user.png" },
            { nombre: "Jaime Sánchez", foto: "user.png" },
            { nombre: "Carlos Ramírez", foto: "user.png" },
            { nombre: "Yessica Regalado", foto: "user.png" },
            { nombre: "Mayra Hinojoza", foto: "user.png" }
        ];

        // Cargar datos de localStorage o usar iniciales
        let docentes = JSON.parse(localStorage.getItem("docentes_data_v6")) || iniciales;

        function render() {
            const lista = document.getElementById("listaDocentes");
            lista.innerHTML = "";
            docentes.forEach((d, i) => {
                lista.innerHTML += `
                    <li>
                        <img src="imagenes/${d.foto}" class="foto-perfil" onerror="this.src='imagenes/user.png'">
                        <div class="nombre-contenedor">${d.nombre}</div>
                        <div>
                            <button class="btn-accion btn-edit" onclick="prepararEdicion(${i})">Editar</button>
                            <button class="btn-accion btn-delete" onclick="borrar(${i})">Borrar</button>
                        </div>
                    </li>
                `;
            });
        }

        function guardarDocente() {
            const nombre = document.getElementById("nombre-input").value;
            let foto = document.getElementById("foto-input").value || "user.png";
            const index = document.getElementById("edit-index").value;

            if (nombre === "") return alert("Ingresa un nombre");

            foto = foto.replace("imagenes/", "");

            if (index === "") {
                docentes.push({ nombre, foto });
            } else {
                docentes[index] = { nombre, foto };
                document.getElementById("edit-index").value = "";
                document.getElementById("titulo-form").innerText = "Agregar / Editar Docente";
            }

            localStorage.setItem("docentes_data_v6", JSON.stringify(docentes));
            limpiar();
            render();
        }

        function prepararEdicion(i) {
            document.getElementById("nombre-input").value = docentes[i].nombre;
            document.getElementById("foto-input").value = docentes[i].foto;
            document.getElementById("edit-index").value = i;
            document.getElementById("titulo-form").innerText = "Editando a: " + docentes[i].nombre;
            // Bajar suavemente al panel
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        }

        function borrar(i) {
            if (confirm("¿Deseas eliminar a este docente de la lista?")) {
                docentes.splice(i, 1);
                localStorage.setItem("docentes_data_v6", JSON.stringify(docentes));
                render();
            }
        }

        function limpiar() {
            document.getElementById("nombre-input").value = "";
            document.getElementById("foto-input").value = "";
        }

        render();
    </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>