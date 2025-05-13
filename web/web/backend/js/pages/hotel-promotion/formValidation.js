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
  $('#hotelPromotionForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#saveHotelPromotion',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

  $('#hotelPromotionDeleteForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#deleteHotelPromotion',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

})(document, window, jQuery);