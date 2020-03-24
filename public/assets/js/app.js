require('../css/main.css');
//require('../css/util.css');
require('../css/app.scss');

window.$ = require('jquery');
require('bootstrap')
let datatablesbs4 = require('datatables.net-bs4')
window.$.DataTable = datatablesbs4;
require('bootstrap/dist/css/bootstrap.min.css');
require('@popperjs/core');
require('select2');

require('bootstrap-select/js/bootstrap-select');
require('bootstrap-select/dist/css/bootstrap-select.css');

if(lang === 'ru') {
    require('bootstrap-select/dist/js/i18n/defaults-ru_RU.js');
    require('select2/dist/js/i18n/ru.js');
} else if(lang === 'es') {
    require('bootstrap-select/dist/js/i18n/defaults-es_ES.js');
    require('select2/dist/js/i18n/es.js');
} else if(lang === 'ua') {
    require('bootstrap-select/dist/js/i18n/defaults-ua_UA.js');
    require('select2/dist/js/i18n/uk.js');
} else if(lang === 'pl') {
    require('bootstrap-select/dist/js/i18n/defaults-pl_PL.js');
    require('select2/dist/js/i18n/pl.js');
} else if(lang === 'fr') {
    require('bootstrap-select/dist/js/i18n/defaults-fr_FR.js');
    require('select2/dist/js/i18n/fr.js');
} else {
    require('bootstrap-select/dist/js/i18n/defaults-en_US.js');
    require('select2/dist/js/i18n/en.js');
}

require('datatables.net-responsive-bs4');
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
    let selectNbRow = $('.dataTables_length select');
    selectNbRow.removeClass(['custom-select','custom-select-sm','form-control','form-control-sm']).addClass(['displayBlock','selectpicker','btselect', 'minWith60']);
    appendTo.removeClass(['col-sm-12', 'col-md-6']).addClass(['col']);
    $('#weaponsStats_filter').parent().removeClass(['col-sm-12', 'col-md-6']).addClass(['col', 'textRight'])

    let select = '<select multiple id="colSelector" class="displayBlock selectpicker btselect">';
    $.each(window.headersIndex, function (index, value) {
        select = select + '<option class="colToHide" value="' + value + '">' + value + '</option>'
    });
    select = select + '</select>';

    let colSelector = '<div class="col textCenter" style="margin-top: -5px; padding: 0;">' +
        '<div class="dataTables_length" id="weaponsStats_selector">' +
        '<label>'+ window.translations.selectToHide + select + '</label></div></div>';
    $(colSelector).insertAfter(appendTo);

    window.weaponStats.css('min-width', '780px;');
    window.weaponStats.css('width', 'auto');
}

