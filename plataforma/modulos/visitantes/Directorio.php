<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Directorio Institucional - TSJ Chapala';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>

    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Inicio" style="width: 80px; height: auto;">
    </a>

    <div class="container">
        <h2>Directorio Institucional</h2>

        <div class="tabla-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Puesto</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                    </tr>
                </thead>
                <tbody id="cuerpo-tabla"></tbody>
            </table>
        </div>
    </div>

    <script>
        const directorio = [
            { foto: 'imagenes/miguel.png',    nombre: 'Miguel Angel Delgado Lopez',        puesto: 'Docente Sistemas',    telefono: 'S/N', correo: 'miguel.delgado@chapala.tecnm.mx' },
            { foto: 'imagenes/julio.png',     nombre: 'Julio Cesar Chavez Novoa',           puesto: 'Docente Sistemas',    telefono: 'S/N', correo: 'julio.chavez@chapala.tecnm.mx' },
            { foto: 'imagenes/carmen.png',    nombre: 'Carmen Leticia Salcedo Quevedo',     puesto: 'Docente Sistemas',    telefono: 'S/N', correo: 'carmen.salcedo@chapala.tecnm.mx' },
            { foto: 'imagenes/jorge.png',     nombre: 'Jose Jorge Hernandez Ochoa',         puesto: 'Docente Sistemas',    telefono: 'S/N', correo: 'jorge.hernandez@chapala.tecnm.mx' },
            { foto: 'imagenes/user.png',      nombre: 'Francisco Javier Gonzalez Siordia',  puesto: 'Docente Sistemas',    telefono: 'S/N', correo: 'francisco.gonzales@chapala.tecnm.mx' },
            { foto: 'imagenes/gamas.png',     nombre: 'Jose Guadalupe Gamas Gamas',         puesto: 'Docente Sistemas',    telefono: 'S/N', correo: 'jose.gamas@chapala.tecnm.mx' }
        ];

        function render() {
            const tbody = document.getElementById('cuerpo-tabla');
            tbody.innerHTML = '';
            directorio.forEach(item => {
                const tr = document.createElement('tr');

                const foto = document.createElement('td');
                const img  = document.createElement('img');
                img.src     = item.foto;
                img.alt     = item.nombre;
                img.className = 'foto-perfil';
                img.onerror = function() { this.src = 'imagenes/user.png'; };
                foto.appendChild(img);

                const nombre   = document.createElement('td'); const b = document.createElement('b'); b.textContent = item.nombre; nombre.appendChild(b);
                const puesto   = document.createElement('td'); puesto.textContent   = item.puesto;
                const telefono = document.createElement('td'); telefono.textContent = item.telefono;
                const correo   = document.createElement('td'); correo.textContent   = item.correo;

                tr.appendChild(foto); tr.appendChild(nombre); tr.appendChild(puesto); tr.appendChild(telefono); tr.appendChild(correo);
                tbody.appendChild(tr);
            });
        }

        render();
    </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
