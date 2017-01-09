import GoogleMapsLoader from 'google-maps';

GoogleMapsLoader.KEY = data.api_key;
GoogleMapsLoader.LIBRARIES = ['places'];

GoogleMapsLoader.load((google) => {
    var autocomlete;

    autocomlete = new google.maps.places.Autocomplete(
        (document.getElementById('autocomplete')),
        {types: ['geocode']});

});
