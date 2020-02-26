require('../css/main.css');
require('../css/util.css');

window.$ = require('jquery');
var dt = require('datatables.net');
window.$.DataTable = dt;
var boostrap = require('bootstrap')
var boostrapCss = require('bootstrap/dist/css/bootstrap.min.css');
var select2 = require('@popperjs/core');
var select2 = require('select2');
var fontawesome = require('@fortawesome/fontawesome-free');
var animate = require('animate');

$(document).ready(function () {
    $('.select2JS').select2();
});

table = $('#weaponsStats').DataTable({
    select: true,
    initComplete: function () {
        this.api().columns().every(function () {
            var collator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
            var column = this;
            var select = $('<select><option value=""></option></select>')
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