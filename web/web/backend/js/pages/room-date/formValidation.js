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
  $('#dateForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#saveDate',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

  $('#dateDeleteForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#deleteDate',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

  $('#gushh_corebundle_date_dailyRates').on('change', function (e) {
    if ($(this).is(":checked")) {
      $('#weekly').hide('fast', function (e) {
        $('#daily').removeClass('hidden').show('fast');
      })
    } else {
      $('#daily').hide('fast', function (e) {
        $('#weekly').show('fast');
      })
      
    }
    // if ($(this).val()) {};
  })

  $('#gushh_corebundle_date_rate').on('change', function (e) {
    $('#gushh_corebundle_date_mondayRate, #gushh_corebundle_date_tuesdayRate, #gushh_corebundle_date_wednesdayRate, 
      #gushh_corebundle_date_thursdayRate, #gushh_corebundle_date_fridayRate, #gushh_corebundle_date_saturdayRate, 
      #gushh_corebundle_date_sundayRate').val($(this).val());
  });

})(document, window, jQuery);