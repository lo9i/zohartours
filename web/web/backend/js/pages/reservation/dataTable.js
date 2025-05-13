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

      $.fn.dataTable.moment( 'DD MMM YYYY' );
      var defaults = $.components.getDefaults("dataTable");

      var options = $.extend(true, {}, defaults, {
          "order": [[ 1, "desc" ]],
        initComplete: function() {
            setTimeout(function(){ $('.dataTables_filter input').focus() }, 1000);
        }
      });

      $('#reservationTable').DataTable(options);
    });
  })();

})(document, window, jQuery);
