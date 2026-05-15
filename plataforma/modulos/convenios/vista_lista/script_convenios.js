document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  if (typeof $ === 'undefined') return;

  $('#example').DataTable({
    dom: 'Bfrtip',
    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
    language: {
      url: 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/es-ES.json',
      emptyTable: 'No se encontraron convenios para esta selección.'
    }
  });

  /* Filas clickables */
  $(document).on('click', '.data-row', function () {
    var href = $(this).data('href');
    if (href) window.location.href = href;
  });
});
