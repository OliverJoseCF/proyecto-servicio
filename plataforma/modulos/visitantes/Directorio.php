<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Directorio de Sistemas - Tec Chapala';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Inicio" style="width: 80px; height: auto;">
    </a>

    <div class="container">
        <h2>Directorio Institucional</h2>
        
        <button class="btn btn-admin-toggle" onclick="toggleAdmin()">Modo Administrador (Añadir/Editar)</button>

        <div id="admin-panel">
            <h3>Panel de Control</h3>
            <input type="hidden" id="index-edit">
            <div class="grid-inputs">
                <input type="text" id="nombre" placeholder="Nombre completo">
                <input type="text" id="puesto" placeholder="Puesto (ej. Docente)">
                <input type="text" id="telefono" placeholder="Teléfono">
                <input type="email" id="correo" placeholder="Correo electrónico">
                <input type="text" id="foto" placeholder="Nombre de imagen (ej: profe.jpg)">
            </div>
            <button class="btn btn-save" onclick="guardarDato()">Guardar / Actualizar Registro</button>
        </div>

        <div class="tabla-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Puesto</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th class="acciones-col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="cuerpo-tabla">
                    </tbody>
            </table>
        </div>
    </div>

    <script>
        // LISTA INICIAL DE MAESTROS QUE ME PASASTE
        const datosIniciales = [
            { foto: 'imagenes/user.png', nombre: 'Miguel Angel Delgado Lopez', puesto: 'Docente Sistemas', telefono: 'S/N', correo: 'miguel.delgado@chapala.tecnm.mx' },
            { foto: 'imagenes/user.png', nombre: 'Julio Cesar Chavez Novoa', puesto: 'Docente Sistemas', telefono: 'S/N', correo: 'julio.chavez@chapala.tecnm.mx' },
            { foto: 'imagenes/user.png', nombre: 'Carmen Leticia Salcedo Quevedo', puesto: 'Docente Sistemas', telefono: 'S/N', correo: 'carmen.salcedo@chapala.tecnm.mx' },
            { foto: 'imagenes/user.png', nombre: 'Jose Jorge Hernandez Ochoa', puesto: 'Docente Sistemas', telefono: 'S/N', correo: 'jorge.hernandez@chapala.tecnm.mx' },
            { foto: 'imagenes/user.png', nombre: 'Francisco Javier Gonzales Siordia', puesto: 'Docente Sistemas', telefono: 'S/N', correo: 'francisco.gonzales@chapala.tecnm.mx' },
            { foto: 'imagenes/user.png', nombre: 'Jose Guadalupe Gamas Gamas', puesto: 'Docente Sistemas', telefono: 'S/N', correo: 'jose.gamas@chapala.tecnm.mx' }
        ];

        // Se intenta cargar de LocalStorage, si no hay nada, se usan los datosIniciales
        let directorio = JSON.parse(localStorage.getItem('db_directorio')) || datosIniciales;

        function render() {
            const tabla = document.getElementById('cuerpo-tabla');
            const isAdmin = document.getElementById('admin-panel').style.display === 'block';
            const colsAcciones = document.querySelectorAll('.acciones-col');
            
            // Mostrar u ocultar columna de acciones
            colsAcciones.forEach(el => el.style.display = isAdmin ? 'table-cell' : 'none');

            tabla.innerHTML = '';
            directorio.forEach((item, i) => {
                tabla.innerHTML += `
                    <tr>
                        <td><img src="${item.foto}" class="foto-perfil" onerror="this.src='imagenes/user.png'"></td>
                        <td><b>${item.nombre}</b></td>
                        <td>${item.puesto}</td>
                        <td>${item.telefono}</td>
                        <td>${item.correo}</td>
                        <td class="acciones-col" style="display: ${isAdmin ? 'table-cell' : 'none'}">
                            <button class="btn btn-edit" onclick="prepararEdicion(${i})">Editar</button>
                            <button class="btn btn-delete" onclick="eliminar(${i})">Borrar</button>
                        </td>
                    </tr>
                `;
            });
        }

        function toggleAdmin() {
            const panel = document.getElementById('admin-panel');
            panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
            render();
        }

        function guardarDato() {
            const id = document.getElementById('index-edit').value;
            let fotoInput = document.getElementById('foto').value;

            // OPCIÓN 1: Lógica para autocompletar la ruta de la carpeta imagenes
            if (fotoInput !== "" && !fotoInput.includes("/") && !fotoInput.startsWith("http")) {
                fotoInput = "imagenes/" + fotoInput;
            }

            const nuevo = {
                nombre: document.getElementById('nombre').value,
                puesto: document.getElementById('puesto').value,
                telefono: document.getElementById('telefono').value, 
                correo: document.getElementById('correo').value,
                foto: fotoInput || 'imagenes/user.png'
            };

            if(nuevo.nombre === "") return alert("Debes poner al menos el nombre");

            if(id === "") {
                directorio.push(nuevo); // Añadir nuevo registro
            } else {
                directorio[id] = nuevo; // Actualizar registro existente
                document.getElementById('index-edit').value = "";
            }

            actualizarStorage();
        }

        function eliminar(i) {
            if(confirm("¿Estás seguro de eliminar a este docente del directorio?")) {
                directorio.splice(i, 1);
                actualizarStorage();
            }
        }

        function prepararEdicion(i) {
            const m = directorio[i];
            document.getElementById('nombre').value = m.nombre;
            document.getElementById('puesto').value = m.puesto;
            document.getElementById('telefono').value = m.telefono;
            document.getElementById('correo').value = m.correo;
            document.getElementById('foto').value = m.foto;
            document.getElementById('index-edit').value = i;
            window.scrollTo(0,0); // Sube la pantalla para ver el formulario
        }

        function actualizarStorage() {
            localStorage.setItem('db_directorio', JSON.stringify(directorio));
            limpiarForm();
            render();
        }

        function limpiarForm() {
            document.getElementById('nombre').value = '';
            document.getElementById('puesto').value = '';
            document.getElementById('telefono').value = '';
            document.getElementById('correo').value = '';
            document.getElementById('foto').value = '';
            document.getElementById('index-edit').value = '';
        }

        // Primera carga de la tabla al abrir la página
        render();
    </script>


<?php require_once __DIR__ . '/../../shared/footer.php'; ?>