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
  $('#hotelForm').formValidation({
    framework: "bootstrap",
    button: {
      selector: '#saveHotel',
      disabled: 'disabled'
    },
    icon: null,
    fields: {}
  });

  $('#saveHotelTop').on('click', function (e) {
    e.preventDefault();
    $('#saveHotel').click();
  })

})(document, window, jQuery);


// This example displays an address form, using the autocomplete feature
// of the Google Places API to help users fill in the information.
var placeSearch, autocomplete;
var componentForm = {
  /*street_number: 'short_name',
  route: 'long_name',*/
  locality: 'long_name',
  administrative_area_level_1: 'long_name',
  country: 'long_name',
  postal_code: 'short_name'
};

function initialize() {
  // Create the autocomplete object, restricting the search
  // to geographical location types.
  autocomplete = new google.maps.places.Autocomplete(
      /** @type {HTMLInputElement} */(document.getElementById('gushh_corebundle_hotel_address')),
      { types: ['geocode'] });
  // When the user selects an address from the dropdown,
  // populate the address fields in the form.
  google.maps.event.addListener(autocomplete, 'place_changed', function() {
    fillInAddress();
  });
}

function fillInAddress() {
  // Get the place details from the autocomplete object.
  var place = autocomplete.getPlace();
  var lat = place.geometry.location.lat(),
    lng = place.geometry.location.lng();

  document.getElementById('gushh_corebundle_hotel_coords').value = lat + ',' + lng;

  for (var component in componentForm) {
    document.getElementById(component).value = '';
    document.getElementById(component).disabled = false;
  }
  // Get each component of the address from the place details
  // and fill the corresponding field on the form.
  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (componentForm[addressType]) {
      var val = place.address_components[i][componentForm[addressType]];
      document.getElementById(addressType).value = val;
      if( place.address_components[i].types[0] === 'country') {

        switch(val){
            case 'Méco':
            case 'Mejico':
            case 'Méco':
            case 'Mexico':
              $('#region').val('Mexico');
              break;
            case 'United States':
            case 'Estados Unidos':
                $('#region').val('United States');
                break;
            default:
                $('#region').val('Europe');
        }
        $('#region').removeAttr('disabled');
      }
    }
  }
}

// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = new google.maps.LatLng(
          position.coords.latitude, position.coords.longitude);

      var circle = new google.maps.Circle({
        center: geolocation,
        radius: position.coords.accuracy
      });
      autocomplete.setBounds(circle.getBounds());
    });
  }
}
