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
  $('#roomImageForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#saveRoomImage',
      disabled: 'disabled'
    },
    icon: null,
    fields: {
      'gushh_corebundle_roomimage[file]': {
        validators: {
          notEmpty: {
            message: 'Please select an image'
          },
          file: {
            extension: 'jpeg,jpg,png',
            type: 'image/jpeg,image/png',
            message: 'Please choose a JPEG/JPG/PNG file'
          }
        }
      }
    }
  });

  // $('#hotelServiceDeleteForm').formValidation({
  //   framework: "bootstrap",
  //   button: {
  //     selector: '#deleteHotelService',
  //     disabled: 'disabled'
  //   },
  //   icon: null,
  //   fields: {}
  // });

})(document, window, jQuery);