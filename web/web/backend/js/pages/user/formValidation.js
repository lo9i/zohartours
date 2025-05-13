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
  $('#newUserForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#saveUser',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

  $('#editUserForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#saveUser',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

  $('#userDeleteForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#deleteUser',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

})(document, window, jQuery);