<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'DOCENTES - Sistemas';
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
        /* Cuadro de la Lista Principal */
        .docentes {
            background-color: white;
            color: black;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            margin: 20px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: left;
        }
        .docentes h2 { text-align: center; color: #4B0082; }
        
        .docentes ul {
            list-style-type: none;
            padding: 0;
        }
        .docentes li {
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f3f3f3;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-left: 5px solid #5757c0;
        }
        .foto-perfil {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            border: 1px solid #ccc;
        }
        .nombre-texto { flex-grow: 1; font-size: 18px; }

        /* Botones pequeños de la lista */
        .btn-mini {
            border: none;
            padding: 5px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 4px;
        }
        .edit-btn { background: #ffc107; }
        .del-btn { background: #dc3545; color: white; }

        /* PANEL DE ADMINISTRACIÓN (AL FINAL) */
        .admin-panel {
            background-color: white;
            color: black;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            margin: 20px auto 40px auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }
        .admin-panel h3 { margin-top: 0; font-size: 18px; }
        .admin-panel input {
            width: 90%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .btn-guardar {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            width: 95%;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
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
        <ul id="listaUL">
            </ul>
    </div>

    <div class="admin-panel">
        <h3 id="titulo-panel">Añadir / Editar Docente</h3>
        <input type="hidden" id="indice-edit" value="">
        <input type="text" id="nombre-in" placeholder="Nombre completo">
        <input type="text" id="foto-in" placeholder="Nombre de imagen (ej: miguel.png)">
        <button class="btn-guardar" onclick="procesarDatos()">Guardar Maestro</button>
    </div>

    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Inicio" style="width: 80px; height: auto;">
    </a>

    <script>
        // Datos iniciales
        const iniciales = [
            { nombre: "Miguel Delgado", foto: "miguel.png" },
            { nombre: "María Gómez", foto: "user.png" },
            { nombre: "Rodolfo Rojas", foto: "user.png" },
            { nombre: "Francisco Luis Juan", foto: "user.png" },
            { nombre: "Julio Chávez", foto: "user.png" },
            { nombre: "José Gamas", foto: "user.png" }
        ];

        let docentes = JSON.parse(localStorage.getItem("docentes_v4")) || iniciales;

        function dibujarLista() {
            const lista = document.getElementById("listaUL");
            lista.innerHTML = "";
            docentes.forEach((d, i) => {
                lista.innerHTML += `
                    <li>
                        <img src="imagenes/${d.foto}" class="foto-perfil" onerror="this.src='imagenes/user.png'">
                        <span class="nombre-texto">${d.nombre}</span>
                        <button class="btn-mini edit-btn" onclick="editar(${i})">editar</button>
                        <button class="btn-mini del-btn" onclick="borrar(${i})">borrar</button>
                    </li>
                `;
            });
        }

        function procesarDatos() {
            const nom = document.getElementById("nombre-in").value;
            let fot = document.getElementById("foto-in").value || "user.png";
            const idx = document.getElementById("indice-edit").value;

            if(nom === "") return alert("Escribe un nombre");

            fot = fot.replace("imagenes/", "");

            if(idx === "") {
                docentes.push({ nombre: nom, foto: fot });
            } else {
                docentes[idx] = { nombre: nom, foto: fot };
                document.getElementById("indice-edit").value = "";
                document.getElementById("titulo-panel").innerText = "Añadir / Editar Docente";
            }

            localStorage.setItem("docentes_v4", JSON.stringify(docentes));
            document.getElementById("nombre-in").value = "";
            document.getElementById("foto-in").value = "";
            dibujarLista();
        }

        function editar(i) {
            document.getElementById("nombre-in").value = docentes[i].nombre;
            document.getElementById("foto-in").value = docentes[i].foto;
            document.getElementById("indice-edit").value = i;
            document.getElementById("titulo-panel").innerText = "Editando: " + docentes[i].nombre;
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        }

        function borrar(i) {
            if(confirm("¿Borrar este docente?")) {
                docentes.splice(i, 1);
                localStorage.setItem("docentes_v4", JSON.stringify(docentes));
                dibujarLista();
            }
        }

        dibujarLista();
    </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>