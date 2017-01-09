import GoogleMapsLoader from 'google-maps';

GoogleMapsLoader.KEY = data.api_key;
GoogleMapsLoader.LIBRARIES = ['places'];
var autocomplete;

GoogleMapsLoader.load((google) => {
    var autocomplete;
    var place;

    function fillInAddress() {
        place = autocomplete.getPlace();
        placeId = place.place_id;

        document.getElementById('obj-google-address-place-id').value = 'testing';

    }

    autocomplete = new google.maps.places.Autocomplete(
        (document.getElementById('autocomplete')),
        {types: ['geocode']});

    autocomplete.addListener('place_changed', fillInAddress);

    console.log(place);
});
