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

  // Hotel Form
  // ------------------------
  $('#hotelServiceForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#saveHotelService',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

  $('#hotelServiceDeleteForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#deleteHotelService',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

})(document, window, jQuery);