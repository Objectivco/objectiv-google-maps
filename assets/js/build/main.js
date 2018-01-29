(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var GoogleMapsLoader = require('google-maps');
var userMarkers = [];

GoogleMapsLoader.KEY = data.apiKey;
GoogleMapsLoader.LIBRARIES = ['places', 'geometry'];

function clearUserMarkers() {
    for (var i = 0; i < userMarkers.length; i++) {
        userMarkers[i].setMap(null);
    }
}

GoogleMapsLoader.load(function (google) {
    var map;
    var autocomplete;
    var places;
    var el = document.getElementById('obj-google-maps');
    var input = document.getElementById('obj-search-input');
    var searchBox = new google.maps.places.SearchBox(input);
    var locations = data.locations;
    var userIcon = 'https://maps.google.com/mapfiles/ms/micons/man.png';
    var options = {
        zoom: parseInt(data.mapZoom),
        mapTypeId: data.mapType,
		center: new google.maps.LatLng(data.mapCenterLat, data.mapCenterLng)
    };

    /**
    * When a city has been selected pan and zoom to the center
    */
    function onPlaceChanged() {
        var place = autocomplete.getPlace();
        if (place.geometry) {
            clearUserMarkers();
            map.panTo(place.geometry.location);
            map.setZoom(options.zoom);

            var marker = new google.maps.Marker({
                map: map,
                position: { lat: place.geometry.location.lat(), lng: place.geometry.location.lng() },
                icon: userIcon
            });
            userMarkers.push(marker);

            fitMapBounds( new google.maps.LatLng( place.geometry.location.lat(), place.geometry.location.lng() ) );
        } else {
            input.placeholder = 'Search by city...';
        }
    }

	function loadMap() {
		// initiate the map
		map = new google.maps.Map(el, options);

		// add the input to the top left of the map
		map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

		// set the bounds for the search box
		map.addListener('bounds_changed', function () {
			searchBox.setBounds(map.getBounds());
		});

		// set up the google places autocomplete restricted to cities
		autocomplete = new google.maps.places.Autocomplete(
		/** @type {!HTMLInputElement} */document.getElementById('obj-search-input'), {
			types: [data.mapSearch]
		});

		places = new google.maps.places.PlacesService(map);

		// add listener to run onPlaceChanged when a city has been selected
		autocomplete.addListener('place_changed', onPlaceChanged);

		// Loop through locations and add the markers
		locations.forEach(function (location) {
			var lat = location.lat;
			var lng = location.lng;

			if (lat && lng) {
				var numLat = parseFloat(lat);
				var numLng = parseFloat(lng);

				var marker = new google.maps.Marker({
						map: map,
						position: { lat: numLat, lng: numLng }
					});

				var infoWindow = new google.maps.InfoWindow({
					content: location.content
				});

				marker.addListener('click', function () {
					infoWindow.open(map, marker);
				});
			}
		});

		google.maps.event.addListenerOnce(map, 'idle', function(){
			fitMapBounds( options.center );
		});
	}

	//Ensure map has all markers in view within 100 miles
	function fitMapBounds( center ) {
		var markerBoundsRange = 160934,
			markersInBoundsRange = []; //100 Miles
		
		locations.forEach(function (location) {
			var lat = location.lat;
			var lng = location.lng;

			if (lat && lng) {
				var numLat = parseFloat(lat);
				var numLng = parseFloat(lng);

				var markerLatLng = new google.maps.LatLng({ lat: numLat, lng: numLng }),
					toCenterDistance = google.maps.geometry.spherical.computeDistanceBetween( center, markerLatLng );

				if ( markerBoundsRange >= toCenterDistance) {
					markersInBoundsRange.push( markerLatLng );
				}
			}
		});

		var mapBounds = map.getBounds();
		markersInBoundsRange.forEach( function(markerLatLng) {
			mapBounds.extend( markerLatLng );
		});
		map.fitBounds( mapBounds, 20 );
	}


    /*
	* Get lat and long from browsers geolocation API
	*/
	if (window.location.protocol === 'https:' && "geolocation" in navigator) {
		navigator.geolocation.getCurrentPosition( function (position) {
			//Success
			options.center = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			loadMap();

			// add user marker on center of map
			var marker = new google.maps.Marker({
				map: map,
				position: { lat:  options.center.lat(), lng:  options.center.lng() },
				icon: userIcon
			});
			userMarkers.push(marker);
		}, function() {
			//Error
			loadMap();
		});
	} else {
		//Geolocation API not available
		loadMap();
	}
});

},{"google-maps":2}],2:[function(require,module,exports){
(function(root, factory) {

	if (root === null) {
		throw new Error('Google-maps package can be used only in browser');
	}

	if (typeof define === 'function' && define.amd) {
		define(factory);
	} else if (typeof exports === 'object') {
		module.exports = factory();
	} else {
		root.GoogleMapsLoader = factory();
	}

})(typeof window !== 'undefined' ? window : null, function() {


	'use strict';


	var googleVersion = '3.18';

	var script = null;

	var google = null;

	var loading = false;

	var callbacks = [];

	var onLoadEvents = [];

	var originalCreateLoaderMethod = null;


	var GoogleMapsLoader = {};


	GoogleMapsLoader.URL = 'https://maps.googleapis.com/maps/api/js';

	GoogleMapsLoader.KEY = null;

	GoogleMapsLoader.LIBRARIES = [];

	GoogleMapsLoader.CLIENT = null;

	GoogleMapsLoader.CHANNEL = null;

	GoogleMapsLoader.LANGUAGE = null;

	GoogleMapsLoader.REGION = null;

	GoogleMapsLoader.VERSION = googleVersion;

	GoogleMapsLoader.WINDOW_CALLBACK_NAME = '__google_maps_api_provider_initializator__';


	GoogleMapsLoader._googleMockApiObject = {};


	GoogleMapsLoader.load = function(fn) {
		if (google === null) {
			if (loading === true) {
				if (fn) {
					callbacks.push(fn);
				}
			} else {
				loading = true;

				window[GoogleMapsLoader.WINDOW_CALLBACK_NAME] = function() {
					ready(fn);
				};

				GoogleMapsLoader.createLoader();
			}
		} else if (fn) {
			fn(google);
		}
	};


	GoogleMapsLoader.createLoader = function() {
		script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = GoogleMapsLoader.createUrl();

		document.body.appendChild(script);
	};


	GoogleMapsLoader.isLoaded = function() {
		return google !== null;
	};


	GoogleMapsLoader.createUrl = function() {
		var url = GoogleMapsLoader.URL;

		url += '?callback=' + GoogleMapsLoader.WINDOW_CALLBACK_NAME;

		if (GoogleMapsLoader.KEY) {
			url += '&key=' + GoogleMapsLoader.KEY;
		}

		if (GoogleMapsLoader.LIBRARIES.length > 0) {
			url += '&libraries=' + GoogleMapsLoader.LIBRARIES.join(',');
		}

		if (GoogleMapsLoader.CLIENT) {
			url += '&client=' + GoogleMapsLoader.CLIENT + '&v=' + GoogleMapsLoader.VERSION;
		}

		if (GoogleMapsLoader.CHANNEL) {
			url += '&channel=' + GoogleMapsLoader.CHANNEL;
		}

		if (GoogleMapsLoader.LANGUAGE) {
			url += '&language=' + GoogleMapsLoader.LANGUAGE;
		}

		if (GoogleMapsLoader.REGION) {
			url += '&region=' + GoogleMapsLoader.REGION;
		}

		return url;
	};


	GoogleMapsLoader.release = function(fn) {
		var release = function() {
			GoogleMapsLoader.KEY = null;
			GoogleMapsLoader.LIBRARIES = [];
			GoogleMapsLoader.CLIENT = null;
			GoogleMapsLoader.CHANNEL = null;
			GoogleMapsLoader.LANGUAGE = null;
			GoogleMapsLoader.REGION = null;
			GoogleMapsLoader.VERSION = googleVersion;

			google = null;
			loading = false;
			callbacks = [];
			onLoadEvents = [];

			if (typeof window.google !== 'undefined') {
				delete window.google;
			}

			if (typeof window[GoogleMapsLoader.WINDOW_CALLBACK_NAME] !== 'undefined') {
				delete window[GoogleMapsLoader.WINDOW_CALLBACK_NAME];
			}

			if (originalCreateLoaderMethod !== null) {
				GoogleMapsLoader.createLoader = originalCreateLoaderMethod;
				originalCreateLoaderMethod = null;
			}

			if (script !== null) {
				script.parentElement.removeChild(script);
				script = null;
			}

			if (fn) {
				fn();
			}
		};

		if (loading) {
			GoogleMapsLoader.load(function() {
				release();
			});
		} else {
			release();
		}
	};


	GoogleMapsLoader.onLoad = function(fn) {
		onLoadEvents.push(fn);
	};


	GoogleMapsLoader.makeMock = function() {
		originalCreateLoaderMethod = GoogleMapsLoader.createLoader;

		GoogleMapsLoader.createLoader = function() {
			window.google = GoogleMapsLoader._googleMockApiObject;
			window[GoogleMapsLoader.WINDOW_CALLBACK_NAME]();
		};
	};


	var ready = function(fn) {
		var i;

		loading = false;

		if (google === null) {
			google = window.google;
		}

		for (i = 0; i < onLoadEvents.length; i++) {
			onLoadEvents[i](google);
		}

		if (fn) {
			fn(google);
		}

		for (i = 0; i < callbacks.length; i++) {
			callbacks[i](google);
		}

		callbacks = [];
	};


	return GoogleMapsLoader;

});

},{}]},{},[1]);
