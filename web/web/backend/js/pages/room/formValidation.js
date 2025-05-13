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
  $('#roomForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#saveRoom',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

  $('#saveRoomTop').on('click', function (e) {
    e.preventDefault();
    $('#saveRoom').click();
  })
})(document, window, jQuery);