require('./app.js');
require('multiselect/css/multi-select.css');
require('multiselect/js/jquery.multi-select');
require('jquery.quicksearch');

$(document).ready(function () {
    $('#wcMultiSelect').multiSelect({
        selectableHeader: "<input type='text' class='control-form search-input' autocomplete='off' placeholder='Normal weapons'>",
        selectionHeader: "<input type='text' class='control-form search-input' autocomplete='off' placeholder='Special Weapons'>",
        afterInit: function afterInit(ms) {
            let that = this,
                $selectableSearch = that.$selectableUl.prev(),
                $selectionSearch = that.$selectionUl.prev(),
                selectableSearchString = "#" + that.$container.attr("id") + " .ms-elem-selectable:not(.ms-selected)",
                selectionSearchString = "#" + that.$container.attr("id") + " .ms-elem-selection.ms-selected";
            that.qs1 = $selectableSearch.quicksearch(selectableSearchString).on("keydown", function (e) {
                if (e.which === 40) {
                    that.$selectableUl.focus();
                    return false;
                }
            });
            that.qs2 = $selectionSearch.quicksearch(selectionSearchString).on("keydown", function (e) {
                if (e.which === 40) {
                    that.$selectionUl.focus();
                    return false;
                }
            });
        },
        afterSelect: function afterSelect() {
            this.qs1.cache();
            this.qs2.cache();
        },
        afterDeselect: function afterDeselect() {
            this.qs1.cache();
            this.qs2.cache();
        }
    });

    $("#contentBody").css('display', 'table');
    $("#progress").hide();
});
