require('../css/app.scss');
require('../css/main.css');
require('../css/util.css');


window.$ = require('jquery');
require('bootstrap')
var datatablesbs4 = require('datatables.net-bs4')
window.$.DataTable = datatablesbs4;
require('bootstrap/dist/css/bootstrap.min.css');
require('@popperjs/core');
require('select2');
require('@fortawesome/fontawesome-free');
require('animate');

require('../css/select2.css');

window.table = $('#weaponsStats').dataTable({
    select: true,
    initComplete: function () {
    	  $('.select2JS').select2();
        this.api().columns([0,1]).every(function (indexCol) {
            var collator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
            var column = this;
            
            if(indexCol == 0) {
            	var select = $('<select style="width:300px;" class="form-control"><option value="">Select Weapon</option></select>')
            }
            else {
            	var select = $('<select style="width:152px;"class="form-control"><option value="">Select Type</option></select>')
            }
            select.appendTo($(column.footer()).empty())
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

        $("#contentBody").show();
 		  $("#progress").hide();
    }
});

$('#weaponsStats').on( 'draw.dt', function () {
        $('#weaponsStats tbody tr td:nth-child(15)').addClass('border1px'); //HightLight Time To Kill
        $('#weaponsStats tbody tr td').addClass('column');
} );

$('#ajaxForm').submit(function(e) {
	 $('#form_save').addClass('disabled');
	 $('#form_save').removeClass('btn-secondary');
    $('#form_save').addClass('btn-outline-secondary');
    e.preventDefault();
    var formSerialize = $(this).serialize();
    //ajaxStatsUrl defined in twig template
    $.post(ajaxStatsUrl, formSerialize, function(response) {
        window.table.fnClearTable();
        var weapons = response.data;
        $.each(weapons, function () {        	
	     		var values = $.map( $.makeArray( this )[0], function( value, key ) {
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