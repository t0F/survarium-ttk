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