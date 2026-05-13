<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'DOCENTES - Administración';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <h1>DOCENTES</h1>

    <div class="docentes">
        <h2>Lista de Docentes</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th style="width: 160px; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaBody">
                </tbody>
        </table>
    </div>

    <div class="admin-panel">
        <h3 id="panel-titulo" style="margin:0; color:#4B0082;">Gestionar Maestro</h3>
        <input type="hidden" id="edit-idx" value="">
        <input type="text" id="nombre-input" placeholder="Nombre completo del docente">
        <input type="text" id="foto-input" placeholder="Nombre de imagen (ej: carlos.png)">
        <button class="btn-save" onclick="guardarCambios()">Guardar en Lista</button>
    </div>

    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Inicio" style="width: 80px; height: auto;">
    </a>

    <script>
        // Lista original de tu código
        const iniciales = [
            { nombre: "Carlos Ramírez", foto: "user.png" },
            { nombre: "Fidel Rodríguez", foto: "user.png" },
            { nombre: "Alberto Chavoya", foto: "user.png" },
            { nombre: "Alma González", foto: "user.png" },
            { nombre: "José Aguilera", foto: "user.png" },
            { nombre: "María Estrada", foto: "user.png" }
        ];

        let docentes = JSON.parse(localStorage.getItem("docentes_final_v5")) || iniciales;

        function renderizar() {
            const tbody = document.getElementById("tablaBody");
            tbody.innerHTML = "";
            docentes.forEach((d, i) => {
                tbody.innerHTML += `
                    <tr>
                        <td>
                            <img src="imagenes/${d.foto}" class="foto-tabla" onerror="this.src='imagenes/user.png'">
                            ${d.nombre}
                        </td>
                        <td style="text-align: center;">
                            <button class="btn-acc btn-edit" onclick="cargarParaEditar(${i})">Editar</button>
                            <button class="btn-acc btn-del" onclick="eliminar(${i})">Borrar</button>
                        </td>
                    </tr>
                `;
            });
        }

        function guardarCambios() {
            const nom = document.getElementById("nombre-input").value;
            let fot = document.getElementById("foto-input").value || "user.png";
            const idx = document.getElementById("edit-idx").value;

            if (nom === "") return alert("Debes escribir un nombre");

            fot = fot.replace("imagenes/", "");

            if (idx === "") {
                docentes.push({ nombre: nom, foto: fot });
            } else {
                docentes[idx] = { nombre: nom, foto: fot };
                document.getElementById("edit-idx").value = "";
                document.getElementById("panel-titulo").innerText = "Gestionar Maestro";
            }

            localStorage.setItem("docentes_final_v5", JSON.stringify(docentes));
            limpiarForm();
            renderizar();
        }

        function cargarParaEditar(i) {
            document.getElementById("nombre-input").value = docentes[i].nombre;
            document.getElementById("foto-input").value = docentes[i].foto;
            document.getElementById("edit-idx").value = i;
            document.getElementById("panel-titulo").innerText = "Modificando a: " + docentes[i].nombre;
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        }

        function eliminar(i) {
            if (confirm("¿Seguro que quieres borrar a este docente?")) {
                docentes.splice(i, 1);
                localStorage.setItem("docentes_final_v5", JSON.stringify(docentes));
                renderizar();
            }
        }

        function limpiarForm() {
            document.getElementById("nombre-input").value = "";
            document.getElementById("foto-input").value = "";
        }

        renderizar();
    </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>