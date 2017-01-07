import GoogleMapsLoader from 'google-maps';

GoogleMapsLoader.KEY = 'AIzaSyA92uX0ssJAJuOoLt6booqV7wQ7lwo4IC0';

GoogleMapsLoader.load((google) => {
    let uluru = {lat: -25.363, lng: 131.044};
    const el = document.getElementById('obj-google-maps');
    const options = {
        zoom: 4,
        center: uluru
    };

    new google.maps.Map(el, options)
});
