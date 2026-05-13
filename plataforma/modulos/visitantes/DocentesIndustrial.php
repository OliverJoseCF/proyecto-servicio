<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'DOCENTES - Sistemas';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <h1>DOCENTES</h1>

    <div class="docentes">
        <h2>Lista de Docentes</h2>
        <table>
            <thead>
                <tr>
                    <th>Docente</th>
                    <th style="width: 150px;">Acciones</th>
                </tr>
            </thead>
            <tbody id="docentesList">
                </tbody>
        </table>
    </div>

    <div class="admin-panel">
        <h3 id="panel-title">Añadir Nuevo Docente</h3>
        <input type="hidden" id="edit-index" value="">
        <input type="text" id="nuevo-nombre" placeholder="Nombre del docente">
        <input type="text" id="nueva-foto" placeholder="Nombre de imagen (ej: miguel.png)">
        <button class="btn-save" onclick="guardarDocente()">Guardar Cambios</button>
    </div>

    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Inicio" style="width: 80px; height: auto;">
    </a>

    <script>
        // Lista inicial cargada por defecto
        const iniciales = [
            { nombre: "Miguel Ángel Delgado", foto: "miguel.png" },
            { nombre: "Alberto Chavolla", foto: "user.png" },
            { nombre: "Francisco", foto: "user.png" },
            { nombre: "Julio César Chávez", foto: "user.png" },
            { nombre: "Francisco Javier", foto: "user.png" },
            { nombre: "Edgar Martínez", foto: "user.png" },
            { nombre: "José Jorge", foto: "user.png" }
        ];

        let docentes = JSON.parse(localStorage.getItem("docentes_db")) || iniciales;

        function render() {
            const list = document.getElementById("docentesList");
            list.innerHTML = "";
            docentes.forEach((docente, i) => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>
                        <img src="imagenes/${docente.foto}" class="foto-lista" onerror="this.src='imagenes/user.png'">
                        ${docente.nombre}
                    </td>
                    <td>
                        <button class="btn-tabla btn-edit" onclick="cargarEdicion(${i})">Editar</button>
                        <button class="btn-tabla btn-delete" onclick="borrarDocente(${i})">Borrar</button>
                    </td>
                `;
                list.appendChild(tr);
            });
        }

        function guardarDocente() {
            const nombre = document.getElementById("nuevo-nombre").value;
            let foto = document.getElementById("nueva-foto").value || "user.png";
            const index = document.getElementById("edit-index").value;

            if (nombre === "") return alert("Escribe un nombre");

            // Limpiar si el usuario escribe la ruta completa
            foto = foto.replace("imagenes/", "");

            if (index === "") {
                docentes.push({ nombre, foto });
            } else {
                docentes[index] = { nombre, foto };
                document.getElementById("edit-index").value = "";
                document.getElementById("panel-title").innerText = "Añadir Nuevo Docente";
            }

            actualizarStorage();
            limpiar();
        }

        function cargarEdicion(i) {
            document.getElementById("nuevo-nombre").value = docentes[i].nombre;
            document.getElementById("nueva-foto").value = docentes[i].foto;
            document.getElementById("edit-index").value = i;
            document.getElementById("panel-title").innerText = "Editando a: " + docentes[i].nombre;
            // Bajar automáticamente al panel de edición
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        }

        function borrarDocente(i) {
            if (confirm("¿Seguro que quieres eliminar este registro?")) {
                docentes.splice(i, 1);
                actualizarStorage();
            }
        }

        function actualizarStorage() {
            localStorage.setItem("docentes_db", JSON.stringify(docentes));
            render();
        }

        function limpiar() {
            document.getElementById("nuevo-nombre").value = "";
            document.getElementById("nueva-foto").value = "";
        }

        render();
    </script>


<?php require_once __DIR__ . '/../../shared/footer.php'; ?>