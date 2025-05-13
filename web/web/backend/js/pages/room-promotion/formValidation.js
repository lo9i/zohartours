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

    if ($('#gushh_corebundle_promotionnightbenefit_cumulative').is(":checked")) {
      $('#gushh_corebundle_promotionnightbenefit_hasLimit').prop('disabled', false);
      if ($('#gushh_corebundle_promotionnightbenefit_hasLimit').is(":checked")) {
        $('#gushh_corebundle_promotionnightbenefit_limitValue').prop('disabled', false);
      } else {
        $('#gushh_corebundle_promotionnightbenefit_limitValue').prop('disabled', true);
        $('#gushh_corebundle_promotionnightbenefit_limitValue').val(1);
      }
    } else {
      $('#gushh_corebundle_promotionnightbenefit_limitValue').prop('disabled', true);
      $('#gushh_corebundle_promotionnightbenefit_limitValue').val(1);
      $('#gushh_corebundle_promotionnightbenefit_hasLimit').prop('disabled', true);
    }

  });

  // Room Promotion Form
  // ------------------------
  $('#roomPromotionForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#saveRoomPromotion',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

  $('#roomPromotionDeleteForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#deleteRoomPromotion',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

  $('#gushh_corebundle_promotionnightbenefit_hasLimit').on('change', function (e) {
    if ($(this).is(":checked")) {
      $('#gushh_corebundle_promotionnightbenefit_limitValue').prop('disabled', false);
    } else {
      $('#gushh_corebundle_promotionnightbenefit_limitValue').prop('disabled', true);
      $('#gushh_corebundle_promotionnightbenefit_limitValue').val(1);
    }
  })

  $('#gushh_corebundle_promotionnightbenefit_cumulative').on('change', function (e) {
    if ($(this).is(":checked")) {
      $('#gushh_corebundle_promotionnightbenefit_hasLimit').prop('disabled', false);
    } else {
      $('#gushh_corebundle_promotionnightbenefit_hasLimit').prop('disabled', true);
      $('#gushh_corebundle_promotionnightbenefit_hasLimit').prop('checked', false);
      $('#gushh_corebundle_promotionnightbenefit_limitValue').prop('disabled', true);
      $('#gushh_corebundle_promotionnightbenefit_limitValue').val(1);
    }
  })

})(document, window, jQuery);