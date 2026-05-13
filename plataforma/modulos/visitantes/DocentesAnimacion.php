<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'DOCENTES - Sistemas';
$tsj_extra_css  = ['style.css'];

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