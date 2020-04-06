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
let CanvasJSChart = require('./jquery.canvasjs.min');

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
        {responsivePriority: 1, targets: 2},
        {targets: 1, render: function (data, type, row, meta) {
                if(data === "/assets/img/weapons/") return "";
                return '<div data-toggle="tooltip" title="<img src='+data+' />" onclick="window.showIcon(this);" class="divWIco"><img class="wIco" src="'+data+'" /></div>'
            }
        },
    ];
    window.dtSelect = [0, 5];
    window.dtDefaultSort = [[2, 'asc'], [0, 'asc']];
} else {
    window.bResponsive = false;
    window.columnsDefs = [{targets: 1, render: function (data, type, row, meta) {
            if(data === "assets/img/weapons/") return "";
            return '<div data-toggle="tooltip" title="<img src='+data+' />" onclick="window.showIcon(this);" class="divWIco"><img class="wIco" src="'+data+'" /></div>'
        }
    },];
    window.dtSelect = [0, 2];
    window.dtDefaultSort = [[16, 'asc'], [0, 'asc']];
}

if ($(window).width() > 400) {
    window.dtPagination = 'simple_numbers';
} else {
    window.dtPagination = 'numbers';
}

window.lang = lang;
//CREATE DATATABLE
window.table = window.weaponStats.dataTable({
    select: true,
    responsive: window.bResponsive,
    columnDefs: window.columnsDefs,
    language: window.locales,
    order: window.dtDefaultSort,
    pagingType: window.dtPagination,
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

        const spanRange = $('#spanRange');
        spanRange.prev().append(spanRange).addClass('displayBlock');
        const valueRange = $('#form_range');
        spanRange.html(valueRange.val() + 'm');
        valueRange.on('input change', () => {
            spanRange.html(valueRange.val() + 'm');
        });

        const spanOnyx = $('#spanOnyx');
        spanOnyx.prev().append(spanOnyx).addClass('displayBlock');
        const valueOnyx = $('#form_onyx');
        spanOnyx.html(valueOnyx.val() + '%');
        valueOnyx.on('input change', () => {
            spanOnyx.html(valueOnyx.val() + '%');
        });

        if(window.bResponsive === true) {
            $('table#weaponsStats > tbody > tr > td:first-child').css('cursor', 'pointer');
        }

        $('.select2-container').css('width', 'auto');

        //ready to show
        $("#contentBody").css('display', 'table');
        $("#progress").hide();

        $('[data-toggle="tooltip"]').tooltip({
            animated: 'fade',
            placement: 'bottom',
            html: true
        });

        window.weaponStats.DataTable()
            .columns.adjust()
            .responsive.recalc();
    }
});

window.patternCall = function(weaponButton) {
    let weaponIdVal = $(weaponButton).data('id');

    let closeButton = '<div class="modal-header customHeader"><button type="button" class="close customClose" data-dismiss="modal" aria-label="Close">'
        + '<span class="cross" aria-hidden="true">&times;</span></button></div>';
    $.ajax({
        url: window.recoilUrl,
        type: 'get',
        data: {weaponId: weaponIdVal},
        success: function(response){
            $('.modal-body').html(closeButton + response);
            let chart = new CanvasJSChart.Chart("chartContainer", {
                backgroundColor: "rgba(42, 51, 62, .95)",
                color: 'white',
                title:{
                    text: decodeURI(window.weaponName.replace('&amp;', '&')),
                    fontFamily: 'Play',
                    fontColor: '#CCCCCCAA',
                    fontSize: 12
                },
                toolTip:{
                    backgroundColor: "#000000EE",
                    cornerRadius: 3,
                    borderColor: '#000000EE',
                    fontSize: 10,
                    fontFamily: 'Play',
                    fontColor: '#CCCCCCAA',
                },
                height: 400,
                width: 240,
                interactivityEnabled: true,
                axisX:{
                    minimum: -window.startX,
                    maximum: window.startX,
                    gridThickness: 0,
                    labelFontColor: '#CCCCCCAA',
                    labelFontFamily: 'Play',
                    labelFontSize: 12,
                },
                axisY:{
                    gridThickness: 0,
                    labelFontColor: '#CCCCCCAA',
                    labelFontFamily: 'Play',
                    labelFontSize: 12,
                },
                data: [{
                    type: "line",
                    labelWrap: true,
                    labelMaxWidth: 80,
                    markerSize: 0,
                    toolTipContent: "{label}",
                    lineThickness: 1,
                    dataPoints: window.recoilJson
                }]
            });
            chart.render();
            $('.customModal').removeClass('iconModal');
            $('.canvasjs-chart-credit').remove();
            $('#empModal').modal({'backdrop' : true});
            $('#empModal').modal('show');
        }
    });
}

//RESET PARTICULAR CSS ON REDRAW
window.weaponStats.on('draw.dt', function () {
    //HightLight Time To Kill by selector .border1px
    $('#weaponsStats tbody tr td:nth-child(' + ($("#weaponsStats thead tr th.border1px").index() + 1) + ')')
        .addClass('border1px');
    $('#weaponsStats tbody tr').addClass(['cell100']);
    $('table#weaponsStats > tbody > tr > td:first-child').css('cursor', 'pointer');
    window.table.DataTable()
        .columns.adjust()
        .responsive.recalc();

    $('[data-toggle="tooltip"]').tooltip({
        animated: 'fade',
        placement: 'bottom',
        html: true
    });
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

window.ajaxForm = $('#ajaxForm');
$('form :input').each(function () {
    $(this).change(function () {
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