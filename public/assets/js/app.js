require('../css/main.css');
require('../css/app.scss');
window.$ = require('jquery');
require('bootstrap');
window.$.DataTable = require('datatables.net-bs4');
require('bootstrap/dist/css/bootstrap.min.css');
require('@popperjs/core');
require('select2');
require('bootstrap-select/js/bootstrap-select');
require('bootstrap-select/dist/css/bootstrap-select.css');
require('datatables.net-responsive-bs4');
require('@fortawesome/fontawesome-free');
require('../css/select2.css');
require('./dtLocales.js');

window.weaponStats = $('#weaponsStats');
window.headersIndex = [];

$('#weaponsStats thead tr th').each(function (indexCol, val) {
    window.headersIndex[indexCol] = val.textContent;
});

if (window.responsive === 1) {
    window.contentBody = $('#contentBody');
    window.weaponStatsDiv = $('#weaponStatsDiv');
    window.bResponsive = true;
    responsiveCol();
    window.columnsDefs = [
        {responsivePriority: 1, targets: 0},
        {responsivePriority: 1, targets: 1}
    ];
    window.dtSelect = [0, 4];
    window.dtDefaultSort = [[1, 'asc'], [0, 'asc']];
} else {
    window.bResponsive = false;
    window.columnsDefs = [];
    window.dtSelect = [0, 1];
    window.dtDefaultSort = [[15, 'asc'], [0, 'asc']];
}

window.lang = lang;
//CREATE DATATABLE
window.table = window.weaponStats.dataTable({
    select: true,
    responsive: window.bResponsive,
    columnDefs: window.columnsDefs,
    language: window.locales,
    order: window.dtDefaultSort,
    initComplete: function () {
        $('.select2JS').select2({language: window.lang});
        this.api().columns(window.dtSelect).every(function () {
            let collator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
            let column = this;
            let select = $('<select multiple class="form-control selectpicker customWidth"></select>')

            window.weaponStats.css('width', 'auto');
            //Footer (multi)select
            select.appendTo($(column.footer()).empty())
                .on('change', function () {
                    let val = '';
                    let first = true;
                    $.each($(this).val(), function (i, d) {
                        val = val + ((first === true) ? '' : '|') + $.fn.dataTable.util.escapeRegex(d);
                        first = false;
                    });

                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                });

            column.data().unique().sort(collator.compare).each(function (d, j) {
                select.append('<option value="' + d + '">' + d + '</option>')
            });
        });
        createColSelector();

        //ready to show
        $("#contentBody").css('display', 'table');
        $("#progress").hide();
    }
});

//RESET PARTICULAR CSS ON REDRAW
window.weaponStats.on('draw.dt', function () {
    //HightLight Time To Kill by selector .border1px
    $('#weaponsStats tbody tr td:nth-child(' + ($("#weaponsStats thead tr th.border1px").index() + 1) + ')')
        .addClass('border1px');
    $('#weaponsStats tbody tr').addClass(['cell100']);
});

// SHOW / HIDE COLUMNS
$('#colSelector').change(function () {
    let selected = $(this).val();
    $.each(window.headersIndex, function (a, val) {
        let visible = window.table.fnSettings().aoColumns[a].bVisible;
        if (!selected.includes(val) && !visible) {
            window.table.fnSetColumnVis(a, true);
        } else if (selected.includes(val) && visible) {
            window.table.fnSetColumnVis(a, false);
        }
    });
    window.weaponStats.css('width', 'auto');
});

// AJAX CALL
function submitForm() {
    let formSave = $('#form_save');
    formSave.addClass(['disabled', 'btn-outline-secondary']);
    formSave.removeClass('btn-secondary');
    let formSerialize = $(this).serialize();
    let order = window.table.fnSettings().aaSorting;
    console.log(order);
    //ajaxStatsUrl defined in twig template
    $.ajax({
        type: "POST", url: ajaxStatsUrl, data: formSerialize, success: function (response) {
            window.table.fnClearTable();
            let weapons = response.data;
            $.each(weapons, function () {
                let values = $.map($.makeArray(this)[0], function (value, key) {
                    return value;
                });
                window.table.fnAddData(values, false);
            });
            window.table.fnSettings().aaSorting = order;
            window.table.fnDraw();
            $('#message').text(response.message);
            formSave.removeClass(['btn-outline-secondary', 'disabled']);
            formSave.addClass('btn-secondary');
        }
    });
}

window.ajaxForm = $('#ajaxForm');
let inputForm = $('form :input');//.filter(":input")
console.log(inputForm);
$('form :input').each(function () {
    console.log('inputForm');
    $(this).change(function () {
        console.log('change');
        window.ajaxForm.submit();
    });
});

window.ajaxForm.submit(function (e) {
    let formSave = $('#form_save');
    formSave.addClass(['disabled', 'btn-outline-secondary']);
    formSave.removeClass('btn-secondary');
    e.preventDefault();
    let formSerialize = $(this).serialize();
    let order = window.table.fnSettings().aaSorting;
    console.log(order);
    //ajaxStatsUrl defined in twig template
    $.ajax({
        type: "POST", url: ajaxStatsUrl, data: formSerialize, success: function (response) {
            window.table.fnClearTable();
            let weapons = response.data;
            $.each(weapons, function () {
                let values = $.map($.makeArray(this)[0], function (value, key) {
                    return value;
                });
                window.table.fnAddData(values, false);
            });
            window.table.fnSettings().aaSorting = order;
            window.table.fnDraw();
            $('#message').text(response.message);
            formSave.removeClass(['btn-outline-secondary', 'disabled']);
            formSave.addClass('btn-secondary');
        }
    });
});

function createColSelector() {
    let appendTo = $('.dataTables_length').parent();
    let selectNbRow = $('.dataTables_length select');
    selectNbRow.removeClass(['custom-select', 'custom-select-sm', 'form-control', 'form-control-sm'])
        .addClass(['displayBlock', 'selectpicker', 'btselect', 'minWith60']);
    appendTo.removeClass(['col-sm-12', 'col-md-6']).addClass(['col']);
    $('#weaponsStats_filter').parent().removeClass(['col-sm-12', 'col-md-6']).addClass(['col', 'textRight'])

    let select = '<select multiple id="colSelector" class="displayBlock selectpicker btselect">';
    $.each(window.headersIndex, function (index, value) {
        select = select + '<option class="colToHide" value="' + value + '">' + value + '</option>'
    });
    select = select + '</select>';

    let colSelector = '<div class="col textCenter" style="margin-top: -5px; padding: 0;">' +
        '<div class="dataTables_length" id="weaponsStats_selector">' +
        '<label>' + window.translations.selectToHide + select + '</label></div></div>';
    $(colSelector).insertAfter(appendTo);

    window.weaponStats.css('min-width', '780px;');
    window.weaponStats.css('width', 'auto');
}

function responsiveCol() {
    if (window.bResponsive === true) {
        window.contentBody.addClass('responsiveFrame');
        window.weaponStatsDiv.addClass('responsiveFrame');
        $('#weaponsStats_wrapper').addClass('responsiveFrame');
        $('#weaponsStats').addClass('tableWidth100');
    } else {
        window.contentBody.removeClass('responsiveFrame');
        window.weaponStatsDiv.removeClass('responsiveFrame');
        $('#weaponsStats_wrapper').removeClass('responsiveFrame');
        $('#weaponsStats').removeClass('tableWidth100');
    }
}