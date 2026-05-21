$(document).ready(function () {
  'use strict';

  var table = $('#example').DataTable({
    dom: '<"dt-top"<"dt-length"l><"dt-buttons-container"B><"dt-search"f>>rt<"dt-bottom"ip>',
    responsive: true,
    language: {
      search:     "Buscar:",
      lengthMenu: "Mostrar _MENU_ registros",
      info:       "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoEmpty:  "Mostrando 0 a 0 de 0 registros",
      emptyTable: "No se encontraron convenios.",
      zeroRecords:"No se encontraron convenios.",
      paginate: {
        first: "Primero", last: "Último",
        next: "Siguiente", previous: "Anterior"
      }
    },
    buttons: [
      'excel',
      'pdf',
      { extend: 'print', text: 'Imprimir' }
    ]
  });

  /* Navegar a la empresa al hacer clic en una fila (excepto acciones) */
  $(document).on('click', '.data-row td:not(.actions-cell)', function () {
    var href = $(this).parent().data('href');
    if (href) window.location.href = href;
  });

  /* Auto-ocultar el mensaje flash de éxito tras 6 s.
     (Antes era un <script> inline en lista.php; movido aquí porque la CSP
     del módulo Convenios no permite 'unsafe-inline' en script-src.) */
  var flash = document.getElementById('flashMsg');
  if (flash) {
    setTimeout(function () {
      flash.style.transition = 'opacity 0.5s';
      flash.style.opacity = '0';
      setTimeout(function () { flash.remove(); }, 500);
    }, 6000);
  }
});
