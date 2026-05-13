<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'DOCENTES - Gestión';
$tsj_extra_css  = ['style.css'];

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