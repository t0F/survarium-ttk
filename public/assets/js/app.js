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

function getHeadersIndex() {
    let headerIndex = [];
    $('#weaponsStats thead tr th').each(function (indexCol, val) {
        headerIndex[indexCol] = val.textContent;
    });
    return headerIndex;
}

/*
$(window).on('resize', function() {
    if($(window).height() > 700) {
        $('#body').addClass('limit1200');
        $('#body').removeClass('limit400');
    }else{
        $('#body').addClass('limit400');
        $('#body').removeClass('limit1200');
    }
})
*/

function createColSelector() {
    let appendTo = $('.dataTables_length').parent();
    appendTo.removeClass(['col-sm-12', 'col-md-6']).addClass(['col-sm-8', 'col-md-4']);
    $('#weaponsStats_filter').parent().removeClass(['col-sm-12', 'col-md-6']).addClass(['col-sm-8', 'col-md-4'])
    let indexes = getHeadersIndex();
    let select = '<select multiple="multiple" id="colSelector" class="selectpicker btselect" multiple" name="colSelector[]">' +
        '<option value=null>Show All</option>';
    $.each(indexes, function (index, value) {
        select = select + '<option value="' + value + '">' + value + '</option>'
    });
    select = select + '</select>';

    let newHtml = '<div class="col-sm-8 col-md-4" style="margin-top: -5px; padding: 0;">' +
        '<div class="dataTables_length" id="weaponsStats_selector">' +
        '<label>Select to hide column(s) (use ctrl):' + select + '</label>' +
        '</div>' +
        '</div>';
    $(newHtml).insertAfter(appendTo);

    $('#colSelector').change(function (e) {
        let selected = $(e.target).val();

        $.each(indexes, function (a, val) {
            if (selected.includes(val)) {
                window.table.fnSetColumnVis(a, false)
            } else {
                window.table.fnSetColumnVis(a, true);
            }
        });
        $('#weaponsStats').css('width', 'auto');
    });
    $('#weaponsStats').css('min-width', '780px;');
    $('#weaponsStats').css('width', 'auto');
}

window.table = $('#weaponsStats').dataTable({
    select: true,
    initComplete: function () {
        $('.select2JS').select2();

        this.api().columns([0, 1]).every(function (indexCol) {
            let collator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
            let column = this;
            let select = $('<select class="form-control"><option value="">Select Weapon</option></select>')
            /* fix width are bad
            if(indexCol == 0) {
                var select = $('<select style="width:300px;" class="form-control"><option value="">Select Weapon</option></select>')
            }
            else {
            	var select = $('<select style="width:152px;"class="form-control"><option value="">Select Type</option></select>')
            }
            */
            $('#weaponsStats').css('width', 'auto');

            select.appendTo($(column.footer()).empty())
                .on('change', function () {
                    let val = $.fn.dataTable.util.escapeRegex(
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
        createColSelector();

        $("#contentBody").show();
        $("#progress").hide();
       // $('.btselect').selectpicker();
    }
});

$('#weaponsStats').on('draw.dt', function () {
    let index = getHeadersIndex();
    $('#weaponsStats tbody tr td:nth-child(' + (index.indexOf("Sample TimeToKill") + 1) + ')').addClass('border1px'); //HightLight Time To Kill
    $('#weaponsStats tbody tr td').addClass('column');
});

/*
function decimalDatatable() {
    //reduce decimals
    $('#weaponsStats tbody tr td:nth-child(13)').each(function () {
        let val = $(this).text().split('.');
        $(this).html(val[0]+'<span class="smallDemicals">.'+val[1]+"</span>");
    });
}
*/

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