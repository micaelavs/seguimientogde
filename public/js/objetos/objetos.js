$(document).ready(function () {
    var _table = $('.objetos').DataTable({
        language: {
            url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
            search: '_INPUT_',
            searchPlaceholder: 'Ingrese búsqueda'
        },
        autoWidth: false,
        bFilter: true,
        info: true,
        columnDefs: [
            { targets: 0, width: '35%' },
            { targets: 0, width: '35%' },
            { targets: 1, width: '30%' }
        ],
        order: [[0, 'asc']]
    });

});