<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Lista de Docentes - Sistemas';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Inicio" style="width: 70px;">
    </a>

    <h1>DOCENTES DE SISTEMAS</h1>

    <div class="tabla-container">
        <table>
            <thead>
                <tr>
                    <th>Docente</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody id="lista-docentes">
                </tbody>
        </table>
    </div>

    <div class="admin-panel">
        <h3>⚙️ Panel de Gestión (Añadir o Editar)</h3>
        <input type="hidden" id="edit-index" value="">
        <div class="form-grid">
            <input type="text" id="nombre-input" placeholder="Nombre completo del docente">
            <input type="text" id="foto-input" placeholder="Nombre de foto (ej: miguel.png)">
            <button class="btn btn-save" id="btn-main" onclick="guardarDocente()">✅ Guardar</button>
        </div>
    </div>

    <script>
        // LISTA DE MAESTROS
        const iniciales = [
            { nombre: "Miguel Ángel Delgado López", foto: "miguel.png" },
            { nombre: "Alberto Chavolla", foto: "user.png" },
            { nombre: "Francisco Javier González", foto: "user.png" },
            { nombre: "Julio César Chávez Novoa", foto: "user.png" },
            { nombre: "Edgar Martínez", foto: "user.png" },
            { nombre: "José Jorge Hernández Ochoa", foto: "user.png" },
            { nombre: "Carmen Leticia Salcedo", foto: "user.png" },
            { nombre: "José Guadalupe Gamas", foto: "user.png" }
        ];

        let docentes = JSON.parse(localStorage.getItem("docentes_lista_final")) || iniciales;

        function render() {
            const tbody = document.getElementById("lista-docentes");
            tbody.innerHTML = "";

            docentes.forEach((d, i) => {
                tbody.innerHTML += `
                    <tr>
                        <td>
                            <img src="imagenes/${d.foto}" class="foto-mini" onerror="this.src='imagenes/user.png'">
                            <b>${d.nombre}</b>
                        </td>
                        <td style="text-align: center;">
                            <button class="btn btn-edit" onclick="prepararEdicion(${i})">Editar</button>
                            <button class="btn btn-delete" onclick="borrar(${i})">Borrar</button>
                        </td>
                    </tr>
                `;
            });
        }

        function guardarDocente() {
            const nombre = document.getElementById("nombre-input").value;
            let foto = document.getElementById("foto-input").value || "user.png";
            const index = document.getElementById("edit-index").value;

            if (nombre === "") return alert("Escribe el nombre");

            // Limpiar el nombre de la foto
            foto = foto.replace("imagenes/", "");

            if (index === "") {
                docentes.push({ nombre, foto });
            } else {
                docentes[index] = { nombre, foto };
                document.getElementById("edit-index").value = "";
                document.getElementById("btn-main").textContent = "Guardar";
            }

            localStorage.setItem("docentes_lista_final", JSON.stringify(docentes));
            limpiar();
            render();
        }

        function prepararEdicion(i) {
            document.getElementById("nombre-input").value = docentes[i].nombre;
            document.getElementById("foto-input").value = docentes[i].foto;
            document.getElementById("edit-index").value = i;
            document.getElementById("btn-main").textContent = "Actualizar";
            // Desplazar hacia abajo para que vean el panel de edición
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        }

        function borrar(i) {
            if (confirm("¿Seguro que quieres eliminar este docente?")) {
                docentes.splice(i, 1);
                localStorage.setItem("docentes_lista_final", JSON.stringify(docentes));
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