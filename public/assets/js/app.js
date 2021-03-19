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
window.explSelect = $('#explosiveSelect');
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
                if(data === "") return "";
                return '<div data-toggle="tooltip" title="<img src='+window.fullBaseUri+data+' />" onclick="window.showIcon(this);" class="divWIco"><img class="wIco" src="'+window.fullBaseUri+data+'" /></div>'
            }
        },
    ];
    window.dtSelect = [0, 5];
    window.dtDefaultSort = [[2, 'asc'], [0, 'asc']];
} else {
    window.bResponsive = false;
    window.columnsDefs = [{targets: 1, render: function (data, type, row, meta) {
            if(data === "") return "";
            return '<div data-toggle="tooltip" title="<img src='+window.fullBaseUri+data+' />" onclick="window.showIcon(this);" class="divWIco"><img class="wIco" src="'+window.fullBaseUri+data+'" /></div>'
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

window.convertC = {
    "Ё":"YO","Й":"I","Ц":"TS","У":"U","К":"K","Е":"E","Н":"N","Г":"G","Ш":"SH","Щ":"SCH","З":"Z","Х":"H","Ъ":"'","ё":
    "yo","й":"i","ц":"ts","у":"u","к":"k","е":"e","н":"n","г":"g","ш":"sh","щ":"sch","з":"z","х":"h","ъ":"'","Ф":"F",
    "Ы":"I","В":"V","А":"a","П":"P","Р":"R","О":"O","Л":"L","Д":"D","Ж":"ZH","Э":"E","ф":"f","ы":"i","в":"v","а":"a",
    "п":"p","р":"r","о":"o","л":"l","д":"d","ж":"zh","э":"e","Я":"Ya","Ч":"CH","С":"S","М":"M","И":"I","Т":"T","Ь":"'",
    "Б":"B","Ю":"YU","я":"ya","ч":"ch","с":"s","м":"m","и":"i","т":"t","ь":"'","б":"b","ю":"yu"
};

window.transliterate = function(word){
    return word.split('').map(function (char) {
        return window.convertC[char] || char;
    }).join("");
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

        const spanStrength = $('#spanStrength');
        spanStrength.prev().append(spanStrength).addClass('displayBlock');
        const valueStrength = $('#form_strength_actif');
        spanStrength.html(valueStrength.val() + '%');
        valueStrength.on('input change', () => {
            spanStrength.html(valueStrength.val() + '%');
            explosiveCalc();
        });

        const spanDuration = $('#spanDuration');
        const valueDuration = $('#form_duration');
        spanDuration.html(valueDuration.val() + '%');
        valueDuration.on('input change', () => {
            spanDuration.html(valueDuration.val() + '%');
            explosiveCalc();
        });

        const spanExplReduc = $('#spanExplReduc');
        const valueExplReduc = $('#form_expl_reduc');
        spanExplReduc.html(valueExplReduc.val() + '%');
        valueExplReduc.on('input change', () => {
            spanExplReduc.html(valueExplReduc.val() + '%');
            explosiveCalc();
        });

        const spanDistance = $('#spanDistance');
        const valueDistance = $('#form_distance');
        spanDistance.html(valueDistance.val() + 'm');
        valueDistance.on('input change', () => {
            spanDistance.html(valueDistance.val() + 'm');
            explosiveCalc();
        });

        const spanOnyxSponge = $('#spanOnyxSponge');
        const valueOnyxSponge = $('#form_onyx_sponge');
        spanOnyxSponge.html(valueOnyxSponge.val() + '%');
        valueOnyxSponge.on('input change', () => {
            spanOnyxSponge.html(valueOnyxSponge.val() + '%');
            explosiveCalc();
        });

        const spanSponge = $('#spanSponge');
        const valueSponge = $('#form_sponge');
        spanSponge.html(valueSponge.val() + '%');
        valueSponge.on('input change', () => {
            spanSponge.html(valueSponge.val() + '%');
            explosiveCalc();
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

window.explSelect.change(function () {
    explForm();
});
window.explForm = function() {
    if($( "#explosiveSelect option:selected" ).data('type') == 'sponge') {
        $( "#formExplosives").css('display', 'none');
        $( "#formSponge").css('display', 'block');
        $( "#resultSponge").css('display', 'block');
    } else {
        $( "#formExplosives").css('display', 'block');
        $( "#formSponge").css('display', 'none');
        $( "#resultSponge").css('display', 'none');
    }
    explosiveCalc()
}

window.explosives = function() {
    $('#empModalExplosives').modal({'backdrop' : true});
    $('#empModalExplosives').modal('show');
    explosiveCalc();
}

window.explosiveCalc = function () {
    const formDist = $('#form_distance');
    const valueStrength =  $('#form_strength_actif').val();
    const valueDuration = $('#form_duration').val();
    let valueDistance = formDist.val();
    const valueExplReduc = $('#form_expl_reduc').val();
    const valueOnyxRange = $('#form_onyx_sponge').val();
    const valueSponge = $('#form_sponge').val();
    let explosive = $.grep(window.explosivesArr, function( e ) {
        return e.name === $( "#explosiveSelect option:selected" ).val();
    })[0];
    const baseDamage = explosive.maxDamage * 100;
    const maxRange = explosive.maxRange;
    formDist.attr("max", maxRange);
    if(valueDistance > maxRange) {
        valueDistance = maxRange;
        formDist.val(maxRange);
        $('#spanDistance').html(valueDistance + 'm');
    }
    const baseDps = 20;

    let strengthRatio = 1 + (valueStrength / 100);
    let type = $( "#explosiveSelect option:selected" ).data('type');
    let ratioOnyx = (1 - (valueOnyxRange / 100));
    let ratioSponge = (1 - (valueSponge / 100));
    let ratioDistance = (maxRange - valueDistance) / maxRange;
    let explReducRatio = (1 - (valueExplReduc / 100));
    let damageExplosion = (type == "explosive")
        ? baseDamage * explReducRatio * ratioOnyx * ratioSponge * ratioDistance
        : baseDamage * strengthRatio * ratioOnyx;


    let dps = baseDps * ratioDistance * ratioOnyx * strengthRatio;
    let duration = 15 * (1 + (valueDuration / 100))
    duration = duration.toFixed(0);

    $('#spanDamageOnExplosion').html(damageExplosion.toFixed(2));
    $('#spanDamagePerSecond').html(dps.toFixed(2));

    let TTKW = 'N/A';
    for (let i = 0; i <= duration * 100; i++) {
        if (damageExplosion + (i / 100) * dps >= 100) {
            TTKW = (i / 100) + 's';
            break;
        }
    }
    if (TTKW == 'N/A') {
        let damageMax = damageExplosion + duration * dps;
        TTKW = TTKW + ' (' + damageMax.toFixed(0) + ' dmg)';
    }

    let TTKWO = 'N/A';
    for (let i = 0; i <= duration * 100; i++) {
        if ((i / 100) * dps >= 100) {
            TTKWO = (i / 100) + 's';
            break;
        }
    }
    if (TTKWO == 'N/A') {
        let damageMaxWO = duration * dps;
        TTKWO = TTKWO + ' (' + damageMaxWO.toFixed(0) + ' dmg)';
    }
    $('#spanTTKWithExplosion').html(TTKW);
    $('#spanTTKWithoutExplosion').html(TTKWO);
}


window.patternCall = function(weaponButton) {
    let weaponIdVal = $(weaponButton).data('id');

    let closeButton = '<div class="modal-header customHeader"><button type="button" class="close customClose" data-dismiss="modal" aria-label="Close">'
        + '<span class="cross" aria-hidden="true">&times;</span></button></div>';
    $.ajax({
        url: window.recoilUrl,
        type: 'get',
        data: {weaponId: weaponIdVal},
        success: function(response){
            $('#modalRecoil').html(closeButton + response);
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