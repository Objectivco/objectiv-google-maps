import GoogleMapsLoader from 'google-maps';

GoogleMapsLoader.KEY = data.apiKey;

GoogleMapsLoader.load((google) => {
    const el = document.getElementById('obj-google-maps');
    const latlng = new google.maps.LatLng(-34.397, 150.644);
    const options = {
        zoom: 4,
        mapTypeId: data.mapType
    };

    let geocoder = new google.maps.Geocoder();
    geocoder.geocode( { 'address': data.mapCenter }, function(results, status) {
        if (status == 'OK') {
            options.center = results[0].geometry.location;
            const map = new google.maps.Map(el, options);
        }
    });
});
