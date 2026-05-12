document.addEventListener('DOMContentLoaded', function () {
    if (typeof $ === 'undefined') return;

    $('#example').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        language: { url: '//cdn.datatables.net/plug-ins/2.2.2/i18n/es-ES.json' }
    });

    $(document).on('click', '.data-row', function () {
        var href = $(this).data('href');
        if (href) window.location.href = href;
    });
});
