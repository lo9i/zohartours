/*!
* remark (http://getbootstrapadmin.com/remark)
* Copyright 2015 amazingsurge
* Licensed under the Themeforest Standard Licenses
*/
(function(document, window, $) {
'use strict';

    var Site = window.Site;

    $(document).ready(function($) {
    Site.run();
    });

    // Individual column searching
    // ---------------------------
    (function() {
        $(document).ready(function() {

            var defaults = $.components.getDefaults("dataTable");
            var options2 = $.extend(true, {}, defaults, {
                initComplete: function() {
                    this.api().columns().every(function() {
                        var column = this;
                        var select = $('<select class="form-control width-full"><option value=""></option></select>')
                                    .appendTo($(column.footer()).empty())
                                    .on('change', function() {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                                    });

                        column.data().unique().sort().each(function(d, j) {select.append('<option value="' + d + '">' + d + '</option>')});
                    });
                },
                "lengthMenu": [10, 25, 50]
            });
            $('#serviceTable').DataTable(options2);
            $('#rateTable').DataTable(options2);
            $('#policyTable').DataTable(options2);
            $('#promotionTable').DataTable(options2);

            var options = $.extend(true, {}, defaults, {
                "order": [[ 1, "asc" ]],
                initComplete: function() {
                    setTimeout(function(){ $('.dataTables_filter input').focus() }, 1000);
                },
                "autoWidth": false,
                columnDefs: [ {
                    "width": "15px",
                    "targets": 0,
                    "orderable": false,
                    "fixedColumns": true,
                } ],
                "lengthMenu": [10, 25, 50, 100, 365, 500, 1000]
            });

            var table = $('#dateTable').DataTable(options);

            $('#stopSellForm').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget) // Button that triggered the modal
                stopSellDate = button.parents('td').attr('dateId');
                var theRow = button.parents('tr');
                var data = table.row( theRow ).data();
                theDiv = $(theRow.find('td')[1]);
                $(this).find('.modal-body').html('Confirm Stop Sell for ' + $(data[1]).find('.label').text() + '?');
            });
            $('#cancelStopSellForm').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget) // Button that triggered the modal
                stopSellDate = button.parents('td').attr('dateId');
                var theRow = button.parents('tr');
                var data = table.row( theRow ).data();
                theDiv = $(theRow.find('td')[1]);
                $(this).find('.modal-body').html('Confirm Cancel Stop Sell for ' + $(data[1]).find('.label').text() + '?');
            });
        }); // $(document).ready
    })();

})(document, window, jQuery);

