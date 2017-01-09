import GoogleMapsLoader from 'google-maps';

GoogleMapsLoader.KEY = data.apiKey;

GoogleMapsLoader.load((google) => {
    var map;
    const el = document.getElementById('obj-google-maps');
    const latlng = new google.maps.LatLng(-34.397, 150.644);
    const locations = data.locations;
    const options = {
        zoom: parseInt(data.mapZoom),
        mapTypeId: data.mapType
    };

    let geocoder = new google.maps.Geocoder();
    geocoder.geocode( { 'address': data.mapCenter }, function(results, status) {
        if (status == 'OK') {
            options.center = results[0].geometry.location;
            map = new google.maps.Map(el, options);
        }
    });

    locations.forEach((location) => {
        console.log(location);
        geocoder.geocode( { 'address': location.address }, function(results, status) {
            if (status == 'OK') {
                var marker = new google.maps.Marker({
                    position: results[0].geometry.location,
                    title:"Hello World!"
                });
                marker.setMap(map);
            }
        })
    });
});
