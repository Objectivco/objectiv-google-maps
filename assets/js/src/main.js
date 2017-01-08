import GoogleMapsLoader from 'google-maps';

GoogleMapsLoader.KEY = data.api_key;

GoogleMapsLoader.load((google) => {
    let uluru = {lat: -25.363, lng: 131.044};
    const el = document.getElementById('obj-google-maps');
    const options = {
        zoom: 4,
        center: uluru,
        mapTypeId: data.map_type
    };

    new google.maps.Map(el, options)
});
