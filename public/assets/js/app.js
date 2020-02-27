require('../css/app.scss');
require('../css/main.css');
require('../css/util.css');


window.$ = require('jquery');
var boostrap = require('bootstrap')
var datatablesbs4 = require('datatables.net-bs4')
window.$.DataTable = datatablesbs4;
require('bootstrap/dist/css/bootstrap.min.css');
require('@popperjs/core');
require('select2');
require('@fortawesome/fontawesome-free');
require('animate');

require('../css/select2.css');

$(document).ready(function () {
    $('.select2JS').select2();
});

table = $('#weaponsStats').DataTable({
    select: true,

    initComplete: function () {
        this.api().columns().every(function () {
            var collator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
            var column = this;
            var select = $('<select class="form-control"><option value=""></option></select>')
                .appendTo($(column.footer()).empty())
                .on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex(
                        $(this).val()
                    );

                    column
                        .search(val ? '^' + val + '$' : '', true, false)
                        .draw();
                });

            column.data().unique().sort(collator.compare).each(function (d, j) {
                select.append('<option value="' + d + '">' + d + '</option>')
            });
        });
    }
});
