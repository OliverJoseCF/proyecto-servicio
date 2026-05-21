<?php
$tsj_module    = 'visitantes';
$tsj_title     = 'Directorio Institucional';
$tsj_extra_css = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

<main id="main">
  <a href="index.php" class="top-right" aria-label="Volver al menú principal">
    <img src="imagenes/casa.png" alt="">
  </a>

  <h1 class="vis-page-title">Directorio Institucional</h1>

  <div class="container">
    <div class="tabla-scroll">
      <table>
        <thead>
          <tr>
            <th scope="col">Foto</th>
            <th scope="col">Nombre</th>
            <th scope="col">Puesto</th>
            <th scope="col">Teléfono</th>
            <th scope="col">Correo</th>
          </tr>
        </thead>
        <tbody id="cuerpo-tabla">
          <tr>
            <td colspan="5" style="text-align:center;padding:2rem;color:#9ca3af;">
              Cargando directorio…
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<script>
(function () {
  'use strict';

  /* Avatar placeholder embebido (SVG data-URI): evita depender de un archivo
     que puede no existir y previene imágenes rotas. */
  var PLACEHOLDER = 'data:image/svg+xml,' + encodeURIComponent(
    '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48">' +
    '<rect width="48" height="48" fill="#e5e7eb"/>' +
    '<circle cx="24" cy="19" r="8" fill="#9ca3af"/>' +
    '<path d="M8 44c0-9 7-14 16-14s16 5 16 14z" fill="#9ca3af"/></svg>'
  );

  var directorio = [
    { foto: 'imagenes/miguel.png', nombre: 'Miguel Ángel Delgado López',       departamento: 'Sistemas Computacionales', telefono: 'S/N', correo: 'miguel.delgado@chapala.tecmm.edu.mx' },
    { foto: 'imagenes/julio.png',  nombre: 'Julio César Chávez Novoa',          departamento: 'Sistemas Computacionales', telefono: 'S/N', correo: 'julio.chavez@chapala.tecmm.edu.mx' },
    { foto: 'imagenes/carmen.png', nombre: 'Carmen Leticia Salcedo Quevedo',    departamento: 'Sistemas Computacionales', telefono: 'S/N', correo: 'carmen.salcedo@chapala.tecmm.edu.mx' },
    { foto: 'imagenes/jorge.png',  nombre: 'José Jorge Hernández Ochoa',        departamento: 'Sistemas Computacionales', telefono: 'S/N', correo: 'jorge.hernandez@chapala.tecmm.edu.mx' },
    { foto: PLACEHOLDER,           nombre: 'Francisco Javier González Siordia', departamento: 'Sistemas Computacionales', telefono: 'S/N', correo: 'francisco.gonzales@chapala.tecmm.edu.mx' },
    { foto: 'imagenes/gamas.png',  nombre: 'José Guadalupe Gamas Gamas',        departamento: 'Sistemas Computacionales', telefono: 'S/N', correo: 'jose.gamas@chapala.tecmm.edu.mx' }
  ];

  var tbody = document.getElementById('cuerpo-tabla');
  tbody.innerHTML = '';

  directorio.forEach(function (item) {
    var tr = document.createElement('tr');

    var tdFoto = document.createElement('td');
    var img    = document.createElement('img');
    img.src       = item.foto;
    img.alt       = item.nombre;
    img.className = 'foto-tabla';
    img.onerror   = function () { this.onerror = null; this.src = PLACEHOLDER; };
    tdFoto.appendChild(img);

    var tdNombre = document.createElement('td');
    tdNombre.textContent = item.nombre;
    tdNombre.style.fontWeight = '600';

    var tdDep    = document.createElement('td'); tdDep.textContent    = item.departamento;
    var tdTel    = document.createElement('td'); tdTel.textContent    = item.telefono;
    var tdCorreo = document.createElement('td');
    var link = document.createElement('a');
    link.href = 'mailto:' + item.correo;
    link.textContent = item.correo;
    link.style.color = '#32129a';
    tdCorreo.appendChild(link);

    tr.appendChild(tdFoto); tr.appendChild(tdNombre); tr.appendChild(tdDep);
    tr.appendChild(tdTel); tr.appendChild(tdCorreo);
    tbody.appendChild(tr);
  });
})();
</script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
