var GoogleMapsLoader = require('google-maps');
var userMarkers = [];

GoogleMapsLoader.KEY = data.apiKey;
GoogleMapsLoader.LIBRARIES = ['places'];

function clearUserMarkers() {
    for (var i = 0; i < userMarkers.length; i++) {
      userMarkers[i].setMap(null);
    }
}

GoogleMapsLoader.load(function(google) {
    var map;
    var autocomplete;
    var places;
    var el = document.getElementById('obj-google-maps');
    var input = document.getElementById('obj-search-input');
    var searchBox = new google.maps.places.SearchBox(input);
    var locations = data.locations;
    var userIcon = 'http://maps.google.com/mapfiles/ms/micons/man.png';
    var options = {
        zoom: parseInt(data.mapZoom),
        mapTypeId: data.mapType
    };

    /**
    * When a city has been selected pan and zoom to the center
    */
    function onPlaceChanged() {
        var place = autocomplete.getPlace();
        if (place.geometry) {
            clearUserMarkers();
            map.panTo(place.geometry.location);
            map.setZoom(8);

            var marker = new google.maps.Marker({
                map: map,
                position: {lat: place.geometry.location.lat(), lng: place.geometry.location.lng()},
                icon: userIcon
            });
            userMarkers.push(marker);

            console.log(userMarkers);

        } else {
            input.placeholder = 'Search by city...';
        }
    }

    /**
    * Get lat and long from center map
    */
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode( { 'address': data.mapCenter }, function(results, status) {
        if (status == 'OK') {
            // get the center from the geocoded address
            options.center = results[0].geometry.location;

            // initiate the map
            map = new google.maps.Map(el, options);

            // add the input to the top left of the map
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            // set the bounds for the search box
            map.addListener('bounds_changed', function() {
                searchBox.setBounds(map.getBounds());
            });

            // set up the google places autocomplete restricted to cities
            autocomplete = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */ (
                document.getElementById('obj-search-input')), {
                    types: [data.mapSearch]
                });

                places = new google.maps.places.PlacesService(map);

                // add listener to run onPlaceChanged when a city has been selected
                autocomplete.addListener('place_changed', onPlaceChanged);

                // Loop through locations and add the markers
                locations.forEach(function(location) {
                    var lat = location.lat;
                    var lng = location.lng;

                    if ( lat && lng ) {
                        var numLat = parseFloat(lat);
                        var numLng = parseFloat(lng);

                        console.log(numLat);
                        console.log(numLng);

                        var marker = new google.maps.Marker({
                            map: map,
                            position: {lat: numLat, lng: numLng}
                        });

                        var infoWindow = new google.maps.InfoWindow({
                            content: '<strong>' + location.post_title + '</strong><br>' + location.address + '<br><a href="' + location.permalink + '">View ' + location.post_type_label + '</a>'
                        });

                        marker.addListener('click', function() {
                            infoWindow.open(map, marker);
                        });

                    }
                });
            }
        });
    });
