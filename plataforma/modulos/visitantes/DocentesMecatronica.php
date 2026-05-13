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
                    <th>Nombre del Docente</th>
                    <th style="width: 180px; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody id="listaCuerpo">
                </tbody>
        </table>
    </div>

    <div class="admin-panel">
        <h3 id="panel-titulo" style="margin: 0 0 10px 0; color: #4B0082;">Añadir / Editar Maestro</h3>
        <input type="hidden" id="edit-index" value="">
        <input type="text" id="input-nombre" placeholder="Nombre completo">
        <input type="text" id="input-foto" placeholder="Nombre de imagen (ej: miguel.png)">
        <button class="btn-guardar" onclick="guardarDocente()">Guardar en la Lista</button>
    </div>

    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Inicio" style="width: 80px; height: auto;">
    </a>

    <script>
        // Lista inicial de maestros
        const iniciales = [
            { nombre: "Miguel Ángel Delgado", foto: "miguel.png" },
            { nombre: "María Gómez", foto: "user.png" },
            { nombre: "Rodolfo Rojas", foto: "user.png" },
            { nombre: "José Hernández", foto: "user.png" },
            { nombre: "Juan Desales", foto: "user.png" },
            { nombre: "José Gamas", foto: "user.png" }
        ];

        let docentes = JSON.parse(localStorage.getItem("db_docentes_kiosco")) || iniciales;

        function render() {
            const cuerpo = document.getElementById("listaCuerpo");
            cuerpo.innerHTML = "";
            docentes.forEach((d, i) => {
                cuerpo.innerHTML += `
                    <tr>
                        <td>
                            <img src="imagenes/${d.foto}" class="foto-lista" onerror="this.src='imagenes/user.png'">
                            ${d.nombre}
                        </td>
                        <td style="text-align: center;">
                            <button class="btn-accion btn-editar" onclick="prepararEdicion(${i})">Editar</button>
                            <button class="btn-accion btn-borrar" onclick="eliminarDocente(${i})">Borrar</button>
                        </td>
                    </tr>
                `;
            });
        }

        function guardarDocente() {
            const nombre = document.getElementById("input-nombre").value;
            let foto = document.getElementById("input-foto").value || "user.png";
            const index = document.getElementById("edit-index").value;

            if (nombre === "") return alert("Por favor escribe el nombre");

            // Limpiar ruta si escriben "imagenes/"
            foto = foto.replace("imagenes/", "");

            if (index === "") {
                docentes.push({ nombre, foto });
            } else {
                docentes[index] = { nombre, foto };
                document.getElementById("edit-index").value = "";
                document.getElementById("panel-titulo").innerText = "Añadir Maestro";
            }

            localStorage.setItem("db_docentes_kiosco", JSON.stringify(docentes));
            limpiar();
            render();
        }

        function prepararEdicion(i) {
            document.getElementById("input-nombre").value = docentes[i].nombre;
            document.getElementById("input-foto").value = docentes[i].foto;
            document.getElementById("edit-index").value = i;
            document.getElementById("panel-titulo").innerText = "Editando a: " + docentes[i].nombre;
            // Desplazar suavemente al panel de abajo
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        }

        function eliminarDocente(i) {
            if (confirm("¿Seguro que quieres eliminar este docente?")) {
                docentes.splice(i, 1);
                localStorage.setItem("db_docentes_kiosco", JSON.stringify(docentes));
                render();
            }
        }

        function limpiar() {
            document.getElementById("input-nombre").value = "";
            document.getElementById("input-foto").value = "";
        }

        render();
    </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>