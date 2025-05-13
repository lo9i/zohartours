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
  $('#rateForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#saveRate',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

  $('#rateDeleteForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#deleteRate',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

})(document, window, jQuery);