function responsiveCol(){
    if(window.bResponsive === true) {
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

/*
// use for tests
$( "#form_save" ).click(function() {
    responsiveCol();
});
*/

if(lang === 'ru') {
    window.locales = {
        "processing": "Подождите...",
        "search": "Поиск:",
        "lengthMenu": "Показать _MENU_ записей",
        "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
        "infoEmpty": "Записи с 0 до 0 из 0 записей",
        "infoFiltered": "(отфильтровано из _MAX_ записей)",
        "infoPostFix": "",
        "loadingRecords": "Загрузка записей...",
        "zeroRecords": "Записи отсутствуют.",
        "emptyTable": "В таблице отсутствуют данные",
        "paginate": {
            "first": "Первая",
            "previous": "Предыдущая",
            "next": "Следующая",
            "last": "Последняя"
        },
        "aria": {
            "sortAscending": ": активировать для сортировки столбца по возрастанию",
            "sortDescending": ": активировать для сортировки столбца по убыванию"
        },
        "select": {
            "rows": {
                "_": "Выбрано записей: %d",
                "0": "Кликните по записи для выбора",
                "1": "Выбрана одна запись"
            }
        }
    };
} else if(lang === 'es') {
    window.locales = {
        "sProcessing":     "Procesando...",
        "sLengthMenu":     "Mostrar _MENU_ registros",
        "sZeroRecords":    "No se encontraron resultados",
        "sEmptyTable":     "Ningún dato disponible en esta tabla =(",
        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix":    "",
        "sSearch":         "Buscar:",
        "sUrl":            "",
        "sInfoThousands":  ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
            "sFirst":    "Primero",
            "sLast":     "Último",
            "sNext":     "Siguiente",
            "sPrevious": "Anterior"
        },
        "oAria": {
            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        },
        "buttons": {
            "copy": "Copiar",
            "colvis": "Visibilidad"
        }
    };
} else if(lang === 'ua') {
    window.locales = {
        "sProcessing":   "Зачекайте...",
        "sLengthMenu":   "Показати _MENU_ записів",
        "sZeroRecords":  "Записи відсутні.",
        "sInfo":         "Записи з _START_ по _END_ із _TOTAL_ записів",
        "sInfoEmpty":    "Записи з 0 по 0 із 0 записів",
        "sInfoFiltered": "(відфільтровано з _MAX_ записів)",
        "sInfoPostFix":  "",
        "sSearch":       "Пошук:",
        "sUrl":          "",
        "oPaginate": {
            "sFirst": "Перша",
            "sPrevious": "Попередня",
            "sNext": "Наступна",
            "sLast": "Остання"
        },
        "oAria": {
            "sSortAscending":  ": активувати для сортування стовпців за зростанням",
            "sSortDescending": ": активувати для сортування стовпців за спаданням"
        }
    };
} else if(lang === 'pl') {
    window.locales = {
        "processing":     "Przetwarzanie...",
        "search":         "Szukaj:",
        "lengthMenu":     "Pokaż _MENU_ pozycji",
        "info":           "Pozycje od _START_ do _END_ z _TOTAL_ łącznie",
        "infoEmpty":      "Pozycji 0 z 0 dostępnych",
        "infoFiltered":   "(filtrowanie spośród _MAX_ dostępnych pozycji)",
        "infoPostFix":    "",
        "loadingRecords": "Wczytywanie...",
        "zeroRecords":    "Nie znaleziono pasujących pozycji",
        "emptyTable":     "Brak danych",
        "paginate": {
            "first":      "Pierwsza",
            "previous":   "Poprzednia",
            "next":       "Następna",
            "last":       "Ostatnia"
        },
        "aria": {
            "sortAscending": ": aktywuj, by posortować kolumnę rosnąco",
            "sortDescending": ": aktywuj, by posortować kolumnę malejąco"
        }
    };
} else if(lang === 'fr') {
    window.locales = {
        "sEmptyTable":     "Aucune donnée disponible dans le tableau",
        "sInfo":           "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
        "sInfoEmpty":      "Affichage de l'élément 0 à 0 sur 0 élément",
        "sInfoFiltered":   "(filtré à partir de _MAX_ éléments au total)",
        "sInfoPostFix":    "",
        "sInfoThousands":  ",",
        "sLengthMenu":     "Afficher _MENU_ éléments",
        "sLoadingRecords": "Chargement...",
        "sProcessing":     "Traitement...",
        "sSearch":         "Rechercher :",
        "sZeroRecords":    "Aucun élément correspondant trouvé",
        "oPaginate": {
            "sFirst":    "Premier",
            "sLast":     "Dernier",
            "sNext":     "Suivant",
            "sPrevious": "Précédent"
        },
        "oAria": {
            "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
            "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
        },
        "select": {
            "rows": {
                "_": "%d lignes sélectionnées",
                "0": "Aucune ligne sélectionnée",
                "1": "1 ligne sélectionnée"
            }
        }
    };
} else {
    window.locales = {}; // default english
}

if (window.responsive === 1) {
    window.contentBody = $('#contentBody');
    window.weaponStatsDiv = $('#weaponStatsDiv');
    window.bResponsive = true;
    responsiveCol();
    window.columnsDefs = [
        { responsivePriority: 1, targets: 0 },
        { responsivePriority: 1, targets: 1 }
    ];
    window.dtSelect = [0, 4];
} else {
    window.bResponsive = false;
    window.columnsDefs = [];
    window.dtSelect = [0, 1];
}


window.lang = lang;
//CREATE DATATABLE
window.table = window.weaponStats.dataTable({
    select: true,
    responsive: window.bResponsive,
    columnDefs: window.columnsDefs,
    language: window.locales,
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
$('#ajaxForm').submit(function (e) {
    let formSave = $('#form_save');
    formSave.addClass(['disabled', 'btn-outline-secondary']);
    formSave.removeClass('btn-secondary');
    e.preventDefault();
    let formSerialize = $(this).serialize();
    //ajaxStatsUrl defined in twig template


    $.ajax({type: "POST", url: ajaxStatsUrl, data: formSerialize, success: function (response) {
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
        formSave.removeClass(['btn-outline-secondary','disabled']);
        formSave.addClass('btn-secondary');
    }});
});