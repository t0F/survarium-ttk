require('../css/main.css');
require('../css/util.css');

window.$ = require('jquery');
var dt = require('datatables.net');
window.$.DataTable = dt;

var boostrap = require('bootstrap')
window.$.boostrap = boostrap;

var boostrapCss = require('bootstrap/dist/css/bootstrap.min.css');
window.$.boostrapCss = boostrapCss;

var select2 = require('select2');
window.$.DataTable = select2;
var animate = require('animate');
window.$.DataTable = animate;


//var fontAwesome = require('font-awesome');
//var popper = require('popper');
//.addEntry('perfect-scrollbar', './assets/vendor/perfect-scrollbar/perfect-scrollbar.css"');
//var perfectScrollbar = require('perfect-scrollbar');

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