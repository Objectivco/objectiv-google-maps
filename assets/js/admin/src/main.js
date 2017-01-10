import GoogleMapsLoader from 'google-maps';

GoogleMapsLoader.KEY = data.api_key;
GoogleMapsLoader.LIBRARIES = ['places'];
var autocomplete;

GoogleMapsLoader.load((google) => {
    var autocomplete;

    autocomplete = new google.maps.places.Autocomplete(
        (document.getElementById('autocomplete')),
        {types: ['address']});

});
