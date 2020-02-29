require('../css/app.scss');
require('../css/main.css');
require('../css/util.css');


window.$ = require('jquery');
require('bootstrap')
let datatablesbs4 = require('datatables.net-bs4')
window.$.DataTable = datatablesbs4;
require('bootstrap/dist/css/bootstrap.min.css');
require('@popperjs/core');
require('select2');
require('bootstrap-select/js/bootstrap-select');
require('bootstrap-select/dist/css/bootstrap-select.css');
require('@fortawesome/fontawesome-free');
require('animate');

require('../css/select2.css');

window.weaponStats = $('#weaponsStats');
window.headersIndex = [];

$('#weaponsStats thead tr th').each(function (indexCol, val) {
    window.headersIndex[indexCol] = val.textContent;
});



function createColSelector() {
    let appendTo = $('.dataTables_length').parent();
    appendTo.removeClass(['col-sm-12', 'col-md-6']).addClass(['col']);
    $('#weaponsStats_filter').parent().removeClass(['col-sm-12', 'col-md-6']).addClass(['col', 'textRight'])

    let select = '<select multiple id="colSelector" class="displayBlock selectpicker btselect">';
    $.each(window.headersIndex, function (index, value) {
        select = select + '<option class="colToHide" value="' + value + '">' + value + '</option>'
    });
    select = select + '</select>';

    let newHtml = '<div class="col textCenter" style="margin-top: -5px; padding: 0;">' +
        '<div class="dataTables_length" id="weaponsStats_selector">' +
        '<label>Select to hide column(s) (use ctrl):' + select + '</label>' +
        '</div>' +
        '</div>';
    $(newHtml).insertAfter(appendTo);

    /*$('#colSelector').change(function (e) {
        let selected = $(e.target).val();

        $.each(window.headersIndex, function (a, val) {
            window.table.fnSetColumnVis(a, (!selected.includes(val)));
        });
        $('#weaponsStats').css('width', 'auto');
    });*/
    window.weaponStats.css('min-width', '780px;');
    window.weaponStats.css('width', 'auto');
}

//CREATE DATATABLE
window.table = window.weaponStats.dataTable({
    select: true,
    initComplete: function () {
        $('.select2JS').select2();

        this.api().columns([0, 1]).every(function (indexCol) {
            let collator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
            let column = this;

            let select = $('<select multiple class="form-control selectpicker customWidth"></select>')
            window.weaponStats.css('width', 'auto');

            select.appendTo($(column.footer()).empty())
                .on('change', function () {
                    let val = '';
                    let first = true;
                    $.each($(this).val(), function (i, d) {
                        val = val +((first === true) ? '': '|') + $.fn.dataTable.util.escapeRegex( d );
                        first = false;
                    });

                    column
                        .search(val ? '^' + val + '$' : '', true, false)
                        .draw();
                });

            column.data().unique().sort(collator.compare).each(function (d, j) {
                select.append('<option value="' + d + '">' + d + '</option>')
            });
        });
        createColSelector();

        $("#contentBody").css('display', 'table');
        $("#progress").hide();
       // $('.btselect').selectpicker();
    }
});

window.weaponStats.on('draw.dt', function () {
    //HightLight Time To Kill by selector .border1px
    $('#weaponsStats tbody tr td:nth-child('+($("#weaponsStats thead tr th.border1px").index()+1)+')')
        .addClass('border1px');
    $('#weaponsStats tbody tr').addClass('column');
});

$('#colSelector').change(function() {
    let selected = $(this).val();
    $.each(window.headersIndex, function (a, val) {
        let visible = window.table.fnSettings().aoColumns[a].bVisible;
        if(!selected.includes(val) && !visible) {
                window.table.fnSetColumnVis(a, true);
        } else if(selected.includes(val) && visible) {
            window.table.fnSetColumnVis(a, false);
        }
    });
    window.weaponStats.css('width', 'auto');
});

$('#ajaxForm').submit(function (e) {
    $('#form_save').addClass('disabled');
    $('#form_save').removeClass('btn-secondary');
    $('#form_save').addClass('btn-outline-secondary');
    e.preventDefault();
    let formSerialize = $(this).serialize();
    //ajaxStatsUrl defined in twig template
    $.post(ajaxStatsUrl, formSerialize, function (response) {
        window.table.fnClearTable();
        let weapons = response.data;
        $.each(weapons, function () {
            let values = $.map($.makeArray(this)[0], function (value, key) {
                return value;
            });
            window.table.fnAddData(values, false);
        });
        window.table.fnDraw();
        $('#message').text(response.message);
        $('#form_save').removeClass('btn-outline-secondary');
        $('#form_save').addClass('btn-secondary');
        $('#form_save').removeClass('disabled');
    }, 'JSON');
});