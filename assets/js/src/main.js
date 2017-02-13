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
                        var components = location.address_components;
                        var streetNumber;
                        var streetName;
                        var cityName;
                        var stateName;
                        var countryName;
                        var zip;

                        for( var i=0; i < components.length; i++) {
                            if (components[i].types[0] === "street_number") {
                                streetNumber = components[i].long_name;
                            }

                            if (components[i].types[0] === "route") {
                                streetName = components[i].short_name;
                            }

                            if (components[i].types[0] === "locality") {
                                cityName = components[i].long_name;
                            }

                            if (components[i].types[0] === "administrative_area_level_1") {
                                stateName = components[i].short_name;
                            }

                            if (components[i].types[0] === "country") {
                                countryName = components[i].short_name;
                            }

                            if (components[i].types[0] === "postal_code") {
                                zip = components[i].short_name;
                            }
                        }

                        var marker = new google.maps.Marker({
                            map: map,
                            position: {lat: numLat, lng: numLng}
                        });

                        var infoWindow = new google.maps.InfoWindow({
                            content: '<div class="obj-google-maps-infowindow" itemscope itemtype="http://schema.org/LocalBusiness"><p itemprop="name"><strong>' + location.post_title + '</strong></p>' +
                                '<p itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">' + '<span class="street-address" itemprop="streetAddress">' + streetNumber + ' ' + streetName + '</span><br><span class="city-name" itemprop="addressLocality">' + cityName + '</span>, <span class="state-name" itemprop="addressRegion">' + stateName + '</span> <span class="country-name" itemprop="addressCountry">' +  countryName + '</span> <span class="zip" itemprop="postalCode">' + zip + '</span></p>' +
                                '<p><a href="' + location.permalink + '" itemprop="url">View ' + location.post_type_label + '</a></p></div>'
                        });

                        marker.addListener('click', function() {
                            infoWindow.open(map, marker);
                        });

                    }
                });
            }
        });
    });
