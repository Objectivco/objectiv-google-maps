import GoogleMapsLoader from 'google-maps';

GoogleMapsLoader.KEY = data.api_key;
GoogleMapsLoader.LIBRARIES = ['places'];

GoogleMapsLoader.load((google) => {
    var autocomplete;

    autocomplete = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
        {types: ['geocode']});

});
