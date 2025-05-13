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
  $('#roomServiceForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#saveRoomService',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

  $('#roomServiceDeleteForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#deleteRoomService',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

})(document, window, jQuery);