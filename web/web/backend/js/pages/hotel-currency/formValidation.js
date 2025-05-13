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
  $('#hotelCurrencyForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#saveHotelCurrency',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

  $('#hotelCurrencyDeleteForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#deleteHotelCurrency',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

})(document, window, jQuery);